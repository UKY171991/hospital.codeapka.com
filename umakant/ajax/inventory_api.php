<?php
// ajax/inventory_api.php - Inventory API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (session_status() === PHP_SESSION_NONE) { 
        session_start();
    }

    require_once '../inc/connection.php';
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        // Dashboard
        case 'get_dashboard_stats':
            getDashboardStats();
            break;
        case 'get_recent_transactions':
            getRecentTransactions();
            break;
            
        // Income
        case 'get_income_records':
            getIncomeRecords();
            break;
        case 'get_income':
            getIncome();
            break;
        case 'add_income':
            addIncome();
            break;
        case 'update_income':
            updateIncome();
            break;
        case 'delete_income':
            deleteIncome();
            break;
            
        // Expense
        case 'get_expense_records':
            getExpenseRecords();
            break;
        case 'get_expense':
            getExpense();
            break;
        case 'add_expense':
            addExpense();
            break;
        case 'update_expense':
            updateExpense();
            break;
        case 'delete_expense':
            deleteExpense();
            break;
            
        // Client
        case 'get_clients':
            getClients();
            break;
        case 'get_client':
            getClient();
            break;
        case 'get_client_details':
            getClientDetails();
            break;
        case 'add_client':
            addClient();
            break;
        case 'update_client':
            updateClient();
            break;
        case 'delete_client':
            deleteClient();
            break;
            
        default:
            throw new Exception('Invalid action specified');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

// Ensure tables exist
function ensureTablesExist() {
    global $pdo;
    
    // Create inventory_clients table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `inventory_clients` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `type` enum('Individual','Corporate','Insurance','Government') NOT NULL DEFAULT 'Individual',
        `email` varchar(255) DEFAULT NULL,
        `phone` varchar(20) NOT NULL,
        `address` text DEFAULT NULL,
        `city` varchar(100) DEFAULT NULL,
        `state` varchar(100) DEFAULT NULL,
        `pincode` varchar(10) DEFAULT NULL,
        `gst_number` varchar(50) DEFAULT NULL,
        `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
        `notes` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Create inventory_income table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `inventory_income` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `date` date NOT NULL,
        `category` varchar(100) NOT NULL,
        `client_id` int(11) DEFAULT NULL,
        `description` text NOT NULL,
        `amount` decimal(10,2) NOT NULL,
        `payment_method` enum('Cash','Card','UPI','Bank Transfer','Cheque') NOT NULL DEFAULT 'Cash',
        `notes` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Create inventory_expense table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `inventory_expense` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `date` date NOT NULL,
        `category` varchar(100) NOT NULL,
        `vendor` varchar(255) DEFAULT NULL,
        `description` text NOT NULL,
        `amount` decimal(10,2) NOT NULL,
        `payment_method` enum('Cash','Card','UPI','Bank Transfer','Cheque') NOT NULL DEFAULT 'Cash',
        `invoice_number` varchar(100) DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// Dashboard Functions
function getDashboardStats() {
    global $pdo;
    ensureTablesExist();
    
    // Get total income
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM inventory_income");
    $totalIncome = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total expense
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM inventory_expense");
    $totalExpense = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total clients
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM inventory_clients WHERE status = 'Active'");
    $totalClients = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_profit' => $totalIncome - $totalExpense,
            'total_clients' => $totalClients
        ]
    ]);
}

function getRecentTransactions() {
    global $pdo;
    
    $limit = intval($_GET['limit'] ?? 10);
    
    $sql = "
        (SELECT 'income' as type, i.id, i.date, i.category, i.description, i.amount, c.name as client_name
         FROM inventory_income i
         LEFT JOIN inventory_clients c ON i.client_id = c.id
         ORDER BY i.date DESC, i.id DESC
         LIMIT :limit)
        UNION ALL
        (SELECT 'expense' as type, e.id, e.date, e.category, e.description, e.amount, e.vendor as client_name
         FROM inventory_expense e
         ORDER BY e.date DESC, e.id DESC
         LIMIT :limit)
        ORDER BY date DESC, id DESC
        LIMIT :limit
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $transactions
    ]);
}

// Income Functions
function getIncomeRecords() {
    global $pdo;
    ensureTablesExist();
    
    $sql = "SELECT i.*, c.name as client_name 
            FROM inventory_income i
            LEFT JOIN inventory_clients c ON i.client_id = c.id
            ORDER BY i.date DESC, i.id DESC";
    
    $stmt = $pdo->query($sql);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $records
    ]);
}

function getIncome() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    
    $stmt = $pdo->prepare("SELECT * FROM inventory_income WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$record) {
        throw new Exception('Income record not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $record
    ]);
}

function addIncome() {
    global $pdo;
    ensureTablesExist();
    
    $sql = "INSERT INTO inventory_income (date, category, client_id, description, amount, payment_method, notes, created_at)
            VALUES (:date, :category, :client_id, :description, :amount, :payment_method, :notes, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':date' => $_POST['date'],
        ':category' => $_POST['category'],
        ':client_id' => $_POST['client_id'] ?: null,
        ':description' => $_POST['description'],
        ':amount' => $_POST['amount'],
        ':payment_method' => $_POST['payment_method'],
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Income added successfully',
        'id' => $pdo->lastInsertId()
    ]);
}

function updateIncome() {
    global $pdo;
    
    $sql = "UPDATE inventory_income 
            SET date = :date, category = :category, client_id = :client_id, 
                description = :description, amount = :amount, payment_method = :payment_method, 
                notes = :notes, updated_at = NOW()
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $_POST['id'],
        ':date' => $_POST['date'],
        ':category' => $_POST['category'],
        ':client_id' => $_POST['client_id'] ?: null,
        ':description' => $_POST['description'],
        ':amount' => $_POST['amount'],
        ':payment_method' => $_POST['payment_method'],
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Income updated successfully'
    ]);
}

function deleteIncome() {
    global $pdo;
    
    $id = intval($_POST['id'] ?? 0);
    
    $stmt = $pdo->prepare("DELETE FROM inventory_income WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Income deleted successfully'
    ]);
}

// Expense Functions
function getExpenseRecords() {
    global $pdo;
    ensureTablesExist();
    
    $sql = "SELECT * FROM inventory_expense ORDER BY date DESC, id DESC";
    
    $stmt = $pdo->query($sql);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $records
    ]);
}

function getExpense() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    
    $stmt = $pdo->prepare("SELECT * FROM inventory_expense WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$record) {
        throw new Exception('Expense record not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $record
    ]);
}

function addExpense() {
    global $pdo;
    ensureTablesExist();
    
    $sql = "INSERT INTO inventory_expense (date, category, vendor, description, amount, payment_method, invoice_number, notes, created_at)
            VALUES (:date, :category, :vendor, :description, :amount, :payment_method, :invoice_number, :notes, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':date' => $_POST['date'],
        ':category' => $_POST['category'],
        ':vendor' => $_POST['vendor'] ?? '',
        ':description' => $_POST['description'],
        ':amount' => $_POST['amount'],
        ':payment_method' => $_POST['payment_method'],
        ':invoice_number' => $_POST['invoice_number'] ?? '',
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Expense added successfully',
        'id' => $pdo->lastInsertId()
    ]);
}

function updateExpense() {
    global $pdo;
    
    $sql = "UPDATE inventory_expense 
            SET date = :date, category = :category, vendor = :vendor, 
                description = :description, amount = :amount, payment_method = :payment_method, 
                invoice_number = :invoice_number, notes = :notes, updated_at = NOW()
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $_POST['id'],
        ':date' => $_POST['date'],
        ':category' => $_POST['category'],
        ':vendor' => $_POST['vendor'] ?? '',
        ':description' => $_POST['description'],
        ':amount' => $_POST['amount'],
        ':payment_method' => $_POST['payment_method'],
        ':invoice_number' => $_POST['invoice_number'] ?? '',
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Expense updated successfully'
    ]);
}

function deleteExpense() {
    global $pdo;
    
    $id = intval($_POST['id'] ?? 0);
    
    $stmt = $pdo->prepare("DELETE FROM inventory_expense WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Expense deleted successfully'
    ]);
}

// Client Functions
function getClients() {
    global $pdo;
    ensureTablesExist();
    
    $sql = "SELECT * FROM inventory_clients ORDER BY name ASC";
    
    $stmt = $pdo->query($sql);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $clients
    ]);
}

function getClient() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    
    $stmt = $pdo->prepare("SELECT * FROM inventory_clients WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        throw new Exception('Client not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $client
    ]);
}

function getClientDetails() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    
    // Get client info
    $stmt = $pdo->prepare("SELECT * FROM inventory_clients WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        throw new Exception('Client not found');
    }
    
    // Get client transactions
    $stmt = $pdo->prepare("
        SELECT 'income' as type, date, description, amount 
        FROM inventory_income 
        WHERE client_id = :id 
        ORDER BY date DESC 
        LIMIT 10
    ");
    $stmt->execute([':id' => $id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total amount
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM inventory_income WHERE client_id = :id");
    $stmt->execute([':id' => $id]);
    $totalAmount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'client' => $client,
            'transactions' => $transactions,
            'total_amount' => $totalAmount
        ]
    ]);
}

function addClient() {
    global $pdo;
    ensureTablesExist();
    
    $sql = "INSERT INTO inventory_clients (name, type, email, phone, address, city, state, pincode, gst_number, status, notes, created_at)
            VALUES (:name, :type, :email, :phone, :address, :city, :state, :pincode, :gst_number, :status, :notes, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $_POST['name'],
        ':type' => $_POST['type'],
        ':email' => $_POST['email'] ?? '',
        ':phone' => $_POST['phone'],
        ':address' => $_POST['address'] ?? '',
        ':city' => $_POST['city'] ?? '',
        ':state' => $_POST['state'] ?? '',
        ':pincode' => $_POST['pincode'] ?? '',
        ':gst_number' => $_POST['gst_number'] ?? '',
        ':status' => $_POST['status'] ?? 'Active',
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Client added successfully',
        'id' => $pdo->lastInsertId()
    ]);
}

function updateClient() {
    global $pdo;
    
    $sql = "UPDATE inventory_clients 
            SET name = :name, type = :type, email = :email, phone = :phone, 
                address = :address, city = :city, state = :state, pincode = :pincode, 
                gst_number = :gst_number, status = :status, notes = :notes, updated_at = NOW()
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $_POST['id'],
        ':name' => $_POST['name'],
        ':type' => $_POST['type'],
        ':email' => $_POST['email'] ?? '',
        ':phone' => $_POST['phone'],
        ':address' => $_POST['address'] ?? '',
        ':city' => $_POST['city'] ?? '',
        ':state' => $_POST['state'] ?? '',
        ':pincode' => $_POST['pincode'] ?? '',
        ':gst_number' => $_POST['gst_number'] ?? '',
        ':status' => $_POST['status'] ?? 'Active',
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Client updated successfully'
    ]);
}

function deleteClient() {
    global $pdo;
    
    $id = intval($_POST['id'] ?? 0);
    
    // Check if client has transactions
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM inventory_income WHERE client_id = :id");
    $stmt->execute([':id' => $id]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($count > 0) {
        throw new Exception('Cannot delete client with existing transactions');
    }
    
    $stmt = $pdo->prepare("DELETE FROM inventory_clients WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Client deleted successfully'
    ]);
}
?>
