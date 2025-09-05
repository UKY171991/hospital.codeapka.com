<?php
// test_sqlite.php - Test SQLite database
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>SQLite Database Test</h1>";

try {
    require_once 'inc/connection_sqlite.php';
    echo "<p>✅ SQLite connection successful</p>";
    
    // Count patients
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM patients");
    $total = $stmt->fetch()['total'];
    echo "<p>📊 Total patients: $total</p>";
    
    // Show some patients
    $stmt = $pdo->query("SELECT * FROM patients LIMIT 3");
    $patients = $stmt->fetchAll();
    
    echo "<h2>Sample Patients:</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>UHID</th><th>Name</th><th>Mobile</th><th>Gender</th></tr>";
    
    foreach ($patients as $patient) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($patient['id']) . "</td>";
        echo "<td>" . htmlspecialchars($patient['uhid']) . "</td>";
        echo "<td>" . htmlspecialchars($patient['name']) . "</td>";
        echo "<td>" . htmlspecialchars($patient['mobile']) . "</td>";
        echo "<td>" . htmlspecialchars($patient['gender']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test API-like query
    echo "<h2>API-like Query Test:</h2>";
    $page = 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    $stmt = $pdo->prepare("SELECT * FROM patients ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $patients = $stmt->fetchAll();
    
    $response = [
        'success' => true,
        'data' => $patients,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_records' => $total,
            'per_page' => $limit
        ]
    ];
    
    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
