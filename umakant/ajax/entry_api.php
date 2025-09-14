<?php
// ajax/entry_api.php - CRUD for entries
try {
    require_once __DIR__ . '/../inc/connection.php';
} catch (Exception $e) {
    // If database connection fails, provide fallback response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error. Please ensure MySQL is running.',
        'error' => $e->getMessage()
    ]);
    exit;
}

require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'stats') {
        // Get statistics for dashboard
        $stats = [];
        
        try {
            // Total entries
            $stmt = $pdo->query("SELECT COUNT(*) FROM entries");
            $stats['total'] = (int) $stmt->fetchColumn();
            
            // Pending entries
            $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'pending'");
            $stats['pending'] = (int) $stmt->fetchColumn();
            
            // Completed entries  
            $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'completed'");
            $stats['completed'] = (int) $stmt->fetchColumn();
            
            // Today's entries - try both date fields
            $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE DATE(COALESCE(test_date, entry_date, created_at)) = CURDATE()");
            $stats['today'] = (int) $stmt->fetchColumn();
            
        } catch (Exception $e) {
            // Fallback for missing columns
            $stats = ['total' => 0, 'pending' => 0, 'completed' => 0, 'today' => 0];
        }
        
        json_response(['success' => true, 'status' => 'success', 'data' => $stats]);
    } else if ($action === 'list') {
        // Updated to match new schema with comprehensive data
        $sql = "SELECT e.*, 
                   p.name AS patient_name, p.uhid, p.age, p.sex AS gender,
                   t.name AS test_name, COALESCE(t.unit, '') AS units,
                   t.reference_range, t.min_male, t.max_male, t.min_female, t.max_female,
                   d.name AS doctor_name,
                   u.username AS added_by_username
            FROM entries e 
            LEFT JOIN patients p ON e.patient_id = p.id 
            LEFT JOIN tests t ON e.test_id = t.id 
            LEFT JOIN doctors d ON e.doctor_id = d.id 
            LEFT JOIN users u ON e.added_by = u.id
            ORDER BY COALESCE(e.test_date, e.entry_date, e.created_at) DESC, e.id DESC";
        
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format data for frontend compatibility
        foreach ($rows as &$row) {
            // Ensure entry_date is available for frontend
            if (empty($row['entry_date']) && !empty($row['test_date'])) {
                $row['entry_date'] = $row['test_date'];
            }
            // Ensure unit field exists
            if (empty($row['unit']) && !empty($row['units'])) {
                $row['unit'] = $row['units'];
            }
        }
        
        json_response(['success' => true, 'data' => $rows, 'total' => count($rows)]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        // Return comprehensive entry data
        $sql = "SELECT e.*, 
                   p.name AS patient_name, p.uhid, p.age, p.sex AS gender,
                   t.name AS test_name, COALESCE(t.unit, '') AS units,
                   t.reference_range, t.min_male, t.max_male, t.min_female, t.max_female,
                   d.name AS doctor_name,
                   u.username AS added_by_username
            FROM entries e 
            LEFT JOIN patients p ON e.patient_id = p.id 
            LEFT JOIN tests t ON e.test_id = t.id 
            LEFT JOIN doctors d ON e.doctor_id = d.id 
            LEFT JOIN users u ON e.added_by = u.id
            WHERE e.id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            http_response_code(404);
            json_response(['success' => false, 'message' => 'Entry not found']);
            return;
        }
        
        // Ensure compatibility fields
        if (empty($row['entry_date']) && !empty($row['test_date'])) {
            $row['entry_date'] = $row['test_date'];
        }
        if (empty($row['unit']) && !empty($row['units'])) {
            $row['unit'] = $row['units'];
        }
        
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        // Parse JSON input if content-type is JSON
        $input = [];
        $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($content_type, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
        } else {
            $input = $_POST;
        }
        
        $id = $input['id'] ?? '';
        $patient_id = $input['patient_id'] ?? null;
        $doctor_id = $input['doctor_id'] ?? null;
        $test_id = $input['test_id'] ?? null;
        
        // Handle both old and new schema fields
        $entry_date = $input['entry_date'] ?? null;
        $test_date = $input['test_date'] ?? $entry_date ?? date('Y-m-d');
        $reported_date = $input['reported_date'] ?? date('Y-m-d H:i:s');
        $result_value = trim($input['result'] ?? $input['result_value'] ?? '');
        $result_status = $input['result_status'] ?? 'normal';
        $unit = trim($input['unit'] ?? '');
        $remarks = trim($input['remarks'] ?? $input['notes'] ?? '');
        $status = $input['status'] ?? 'pending';
        $added_by = $_SESSION['user_id'] ?? null;

        // Validation
        if (empty($patient_id) || empty($test_id)) {
            json_response(['success' => false, 'message' => 'Patient ID and Test ID are required'], 400);
        }

        if ($id) {
            // Update
            $stmt = $pdo->prepare('UPDATE entries SET patient_id=?, doctor_id=?, test_id=?, entry_date=?, test_date=?, reported_date=?, result_value=?, result_status=?, unit=?, remarks=?, status=?, added_by=? WHERE id=?');
            $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $test_date, $reported_date, $result_value, $result_status, $unit, $remarks, $status, $added_by, $id]);
            
            // Fetch updated entry
            $stmt = $pdo->prepare('SELECT e.*, p.name AS patient_name, t.name AS test_name, d.name AS doctor_name FROM entries e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN tests t ON e.test_id = t.id LEFT JOIN doctors d ON e.doctor_id = d.id WHERE e.id = ?');
            $stmt->execute([$id]);
            $saved_entry = $stmt->fetch(PDO::FETCH_ASSOC);
            
            json_response(['success' => true, 'message' => 'Entry updated successfully', 'data' => $saved_entry]);
        } else {
            // Create
            $stmt = $pdo->prepare('INSERT INTO entries (patient_id, doctor_id, test_id, entry_date, test_date, reported_date, result_value, result_status, unit, remarks, status, added_by, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW())');
            $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $test_date, $reported_date, $result_value, $result_status, $unit, $remarks, $status, $added_by]);
            
            $entry_id = $pdo->lastInsertId();
            
            // Fetch created entry
            $stmt = $pdo->prepare('SELECT e.*, p.name AS patient_name, t.name AS test_name, d.name AS doctor_name FROM entries e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN tests t ON e.test_id = t.id LEFT JOIN doctors d ON e.doctor_id = d.id WHERE e.id = ?');
            $stmt->execute([$entry_id]);
            $saved_entry = $stmt->fetch(PDO::FETCH_ASSOC);
            
            json_response(['success' => true, 'message' => 'Entry created successfully', 'data' => $saved_entry]);
        }
    } else if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Entry deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);

} catch (PDOException $e) {
    error_log('Entry API PDO error: ' . $e->getMessage());
    http_response_code(500);
    json_response(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
} catch (Exception $e) {
    error_log('Entry API error: ' . $e->getMessage());
    http_response_code(500);
    json_response(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
}
