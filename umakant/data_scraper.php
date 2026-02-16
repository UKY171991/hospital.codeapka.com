<?php
// data_scraper.php
// Full CRUD functionality for Data Scraper
include 'inc/connection.php';
include 'inc/auth.php'; // Ensure user is logged in

// Handle CSV Export - MUST BE BEFORE ANY HTML OUTPUT
if (isset($_GET['action']) && $_GET['action'] == 'export_csv') {
    // Clear any previous output
    if (ob_get_level()) ob_end_clean();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="scraper_data_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel UTF-8 compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, array('ID', 'Website URL', 'Business Name', 'Business Category', 'Email Address', 'City', 'Country', 'Created At'));
    
    $stmt = $pdo->query("SELECT id, website_url, business_name, business_category, email_address, city, country, created_at FROM data_scraper ORDER BY id DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
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
        city VARCHAR(255),
        country VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
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
            $stmt = $pdo->prepare("INSERT INTO data_scraper (website_url, business_name, business_category, email_address, city, country) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$_POST['website_url'], $_POST['business_name'], $_POST['business_category'], $_POST['email_address'], $_POST['city'], $_POST['country']])) {
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
            $stmt = $pdo->prepare("UPDATE data_scraper SET website_url=?, business_name=?, business_category=?, email_address=?, city=?, country=? WHERE id=?");
            if ($stmt->execute([$_POST['website_url'], $_POST['business_name'], $_POST['business_category'], $_POST['email_address'], $_POST['city'], $_POST['country'], $id])) {
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
    }
}

// Fetch Data for Edit
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM data_scraper WHERE id=?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();
}

// Fetch All Data
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
                <h3 class="card-title">Scraper Data List</h3>
                <div class="card-tools">
                    <a href="data_scraper.php?action=export_csv" class="btn btn-tool" title="Export to CSV">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Business Name</th>
                      <th>Category</th>
                      <th>Email</th>
                      <th>City/Country</th>
                      <th>Website</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $counter = 1;
                    foreach($dataList as $data): 
                    ?>
                    <tr>
                      <td><?php echo $counter++; ?></td>
                      <td><?php echo htmlspecialchars($data['business_name']); ?></td>
                      <td><?php echo htmlspecialchars($data['business_category']); ?></td>
                      <td><?php echo htmlspecialchars($data['email_address']); ?></td>
                      <td><?php echo htmlspecialchars($data['city']) . ', ' . htmlspecialchars($data['country']); ?></td>
                      <td><a href="<?php echo htmlspecialchars($data['website_url']); ?>" target="_blank" title="<?php echo htmlspecialchars($data['website_url']); ?>"><i class="fas fa-link"></i> Link</a></td>
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
