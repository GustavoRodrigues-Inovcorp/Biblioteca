<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$db = $app->make('db');

echo "=== VERIFICAÇÃO DA BASE DE DADOS ===\n\n";

// Listar tabelas
$tables = $db->select('SELECT name FROM sqlite_master WHERE type="table" ORDER BY name');
echo "TABELAS EXISTENTES:\n";
foreach($tables as $t) {
    echo "  - " . $t->name . "\n";
}

echo "\n=== VERIFICAÇÃO DA TABELA LOGS ===\n";
if (in_array('logs', array_column($tables, 'name'))) {
    echo "✓ Tabela 'logs' existe!\n";
    
    // Contar registos
    $count = $db->table('logs')->count();
    echo "✓ Registos de logs: " . $count . "\n";
    
    // Mostrar estrutura
    $columns = $db->select('PRAGMA table_info(logs)');
    echo "\nColunas:\n";
    foreach($columns as $col) {
        echo "  - " . $col->name . " (" . $col->type . ")\n";
    }
} else {
    echo "✗ Tabela 'logs' NÃO EXISTE!\n";
}

echo "\n=== VERIFICAÇÃO DOS MODELOS ===\n";
$modelFile = 'app/Models/ActivityLog.php';
if (file_exists($modelFile)) {
    echo "✓ Modelo ActivityLog.php existe\n";
} else {
    echo "✗ Modelo ActivityLog.php NÃO existe\n";
}

$serviceFile = 'app/Services/AuditLogger.php';
if (file_exists($serviceFile)) {
    echo "✓ Serviço AuditLogger.php existe\n";
} else {
    echo "✗ Serviço AuditLogger.php NÃO existe\n";
}

$controllerFile = 'app/Http/Controllers/Admin/LogController.php';
if (file_exists($controllerFile)) {
    echo "✓ Controlador LogController.php existe\n";
} else {
    echo "✗ Controlador LogController.php NÃO existe\n";
}

echo "\n";
