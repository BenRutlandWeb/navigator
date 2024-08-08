<?php

namespace Navigator\Http\Client;

use ArrayAccess;
use JsonSerializable;
use LogicException;
use Navigator\Collections\Collection;
use Navigator\Foundation\Concerns\Arrayable;
use Stringable;
use WP_HTTP_Requests_Response;

class Response implements Arrayable, ArrayAccess, JsonSerializable, Stringable
{
    protected mixed $decoded = null;

    public function __construct(protected WP_HTTP_Requests_Response $response)
    {
        //
    }

    public function body(): string
    {
        return $this->response->get_data();
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        if (!$this->decoded) {
            $this->decoded = json_decode($this->body(), true);
        }

        if (!$key) {
            return $this->decoded;
        }

        return $this->decoded[$key] ?? $default;
    }

    public function collect(): Collection
    {
        return Collection::make($this->json());
    }

    public function object(): mixed
    {
        return json_decode($this->body(), false);
    }

    public function header(string $header): ?string
    {
        return $this->headers()[$header] ?? null;
    }

    /** @return array<string, string> */
    public function headers(): array
    {
        return $this->response->get_headers()->getAll();
    }

    public function status(): int
    {
        return $this->response->get_status();
    }

    public function statusMessage(): string
    {
        return get_status_header_desc($this->status());
    }

    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function ok(): bool
    {
        return $this->status() === 200;
    }

    public function redirect(): bool
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    public function clientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    public function serverError(): bool
    {
        return $this->status() >= 500;
    }

    /** @return array<string, string> */
    public function cookies(): array
    {
        return $this->response->get_cookies();
    }

    public function toWordPressResponse(): WP_HTTP_Requests_Response
    {
        return $this->response;
    }

    /** @throws RequestException */
    public function throw(): static
    {
        if ($this->serverError() || $this->clientError()) {
            throw new RequestException($this);
        }

        return $this;
    }

    /** @param string $offset */
    public function offsetExists($offset): bool
    {
        return isset($this->json()[$offset]);
    }

    /** @param string $offset */
    public function offsetGet($offset): mixed
    {
        return $this->json()[$offset] ?? null;
    }

    /** @throws \LogicException */
    public function offsetSet($offset, $value): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    /** @throws \LogicException */
    public function offsetUnset($offset): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    public function __toString(): string
    {
        return $this->body();
    }

    public function toArray(): array
    {
        return $this->json();
    }

    public function jsonSerialize(): array
    {
        return $this->json();
    }

    public function __call(string $method, array $parameters): mixed
    {
        return $this->response->{$method}(...$parameters);
    }
}
