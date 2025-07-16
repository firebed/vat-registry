<?php

namespace Firebed\VatRegistry;

use SoapClient;
use SoapFault;
use SoapHeader;
use stdClass;
use Throwable;

class TaxisNet
{
    use Helpers;
    
    private const WSDL = 'https://www1.gsis.gr/wsaade/RgWsPublic2/RgWsPublic2?WSDL';
    private const XSD  = 'https://www1.gsis.gr/wsaade/RgWsPublic2/RgWsPublic2?xsd=1';

    private string $username;
    private string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @throws VatException
     */
    public function handle(string $vatToSearch, string $vatCalledBy = null): ?VatEntity
    {
        if ($this->blank($vatToSearch)) {
            throw new VatException("Please provide a VAT number");
        }

        try {
            $response = $this->request($vatToSearch, $vatCalledBy);
            return $this->handleResponse($response);
        } catch (Throwable $e) {
            throw new VatException($e->getMessage());
        }
    }

    /**
     * @throws VatException|SoapFault
     * @noinspection PhpUndefinedMethodInspection
     */
    protected function request(string $vatToSearch, string $vatCalledBy = null)
    {
        $client = $this->createSoapClient();
        $response = $client->rgWsPublic2AfmMethod([
            'INPUT_REC' => [
                'afm_called_by'  => $vatCalledBy,
                'afm_called_for' => $vatToSearch
            ]
        ]);

        $this->validateResponse($response);

        return $response->result->rg_ws_public2_result_rtType;
    }

    /**
     * @return SoapClient
     * @throws SoapFault
     */
    protected function createSoapClient(): SoapClient
    {
        $headers = $this->prepareHeaders($this->username, $this->password);

        $client = new SoapClient(self::WSDL, ['soap_version' => SOAP_1_2]);
        $client->__setSoapHeaders($headers);

        return $client;
    }

    protected function prepareHeaders(string $username, string $password): SoapHeader
    {
        $header = new stdClass();
        $header->UsernameToken = new stdClass();
        $header->UsernameToken->Username = $username;
        $header->UsernameToken->Password = $password;

        return new SoapHeader(self::XSD, 'Security', $header);
    }

    /**
     * @throws VatException
     */
    protected function validateResponse($response): void
    {
        if (!isset($response->result->rg_ws_public2_result_rtType)) {
            $this->invalidResponse();
        }
    }

    /**
     * @throws VatException
     */
    protected function invalidResponse()
    {
        throw new VatException("Invalid response from TaxisNet");
    }

    /**
     * @throws VatException
     */
    protected function handleResponse($response): ?VatEntity
    {
        $error = $response->error_rec;
        if ($this->filled($error->error_code)) {
            if ($error->error_code === "RG_WS_PUBLIC_WRONG_AFM") {
                return null;
            }

            throw new VatException(trim($error->error_descr));
        }

        return $this->parseResponse($response);
    }

    protected function parseResponse(object $data): VatEntity
    {
        $rec = $data->basic_rec;

        $vat = new VatEntity();
        $vat->vatNumber = $this->trim($rec->afm);
        $vat->taxAuthorityId = $this->trim($rec->doy);
        $vat->taxAuthorityName = $this->trim($rec->doy_descr);
        $vat->flagDescription = $this->trim($rec->i_ni_flag_descr);
        $vat->valid = $this->trim($rec->deactivation_flag) === "1";
        $vat->validityDescription = $this->trim($rec->deactivation_flag_descr);
        $vat->firmFlagDescription = $this->trim($rec->firm_flag_descr);
        $vat->legalName = preg_replace('!\s+!', ' ', $this->trim($rec->onomasia));
        $vat->commerceTitle = $this->trim($rec->commer_title);
        $vat->legalStatusDescription = $this->trim($rec->legal_status_descr);
        $vat->street = $this->trim($rec->postal_address);
        $vat->streetNumber = $this->trim($rec->postal_address_no);
        $vat->postcode = $this->trim($rec->postal_zip_code);
        $vat->city = $this->trim($rec->postal_area_description);
        $vat->registrationDate = $this->trim($rec->regist_date);
        $vat->stopDate = $this->trim($rec->stop_date);
        $vat->normalVat = $this->trim($rec->normal_vat_system_flag) === 'Y';

        if (isset($data->firm_act_tab->item)) {
            $firms = $this->wrapArray($data->firm_act_tab->item);

            foreach ($firms as $firm) {
                $vat->firms[] = [
                    'code'             => $this->trim($firm->firm_act_code),
                    'description'      => $this->trim($firm->firm_act_descr),
                    'kind'             => $this->trim($firm->firm_act_kind),
                    'kindDescription' => $this->trim($firm->firm_act_kind_descr),
                ];
            }
        } else {
            $vat->firms = [];
        }

        return $vat;
    }
}