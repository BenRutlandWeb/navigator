<?php

namespace Navigator\Pagination;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Navigator\Collections\Arr;
use Navigator\Foundation\Concerns\Htmlable;
use Navigator\View\View;
use Navigator\View\ViewFactory;
use Traversable;

/** @template T */
class Paginator implements ArrayAccess, Countable, Htmlable, IteratorAggregate, JsonSerializable
{
    protected string $path = '/';

    protected int $lastPage = 1;

    /** @var (callable(): string) $pathResolver */
    protected static $pathResolver;

    /** @var (callable(string): int) $pageResolver */
    protected static $pageResolver;

    /** @var (callable(): ViewFactory) $viewFactory */
    protected static $viewFactoryResolver;

    /** @param iterable<int, T> $items */
    public function __construct(protected iterable $items, protected int $total, protected int $perPage = 15, protected int $page = 1, protected string $pageName = 'page')
    {
        $this->path = static::resolveCurrentPath();
        $this->lastPage = max((int) ceil($total / $perPage), 1);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /** @return iterable<int, T> */
    public function items(): iterable
    {
        return $this->items;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function hasPages(): bool
    {
        return $this->currentPage() != 1 || $this->hasMorePages();
    }

    public function onFirstPage(): bool
    {
        return $this->currentPage() <= 1;
    }

    public function onLastPage(): bool
    {
        return !$this->hasMorePages();
    }

    public function firstPage(): int
    {
        return 1;
    }

    public function currentPage(): int
    {
        return $this->page;
    }

    public function lastPage(): int
    {
        return $this->lastPage;
    }

    public function hasMorePages(): bool
    {
        return $this->currentPage() < $this->lastPage();
    }

    public function url(int $page): string
    {
        return add_query_arg([$this->pageName => $page], $this->path());
    }

    public function firstPageUrl(): string
    {
        return $this->url($this->firstPage());
    }

    public function lastPageUrl(): string
    {
        return $this->url($this->lastPage());
    }

    public function previousPageUrl(): string
    {
        return $this->url(max($this->currentPage() - 1, 1));
    }

    public function nextPageUrl(): string
    {
        return $this->url(min($this->currentPage() + 1, $this->lastPage()));
    }

    public function firstItem(): ?int
    {
        return $this->count() > 0 ? ($this->currentPage() - 1) * $this->perPage() + 1 : null;
    }

    public function lastItem(): int
    {
        return $this->count() > 0 ? $this->firstItem() + $this->count() - 1 : 0;
    }

    public function path(): string
    {
        return static::resolveCurrentPath();
    }

    public function elements(): array
    {
        $pages = Arr::fill('', $this->lastPage(), 1);

        $return = [];

        foreach ($pages as $page => $value) {
            $return[$page] = $this->url($page);
        }

        return $return;
    }

    public function links(?string $view = null, array $data = []): View
    {
        return static::viewFactory()->make($view ?? 'navigator/Pagination/resources/views/default', Arr::merge($data, [
            'paginator' => $this,
            'elements'  => $this->elements(),
        ]));
    }

    public function jsonSerialize(): array
    {
        return [
            'total'          => $this->total(),
            'per_page'       => $this->perPage(),
            'current_page'   => $this->currentPage(),
            'first_page_url' => $this->firstPageUrl(),
            'last_page_url'  => $this->lastPageUrl(),
            'next_page_url'  => $this->nextPageUrl(),
            'prev_page_url'  => $this->previousPageUrl(),
            'path'           => $this->path(),
            'from'           => $this->firstItem(),
            'to'             => $this->lastItem(),
            'data'           => $this->items(),
            'links'          => $this->links(),
        ];
    }

    public static function resolveCurrentPath(string $default = '/'): string
    {
        if (isset(static::$pathResolver)) {
            return call_user_func(static::$pathResolver);
        }

        return $default;
    }

    /** @param (callable(): string) $resolver */
    public static function currentPathResolver(callable $resolver): void
    {
        static::$pathResolver = $resolver;
    }

    public static function resolveCurrentPage(string $pageName = 'page', int $default = 1): int
    {
        if (isset(static::$pageResolver)) {
            return (int) call_user_func(static::$pageResolver, $pageName);
        }

        return $default;
    }

    /** @param (callable(string): int) $resolver */
    public static function currentPageResolver(callable $resolver): void
    {
        static::$pageResolver = $resolver;
    }

    public static function viewFactory(): ViewFactory
    {
        return call_user_func(static::$viewFactoryResolver);
    }

    /** @param (callable(): ViewFactory) $resolver */
    public static function viewFactoryResolver(callable $resolver): void
    {
        static::$viewFactoryResolver = $resolver;
    }

    /** @param array-key $key */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * @param array-key $key
     * @return T
     */
    public function offsetGet($key): mixed
    {
        return $this->items[$key] ?? null;
    }

    /**
     * @param array-key $key
     * @param T $value
     */
    public function offsetSet($key, $value): void
    {
        $this->items[$key] = $value;
    }

    /** @param array-key $key */
    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }

    /** @return ArrayIterator<int, T> */
    public function getIterator(): Traversable
    {
        return $this->items instanceof IteratorAggregate ?
            $this->items->getIterator() :
            new ArrayIterator($this->items);
    }

    public function toHtml(): string
    {
        return $this->links();
    }
}
