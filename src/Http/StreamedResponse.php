<?php

namespace Navigator\Http;

use Navigator\Http\Concerns\ContentDisposition;

class StreamedResponse extends Response
{
    public function __construct(callable $callback, string $filename, int $status = 200, array $headers = [])
    {
        parent::__construct($callback(), $status, $headers);

        $this->withHeaders([
            'content-type'        => 'application/octet-stream',
            'content-disposition' => ContentDisposition::ATTACHMENT->value . ';filename=' . $filename,
        ]);
    }
}
