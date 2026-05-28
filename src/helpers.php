<?php

if (!function_exists('html_name_to_dot')) {
    function html_name_to_dot(string $name): string
    {
        $name = str_replace('[', '.', $name);
        return str_replace(']', '', $name);
    }
}

if (!function_exists('dot_to_html_name')) {
    function dot_to_html_name(string $name): string
    {
        return preg_replace_callback(
            '/\.([^.[]+|\[\])/',
            fn($matches) => '[' . trim($matches[1], '[]') . ']',
            $name
        );
    }
}

if (!function_exists('dot_to_array')) {
    function dot_to_array(string $dot, mixed $value): array
    {
        $keys = explode('.', $dot);
        $result = [];

        $current = &$result;
        foreach ($keys as $key) {
            $current[$key] = [];
            $current = &$current[$key];
        }

        $current = $value;

        return $result;
    }
}

if (!function_exists('set_url_query_params')) {
    function set_url_query_params(string $url, array $params): string
    {
        $parsedUrl = parse_url($url);
        $queryParams = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
        }

        foreach ($params as $key => $value) {
            $queryParams['search'] = $value;
        }

        $newQuery = http_build_query($queryParams);

        return "{$parsedUrl['scheme']}://{$parsedUrl['host']}:{$parsedUrl['port']}{$parsedUrl['path']}" . ($newQuery ? '?' . $newQuery : '');
    }
}
