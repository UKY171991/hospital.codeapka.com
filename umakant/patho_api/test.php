<?php
// patho_api/test.php - public API for tests (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        // return all relevant columns so UI can render full test table
        $stmt = $pdo->query("SELECT t.id,
            tc.name as category_name,
            t.category_id,
            t.name,
            t.description,
            t.price,
            t.unit,
            t.default_result,
            t.reference_range as normal_range,
            t.min,
            t.max,
            t.min_male,
            t.max_male,
            t.min_female,
            t.max_female,
            t.sub_heading,
            t.test_code,
            t.method,
            t.print_new_page,
            t.shortcut,
            t.added_by,
            u.username as added_by_username
            FROM tests t
            LEFT JOIN categories tc ON t.category_id = tc.id
            LEFT JOIN users u ON t.added_by = u.id
            ORDER BY t.id DESC");
        $rows = $stmt->fetchAll();
        json_response(['success'=>true,'data'=>$rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT t.*, tc.name as category_name, u.username as added_by_username
            FROM tests t
            LEFT JOIN categories tc ON t.category_id = tc.id
            LEFT JOIN users u ON t.added_by = u.id
            WHERE t.id = ?");
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Test not found'],404);
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save') {
        // require admin or master
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);

        $id = $_POST['id'] ?? '';
        $category_id = $_POST['category_id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? 0;
    $unit = trim($_POST['unit'] ?? '');
        $default_result = trim($_POST['default_result'] ?? '');
        $reference_range = trim($_POST['reference_range'] ?? '');
    $min = $_POST['min'] ?? null;
    $max = $_POST['max'] ?? null;
    $min_male = $_POST['min_male'] ?? null;
    $max_male = $_POST['max_male'] ?? null;
    $min_female = $_POST['min_female'] ?? null;
    $max_female = $_POST['max_female'] ?? null;
    $sub_heading = $_POST['sub_heading'] ?? 0;
        $test_code = trim($_POST['test_code'] ?? '');
        $method = trim($_POST['method'] ?? '');
        $print_new_page = $_POST['print_new_page'] ?? 0;
        $shortcut = trim($_POST['shortcut'] ?? '');

        // Server-side validation for ranges
        $ranges = [
            ['min'=>$min, 'max'=>$max, 'label'=>'General'],
            ['min'=>$min_male, 'max'=>$max_male, 'label'=>'Male'],
            ['min'=>$min_female, 'max'=>$max_female, 'label'=>'Female']
        ];
        foreach($ranges as $r){
            if($r['min'] !== null && $r['max'] !== null && $r['min'] !== '' && $r['max'] !== ''){
                if(!is_numeric($r['min']) || !is_numeric($r['max'])) json_response(['success'=>false,'message'=>$r['label'].' range must be numeric'],400);
                if(floatval($r['max']) < floatval($r['min'])) json_response(['success'=>false,'message'=>'Max Value ('.$r['label'].') cannot be less than Min Value ('.$r['label'].')'],400);
            }
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE tests SET category_id=?, name=?, description=?, price=?, unit=?, default_result=?, reference_range=?, min=?, max=?, min_male=?, max_male=?, min_female=?, max_female=?, sub_heading=?, test_code=?, method=?, print_new_page=?, shortcut=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$category_id, $name, $description, $price, $unit, $default_result, $reference_range, $min, $max, $min_male, $max_male, $min_female, $max_female, $sub_heading, $test_code, $method, $print_new_page, $shortcut, $id]);
            json_response(['success'=>true,'message'=>'Test updated']);
        } else {
                $added_by = $_SESSION['user_id'] ?? null;
                $data = ['category_id'=>$category_id, 'name'=>$name, 'description'=>$description, 'price'=>$price, 'unit'=>$unit, 'default_result'=>$default_result, 'reference_range'=>$reference_range, 'min'=>$min, 'max'=>$max, 'min_male'=>$min_male, 'max_male'=>$max_male, 'min_female'=>$min_female, 'max_female'=>$max_female, 'sub_heading'=>$sub_heading, 'test_code'=>$test_code, 'method'=>$method, 'print_new_page'=>$print_new_page, 'shortcut'=>$shortcut, 'added_by'=>$added_by];
                if ($test_code !== '') $unique = ['test_code'=>$test_code]; else $unique = ['name'=>$name, 'category_id'=>$category_id];
                $res = upsert_or_skip($pdo, 'tests', $unique, $data);

                // return the newly created/updated record with joined fields
                $stmt = $pdo->prepare("SELECT t.id,
                tc.name as category_name,
                t.category_id,
                t.name,
                t.description,
                t.price,
                t.unit,
                t.min,
                t.max,
                t.min_male,
                t.max_male,
                t.min_female,
                t.max_female,
                t.sub_heading,
                t.print_new_page,
                t.added_by,
                u.username as added_by_username
                FROM tests t
                LEFT JOIN categories tc ON t.category_id = tc.id
                LEFT JOIN users u ON t.added_by = u.id
                WHERE t.id = ?");
                $stmt->execute([$res['id']]);
                $newRecord = $stmt->fetch();
                json_response(['success'=>true,'message'=>'Test '.$res['action'], 'data'=>$newRecord]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM tests WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'Test deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>$e->getMessage()],500);
}
