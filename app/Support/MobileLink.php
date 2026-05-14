<?php

namespace App\Support;

class MobileLink
{
    public static function url(string $path = '/', array $query = []): string
    {
        $base = rtrim(config('app.mobile_deep_link_base', env('APP_DEEP_LINK_BASE', 'antropometri://app')), '/');
        $path = '/' . ltrim($path, '/');
        $url = $base . $path;

        $query = array_filter($query, fn ($value) => $value !== null && $value !== '');
        if (! empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }
}
