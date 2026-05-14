<?php

namespace App\Support;

class MobileLink
{
    public static function url(string $path = '/', array $query = []): string
    {
        $base = rtrim(config('app.mobile_app_link_base', config('app.url')), '/');
        $path = '/' . ltrim($path, '/');
        $url = $base . $path;

        $query = array_filter($query, fn ($value) => $value !== null && $value !== '');
        if (! empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    public static function schemeUrl(string $path = '/', array $query = []): string
    {
        $base = rtrim(config('app.mobile_deep_link_base', 'antropometri://app'), '/');
        $path = '/' . ltrim($path, '/');
        $url = $base . $path;

        $query = array_filter($query, fn ($value) => $value !== null && $value !== '');
        if (! empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }
}
