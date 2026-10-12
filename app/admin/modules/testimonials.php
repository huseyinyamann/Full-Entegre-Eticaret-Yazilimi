<?php
require_once APP_PATH . '/admin/crud.php';

crud_handle([
    'table' => 'testimonials', 'route' => 'yorumlar', 'entity' => 'testimonial',
    'singular' => 'Yorum', 'plural' => 'Müşteri Yorumları', 'icon' => 'bi-chat-quote',
    'description' => 'Anasayfadaki “Müşterilerimiz ne diyor?” bölümü.',
    'order' => 'sort_order, id',
    'title_field' => 'name',
    'columns' => ['name' => 'Müşteri', 'city' => 'Şehir', 'rating' => 'Puan', 'text' => 'Yorum'],
    'fields' => [
        'name' => ['type' => 'text', 'label' => 'Müşteri adı', 'required' => true, 'max' => 100],
        'city' => ['type' => 'text', 'label' => 'Şehir', 'max' => 60],
        'text' => ['type' => 'textarea', 'label' => 'Yorum', 'required' => true, 'max' => 600, 'rows' => 4],
        'rating' => ['type' => 'rating', 'label' => 'Puan', 'default' => 5, 'side' => true],
        'is_active' => ['type' => 'switch', 'label' => 'Yayında', 'side' => true],
        'sort_order' => ['type' => 'number', 'label' => 'Sıra', 'side' => true],
    ],
], $action, $id);
