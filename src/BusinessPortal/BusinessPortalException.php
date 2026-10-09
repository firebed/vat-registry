<?php

namespace Firebed\VatRegistry\BusinessPortal;

use Exception;

/**
 * The code tells where the request failed:
 * - a cURL error number (CURLE_*) below 100 when the service did not answer,
 *   e.g. 28 (CURLE_OPERATION_TIMEDOUT), 6 (CURLE_COULDNT_RESOLVE_HOST) or 7 (CURLE_COULDNT_CONNECT);
 * - the HTTP status (>= 100) when the service answered with an error;
 * - 0 when the response is not valid JSON.
 */
class BusinessPortalException extends Exception
{

}