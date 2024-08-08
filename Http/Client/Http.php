<?php

namespace Navigator\Http\Client;

use Navigator\Collections\Arr;
use WP_HTTP_Requests_Response;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response as BaseResponse;

class Http
{
    protected string $baseUrl = '';

    protected string $bodyFormat = '';

    protected array $cookies = [];

    protected array $options = [];

    protected bool $async = false;

    protected array $promise = [];

    public function baseUrl(string $url): static
    {
        $this->baseUrl = $url;

        return $this;
    }

    public function bodyFormat(string $format): static
    {
        $this->bodyFormat = $format;

        return $this;
    }

    public function asJson(): static
    {
        return $this->bodyFormat('json')->contentType('application/json');
    }

    public function asForm(): static
    {
        return $this->bodyFormat('form')->contentType('application/x-www-form-urlencoded');
    }

    public function contentType(string $contentType): static
    {
        return $this->withHeaders(['Content-Type' => $contentType]);
    }

    public function acceptJson(): static
    {
        return $this->accept('application/json');
    }

    public function accept(string $contentType): static
    {
        return $this->withHeaders(['Accept' => $contentType]);
    }

    /** @param array<string, string> $headers */
    public function withHeaders(array $headers): static
    {
        $this->options = Arr::mergeRecursive($this->options, [
            'headers' => $headers,
        ]);

        return $this;
    }

    public function withBasicAuth(string $username, string $password): static
    {
        return $this->withHeaders(
            ['Authorization' => 'Basic ' . base64_encode($username . ':' . $password)]
        );
    }

    public function withToken(string $token, string $type = 'Bearer'): static
    {
        return $this->withHeaders(
            ['Authorization' => trim($type . ' ' . $token)]
        );
    }

    /** @param array<string, string> $cookies */
    public function withCookies(array $cookies): static
    {
        $this->options = Arr::mergeRecursive($this->options, [
            'cookies' => $cookies,
        ]);

        return $this;
    }

    public function withoutRedirecting(): static
    {
        $this->options['redirection'] = 0;

        return $this;
    }

    public function withoutVerifying(): static
    {
        $this->options['sslverify'] = false;
        $this->options['verify'] = false;

        return $this;
    }

    public function timeout(int $seconds): static
    {
        $this->options['timeout'] = $seconds;

        return $this;
    }

    /** @param array<string, mixed> $options */
    public function withOptions(array $options): static
    {
        $this->options = Arr::mergeRecursive($this->options, $options);

        return $this;
    }

    public function get(string $url, string|array|null $query = null): Response|static
    {
        return $this->send('GET', $url, ['body' => $query]);
    }

    public function post(string $url, string|array $data = []): Response|static
    {
        return $this->send('POST', $url, ['body' => $data]);
    }

    public function patch(string $url, string|array $data = []): Response|static
    {
        return $this->send('PATCH', $url, ['body' => $data]);
    }

    public function put(string $url, string|array $data = []): Response|static
    {
        return $this->send('PUT', $url, ['body' => $data]);
    }

    public function delete(string $url, string|array $data = []): Response|static
    {
        return $this->send('DELETE', $url, ['body' => $data]);
    }

    /** @param  array<string, mixed> $options */
    public function send(string $method, string $url, array $options = []): Response|static
    {
        $url = ltrim(rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/'), '/');

        if ($this->bodyFormat === 'json' && $options['body']) {
            $options['body'] = json_encode($options['body']);
        }

        if ($this->async) {
            return $this->makePromise($method, $url, $options);
        }

        return $this->sendRequest($method, $url, $options);
    }

    /**
     * @param array<string, mixed> $options
     * @throws ConnectionException
     */
    protected function sendRequest(string $method, string $url, array $options = []): Response
    {
        $response = wp_remote_request($url, $this->mergeOptions([
            'method' => $method,
        ], $options));

        if (!is_wp_error($response)) {
            return new Response($response['http_response']);
        }

        throw new ConnectionException($response->get_error_message());
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function mergeOptions(...$options): array
    {
        return Arr::mergeRecursive($this->options, ...$options);
    }

    public function async(bool $async = true): static
    {
        $this->async = $async;

        return $this;
    }

    /**
     * @param (callable(Pool): array<array-key, Response>) $callback
     * @return array<array-key, Response>
     */
    public function pool(callable $callback): array
    {
        $promises = [];

        $callback($pool = new Pool());

        foreach ($pool->getRequests() as $key => $item) {
            $promises[$key] = $item instanceof static ? $item->getPromise() : $item;
        }

        $responses = Arr::map(
            Requests::request_multiple($promises),
            [$this, 'resolveResponse']
        );

        return Arr::sortKeys($responses);
    }

    /** @param array<string, mixed> $options */
    protected function makePromise(string $method, string $url, array $options = []): static
    {
        $options = $this->mergeOptions($options);

        $this->promise = [
            'url'     => $url,
            'headers' => $options['headers'] ?? [],
            'data'    => $options['body'] ?? [],
            'type'    => $method,
            'options' => $options,
            'cookies' => $options['cookies'] ?? [],
        ];

        return $this;
    }

    public function getPromise(): array
    {
        return $this->promise;
    }

    /** @throws ConnectionException */
    protected function resolveResponse(BaseResponse|Exception $response): Response
    {
        if ($response instanceof Exception) {
            throw new ConnectionException($response->getMessage());
        }

        $response = new WP_HTTP_Requests_Response($response);

        return new Response($response);
    }
}
