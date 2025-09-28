<?php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

function ownerColumnExists(PDO $pdo, string $column): bool {
    static $cache = [];
    if (array_key_exists($column, $cache)) {
        return $cache[$column];
    }
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'owners' AND COLUMN_NAME = ?");
        $stmt->execute([$column]);
        $cache[$column] = $stmt->fetchColumn() > 0;
    } catch (Throwable $e) {
        error_log('Owner column check failed: ' . $e->getMessage());
        $cache[$column] = false;
    }
    return $cache[$column];
}

if ($action === 'list'){
    try {
        $hasLink = ownerColumnExists($pdo, 'link');
        $hasAddedBy = ownerColumnExists($pdo, 'added_by');

        $fields = [
            'o.id',
            'o.name',
            'o.phone',
            'o.whatsapp',
            'o.email',
            'o.address'
        ];

        $fields[] = $hasLink ? 'o.link' : "NULL AS link";
        $fields[] = $hasAddedBy ? 'o.added_by' : "NULL AS added_by";
        $fields[] = $hasAddedBy ? 'u.username as added_by_username' : "NULL AS added_by_username";

        $sql = 'SELECT ' . implode(', ', $fields) . ' FROM owners o';
        if ($hasAddedBy) {
            $sql .= ' LEFT JOIN users u ON o.added_by = u.id';
        }
        $sql .= ' ORDER BY o.id DESC';

        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll();
        json_response(['success'=>true,'data'=>$rows]);
    } catch (Throwable $e) {
        error_log('Owner API list failed: ' . $e->getMessage());
        json_response([
            'success' => false,
            'message' => 'Failed to load owners',
            'debug' => ['message' => $e->getMessage()]
        ], 500);
    }
}

if ($action === 'get' && isset($_GET['id'])){
    try {
        $hasLink = ownerColumnExists($pdo, 'link');
        $hasAddedBy = ownerColumnExists($pdo, 'added_by');

        $fields = [
            'o.id',
            'o.name',
            'o.phone',
            'o.whatsapp',
            'o.email',
            'o.address'
        ];
        $fields[] = $hasLink ? 'o.link' : "NULL AS link";
        $fields[] = $hasAddedBy ? 'o.added_by' : "NULL AS added_by";
        $fields[] = $hasAddedBy ? 'u.username as added_by_username' : "NULL AS added_by_username";

        $sql = 'SELECT ' . implode(', ', $fields) . ' FROM owners o';
        if ($hasAddedBy) {
            $sql .= ' LEFT JOIN users u ON o.added_by = u.id';
        }
        $sql .= ' WHERE o.id = ? LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if ($row) {
            json_response(['success'=>true,'data'=>$row]);
        }
        json_response(['success'=>false,'message'=>'Owner not found'], 404);
    } catch (Throwable $e) {
        error_log('Owner API get failed: ' . $e->getMessage());
        json_response([
            'success' => false,
            'message' => 'Failed to fetch owner',
            'debug' => ['message' => $e->getMessage()]
        ], 500);
    }
}

if ($action === 'save'){
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $hasLink = ownerColumnExists($pdo, 'link');
    $hasAddedBy = ownerColumnExists($pdo, 'added_by');
    $hasUpdatedAt = ownerColumnExists($pdo, 'updated_at');

    if ($name === '') json_response(['success'=>false,'message'=>'Name required'],400);
    try{
        $fieldValues = [
            'name' => $name,
            'phone' => $phone,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'address' => $address
        ];
        if ($hasLink) {
            $fieldValues['link'] = $link;
        }

        if ($id){
            $setParts = [];
            $params = [];
            foreach ($fieldValues as $column => $value) {
                $setParts[] = "$column = ?";
                $params[] = $value;
            }
            if ($hasUpdatedAt) {
                $setParts[] = 'updated_at = NOW()';
            }
            $params[] = $id;

            $sql = 'UPDATE owners SET ' . implode(', ', $setParts) . ' WHERE id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            json_response(['success'=>true,'message'=>'Owner updated']);
        } else {
            $columns = array_keys($fieldValues);
            $placeholders = array_fill(0, count($columns), '?');
            $params = array_values($fieldValues);

            if ($hasAddedBy) {
                $columns[] = 'added_by';
                $placeholders[] = '?';
                $params[] = $_SESSION['user_id'] ?? null;
            }

            $sql = 'INSERT INTO owners (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            json_response(['success'=>true,'message'=>'Owner created']);
        }
    }catch(PDOException $e){
        error_log('Owner API save failed: ' . $e->getMessage());
        json_response(['success'=>false,'message'=>'Server error while saving owner','debug'=>['message'=>$e->getMessage()]],500);
    }
}

if ($action === 'delete' && isset($_POST['id'])){
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    try{
        $stmt = $pdo->prepare('DELETE FROM owners WHERE id=?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'Owner deleted']);
    } catch(PDOException $e){
        error_log('Owner API delete failed: ' . $e->getMessage());
        json_response(['success'=>false,'message'=>'Server error while deleting owner','debug'=>['message'=>$e->getMessage()]],500);
    }
}

json_response(['success'=>false,'message'=>'Invalid action'],400);
