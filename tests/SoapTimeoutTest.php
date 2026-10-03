<?php

namespace Tests;

use Firebed\VatRegistry\TaxisNet;
use Firebed\VatRegistry\VatException;
use Firebed\VatRegistry\VIES;
use PHPUnit\Framework\TestCase;
use SoapClient;
use SoapFault;

class SoapTimeoutTest extends TestCase
{
    private string $defaultSocketTimeout;

    protected function setUp(): void
    {
        $this->defaultSocketTimeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', '30');
    }

    protected function tearDown(): void
    {
        ini_set('default_socket_timeout', $this->defaultSocketTimeout);
    }

    public function test_taxis_soap_options_are_merged_over_defaults()
    {
        $taxis = new class('username', 'password', ['connection_timeout' => 5, 'cache_wsdl' => WSDL_CACHE_BOTH]) extends TaxisNet {
            public function options(): array
            {
                return $this->getSoapOptions();
            }
        };

        $this->assertSame([
            'soap_version'       => SOAP_1_2,
            'connection_timeout' => 5,
            'cache_wsdl'         => WSDL_CACHE_BOTH,
        ], $taxis->options());
    }

    public function test_taxis_soap_options_override_defaults()
    {
        $taxis = new class('username', 'password', ['soap_version' => SOAP_1_1]) extends TaxisNet {
            public function options(): array
            {
                return $this->getSoapOptions();
            }
        };

        $this->assertSame(['soap_version' => SOAP_1_1], $taxis->options());
    }

    public function test_vies_soap_options_are_passed()
    {
        $vies = new class(['connection_timeout' => 5]) extends VIES {
            public function options(): array
            {
                return $this->getSoapOptions();
            }
        };

        $this->assertSame(['connection_timeout' => 5], $vies->options());
    }

    public function test_taxis_timeout_is_applied_and_restored_after_success()
    {
        $taxis = new class('username', 'password', [], 7) extends TaxisNet {
            public ?string $timeoutDuringRequest = null;

            protected function request(string $vatToSearch, ?string $vatCalledBy = null)
            {
                $this->timeoutDuringRequest = ini_get('default_socket_timeout');

                return (object) ['error_rec' => (object) ['error_code' => 'RG_WS_PUBLIC_WRONG_AFM']];
            }
        };

        $this->assertNull($taxis->handle('000000000'));
        $this->assertSame('7', $taxis->timeoutDuringRequest);
        $this->assertSame('30', ini_get('default_socket_timeout'));
    }

    public function test_taxis_timeout_is_restored_after_exception()
    {
        $taxis = new class('username', 'password', [], 7) extends TaxisNet {
            public ?string $timeoutDuringRequest = null;

            protected function request(string $vatToSearch, ?string $vatCalledBy = null)
            {
                $this->timeoutDuringRequest = ini_get('default_socket_timeout');

                throw new SoapFault('HTTP', 'Error Fetching http headers');
            }
        };

        try {
            $taxis->handle('094014201');
            $this->fail('VatException was not thrown');
        } catch (VatException) {
        }

        $this->assertSame('7', $taxis->timeoutDuringRequest);
        $this->assertSame('30', ini_get('default_socket_timeout'));
    }

    public function test_vies_timeout_is_applied_and_restored_after_success()
    {
        $vies = new class([], 7) extends VIES {
            public ?string $timeoutDuringRequest = null;

            protected function request(string $countryCode, string $vatNumber)
            {
                $this->timeoutDuringRequest = ini_get('default_socket_timeout');

                return null;
            }
        };

        $this->assertNull($vies->handle('EL', '000000000'));
        $this->assertSame('7', $vies->timeoutDuringRequest);
        $this->assertSame('30', ini_get('default_socket_timeout'));
    }

    public function test_vies_timeout_is_restored_after_exception()
    {
        $vies = new class([], 7) extends VIES {
            public ?string $timeoutDuringRequest = null;

            protected function request(string $countryCode, string $vatNumber)
            {
                $this->timeoutDuringRequest = ini_get('default_socket_timeout');

                throw new SoapFault('HTTP', 'Error Fetching http headers');
            }
        };

        try {
            $vies->handle('EL', '094014201');
            $this->fail('VatException was not thrown');
        } catch (VatException) {
        }

        $this->assertSame('7', $vies->timeoutDuringRequest);
        $this->assertSame('30', ini_get('default_socket_timeout'));
    }

    public function test_timeout_is_not_changed_when_not_set()
    {
        $vies = new class extends VIES {
            public ?string $timeoutDuringRequest = null;

            protected function request(string $countryCode, string $vatNumber)
            {
                $this->timeoutDuringRequest = ini_get('default_socket_timeout');

                return null;
            }
        };

        $vies->handle('EL', '000000000');

        $this->assertSame('30', $vies->timeoutDuringRequest);
    }

    public function test_previous_exception_is_preserved()
    {
        $fault = new SoapFault('HTTP', 'Error Fetching http headers');

        $taxis = new class('username', 'password') extends TaxisNet {
            public SoapFault $fault;

            protected function request(string $vatToSearch, ?string $vatCalledBy = null)
            {
                throw $this->fault;
            }
        };
        $taxis->fault = $fault;

        try {
            $taxis->handle('094014201');
            $this->fail('VatException was not thrown');
        } catch (VatException $e) {
            $this->assertSame('Error Fetching http headers', $e->getMessage());
            $this->assertSame($fault, $e->getPrevious());
        }
    }

    public function test_request_times_out_when_server_does_not_respond()
    {
        // The kernel completes the TCP handshake from the listen backlog, but
        // nothing is ever written back: the same as a server that hangs.
        $server = stream_socket_server('tcp://127.0.0.1:0');
        $address = stream_socket_get_name($server, false);

        $vies = new class([], 1) extends VIES {
            public string $location;

            protected function createSoapClient(): SoapClient
            {
                return new SoapClient(null, ['location' => $this->location, 'uri' => 'urn:test']);
            }
        };
        $vies->location = "http://$address/";

        $start = microtime(true);

        try {
            $vies->handle('EL', '094014201');
            $this->fail('VatException was not thrown');
        } catch (VatException $e) {
            $this->assertInstanceOf(SoapFault::class, $e->getPrevious());
            $this->assertSame('HTTP', $e->getPrevious()->faultcode);
        } finally {
            fclose($server);
        }

        $this->assertLessThan(10, microtime(true) - $start);
        $this->assertSame('30', ini_get('default_socket_timeout'));
    }
}
