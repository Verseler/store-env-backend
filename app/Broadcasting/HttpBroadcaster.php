<?php

namespace App\Broadcasting;

use Exception;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Contracts\Broadcasting\Broadcaster as BroadcasterContract;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HttpBroadcaster extends Broadcaster implements BroadcasterContract
{
    /**
     * The base URL of the broadcasting service.
     *
     * @var string
     */
    private string $baseUrl;

    /**
     * The token used to authenticate with the broadcasting service.
     *
     * @var string
     */
    private string $token;

    /**
     * The key used to authenticate with the broadcasting service.
     *
     * @var string
     */
    private string $key;

    /**
     * The type of authentication used to authenticate with the broadcasting service.
     *
     * @var string
     */
    private string $authType;

    /**
     * The endpoint to which the broadcast will be sent.
     *
     * @var string
     */
    private string $endpoint;

    /**
     * The headers to be sent with the broadcast.
     *
     * @var array
     */
    private array $headers = [];

    /**
     * Whether to verify the SSL certificate of the broadcasting service.
     *
     * @var bool
     */
    private bool $verify = true;

    public function __construct(
        private array $config = []
    ) {
        // set defaults
        $this->setBaseUrl($this->config['base_url'] ?? '');
        $this->setToken($this->config['token'] ?? '');
        $this->setKey($this->config['key'] ?? '');
        $this->setAuthType($this->config['auth'] ?? 'bearer');
        $this->setVerify($this->config['verify'] ?? true);
    }

    public function setBaseUrl(string $url): self
    {
        $this->baseUrl = rtrim($url, '/');
        return $this;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function setAuthType(string $authType): self
    {
        $this->authType = $authType;
        return $this;
    }

    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = ltrim($endpoint, '/');
        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function setVerify(bool $verify): self
    {
        $this->verify = $verify;
        return $this;
    }

    public function client(): PendingRequest
    {
        if ($this->authType === 'null') {
            return Http::withoutVerifying();
        }

        if ($this->authType === 'basic') {
            return Http::withBasicAuth($this->key, $this->token);
        }

        if ($this->authType === 'digest') {
            return Http::withDigestAuth($this->key, $this->token);
        }

        if ($this->authType === 'key') {
            return Http::withHeaders([
                'X-Api-Key' => $this->key,
            ]);
        }

        return Http::withToken($this->token);
    }

    /**
     * @throws Exception
     */
    public function auth($request)
    {

    }

    /**
     * @throws Exception
     */
    public function validAuthenticationResponse($request, $result)
    {

    }

    /**
     * @throws Exception
     */
    public function broadcast(array $channels, $event, array $payload = []): void
    {
        // Convert channels to the format required by your custom broadcaster
        $formattedChannels = $this->formatChannels($channels);

        // Send the payload to your custom broadcasting service
        $response = $this->send($formattedChannels, $event, $payload);

        if ($response->failed()) {
            throw new Exception($response->reason());
        }
    }

    protected function formatChannels(array $channels): array
    {
        return array_map(function ($channel) {
            return str_replace('private-', '', $channel);
        }, $channels);
    }

    protected function send(array $channels, string $event, array $payload): Response
    {
        $url = empty($this->endpoint)
            ? $this->baseUrl . '/' . $event
            : $this->baseUrl . '/' . $this->endpoint;

        $data = [
            'channels' => $channels,
            'event' => $event,
            'payload' => $payload,
        ];

        $request = $this->client();

        if ($this->headers) {
            $request->withHeaders($this->headers);
        }

        if (!$this->verify) {
            $request->withoutVerifying();
        }

        return $request->post($url, $data);
    }
}
