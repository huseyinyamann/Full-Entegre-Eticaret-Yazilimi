<?php

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;

    $c = config('db');
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $c['host'], $c['port'], $c['name']);
    $pdo = new PDO($dsn, $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

function db_run(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_all(string $sql, array $params = []): array
{
    return db_run($sql, $params)->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
    return db_run($sql, $params)->fetch() ?: null;
}

function db_value(string $sql, array $params = []): mixed
{
    $value = db_run($sql, $params)->fetchColumn();
    return $value === false ? null : $value;
}

/** Tablo/kolon adları kullanıcıdan gelmez; değerler daima parametre olarak bağlanır. */
function db_insert(string $table, array $data): int
{
    $cols = array_keys($data);
    $sql = sprintf('INSERT INTO `%s` (`%s`) VALUES (%s)', $table, implode('`, `', $cols), implode(', ', array_fill(0, count($cols), '?')));
    db_run($sql, array_values($data));
    return (int) db()->lastInsertId();
}

function db_update(string $table, array $data, int $id): void
{
    $set = implode(', ', array_map(fn($c) => "`$c` = ?", array_keys($data)));
    db_run("UPDATE `$table` SET $set WHERE id = ?", [...array_values($data), $id]);
}
