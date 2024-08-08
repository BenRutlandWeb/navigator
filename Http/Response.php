<?php

namespace Navigator\Http;

use JsonSerializable;
use Navigator\Foundation\Concerns\Htmlable;
use WP_REST_Response;

class Response extends WP_REST_Response implements Htmlable, JsonSerializable
{
    /** @param array<string, string> $headers */
    public function __construct(?string $content = '', int $status = 200, array $headers = [])
    {
        $this->header('content-type', 'text/html');

        parent::__construct($content, $status, $headers);
    }

    public static function createFromBase(WP_REST_Response $response): static
    {
        return new static(
            $response->get_data(),
            $response->get_status(),
            $response->get_headers()
        );
    }

    public function status(): int
    {
        return $this->get_status();
    }

    public function setStatusCode(int $status): static
    {
        $this->set_status($status);

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->get_data();
    }

    public function setContent(?string $content): static
    {
        $this->set_data($content);

        return $this;
    }

    public function isEmpty(): bool
    {
        return in_array($this->status(), [204, 304]);
    }

    /** @return array<string, string> */
    public function headers(): array
    {
        return $this->get_headers();
    }

    /** @param array<string, string> $headers */
    public function set_headers($headers): static
    {
        foreach ($headers as $key => $value) {
            $this->header($key, $value);
        }

        return $this;
    }

    /** @param array<string, string> $headers */
    public function withHeaders(array $headers): static
    {
        return $this->set_headers($headers);
    }

    /**
     * @param string $key
     * @param string $value
     * @param boolean $replace
     */
    public function header($key, $value, $replace = true): static
    {
        parent::header(ucwords(strtolower($key), '-'), $value, $replace);

        return $this;
    }

    public function send(): static
    {
        return $this->sendHeaders()->sendContent();
    }

    public function sendHeaders(): static
    {
        foreach ($this->headers() as $key => $value) {
            $value = preg_replace('/\s+/', ' ', $value);
            header("{$key}: {$value}");
        }

        status_header($this->status());

        return $this;
    }

    public function sendContent(): static
    {
        echo $this->getContent();

        return $this;
    }

    public function toHtml(): string
    {
        return $this->getContent();
    }

    public function __toString(): string
    {
        return $this->getContent();
    }
}
