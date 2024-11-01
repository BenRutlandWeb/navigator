<?php

namespace Navigator\Http;

use Navigator\Collections\Arr;
use Navigator\Foundation\Application;

class Url
{
    public function __construct(protected Application $app, protected Request $request)
    {
        //
    }

    public function withQuery(string $url, array $parameters = []): string
    {
        return add_query_arg($parameters, $url);
    }

    public function current(): string
    {
        return $this->request->url();
    }

    public function full(): string
    {
        return $this->request->fullUrl();
    }

    public function previous(?string $fallback = null): string
    {
        if ($previous = wp_get_referer()) {
            return $previous;
        }

        return $fallback ?: $this->home();
    }

    public function asset(string $path): string
    {
        return $this->app->assetUrl($path);
    }

    public function register(string $redirect = '/', array $parameters = []): string
    {
        return $this->withQuery(wp_registration_url(), Arr::merge($parameters, [
            'redirect_to' => urlencode($redirect),
        ]));
    }

    public function login(string $redirect = '/', array $parameters = []): string
    {
        return $this->withQuery(wp_login_url($redirect), $parameters);
    }

    public function logout(string $redirect = '/', array $parameters = []): string
    {
        return $this->withQuery(wp_logout_url($redirect), $parameters);
    }

    public function home(string $path = '', array $parameters = []): string
    {
        return $this->withQuery(home_url($path), $parameters);
    }

    public function ajax(string $action = '', array $parameters = []): string
    {
        $args = $action ? compact('action') : [];

        return $this->withQuery(
            $this->admin('admin-ajax.php'),
            Arr::merge($parameters, $args)
        );
    }

    public function rest(string $url = '', array $parameters = []): string
    {
        return $this->withQuery(rest_url($url), $parameters);
    }

    public function admin(string $path = '', array $parameters = []): string
    {
        return $this->withQuery(admin_url($path), $parameters);
    }

    public function archive(string $postType): string
    {
        return get_post_type_archive_link($postType) ?: '';
    }

    public function isValidUrl(string $url): bool
    {
        return wp_http_validate_url($url) !== false;
    }
}
