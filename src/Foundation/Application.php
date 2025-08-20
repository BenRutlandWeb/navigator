<?php

namespace Navigator\Foundation;

use ErrorException;
use Navigator\Acf\AcfServiceProvider;
use Navigator\Auth\AuthServiceProvider;
use Navigator\Cache\CacheServiceProvider;
use Navigator\Config\ConfigServiceProvider;
use Navigator\Config\Repository;
use Navigator\Console\ConsoleServiceProvider;
use Navigator\Container\Container;
use Navigator\Contracts\ServiceProviderInterface;
use Navigator\Database\DatabaseServiceProvider;
use Navigator\Encryption\EncryptionServiceProvider;
use Navigator\Events\Dispatcher;
use Navigator\Filesystem\FilesystemServiceProvider;
use Navigator\Hashing\HashServiceProvider;
use Navigator\Http\Exceptions\HttpException;
use Navigator\Http\HttpServiceProvider;
use Navigator\Http\ResponseFactory;
use Navigator\Mail\MailServiceProvider;
use Navigator\Notifications\NotificationsServiceProvider;
use Navigator\Pagination\PaginationServiceProvider;
use Navigator\Queue\QueueServiceProvider;
use Navigator\Routing\Router;
use Navigator\Schedule\ScheduleServiceProvider;
use Navigator\Session\SessionServiceProvider;
use Navigator\Str\Str;
use Navigator\Validation\ValidationServiceProvider;
use Navigator\View\ViewServiceProvider;
use Navigator\WordPress\WordPressServiceProvider;
use Throwable;
use Whoops\RunInterface;

class Application extends Container
{
    public readonly Environment $environment;

    /**
     * @var ServiceProviderInterface[]
     */
    protected array $providers = [];

    public readonly string $baseUrl;

    public function __construct(public readonly string $basePath)
    {
        static::setInstance($this);

        $this->instance(
            Environment::class,
            $environment = Environment::from(wp_get_environment_type())
        );

        $this->environment = $environment;

        $this->baseUrl = $this->resolveBaseUrl();

        $this->registerExceptionHandler();
        $this->registerCoreProviders();
    }

    public function registerCoreProviders(): void
    {
        $this->singleton(Dispatcher::class, fn() => new Dispatcher($this));

        $this->singleton(Router::class, fn(Application $app) => new Router(
            $app->get(Dispatcher::class)
        ));

        if ($this->runningInConsole()) {
            $this->register(ConsoleServiceProvider::class);
        }

        $this->register(AcfServiceProvider::class);
        $this->register(AuthServiceProvider::class);
        $this->register(CacheServiceProvider::class);
        $this->register(ConfigServiceProvider::class);
        $this->register(DatabaseServiceProvider::class);
        $this->register(EncryptionServiceProvider::class);
        $this->register(FilesystemServiceProvider::class);
        $this->register(FoundationServiceProvider::class);
        $this->register(HashServiceProvider::class);
        $this->register(HttpServiceProvider::class);
        $this->register(MailServiceProvider::class);
        $this->register(NotificationsServiceProvider::class);
        $this->register(PaginationServiceProvider::class);
        $this->register(QueueServiceProvider::class);
        $this->register(SessionServiceProvider::class);
        $this->register(ScheduleServiceProvider::class);
        $this->register(ValidationServiceProvider::class);
        $this->register(ViewServiceProvider::class);
        $this->register(WordPressServiceProvider::class);
    }

    public function registerExceptionHandler(): void
    {
        $this->register(ExceptionServiceProvider::class);

        add_filter('wp_php_error_message', function (string $message, array $error) {
            $e = new ErrorException(
                $error['message'],
                $error['type'],
                $error['type'],
                $error['file'],
                $error['line']
            );

            $this->handleException($e);
        }, 10, 2);
    }

    public function handleException(Throwable $e): void
    {
        if (!$this->environment->isProduction()) {
            $content = $this->get(RunInterface::class)->handleException($e);

            $this->get(ResponseFactory::class)->make(
                $content,
                $e instanceof HttpException ? $e->statusCode : 500,
                $e instanceof HttpException ? $e->headers : []
            )->send();
        } else {
            wp_die(__('There has been a critical error on this website.'));
        }
    }

    public function abort(int $code, string $message = '', array $headers = []): void
    {
        throw new HttpException($code, $message, $headers);
    }

    public function runningInConsole(): bool
    {
        return class_exists('WP_CLI');
    }

    public function boot(): void
    {
        try {
            $this->bootProviders();
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    protected function bootProviders(): void
    {
        foreach ($this->providers as $provider) {
            $provider->boot();
        }
    }

    /** @param class-string<ServiceProviderInterface> $provider */
    public function register(string $provider): void
    {
        $provider = new $provider($this);

        $provider->register();

        $this->providers[] = $provider;
    }

    /** @return array<int, ServiceProviderInterface> */
    public function getProviders(): array
    {
        return $this->providers;
    }

    public function env(string $key, mixed $default = null): mixed
    {
        if ($env = getenv($key)) {
            return $env;
        }

        return defined($key) ? constant($key) : $default;
    }

    /** @return Repository|mixed */
    public function config(?string $key = null, mixed $default = null): mixed
    {
        $repository = $this->get(Repository::class);

        return $key ? $repository->get($key, $default) : $repository;
    }

    public function path(string $path): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
    }

    public function assetUrl(string $path = ''): string
    {
        return $this->baseUrl . '/dist/' . trim($path, '/');
    }

    protected function resolveBaseUrl(): string
    {
        return esc_url_raw(Str::replace(
            wp_normalize_path(untrailingslashit(ABSPATH)),
            home_url(),
            wp_normalize_path($this->basePath)
        ));
    }
}
