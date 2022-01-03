<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Xr\Components\Xr;

use CurlHandle;

final class Client
{
    public function __construct(
        private int $port = 27420,
        private string $host = '0.0.0.0'
    ) {
    }

    public function getUrl(string $endpoint): string
    {
        return "http://{$this->host}:{$this->port}/{$endpoint}";
    }

    /**
     * @codeCoverageIgnore
     */
    public function sendMessage(Message $message): void
    {
        try {
            $curlError = null;
            $curlHandle = $this->getCurlHandle('message', $message->data());
            curl_exec($curlHandle);
            if (curl_errno($curlHandle)) {
                $curlError = curl_error($curlHandle);
            }
        } finally {
            curl_close($curlHandle);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    private function getCurlHandle(string $endpoint, array $data): CurlHandle
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $this->getUrl($endpoint));
        curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlHandle, CURLOPT_ENCODING, '');
        curl_setopt($curlHandle, CURLINFO_HEADER_OUT, true);
        curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($data));

        return $curlHandle;
    }
}