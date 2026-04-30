<?php
/**
 * Syncsity — PDO Singleton
 *
 * Prepared statements only. No string interpolation in SQL.
 */

declare(strict_types=1);

if (!defined('SYNC_ROOT')) define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';

class DB
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function connection(): PDO
    {
        if (self::$instance !== null) return self::$instance;

        $host   = (string)env('DB_HOST', 'localhost');
        $port   = (int)env('DB_PORT', 3306);
        $dbname = (string)env('DB_NAME', '');
        $user   = (string)env('DB_USER', '');
        $pass   = (string)env('DB_PASS', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        try {
            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]);
        } catch (PDOException $e) {
            error_log('[DB] connection failed: ' . $e->getMessage());
            http_response_code(503);
            die('Service temporarily unavailable. Please try again shortly.');
        }

        return self::$instance;
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function one(string $sql, array $params = []): array|false
    {
        return self::run($sql, $params)->fetch();
    }

    public static function val(string $sql, array $params = []): mixed
    {
        return self::run($sql, $params)->fetchColumn();
    }

    public static function insert(string $sql, array $params = []): string
    {
        self::run($sql, $params);
        return self::connection()->lastInsertId();
    }

    public static function begin(): void   { self::connection()->beginTransaction(); }
    public static function commit(): void  { self::connection()->commit(); }
    public static function rollback(): void
    {
        if (self::connection()->inTransaction()) self::connection()->rollBack();
    }
}
