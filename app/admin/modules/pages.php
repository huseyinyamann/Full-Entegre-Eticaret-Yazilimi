<?php
require_once APP_PATH . '/admin/crud.php';

crud_handle([
    'table' => 'pages', 'route' => 'sayfalar', 'entity' => 'page',
    'singular' => 'Sayfa', 'plural' => 'Sayfalar', 'icon' => 'bi-file-earmark-text',
    'description' => 'Hakkımızda, sözleşmeler, SSS gibi kurumsal sayfalar. Aktif sayfalar mağaza alt menüsünde listelenir.',
    'order' => 'sort_order, id',
    'title_field' => 'title',
    'columns' => ['title' => 'Başlık', 'slug' => 'Adres', 'sort_order' => 'Sıra'],
    'public_url' => fn($r) => 'sayfa/' . $r['slug'],
    'fields' => [
        'title' => ['type' => 'text', 'label' => 'Başlık', 'required' => true, 'max' => 190],
        'content' => ['type' => 'html', 'label' => 'İçerik', 'rows' => 16],
        'slug' => ['type' => 'slug', 'label' => 'URL', 'prefix' => 'sayfa/', 'side' => true],
        'is_active' => ['type' => 'switch', 'label' => 'Yayında', 'side' => true],
        'sort_order' => ['type' => 'number', 'label' => 'Sıra', 'side' => true],
        'meta_title' => ['type' => 'text', 'label' => 'SEO başlığı', 'max' => 190, 'side' => true],
        'meta_description' => ['type' => 'textarea', 'label' => 'Meta açıklama', 'max' => 300, 'side' => true],
    ],
], $action, $id);
