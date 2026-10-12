<?php
/**
 * İzin listesi (allowlist) tabanlı HTML temizleyici.
 * Panelden girilen sayfa/blog/ürün açıklamaları kaydedilmeden önce buradan geçer:
 * listede olmayan etiketler kaldırılır (içerikleri korunur), script/style gibi
 * tehlikeli etiketler içerikleriyle silinir, on* olayları ve javascript: linkleri atılır.
 */

const SANITIZE_ALLOWED = [
    'p' => [], 'br' => [], 'hr' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [],
    'h2' => [], 'h3' => [], 'h4' => [], 'h5' => [], 'blockquote' => [], 'small' => [], 'span' => [],
    'ul' => [], 'ol' => [], 'li' => [],
    'a' => ['href', 'title', 'target'],
    'img' => ['src', 'alt', 'width', 'height'],
    'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => ['colspan', 'rowspan'], 'td' => ['colspan', 'rowspan'],
    'figure' => [], 'figcaption' => [], 'div' => [],
];

const SANITIZE_DROP_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'svg', 'math', 'template', 'noscript', 'link', 'meta', 'base'];

function sanitize_html(?string $html): string
{
    $html = trim((string) $html);
    if ($html === '') return '';

    $doc = new DOMDocument('1.0', 'UTF-8');
    $prev = libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="__root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($prev);

    $root = $doc->getElementById('__root');
    if (!$root) return '';
    sanitize_node($root);

    $out = '';
    foreach ($root->childNodes as $child) {
        $out .= $doc->saveHTML($child);
    }
    return trim($out);
}

function sanitize_node(DOMNode $node): void
{
    foreach (iterator_to_array($node->childNodes) as $child) {
        if ($child instanceof DOMComment || $child instanceof DOMProcessingInstruction) {
            $node->removeChild($child);
            continue;
        }
        if (!$child instanceof DOMElement) continue;

        $tag = strtolower($child->tagName);
        if (in_array($tag, SANITIZE_DROP_WITH_CONTENT, true)) {
            $node->removeChild($child);
            continue;
        }

        sanitize_node($child);

        if (!array_key_exists($tag, SANITIZE_ALLOWED)) {
            // Etiketi kaldır, içindekileri yerinde bırak
            while ($child->firstChild) {
                $node->insertBefore($child->firstChild, $child);
            }
            $node->removeChild($child);
            continue;
        }

        foreach (iterator_to_array($child->attributes) as $attr) {
            $name = strtolower($attr->name);
            if (!in_array($name, SANITIZE_ALLOWED[$tag], true)) {
                $child->removeAttribute($attr->name);
                continue;
            }
            if (in_array($name, ['href', 'src'], true) && !is_safe_url($attr->value, $name === 'src')) {
                $child->removeAttribute($attr->name);
            }
        }

        if ($tag === 'a' && $child->getAttribute('target') !== '') {
            $child->setAttribute('target', '_blank');
            $child->setAttribute('rel', 'noopener noreferrer');
        }
    }
}

/** Yalnızca http(s), göreli yol, # ve (link için) mailto/tel izinli. */
function is_safe_url(string $url, bool $isImage = false): bool
{
    $url = trim(preg_replace('/[\x00-\x20]+/', '', $url));
    if ($url === '') return false;
    if (preg_match('#^(/(?!/)|\#|\?|[a-z0-9._-]+(/|$))#i', $url) && !str_contains(strtolower(explode('/', $url)[0]), ':')) {
        return true;
    }
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    $allowed = $isImage ? ['http', 'https'] : ['http', 'https', 'mailto', 'tel'];
    return in_array($scheme, $allowed, true);
}
