<?php
// data_scraper.php
// Full CRUD functionality for Data Scraper
include 'inc/connection.php';
include 'inc/auth.php'; // Ensure user is logged in

// Search Logic
$search = $_GET['search'] ?? '';
$searchQuery = "";
$params = [];

if (!empty($search)) {
    $searchQuery = "WHERE business_name LIKE ? OR business_category LIKE ? OR email_address LIKE ? OR mobile_number LIKE ? OR city LIKE ? OR country LIKE ? OR website_url LIKE ?";
    $params = array_fill(0, 7, "%$search%");
}

// Handle CSV Export - MUST BE BEFORE ANY HTML OUTPUT
if (isset($_GET['action']) && $_GET['action'] == 'export_csv') {
    // Clear any previous output
    if (ob_get_level()) ob_end_clean();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="scraper_data_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel UTF-8 compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, array('ID', 'Website URL', 'Business Name', 'Business Category', 'Email Address', 'Mobile Number', 'City', 'Country', 'Created At'));
    
    $stmt = $pdo->prepare("SELECT id, website_url, business_name, business_category, email_address, mobile_number, city, country, created_at FROM data_scraper $searchQuery ORDER BY id DESC");
    $stmt->execute($params);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}

// Handle AJAX Search Request
if (isset($_GET['ajax_search'])) {
    $search = $_GET['search'] ?? '';
    $searchQuery = "";
    $params = [];

    if (!empty($search)) {
        $searchQuery = "WHERE business_name LIKE ? OR business_category LIKE ? OR email_address LIKE ? OR mobile_number LIKE ? OR city LIKE ? OR country LIKE ? OR website_url LIKE ?";
        $params = array_fill(0, 7, "%$search%");
    }
    
    $stmt = $pdo->prepare("SELECT * FROM data_scraper $searchQuery ORDER BY id DESC");
    $stmt->execute($params);
    $dataList = $stmt->fetchAll();
    
    $counter = 1;
    if (count($dataList) > 0) {
        foreach($dataList as $data) {
            echo '<tr>';
            echo '<td>' . $counter++ . '</td>';
            echo '<td>' . htmlspecialchars($data['business_name']) . '</td>';
            echo '<td>' . htmlspecialchars($data['business_category']) . '</td>';
            echo '<td>' . htmlspecialchars($data['email_address']) . '</td>';
            echo '<td>' . htmlspecialchars($data['mobile_number']) . '</td>';
            echo '<td>' . htmlspecialchars($data['city']) . ', ' . htmlspecialchars($data['country']) . '</td>';
            echo '<td><a href="' . htmlspecialchars($data['website_url']) . '" target="_blank" title="' . htmlspecialchars($data['website_url']) . '"><i class="fas fa-link"></i> Link</a></td>';
            echo '<td>';
            echo '<div class="custom-control custom-switch">';
            echo '<input type="checkbox" class="custom-control-input status-toggle" id="customSwitch' . $data['id'] . '" data-id="' . $data['id'] . '" ' . ($data['status'] == 1 ? 'checked' : '') . '>';
            echo '<label class="custom-control-label" for="customSwitch' . $data['id'] . '"></label>';
            echo '</div>';
            echo '</td>';
            echo '<td>';
            echo '<a href="data_scraper.php?edit=' . $data['id'] . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
            echo '<form method="POST" action="data_scraper.php" style="display:inline-block;" onsubmit="return confirm(\'Are you sure you want to delete this item?\');">';
            echo '<input type="hidden" name="action" value="delete">';
            echo '<input type="hidden" name="id" value="' . $data['id'] . '">';
            echo '<button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7" class="text-center">No results found</td></tr>';
    }
    exit;
}

include 'inc/header.php';
include 'inc/sidebar.php';

// Database table setup (auto-create if not exists)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS data_scraper (
        id INT AUTO_INCREMENT PRIMARY KEY,
        website_url VARCHAR(255),
        business_name VARCHAR(255),
        business_category VARCHAR(255),
        email_address VARCHAR(255),
        mobile_number VARCHAR(50),
        city VARCHAR(255),
        country VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Add mobile_number column if it doesn't exist (for existing tables)
    $checkCol = $pdo->query("SHOW COLUMNS FROM data_scraper LIKE 'mobile_number'");
    if ($checkCol->rowCount() == 0) {
        $pdo->exec("ALTER TABLE data_scraper ADD COLUMN mobile_number VARCHAR(50) AFTER email_address");
    }

    // Add status column if it doesn't exist
    $checkStatusCol = $pdo->query("SHOW COLUMNS FROM data_scraper LIKE 'status'");
    if ($checkStatusCol->rowCount() == 0) {
        $pdo->exec("ALTER TABLE data_scraper ADD COLUMN status TINYINT(1) DEFAULT 1 AFTER country");
    }
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}


// Handle Form Submissions
$message = '';
$editData = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';


    if ($action === 'create') {
        // Check for duplicates
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM data_scraper WHERE website_url = ? OR email_address = ?");
        $checkStmt->execute([$_POST['website_url'], $_POST['email_address']]);
        $exists = $checkStmt->fetchColumn();

        if ($exists > 0) {
            $message = '<div class="alert alert-warning">Duplicate Entry! Website URL or Email Address already exists.</div>';
        } else {
            $stmt = $pdo->prepare("INSERT INTO data_scraper (website_url, business_name, business_category, email_address, mobile_number, city, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$_POST['website_url'], $_POST['business_name'], $_POST['business_category'], $_POST['email_address'], $_POST['mobile_number'], $_POST['city'], $_POST['country']])) {
                $message = '<div class="alert alert-success">Data Added Successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error Adding Data!</div>';
            }
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        
        // Check for duplicates (excluding current record)
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM data_scraper WHERE (website_url = ? OR email_address = ?) AND id != ?");
        $checkStmt->execute([$_POST['website_url'], $_POST['email_address'], $id]);
        $exists = $checkStmt->fetchColumn();

        if ($exists > 0) {
            $message = '<div class="alert alert-warning">Duplicate Entry! Website URL or Email Address already exists.</div>';
        } else {
            $stmt = $pdo->prepare("UPDATE data_scraper SET website_url=?, business_name=?, business_category=?, email_address=?, mobile_number=?, city=?, country=? WHERE id=?");
            if ($stmt->execute([$_POST['website_url'], $_POST['business_name'], $_POST['business_category'], $_POST['email_address'], $_POST['mobile_number'], $_POST['city'], $_POST['country'], $id])) {
                $message = '<div class="alert alert-success">Data Updated Successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error Updating Data!</div>';
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM data_scraper WHERE id=?");
        if ($stmt->execute([$id])) {
             $message = '<div class="alert alert-success">Data Deleted Successfully!</div>';
        } else {
             $message = '<div class="alert alert-danger">Error Deleting Data!</div>';
        }
    } elseif ($action === 'toggle_status') {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE data_scraper SET status=? WHERE id=?");
        if ($stmt->execute([$status, $id])) {
            echo 'success';
        } else {
            echo 'error';
        }
        exit;
    }
}

// Fetch Data for Edit
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM data_scraper WHERE id=?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();
}

// Fetch All Data (Initial Load)
$stmt = $pdo->query("SELECT * FROM data_scraper ORDER BY id DESC");
$dataList = $stmt->fetchAll();

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Data Scraper Management</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- Form Column -->
          <div class="col-md-4">
            <div class="card card-<?php echo $editData ? 'warning' : 'primary'; ?>">
              <div class="card-header">
                <h3 class="card-title"><?php echo $editData ? 'Edit Data' : 'Add New Data'; ?></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="POST" action="data_scraper.php">
                <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
                <?php if($editData): ?>
                    <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                <?php endif; ?>
                
                <div class="card-body">
                  <?php echo $message; ?>
                  
                  <div class="form-group">
                    <label for="website_url">Website URL</label>
                    <input type="url" class="form-control" id="website_url" name="website_url" placeholder="Enter Website URL" value="<?php echo $editData['website_url'] ?? ''; ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="business_name">Business Name</label>
                    <input type="text" class="form-control" id="business_name" name="business_name" placeholder="Enter Business Name" value="<?php echo $editData['business_name'] ?? ''; ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="business_category">Business Category</label>
                    <input type="text" class="form-control" id="business_category" name="business_category" placeholder="Enter Business Category" value="<?php echo $editData['business_category'] ?? ''; ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="email_address">Email Address</label>
                    <input type="email" class="form-control" id="email_address" name="email_address" placeholder="Enter Email Address" value="<?php echo $editData['email_address'] ?? ''; ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Enter Mobile Number" value="<?php echo $editData['mobile_number'] ?? ''; ?>" required>
                  </div>
                   <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" class="form-control" id="city" name="city" placeholder="Enter City" value="<?php echo $editData['city'] ?? ''; ?>" required>
                  </div>
                   <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" class="form-control" id="country" name="country" placeholder="Enter Country" value="<?php echo $editData['country'] ?? ''; ?>" required>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary"><?php echo $editData ? 'Update' : 'Submit'; ?></button>
                  <?php if($editData): ?>
                    <a href="data_scraper.php" class="btn btn-secondary">Cancel</a>
                  <?php endif; ?>
                </div>
              </form>
            </div>
          </div>

          <!-- Table Column -->
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Scraper Data List <span class="badge badge-info right"><?php echo count($dataList); ?></span></h3>
                <div class="card-tools">
                    <div style="display:inline-block; margin-right: 10px;">
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
                    </div>
                    <a href="data_scraper.php?action=export_csv" id="exportBtn" class="btn btn-tool" title="Export to CSV">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
              </div>
              <!-- /.card-header -->
              <!-- /.card-header -->
              <div class="card-body p-0 table-responsive" style="max-height: 600px;">
                <table class="table table-striped table-head-fixed text-nowrap">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Business Name</th>
                      <th>Category</th>
                      <th>Email</th>
                      <th>Mobile</th>
                      <th>City/Country</th>
                      <th>Website</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="scraperTableBody">
                    <?php 
                    $counter = 1;
                    foreach($dataList as $data): 
                    ?>
                    <tr>
                      <td><?php echo $counter++; ?></td>
                      <td><?php echo htmlspecialchars($data['business_name']); ?></td>
                      <td><?php echo htmlspecialchars($data['business_category']); ?></td>
                      <td><?php echo htmlspecialchars($data['email_address']); ?></td>
                      <td><?php echo htmlspecialchars($data['mobile_number']); ?></td>
                      <td><?php echo htmlspecialchars($data['city']) . ', ' . htmlspecialchars($data['country']); ?></td>
                      <td><a href="<?php echo htmlspecialchars($data['website_url']); ?>" target="_blank" title="<?php echo htmlspecialchars($data['website_url']); ?>"><i class="fas fa-link"></i> Link</a></td>
                      <td>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input status-toggle" id="customSwitch<?php echo $data['id']; ?>" data-id="<?php echo $data['id']; ?>" <?php echo ($data['status'] == 1 ? 'checked' : ''); ?>>
                            <label class="custom-control-label" for="customSwitch<?php echo $data['id']; ?>"></label>
                        </div>
                      </td>
                      <td>
                        <a href="data_scraper.php?edit=<?php echo $data['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="data_scraper.php" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
          </div>

        </div>
      </div>
    </section>
</div>

<?php include 'inc/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        var searchTerm = $(this).val();
        
        // Update Export Link
        var exportUrl = 'data_scraper.php?action=export_csv&search=' + encodeURIComponent(searchTerm);
        $('#exportBtn').attr('href', exportUrl);

        $.ajax({
            url: 'data_scraper.php',
            type: 'GET',
            data: { 
                ajax_search: 1, 
                search: searchTerm 
            },
            success: function(response) {
                $('#scraperTableBody').html(response);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " " + error);
            }
        });
    });

    // Handle Status Toggle
    $(document).on('change', '.status-toggle', function() {
        var id = $(this).data('id');
        var status = $(this).is(':checked') ? 1 : 0;
        
        $.ajax({
            url: 'data_scraper.php',
            type: 'POST',
            data: { 
                action: 'toggle_status', 
                id: id,
                status: status
            },
            success: function(response) {
                if(response !== 'success') {
                    alert('Error updating status');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " " + error);
                alert('Connection error');
            }
        });
    });
});
</script>
