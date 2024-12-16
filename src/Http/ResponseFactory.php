<?php

namespace Navigator\Http;

use Navigator\Http\Concerns\ContentDisposition;
use Navigator\View\ViewFactory;

class ResponseFactory
{
    public function __construct(protected ViewFactory $view)
    {
        //
    }

    public function json(mixed $content = [], int $status = 200, array $headers = [], int $options = 0): JsonResponse
    {
        return new JsonResponse($content, $status, $headers, $options);
    }

    public function make(mixed $content, int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }

    public function noContent(int $status = 204, array $headers = []): Response
    {
        return $this->make('', $status, $headers);
    }

    public function redirect(string $location, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($location, $status);
    }

    public function view(string $view, array $args = [], int $status = 200, array $headers = []): Response
    {
        return $this->make($this->view->make($view, $args), $status, $headers);
    }

    public function markdown(string $view, array $args = [], int $status = 200, array $headers = []): Response
    {
        return $this->make($this->view->markdown($view, $args), $status, $headers);
    }

    public function download(string $path, ?string $name = null, array $headers = [], ContentDisposition $disposition = ContentDisposition::ATTACHMENT): BinaryFileResponse
    {
        $response = new BinaryFileResponse($path, 200, $headers, $disposition);

        if ($name) {
            $response->setContentDisposition($disposition, $name);
        }

        return $response;
    }

    public function file(string $path, array $headers = []): BinaryFileResponse
    {
        return new BinaryFileResponse($path, 200, $headers);
    }

    public function streamDownload(callable $callback, string $filename): StreamedResponse
    {
        return new StreamedResponse($callback, $filename);
    }
}
