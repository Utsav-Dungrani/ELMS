<?php

function getCurrentRoute(): string {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($requestUri, \PHP_URL_PATH) ?? '/';
    $path = trim($path, '/');

    if ($path === '' || $path === 'index.php') {
        return 'dashboard';
    }

    $segments = explode('/', $path);
    $route = $segments[0] ?? 'dashboard';

    return $route === 'index.php' ? 'dashboard' : $route;
}

function buildUrl(string $route, array $params = []): string {
    $base = $route === 'dashboard' ? '/' : '/' . ltrim($route, '/');

    if (!empty($params)) {
        $query = http_build_query($params);
        if ($query !== '') {
            $base .= '?' . $query;
        }
    }

    return $base;
}
?>
