<?php

function config(string $key, $default = null)
{
    global $config;
    return $config[$key] ?? $default;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return config('base_url') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $file = PUBLIC_PATH . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? filemtime($file) : 1;
    return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

/**
 * Görsel yolu döndürür. uploads/ altında dosya varsa onu kullanır,
 * yoksa aynı boyutta geçici (placeholder) görsel verir.
 */
function img(?string $path, int $w = 600, int $h = 800, string $seed = 'yaman'): string
{
    if ($path && is_file(PUBLIC_PATH . '/uploads/' . $path)) {
        return url('uploads/' . $path);
    }
    return 'https://picsum.photos/seed/' . rawurlencode($seed) . "/{$w}/{$h}";
}

function money(float $amount): string
{
    return number_format($amount, 2, ',', '.') . ' ' . config('currency');
}

function slugify(string $text): string
{
    $map = ['ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
            'Ç' => 'c', 'Ğ' => 'g', 'İ' => 'i', 'Ö' => 'o', 'Ş' => 's', 'Ü' => 'u'];
    $text = mb_strtolower(strtr($text, $map));
    return trim(preg_replace('/[^a-z0-9]+/', '-', $text), '-');
}

function discount_rate(array $p): int
{
    return $p['old_price'] ? (int) round(100 - $p['price'] * 100 / $p['old_price']) : 0;
}

function partial(string $name, array $vars = []): void
{
    extract($vars);
    require APP_PATH . '/partials/' . $name . '.php';
}

function render(string $view, array $vars = []): void
{
    $vars += ['title' => null, 'bodyClass' => ''];
    extract($vars);
    require APP_PATH . '/partials/layout-top.php';
    require APP_PATH . '/views/' . $view . '.php';
    require APP_PATH . '/partials/layout-bottom.php';
}

function not_found(): void
{
    http_response_code(404);
    render('404', ['title' => 'Sayfa bulunamadı']);
    exit;
}

/** Hesabım sayfaları kendi yerleşimini (sol menülü panel) kullanır */
function account_render(string $view, array $vars = []): void
{
    $vars += ['title' => null, 'active' => ''];
    extract($vars);
    require APP_PATH . '/views/account/layout-top.php';
    require APP_PATH . '/views/account/' . $view . '.php';
    require APP_PATH . '/views/account/layout-bottom.php';
}

/** Sipariş durumu rozeti (vitrin) */
function order_status(string $status): string
{
    [$label, $tone] = ORDER_STATUSES[$status] ?? [$status, 'secondary'];
    return '<span class="o-status o-status--' . e($tone) . '">' . e($label) . '</span>';
}
