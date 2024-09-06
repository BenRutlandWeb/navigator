<?php

namespace Navigator\Http;

use JsonSerializable;
use Navigator\Foundation\Concerns\Arrayable;

class JsonResponse extends Response
{
    protected mixed $original;

    public function __construct(mixed $data = [], int $status = 200, array $headers = [], protected int $encodingOptions = 0)
    {
        parent::__construct('', $status, $headers);

        $this->setData($data)->header('content-type', 'application/json');
    }

    public function getData(): mixed
    {
        return $this->original;
    }

    public function setData(mixed $data = []): static
    {
        $this->original = $data;

        if ($data instanceof JsonSerializable) {
            $content = $data->jsonSerialize();
        } elseif ($data instanceof Arrayable) {
            $content = $data->toArray();
        } elseif (is_array($data)) {
            $content = $data;
        } elseif ($data && method_exists($data, 'to_array')) {
            $content = $data->to_array();
        } else {
            $content = $data;
        }

        return $this->setContent(
            wp_json_encode($content, $this->encodingOptions)
        );
    }

    public function get_data(): mixed
    {
        if (defined('REST_REQUEST')) {
            return $this->getData();
        }

        return parent::get_data();
    }
}
