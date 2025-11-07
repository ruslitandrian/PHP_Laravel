<?php
/**
 * 初始化資料庫和範例資料
 * 執行此檔案來創建資料表和插入範例資料
 */

require_once __DIR__ . '/../config/db-config.php';
require_once __DIR__ . '/../config/db-connection.php';

try {
    // 初始化資料庫連接
    $db = new DatabaseConnection();
    $tableHandler = new TableHandler($db);
    
    // 創建資料表
    echo "正在創建資料表...\n";
    $tableHandler->initializeTable();
    echo "✓ 資料表創建成功\n";
    
    // 插入範例資料
    echo "正在插入範例資料...\n";
    $tableHandler->insertSampleData();
    echo "✓ 範例資料插入成功\n";
    
    echo "\n資料庫初始化完成！\n";
    echo "現在可以訪問: table-backend.php\n";
    
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
    exit(1);
}


