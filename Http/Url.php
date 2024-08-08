<?php

namespace Navigator\Http;

use Navigator\Foundation\Application;

class Url
{
    public function __construct(protected Application $app, protected Request $request)
    {
        //
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
        return trim($this->app->config('app.asset_url', $this->home('/')), '/') . '/' . $path;
    }

    public function register(string $redirect = '/'): string
    {
        return add_query_arg(
            ['redirect_to' => urlencode($redirect)],
            wp_registration_url()
        );
    }

    public function login(string $redirect = '/'): string
    {
        return wp_login_url($redirect);
    }

    public function logout(string $redirect = '/'): string
    {
        return wp_logout_url($redirect);
    }

    public function home(string $path = ''): string
    {
        return home_url($path);
    }

    public function ajax(string $action = ''): string
    {
        $args = $action ? compact('action') : [];

        return add_query_arg($args, $this->admin('admin-ajax.php'));
    }

    public function rest(string $url = ''): string
    {
        return rest_url($url);
    }

    public function admin(string $path = ''): string
    {
        return admin_url($path);
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
