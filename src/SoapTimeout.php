<?php

namespace Firebed\VatRegistry;

trait SoapTimeout
{
    /**
     * Run the callback with default_socket_timeout set to the response timeout.
     *
     * ext/soap has no per-request read timeout and ignores the stream context's
     * http "timeout" option: a socket takes its read timeout from
     * default_socket_timeout when it is opened. The setting is therefore
     * overridden around the WSDL load and the request, then restored.
     *
     * @param  callable  $callback
     * @return mixed
     */
    protected function withResponseTimeout(callable $callback): mixed
    {
        if ($this->responseTimeout === null) {
            return $callback();
        }

        $previous = ini_set('default_socket_timeout', (string) $this->responseTimeout);

        try {
            return $callback();
        } finally {
            if ($previous !== false) {
                ini_set('default_socket_timeout', $previous);
            }
        }
    }
}
