<?php

namespace Firebed\VatRegistry\BusinessPortal;

use CurlHandle;
use Firebed\VatRegistry\Helpers;

class BusinessPortal
{
    use Helpers;

    private const BASE_URL = "https://opendata-api.businessportal.gr/api/opendata/v1";

    private ?int $lastHttpCode = null;

    public function __construct(protected string $apiKey, protected bool $verifyPeer = true)
    {
    }

    /**
     * Search for a company by its TIN (Tax Identification Number).
     * Only Greek TINs are supported.
     *
     * @throws InvalidTinException If the provided TIN is invalid.
     * @throws BusinessPortalException If an error occurs during the API request.
     */
    public function searchCompany(string $tin, ?bool $isActive = null): ?Company
    {
        $this->validateGreekTin($tin);

        $query = [
            'afm' => $tin,
            'resultsSize' => 1,
        ];

        if (! is_null($isActive)) {
            $query['isActive'] = $isActive;
        }

        $response = $this->cursor('/companies', $query);

        if (empty($response['searchResults']) || ! is_array($response['searchResults'])) {
            return null;
        }

        return new Company($response['searchResults'][0]);
    }

    /**
     * @throws BusinessPortalException
     */
    public function showCompany(string $registrationNumber): ?Company
    {
        $response = $this->cursor("/companies/$registrationNumber");

        if (empty($response)) {
            return null;
        }

        return new Company($response);
    }

    public function getLastHttpCode(): ?int
    {
        return $this->lastHttpCode;
    }

    /**
     * @throws BusinessPortalException
     */
    private function cursor(string $url, array $query = []): array
    {
        $ch = curl_init();

        $fullUrl = self::BASE_URL . $url;
        if (! empty($query)) {
            $fullUrl .= '?' . http_build_query($query);
        }

        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        $this->setupCurlSslOptions($ch);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'api_key: ' . $this->apiKey,
            'Accept: application/json',
        ]);

        $result = curl_exec($ch);
        $err = null;
        if (curl_errno($ch)) {
            $err = curl_error($ch);
            $code = curl_errno($ch);
            curl_close($ch);
            throw new BusinessPortalException($err, $code);
        }

        $this->lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($this->lastHttpCode < 200 || $this->lastHttpCode >= 300) {
            throw new BusinessPortalException($err ?: 'HTTP error: ' . $this->lastHttpCode, $this->lastHttpCode);
        }

        $decoded = json_decode($result, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new BusinessPortalException('Invalid JSON response: ' . json_last_error_msg());
        }

        return $decoded ?? [];
    }

    /**
     * @throws InvalidTinException
     */
    private function validateGreekTin(string $tin): void
    {
        if (strlen($tin) !== 9) {
            throw new InvalidTinException("The given vat number '$tin' is invalid.");
        }

        for ($i = 7, $multiplier = 1, $sum = 0; $i >= 0; $i--) {
            $sum += ((int) $tin[$i]) * $multiplier <<= 1;
        }

        if ($sum % 11 % 10 != $tin[8]) {
            throw new InvalidTinException("The given vat number '$tin' is invalid.");
        }
    }

    /**
     * This method sets up SSL options for the cURL handle.
     * All credits and blames go to claude.ai.
     *
     * @param CurlHandle|bool $ch
     * @return void
     */
    private function setupCurlSslOptions(CurlHandle|bool $ch): void
    {
        if (! $this->verifyPeer) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            return;
        }

        if (defined('CURLSSLOPT_NATIVE_CA') && version_compare(curl_version()['version'], '7.71', '>=')) {
            curl_setopt($ch, CURLOPT_SSL_OPTIONS, CURLSSLOPT_NATIVE_CA);
            return;
        }

        $caInfo = ini_get('curl.cainfo');
        if (! empty($caInfo) && file_exists($caInfo)) {
            curl_setopt($ch, CURLOPT_CAINFO, $caInfo);
            return;
        }

        $opensslCaFile = ini_get('openssl.cafile');
        if (! empty($opensslCaFile) && file_exists($opensslCaFile)) {
            curl_setopt($ch, CURLOPT_CAINFO, $opensslCaFile);
            return;
        }

        $caBundlePaths = [
            '/etc/ssl/certs/ca-certificates.crt',     // Debian/Ubuntu
            '/etc/pki/tls/certs/ca-bundle.crt',       // RedHat/CentOS
            '/etc/ssl/ca-bundle.pem',                 // OpenSUSE
            '/etc/pki/ca-trust/extracted/pem/tls-ca-bundle.pem', // CentOS 7+
            '/etc/ssl/cert.pem',                      // Alpine/macOS
            '/usr/local/etc/openssl/cert.pem',        // macOS Homebrew
        ];

        foreach ($caBundlePaths as $path) {
            if (file_exists($path)) {
                curl_setopt($ch, CURLOPT_CAINFO, $path);
                return;
            }
        }
    }
}