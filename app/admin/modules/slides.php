<?php
require_once APP_PATH . '/admin/crud.php';

crud_handle([
    'table' => 'slides', 'route' => 'slider', 'entity' => 'slide',
    'singular' => 'Slayt', 'plural' => 'Slider', 'icon' => 'bi-images',
    'description' => 'Anasayfanın en üstündeki tam genişlik slider. Önerilen görsel boyutu 1920×820.',
    'order' => 'sort_order, id',
    'title_field' => 'title',
    'columns' => ['image' => '', 'title' => 'Başlık', 'link' => 'Bağlantı', 'sort_order' => 'Sıra'],
    'fields' => [
        'title' => ['type' => 'textarea', 'label' => 'Başlık', 'required' => true, 'max' => 190, 'rows' => 2, 'help' => 'Satır atlamak için Enter kullanabilirsiniz.'],
        'text' => ['type' => 'text', 'label' => 'Alt metin', 'max' => 300],
        'button_text' => ['type' => 'text', 'label' => 'Buton yazısı', 'max' => 60],
        'link' => ['type' => 'url', 'label' => 'Buton bağlantısı', 'max' => 255, 'help' => 'Ör. kategori/kadin veya https://…'],
        'image' => ['type' => 'image', 'label' => 'Görsel', 'required' => true, 'side' => true],
        'is_active' => ['type' => 'switch', 'label' => 'Yayında', 'side' => true],
        'sort_order' => ['type' => 'number', 'label' => 'Sıra', 'side' => true],
    ],
], $action, $id);
