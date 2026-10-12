<?php
/**
 * Basit içerik modülleri (sayfa, blog, slider, yorum) için alan tanımından çalışan ortak CRUD.
 *
 * $def = [
 *   'table' => 'pages', 'route' => 'sayfalar', 'entity' => 'page',
 *   'singular' => 'Sayfa', 'plural' => 'Sayfalar', 'icon' => 'bi-file-earmark-text',
 *   'order' => 'sort_order, id',
 *   'columns' => ['title' => 'Başlık', ...],          // liste kolonları
 *   'fields' => ['title' => ['type' => 'text', 'label' => 'Başlık', 'required' => true, 'max' => 190], ...],
 *   'title_field' => 'title',
 *   'public_url' => fn($row) => 'sayfa/' . $row['slug'],  // isteğe bağlı
 * ]
 *
 * Alan tipleri: text, textarea, html (sanitize edilir), slug, image, number, switch, date, select, rating
 */

function crud_handle(array $def, ?string $action, ?int $id): void
{
    $table = $def['table'];

    if ($action === 'index') {
        $rows = db_all("SELECT * FROM `$table` ORDER BY {$def['order']}");
        admin_render('content/index', ['title' => $def['plural'], 'active' => $def['route'], 'def' => $def, 'rows' => $rows]);
        return;
    }

    $row = null;
    if ($id !== null) {
        $row = db_one("SELECT * FROM `$table` WHERE id = ?", [$id]);
        if (!$row) redirect($def['route']);
    }

    if ($action === 'delete') {
        db_run("DELETE FROM `$table` WHERE id = ?", [$id]);
        foreach ($def['fields'] as $name => $f) {
            if ($f['type'] === 'image') delete_upload($row[$name]);
        }
        audit('delete', $def['entity'], $id, $row[$def['title_field']]);
        flash('success', $def['singular'] . ' silindi.');
        redirect($def['route']);
    }

    $errors = [];
    if (is_post()) {
        $data = [];
        $newImages = [];
        foreach ($def['fields'] as $name => $f) {
            $label = $f['label'];
            switch ($f['type']) {
                case 'switch':
                    $data[$name] = input_bool($name);
                    break;
                case 'number':
                case 'rating':
                    $data[$name] = input_int($name, $f['default'] ?? 0);
                    if ($f['type'] === 'rating') $data[$name] = max(1, min(5, $data[$name]));
                    break;
                case 'date':
                    $v = input($name);
                    $d = DateTime::createFromFormat('Y-m-d', $v);
                    if (!$d || $d->format('Y-m-d') !== $v) $errors[$name] = "$label geçerli bir tarih olmalı.";
                    $data[$name] = $v;
                    break;
                case 'html':
                    $data[$name] = sanitize_html(input($name));
                    break;
                case 'select':
                    $data[$name] = array_key_exists(input($name), $f['options']) ? input($name) : null;
                    break;
                case 'slug':
                    break; // aşağıda başlıktan üretilir
                case 'image':
                    $err = null;
                    $path = upload_image($_FILES[$name] ?? [], $def['route'], $err);
                    if ($err) $errors[$name] = $err;
                    if ($path) $newImages[$name] = $path;
                    break;
                default:
                    $data[$name] = mb_substr(input($name), 0, $f['max'] ?? 190);
                    if ($f['type'] === 'url' && $data[$name] !== '' && !is_safe_url($data[$name])) {
                        $errors[$name] = "$label geçerli bir bağlantı olmalı (ör. kategori/kadin veya https://…).";
                    }
            }
            if (!empty($f['required']) && $f['type'] !== 'image' && ($data[$name] ?? '') === '' && !isset($errors[$name])) {
                $errors[$name] = "$label zorunludur.";
            }
            if (!empty($f['required']) && $f['type'] === 'image' && empty($newImages[$name]) && empty($row[$name]) && !isset($errors[$name])) {
                $errors[$name] = "$label zorunludur.";
            }
            if (array_key_exists($name, $data) && $data[$name] === '' && empty($f['required'])) {
                $data[$name] = null;
            }
        }
        foreach ($def['fields'] as $name => $f) {
            if ($f['type'] === 'slug') {
                $data[$name] = unique_slug($table, input($name) !== '' ? input($name) : (string) $data[$def['title_field']], $id);
            }
        }

        if ($errors) {
            array_map('delete_upload', $newImages);
            $row = array_merge($row ?? [], $data);
        } else {
            foreach ($newImages as $name => $path) {
                $data[$name] = $path;
                if ($row) delete_upload($row[$name]);
            }
            if ($id) {
                db_update($table, $data, $id);
                audit('update', $def['entity'], $id, $data[$def['title_field']]);
            } else {
                $id = db_insert($table, $data);
                audit('create', $def['entity'], $id, $data[$def['title_field']]);
            }
            flash('success', $def['singular'] . ' kaydedildi.');
            redirect($def['route'] . '/' . $id);
        }
    }

    if (!$row) {
        $row = [];
        foreach ($def['fields'] as $name => $f) {
            $row[$name] = $f['default'] ?? ($f['type'] === 'switch' ? 1 : '');
        }
    }

    admin_render('content/form', [
        'title' => ($id ? $def['singular'] . ' Düzenle' : 'Yeni ' . $def['singular']),
        'active' => $def['route'],
        'def' => $def,
        'row' => $row,
        'id' => $id,
        'errors' => $errors,
    ]);
}
