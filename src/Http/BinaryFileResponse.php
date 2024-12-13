<?php

namespace Navigator\Http;

use Navigator\Http\Concerns\ContentDisposition;

class BinaryFileResponse extends Response
{
    public function __construct(string $path, int $status = 200, array $headers = [], ContentDisposition $disposition = ContentDisposition::INLINE)
    {
        parent::__construct(file_get_contents($path), $status, $headers);

        $this->setContentDisposition($disposition, basename($path));

        if ($contentType = mime_content_type($path)) {
            $this->header('content-type', $contentType);
        }
    }

    public function setContentDisposition(ContentDisposition $disposition, ?string $name = null): void
    {
        $header = $name ? $disposition->value . ';filename=' . $name : $disposition->value;

        $this->header('content-disposition', $header);
    }
}
