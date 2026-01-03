<?php
// ajax/followup_client_api.php - Followup Client Management API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

try {
    if (session_status() === PHP_SESSION_NONE) { 
        session_start();
    }

    require_once '../inc/connection.php';
    
    // Ensure table and columns exist before proceeding
    ensureTableExists();
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get_clients':
            getFollowupClients();
            break;
        case 'get_client':
            getFollowupClient();
            break;
        case 'add_client':
            addFollowupClient();
            break;
        case 'update_client':
            updateFollowupClient();
            break;
        case 'delete_client':
            deleteFollowupClient();
            break;
        case 'update_response':
            updateResponse();
            break;
        case 'get_responses':
            getResponses();
            break;
        case 'edit_response':
            editResponse();
            break;
        case 'delete_response':
            deleteResponse();
            break;
        case 'get_dashboard_stats':
            getDashboardStats();
            break;
        default:
            throw new Exception('Invalid action specified');
    }
    
} catch (Exception $e) {
    http_response_code(200);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Ensure table exists
function ensureTableExists() {
    global $pdo;
    
    // Create main table with all initial columns
    $pdo->exec("CREATE TABLE IF NOT EXISTS `followup_clients` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) DEFAULT NULL,
        `phone` varchar(20) NOT NULL,
        `company` varchar(255) DEFAULT NULL,
        `followup_title` varchar(255) DEFAULT NULL,
        `followup_message` text DEFAULT NULL,
        `response_message` text DEFAULT NULL,
        `next_followup_date` date DEFAULT NULL,
        `added_by` int(11) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Manually check and add columns if they are missing (more compatible than IF NOT EXISTS)
    $stmt = $pdo->query("DESC followup_clients");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('followup_title', $columns)) {
        $pdo->exec("ALTER TABLE followup_clients ADD COLUMN `followup_title` varchar(255) DEFAULT NULL AFTER `company`");
    }
    if (!in_array('response_message', $columns)) {
        $pdo->exec("ALTER TABLE followup_clients ADD COLUMN `response_message` text DEFAULT NULL AFTER `followup_message`");
    }
    if (!in_array('next_followup_date', $columns)) {
        $pdo->exec("ALTER TABLE followup_clients ADD COLUMN `next_followup_date` date DEFAULT NULL AFTER `response_message`");
    }
    if (!in_array('added_by', $columns)) {
        $pdo->exec("ALTER TABLE followup_clients ADD COLUMN `added_by` int(11) DEFAULT NULL AFTER `next_followup_date`");
    }

    // Create client_responses table for history
    $pdo->exec("CREATE TABLE IF NOT EXISTS `client_responses` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `client_id` int(11) NOT NULL,
        `response_message` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `client_id` (`client_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function getFollowupClients() {
    global $pdo;
    
    // Get user role and ID from session
    $userRole = $_SESSION['role'] ?? 'user';
    $userId = $_SESSION['user_id'] ?? null;
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search = trim($_GET['search'] ?? '');
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    $whereClauses = [];
    $params = [];
    
    if ($userRole !== 'master') {
        $whereClauses[] = "added_by = :user_id";
        $params[':user_id'] = $userId;
    }
    
    if (!empty($search)) {
        $whereClauses[] = "(name LIKE :search_name OR phone LIKE :search_phone OR email LIKE :search_email OR company LIKE :search_company)";
        $params[':search_name'] = "%$search%";
        $params[':search_phone'] = "%$search%";
        $params[':search_email'] = "%$search%";
        $params[':search_company'] = "%$search%";
    }
    
    $whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM followup_clients $whereSql";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    // Get records - Urgent/Upcoming next followups first, then others by latest activity
    $sql = "SELECT c.*, t.content as latest_template_content 
            FROM followup_clients c 
            LEFT JOIN followup_templates t ON c.followup_title = t.template_name
            $whereSql 
            ORDER BY 
                CASE 
                    WHEN c.next_followup_date IS NOT NULL AND c.next_followup_date <= CURDATE() THEN 1 -- Overdue/Today (High Priority)
                    WHEN c.next_followup_date IS NOT NULL THEN 2 -- Future Followup
                    ELSE 3 -- No Followup set
                    END ASC,
                c.next_followup_date ASC,
                COALESCE(c.updated_at, c.created_at) DESC 
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If template exists, use its content as the message (stripped of HTML)
    foreach ($clients as &$client) {
        if (!empty($client['latest_template_content'])) {
            $client['followup_message'] = trim(strip_tags(str_replace(['<br>', '<br/>', '<p>', '</p>'], ["\n", "\n", "", "\n"], $client['latest_template_content'])));
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $clients,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords
        ]
    ]);
}

function getFollowupClient() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    
    // Get user role and ID from session
    $userRole = $_SESSION['role'] ?? 'user';
    $userId = $_SESSION['user_id'] ?? null;
    
    // Build query based on user role
    if ($userRole === 'master') {
        $stmt = $pdo->prepare("SELECT c.*, t.content as latest_template_content 
                               FROM followup_clients c 
                               LEFT JOIN followup_templates t ON c.followup_title = t.template_name 
                               WHERE c.id = :id");
        $stmt->execute([':id' => $id]);
    } else {
        $stmt = $pdo->prepare("SELECT c.*, t.content as latest_template_content 
                               FROM followup_clients c 
                               LEFT JOIN followup_templates t ON c.followup_title = t.template_name 
                               WHERE c.id = :id AND c.added_by = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client && !empty($client['latest_template_content'])) {
        $client['followup_message'] = trim(strip_tags(str_replace(['<br>', '<br/>', '<p>', '</p>'], ["\n", "\n", "", "\n"], $client['latest_template_content'])));
    }
    
    if (!$client) {
        throw new Exception('Client not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $client
    ]);
}

function updateFollowupClient() {
    global $pdo;
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        throw new Exception('Invalid client ID');
    }
    
    if (empty($_POST['name'])) {
        throw new Exception('Name is required');
    }
    
    if (empty($_POST['email']) && empty($_POST['phone'])) {
        throw new Exception('Either Email or Phone is required');
    }
    
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = preg_replace('/[^\d+]/', '', $_POST['phone'] ?? ''); // Clean phone: allow digits and +
    $name = trim($_POST['name'] ?? '');
    
    // Check for duplicates
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $id]);
        if ($stmt->fetch()) {
            throw new Exception('A client with this email already exists');
        }
    }
    
    if (!empty($phone)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE phone = :phone AND id != :id");
        $stmt->execute([':phone' => $phone, ':id' => $id]);
        if ($stmt->fetch()) {
            throw new Exception('A client with this phone number already exists');
        }
    }

    // Optional: Check for duplicate Name + Phone combination if strict uniqueness is needed
    // But since phone is already checked globally, this is redundant unless we allow same phone for different names.
    
    $sql = "UPDATE followup_clients 
            SET name = :name, email = :email, phone = :phone, company = :company, 
                followup_message = :followup_message, followup_title = :followup_title, 
                response_message = :response_message, next_followup_date = :next_followup_date, updated_at = NOW()
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':company' => $_POST['company'] ?? '',
        ':followup_message' => $_POST['followup_message'] ?? '',
        ':followup_title' => $_POST['followup_title'] ?? '',
        ':response_message' => $_POST['response_message'] ?? '',
        ':next_followup_date' => !empty($_POST['next_followup_date']) ? $_POST['next_followup_date'] : null
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Client updated successfully'
    ]);
}

function addFollowupClient() {
    global $pdo;
    
    if (empty($_POST['name'])) {
        throw new Exception('Name is required');
    }
    
    if (empty($_POST['email']) && empty($_POST['phone'])) {
        throw new Exception('Either Email or Phone is required');
    }
    
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = preg_replace('/[^\d+]/', '', $_POST['phone'] ?? ''); // Clean phone: allow digits and +
    $name = trim($_POST['name'] ?? '');
    
    // Check for duplicates
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            throw new Exception('A client with this email already exists');
        }
    }
    
    if (!empty($phone)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE phone = :phone");
        $stmt->execute([':phone' => $phone]);
        if ($stmt->fetch()) {
            throw new Exception('A client with this phone number already exists');
        }
    }

    // Check if a client with the same name and phone/email already exists
    // (This is mostly covered by the above, but good for clarity)
    
    $sql = "INSERT INTO followup_clients (name, email, phone, company, followup_message, followup_title, next_followup_date, added_by, created_at)
            VALUES (:name, :email, :phone, :company, :followup_message, :followup_title, :next_followup_date, :added_by, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':company' => $_POST['company'] ?? '',
        ':followup_message' => $_POST['followup_message'] ?? '',
        ':followup_title' => $_POST['followup_title'] ?? '',
        ':next_followup_date' => !empty($_POST['next_followup_date']) ? $_POST['next_followup_date'] : null,
        ':added_by' => $_SESSION['user_id'] ?? null
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Client added successfully',
        'id' => $pdo->lastInsertId()
    ]);
}

function deleteFollowupClient() {
    global $pdo;
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        throw new Exception('Invalid client ID');
    }
    
    $stmt = $pdo->prepare("DELETE FROM followup_clients WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Client deleted successfully'
        ]);
    } else {
        throw new Exception('Client not found');
    }
}
function updateResponse() {
    global $pdo;
    $id = intval($_POST['id'] ?? 0);
    $response = trim($_POST['response_message'] ?? '');
    $next_followup_date = !empty($_POST['next_followup_date']) ? $_POST['next_followup_date'] : null;
    
    if ($id <= 0) throw new Exception('Invalid ID');
    if (empty($response) && empty($next_followup_date)) {
        throw new Exception('Response message or next followup date is required');
    }
    
    // Insert into history table ONLY if message is provided
    if (!empty($response)) {
        $stmt = $pdo->prepare("INSERT INTO client_responses (client_id, response_message, created_at) VALUES (:client_id, :response, NOW())");
        $stmt->execute([':client_id' => $id, ':response' => $response]);
    }
    
    // Update main table
    $sqlParts = ["updated_at = NOW()"];
    $params = [':id' => $id];
    
    if (!empty($response)) {
        $sqlParts[] = "response_message = :response";
        $params[':response'] = $response;
    }
    
    // Always update date if it was sent (could be null/cleared)
    if (isset($_POST['next_followup_date'])) {
        $sqlParts[] = "next_followup_date = :next_followup_date";
        $params[':next_followup_date'] = $next_followup_date;
    }
    
    $sql = "UPDATE followup_clients SET " . implode(", ", $sqlParts) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode(['success' => true, 'message' => 'Update successful']);
}

function getResponses() {
    global $pdo;
    $client_id = intval($_GET['client_id'] ?? 0);
    if ($client_id <= 0) throw new Exception('Invalid Client ID');
    
    $stmt = $pdo->prepare("SELECT * FROM client_responses WHERE client_id = :client_id ORDER BY created_at DESC");
    $stmt->execute([':client_id' => $client_id]);
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $responses]);
}

function editResponse() {
    global $pdo;
    $id = intval($_POST['id'] ?? 0);
    $clientId = intval($_POST['client_id'] ?? 0);
    $response = $_POST['response_message'] ?? '';
    $next_followup_date = !empty($_POST['next_followup_date']) ? $_POST['next_followup_date'] : null;
    
    if ($id <= 0) throw new Exception('Invalid ID');
    if (empty($response)) throw new Exception('Response message cannot be empty');
    
    $stmt = $pdo->prepare("UPDATE client_responses SET response_message = :response WHERE id = :id");
    $stmt->execute([':response' => $response, ':id' => $id]);
    
    // Also update client's next followup date if provided
    if ($clientId > 0 && isset($_POST['next_followup_date'])) {
        $stmt = $pdo->prepare("UPDATE followup_clients SET next_followup_date = :next_followup_date, updated_at = NOW() WHERE id = :client_id");
        $stmt->execute([':next_followup_date' => $next_followup_date, ':client_id' => $clientId]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Response updated successfully']);
}

function deleteResponse() {
    global $pdo;
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) throw new Exception('Invalid ID');
    
    $stmt = $pdo->prepare("DELETE FROM client_responses WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    echo json_encode(['success' => true, 'message' => 'Response deleted successfully']);
}

function getDashboardStats() {
    global $pdo;

    $userRole = $_SESSION['role'] ?? 'user';
    $userId = $_SESSION['user_id'] ?? null;
    $whereSql = "";
    $params = [];

    if ($userRole !== 'master') {
        $whereSql = "WHERE added_by = :user_id";
        $params[':user_id'] = $userId;
    }
    
    // counts
    $stats = [];
    
    // Total Clients
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM followup_clients $whereSql");
    $stmt->execute($params);
    $stats['total_clients'] = $stmt->fetchColumn();

    // Today's Followups
    $todaySql = $whereSql ? "$whereSql AND next_followup_date = CURDATE()" : "WHERE next_followup_date = CURDATE()";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM followup_clients $todaySql");
    $stmt->execute($params);
    $stats['today_followups'] = $stmt->fetchColumn();

    // Overdue Followups
    $overdueSql = $whereSql ? "$whereSql AND next_followup_date < CURDATE()" : "WHERE next_followup_date < CURDATE()";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM followup_clients $overdueSql");
    $stmt->execute($params);
    $stats['overdue_followups'] = $stmt->fetchColumn();

    // Upcoming Followups (Next 7 days)
    $upcomingSql = $whereSql ? "$whereSql AND next_followup_date > CURDATE() AND next_followup_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)" : "WHERE next_followup_date > CURDATE() AND next_followup_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM followup_clients $upcomingSql");
    $stmt->execute($params);
    $stats['upcoming_followups'] = $stmt->fetchColumn();

    // Total Templates
    try {
        // Check if table exists first to avoid error if feature not fully set up
        $stmt = $pdo->query("SHOW TABLES LIKE 'followup_templates'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM followup_templates");
            $stats['total_templates'] = $stmt->fetchColumn();
        } else {
            $stats['total_templates'] = 0;
        }
    } catch (Exception $e) {
        $stats['total_templates'] = 0;
    }

    // Recent Activity (Latest Updated/Created Clients)
    $recentSql = "SELECT c.*, t.content as latest_template_content 
                  FROM followup_clients c 
                  LEFT JOIN followup_templates t ON c.followup_title = t.template_name 
                  $whereSql ORDER BY COALESCE(c.updated_at, c.created_at) DESC LIMIT 10";
    $stmt = $pdo->prepare($recentSql);
    $stmt->execute($params);
    $stats['recent_clients'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($stats['recent_clients'] as &$rc) {
        if (!empty($rc['latest_template_content'])) {
            $rc['followup_message'] = trim(strip_tags(str_replace(['<br>', '<br/>', '<p>', '</p>'], ["\n", "\n", "", "\n"], $rc['latest_template_content'])));
        }
    }

    // Urgent Followups (Overdue or Today)
    $urgentSql = "SELECT c.*, t.content as latest_template_content 
                  FROM followup_clients c 
                  LEFT JOIN followup_templates t ON c.followup_title = t.template_name ";
    $urgentSql .= $whereSql ? " " . str_replace("WHERE", "WHERE c.", $whereSql) . " AND " : "WHERE ";
    $urgentSql .= "c.next_followup_date <= CURDATE() AND c.next_followup_date IS NOT NULL ORDER BY c.next_followup_date ASC LIMIT 10";
    $stmt = $pdo->prepare($urgentSql);
    $stmt->execute($params);
    $stats['urgent_followups'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($stats['urgent_followups'] as &$uf) {
        if (!empty($uf['latest_template_content'])) {
            $uf['followup_message'] = trim(strip_tags(str_replace(['<br>', '<br/>', '<p>', '</p>'], ["\n", "\n", "", "\n"], $uf['latest_template_content'])));
        }
    }

    // Status Breakdown (by Followup Title)
    $statusSql = "SELECT followup_title as status, COUNT(*) as count FROM followup_clients $whereSql GROUP BY followup_title ORDER BY count DESC";
    $stmt = $pdo->prepare($statusSql);
    $stmt->execute($params);
    $stats['status_stats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Latest Responses (Global)
    $respSql = "SELECT r.*, c.name as client_name, c.phone as client_phone 
                FROM client_responses r 
                JOIN followup_clients c ON r.client_id = c.id ";
    if ($userRole !== 'master') {
        $respSql .= " WHERE c.added_by = :user_id ";
    }
    $respSql .= " ORDER BY r.created_at DESC LIMIT 5";
    $stmt = $pdo->prepare($respSql);
    $stmt->execute($params);
    $stats['recent_responses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $stats]);
}
?>
