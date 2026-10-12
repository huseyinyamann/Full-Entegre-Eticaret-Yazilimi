<?php
require_once APP_PATH . '/admin/crud.php';

crud_handle([
    'table' => 'posts', 'route' => 'blog', 'entity' => 'post',
    'singular' => 'Yazı', 'plural' => 'Blog', 'icon' => 'bi-journal-richtext',
    'description' => 'Blog yazıları. Yayın tarihi gelecekte olan yazılar o güne kadar mağazada görünmez.',
    'order' => 'published_at DESC, id DESC',
    'title_field' => 'title',
    'columns' => ['image' => '', 'title' => 'Başlık', 'published_at' => 'Yayın tarihi'],
    'public_url' => fn($r) => 'blog/' . $r['slug'],
    'fields' => [
        'title' => ['type' => 'text', 'label' => 'Başlık', 'required' => true, 'max' => 190],
        'excerpt' => ['type' => 'textarea', 'label' => 'Özet', 'max' => 500, 'help' => 'Liste sayfalarında ve yazı başında görünür.'],
        'content' => ['type' => 'html', 'label' => 'İçerik', 'rows' => 16],
        'image' => ['type' => 'image', 'label' => 'Kapak görseli', 'side' => true],
        'published_at' => ['type' => 'date', 'label' => 'Yayın tarihi', 'required' => true, 'default' => date('Y-m-d'), 'side' => true],
        'is_active' => ['type' => 'switch', 'label' => 'Yayında', 'side' => true],
        'slug' => ['type' => 'slug', 'label' => 'URL', 'prefix' => 'blog/', 'side' => true],
    ],
], $action, $id);
