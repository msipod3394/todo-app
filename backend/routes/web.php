<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

// データベース情報確認用ルート
Route::get('/db-status', function () {
    try {
        // データベース接続テスト
        $connection = DB::connection();
        $pdo = $connection->getPdo();

        // 基本情報取得
        $dbConfig = config('database.connections.mysql');
        $tables = DB::select('SHOW TABLES');
        $dbSize = DB::select("
            SELECT
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size (MB)',
                COUNT(*) as 'Table Count'
            FROM information_schema.tables
            WHERE table_schema = ?
        ", [$dbConfig['database']]);

        return response()->json([
            'status' => 'success',
            'connection' => 'MySQL接続成功！',
            'database_info' => [
                'host' => $dbConfig['host'],
                'port' => $dbConfig['port'],
                'database' => $dbConfig['database'],
                'username' => $dbConfig['username'],
                'mysql_version' => DB::select('SELECT VERSION() as version')[0]->version,
            ],
            'statistics' => [
                'table_count' => count($tables),
                'db_size_mb' => $dbSize[0]->{'DB Size (MB)'} ?? 0,
            ],
            'tables' => array_map(function($table) {
                return array_values((array)$table)[0];
            }, $tables),
            'timestamp' => now()->toDateTimeString(),
        ]);

    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'データベース接続エラー: ' . $e->getMessage(),
            'timestamp' => now()->toDateTimeString(),
        ], 500);
    }
});