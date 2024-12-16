<?php

namespace Navigator\Http;

use Navigator\Http\Concerns\ContentDisposition;

class StreamedResponse extends Response
{
    protected $callback;

    public function __construct(callable $callback, int $status = 200, array $headers = [], ContentDisposition $disposition = ContentDisposition::INLINE)
    {
        $this->callback = $callback;

        parent::__construct('', $status, $headers);

        $this->setContentDisposition($disposition);
    }

    public function setContentDisposition(ContentDisposition $disposition, ?string $name = null): void
    {
        $header = $name ? $disposition->value . ';filename=' . $name : $disposition->value;

        $this->header('content-disposition', $header);
    }

    public function sendContent(): static
    {
        $this->setContent(call_user_func($this->callback));

        echo $this->getContent();

        return $this;
    }
}
