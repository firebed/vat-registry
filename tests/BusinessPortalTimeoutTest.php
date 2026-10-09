<?php

namespace Tests;

use Firebed\VatRegistry\BusinessPortal\BusinessPortal;
use Firebed\VatRegistry\BusinessPortal\BusinessPortalException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BusinessPortalTimeoutTest extends TestCase
{
    public function test_timeouts_are_not_set_by_default()
    {
        $portal = new class('api-key') extends BusinessPortal {
            public function options(): array
            {
                return $this->timeoutOptions();
            }
        };

        $this->assertSame([], $portal->options());
    }

    public function test_timeout_options_are_set()
    {
        $portal = new class('api-key', true, 5, 10) extends BusinessPortal {
            public function options(): array
            {
                return $this->timeoutOptions();
            }
        };

        $this->assertSame([
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_NOSIGNAL       => 1,
        ], $portal->options());
    }

    public static function invalidTimeouts(): array
    {
        return [
            'zero connect timeout'     => [0, null],
            'negative connect timeout' => [-1, null],
            'zero timeout'             => [null, 0],
            'negative timeout'         => [null, -1],
        ];
    }

    #[DataProvider('invalidTimeouts')]
    public function test_invalid_timeouts_are_rejected(?int $connectTimeout, ?int $timeout)
    {
        $this->expectException(InvalidArgumentException::class);

        new BusinessPortal('api-key', true, $connectTimeout, $timeout);
    }

    public function test_request_times_out_when_server_does_not_respond()
    {
        // The kernel completes the TCP handshake from the listen backlog, but
        // nothing is ever written back: the same as a server that hangs.
        $server = stream_socket_server('tcp://127.0.0.1:0');
        $address = stream_socket_get_name($server, false);

        $portal = new class('api-key', true, null, 1) extends BusinessPortal {
            public string $url;

            protected function baseUrl(): string
            {
                return $this->url;
            }
        };
        $portal->url = "http://$address";

        $start = microtime(true);

        try {
            $portal->searchCompany('094014201');
            $this->fail('BusinessPortalException was not thrown');
        } catch (BusinessPortalException $e) {
            $this->assertSame(CURLE_OPERATION_TIMEDOUT, $e->getCode());
        } finally {
            fclose($server);
        }

        $this->assertLessThan(5, microtime(true) - $start);
    }
}
