<?php

namespace Navigator\Mail;

use Navigator\Collections\Arr;
use Navigator\Collections\Collection;
use Navigator\Contracts\MailableInterface;
use Navigator\Contracts\ShouldQueue;
use Navigator\Events\Dispatcher;
use Navigator\Foundation\Concerns\Htmlable;

class Mailer
{
    /** @var array<int, string> */
    protected array $to = [];

    protected ?string $from = null;

    /** @var array<int, string> */
    protected array $headers = [];

    /** @var array<int, string> */
    protected array $attachments = [];

    public function __construct(protected Dispatcher $dispatcher)
    {
        //
    }

    /** @param MailableInterface|string $address */
    public function formatAddress(MailableInterface|string $address, string $name = ''): string
    {
        if ($address instanceof MailableInterface) {
            [$address, $name] = [$address->email(), $address->name()];
        }

        return trim("{$name} <{$address}>");
    }

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function to(iterable|MailableInterface|string $address, string $name = ''): static
    {
        if (is_iterable($address)) {
            foreach ($address as $address) {
                $this->to($address);
            }
        }

        $this->to[] = $this->formatAddress($address, $name);

        return $this;
    }

    public function from(MailableInterface|string $address, string $name = ''): static
    {
        $this->from = $this->formatAddress($address, $name);

        return $this;
    }

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    protected function setAddress(string $key, iterable|MailableInterface|string $address, string $name = ''): static
    {
        if (is_iterable($address)) {
            foreach ($address as $address) {
                $this->header($key, $this->formatAddress($address));
            }

            return $this;
        }

        return $this->header($key, $this->formatAddress($address, $name));
    }

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function cc(iterable|MailableInterface|string $address, string $name = ''): static
    {
        return $this->setAddress('cc', $address, $name);
    }

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function bcc(iterable|MailableInterface|string $address, string $name = ''): static
    {
        return $this->setAddress('bcc', $address, $name);
    }

    /** @param iterable<int, MailableInterface|string>|MailableInterface|string $address */
    public function replyTo(iterable|MailableInterface|string $address, string $name = ''): static
    {
        return $this->setAddress('reply-to', $address, $name);
    }

    public function header(string $key, string $value): static
    {
        $this->headers[] = $this->formatHeader($key, $value);

        return $this;
    }

    /** @param array<string, string> $headers */
    public function headers(array $headers): static
    {
        foreach ($headers as $key => $value) {
            $this->header($key, $value);
        }

        return $this;
    }

    protected function formatHeader(string $key, string $value): string
    {
        return strtolower($key) . ': ' . $value;
    }

    /** @param array<int, string>|string $paths */
    public function attach(array|string $paths): static
    {
        $paths = is_array($paths) ? $paths : [$paths];

        foreach ($paths as $path) {
            $this->attachments[] = $path;
        }

        return $this;
    }

    public function send(Mailable $mail): bool
    {
        $mail->envelope($this);

        if ($this->from) {
            $this->header('from', $this->from);
        }

        $to = $this->to;

        $subject = $mail->subject();

        $content = $mail->content();

        if ($content instanceof Htmlable) {
            $this->header('content-type', 'text/html');
            $content = $content->toHtml();
        }

        $content = $this->dispatcher->filter('navigator_mailer_content', $content, $content);

        $headers = Collection::make($mail->headers())
            ->map(fn($value, $key) => $this->formatHeader($key, $value))
            ->merge($this->headers)
            ->values()
            ->all();

        $attachments = Arr::merge($this->attachments, $mail->attachments());

        if ($mail instanceof ShouldQueue) {
            SendQueuedMail::dispatch($to, $subject, $content, $headers, $attachments);
            return true;
        } else {
            return wp_mail($to, $subject, $content, $headers, $attachments);
        }
    }
}
