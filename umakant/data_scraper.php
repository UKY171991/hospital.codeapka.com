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

// Handle Single Record Fetch for Edit Modal
if (isset($_GET['edit']) && isset($_GET['ajax_fetch_one'])) {
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');
    
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM data_scraper WHERE id=?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($data);
    exit;
}


// Handle Form Submissions & AJAX
$message = '';
$editData = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // Handle AJAX Status Toggle - MUST EXIT
    if ($action === 'toggle_status') {
        // Clear buffer just in case
        if (ob_get_level()) ob_end_clean();
        
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

    // Handle Turn All OFF
    if ($action === 'turn_all_off') {
        $stmt = $pdo->prepare("UPDATE data_scraper SET status = 0");
        if ($stmt->execute()) {
             $message = '<div class="alert alert-success">All statuses turned off!</div>';
        } else {
             $message = '<div class="alert alert-danger">Error turning off statuses!</div>';
        }
    }

    // Handle Website Validation
    if ($action === 'validate_website') {
        // Clear buffer
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');

        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT website_url FROM data_scraper WHERE id=?");
        $stmt->execute([$id]);
        $url = $stmt->fetchColumn();

        if (!$url) {
            echo json_encode(['status' => 'error', 'message' => 'Not found']);
            exit;
        }

        // Validate URL format
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
             $pdo->prepare("DELETE FROM data_scraper WHERE id=?")->execute([$id]);
             echo json_encode(['status' => 'deleted', 'message' => 'Invalid URL format']);
             exit;
        }

        // Check URL availability via cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Get body
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Increased timeout slightly for GET
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 400) {
            // Further Check: Garbage Title / Parked Domain
            preg_match('/<title>(.*?)<\/title>/is', $html, $matches);
            $title = isset($matches[1]) ? trim(strip_tags($matches[1])) : '';
            
            $garbageTitles = ['403 Forbidden', '404 Not Found', 'Access Denied', 'Domain For Sale', 'Parking', 'GoDaddy', 'Namecheap', 'Just a moment...', 'Attention Required!', 'Robot Check', 'Security Check', 'Human Verification', 'Yelp', 'Yellow Pages'];
            $isGarbage = false;
            foreach ($garbageTitles as $gt) {
                if (stripos($title, $gt) !== false) {
                   $isGarbage = true;
                   break;
                }
            }

            if ($isGarbage) {
                // Garbage content -> Delete
                $pdo->prepare("DELETE FROM data_scraper WHERE id=?")->execute([$id]);
                echo json_encode(['status' => 'deleted', 'message' => 'Invalid Content: ' . $title]);
            } else {
                echo json_encode(['status' => 'valid', 'message' => 'Website is up (' . $httpCode . ')']);
            }
        } else {
            // Website down or unreachable -> Delete
            $pdo->prepare("DELETE FROM data_scraper WHERE id=?")->execute([$id]);
            echo json_encode(['status' => 'deleted', 'message' => 'Website down (' . $httpCode . ')']);
        }
        exit;
    }

    // Handle Get All IDs for Bulk Check
    if ($action === 'get_all_ids') {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        
        $stmt = $pdo->query("SELECT id FROM data_scraper ORDER BY id DESC");
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo json_encode(['ids' => $ids]);
        exit;
    }

    // Handle Auto Scraper
    if ($action === 'auto_scrape') {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');

        // Increase execution time for scraping (15 minutes for 100 records)
        set_time_limit(900);

        $category = $_POST['category'] ?? '';
        $city = $_POST['city'] ?? '';
        $country = $_POST['country'] ?? '';
        $nextParams = $_POST['next_params'] ?? null;
        
        $url = "https://html.duckduckgo.com/html/";
        $postData = [];

        if ($nextParams) {
             $postData = $nextParams;
        } else {
             // Initial Request
             // Build query parts
             $queryParts = [];
             if ($category) $queryParts[] = "\"$category\"";
             if ($city) $queryParts[] = "\"$city\"";
             if ($country) $queryParts[] = "\"$country\"";
             
             // Base query
             $baseQuery = implode(' ', $queryParts);
             
             // Add exclusions - less aggressive to ensure we get results
             // We filter heavily in PHP anyway
             $query = "$baseQuery -directory -list -wikipedia";
             
             $postData = ['q' => $query, 'kl' => 'us-en']; // Force region if needed, or remove 'kl'
             
             // Check if we have previous debug info
             $debugLog = [];
        }

        // Function to make cURL request
        function fetchUrl($url, $postData = []) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            // Rotate User Agents or use a standard one
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            if (!empty($postData)) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            }

            $output = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Strict HTTP Check (Only 200 OK)
            if ($httpCode !== 200) {
                return false;
            }

            return $output;
        }

        // Initialize loop variables
        $links = [];
        $fetchedPages = 0;
        $maxPages = 30; // Increased limit to find more results
        $html = fetchUrl($url, $postData);
        
        // Check if initial fetch worked
        if (!$html) {
             echo json_encode(['status' => 'error', 'message' => 'Failed to connect to search engine. Please try again or check network.']);
             exit;
        }
        
        while ($fetchedPages < $maxPages) {
            if (!$html) break; // Network error or blocked

            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            
            // Extract Links
            $nodes = $xpath->query("//a[@class='result__a']");
            if ($nodes->length == 0) {
                 $nodes = $dom->getElementsByTagName('a');
            }

            foreach ($nodes as $node) {
                $href = $node->getAttribute('href');
                if (strpos($href, 'uddg=') !== false) {
                    parse_str(parse_url($href, PHP_URL_QUERY), $vars);
                    if (isset($vars['uddg'])) {
                        $href = $vars['uddg'];
                    }
                }
                
                if (!filter_var($href, FILTER_VALIDATE_URL)) continue;

                $isExcluded = false;
                foreach ($exclusionList as $excluded) {
                    if (stripos($href, $excluded) !== false) {
                        $isExcluded = true;
                        break;
                    }
                }
                foreach ($directoryPathSegments as $segment) {
                    if (stripos($href, $segment) !== false) {
                        $isExcluded = true;
                        break;
                    }
                }

                if (!$isExcluded && !in_array($href, $links)) {
                    $links[] = $href;
                }
            }
            
            // Break if we have enough links
            if (count($links) >= 100) break;

            // Get Next Page Params
            $nextPageParams = null;
            $forms = $dom->getElementsByTagName('form');
            foreach ($forms as $form) {
                $action = $form->getAttribute('action');
                // DuckDuckGo next page usually has 'next' in class or action, or input name 's' (start)
                if (strpos($action, '/html/') !== false || $form->getAttribute('class') == 'nav-button' || strpos($action, 'next') !== false) {
                     $inputs = $form->getElementsByTagName('input');
                     $tempParams = [];
                     $hasS = false;
                     $hasNext = false;
                     foreach ($inputs as $input) {
                         $name = $input->getAttribute('name');
                         $value = $input->getAttribute('value');
                         if ($name) {
                             $tempParams[$name] = $value;
                             if ($name === 's' || $name === 'nextParams') $hasS = true; 
                             if ($value === 'Next') $hasNext = true;
                         }
                     }
                     if ($hasS || $hasNext) {
                         $nextPageParams = $tempParams;
                         break;
                     }
                }
            }

            if ($nextPageParams) {
                // Fetch next page
                $html = fetchUrl($url, $nextPageParams);
                $fetchedPages++;
                // Small random delay to be polite
                usleep(rand(500000, 1500000)); 
            } else {
                break; // No more pages
            }
        }

        $insertedCount = 0;

        // 2. Process each link
        foreach ($links as $webUrl) {
            // Check duplicate
            $chk = $pdo->prepare("SELECT COUNT(*) FROM data_scraper WHERE website_url = ?");
            $chk->execute([$webUrl]);
            if ($chk->fetchColumn() > 0) continue;

            // Fetch Site Content
            $siteHtml = fetchUrl($webUrl);
            if (!$siteHtml) continue;

            // Extract Data
            // Title as Business Name
            preg_match('/<title>(.*?)<\/title>/is', $siteHtml, $matches);
            $title = isset($matches[1]) ? trim(strip_tags($matches[1])) : $category . ' Business';
            
            // Text content for searching
            $textContent = strip_tags($siteHtml);

            // Email (Refined Regex)
            preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $textContent, $emailMatches);
            $email = $emailMatches[0] ?? '';

            // Phone (Refined Regex - stricter length)
            preg_match('/(\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}/', $textContent, $phoneMatches);
            $mobile = $phoneMatches[0] ?? '';

            // City Extraction (If not provided)
            $extractedCity = $city;
            
            // If city is empty, try to find it in the content
            if (empty($extractedCity)) {
                
                // 1. Try Schema.org JSON-LD (Best accuracy)
                if (preg_match('/"addressLocality":\s*"([^"]+)"/i', $siteHtml, $schemaMatches)) {
                    $extractedCity = trim($schemaMatches[1]);
                }
                // 2. Try Meta Tags (geo.placename, og:locality, business:contact_data:locality)
                elseif (preg_match('/<meta\s+(?:name|property)="(:?geo\.placename|og:locality|business:contact_data:locality)"\s+content="([^"]+)"/i', $siteHtml, $metaMatches)) {
                    $extractedCity = trim($metaMatches[2]);
                }
                // 3. Try Address Tag
                elseif (preg_match('/<address[^>]*>(.*?)<\/address>/is', $siteHtml, $addrMatches)) {
                    $addrText = strip_tags($addrMatches[1]);
                     if (!empty($country)) {
                         if (preg_match('/([A-Z][a-z]+(?:\s[A-Z][a-z]+)?)\s*,\s*' . preg_quote($country, '/') . '/i', $addrText, $locMatches)) {
                              $extractedCity = trim($locMatches[1]);
                         }
                     }
                }
                
                // 4. Try Standard "City, Country" pattern in main text
                if (empty($extractedCity) && !empty($country)) {
                     // Matches "Toronto, Canada"
                     if (preg_match('/([A-Z][a-z]+(?:\s[A-Z][a-z]+)?)\s*,\s*' . preg_quote($country, '/') . '/i', $textContent, $locMatches)) {
                          $extractedCity = trim($locMatches[1]);
                     }
                     // Matches "Toronto, ON, Canada"
                     elseif (preg_match('/([A-Z][a-z]+(?:\s[A-Z][a-z]+)?)\s*,\s*(?:[A-Z]{2,}|[A-Z][a-z]+)\s*,\s*' . preg_quote($country, '/') . '/i', $textContent, $locMatches)) {
                          $extractedCity = trim($locMatches[1]);
                     }
                }
            }
            
            // STRICT VALIDATION: If no specific city found, SKIP this record. 
            // We only accept records where we can identify the city.
            $extractedCity = trim($extractedCity);
            if (empty($extractedCity) || strtolower($extractedCity) === 'unknown') {
                continue;
            }

            // ENFORCE VALID DATA: 
            // 1. Must have a Business Name
            if (empty($title)) {
                continue; 
            }

            // 2. Reject Garbage Titles (Error pages, Parked domains, Directories)
            $garbageTitles = ['403 Forbidden', '404 Not Found', 'Access Denied', 'Domain For Sale', 'Parking', 'GoDaddy', 'Namecheap', 'Just a moment...', 'Attention Required!', 'Robot Check', 'Security Check', 'Human Verification', 'Yelp', 'Yellow Pages'];
            $isGarbage = false;
            foreach ($garbageTitles as $gt) {
                if (stripos($title, $gt) !== false) {
                   $isGarbage = true;
                   break;
                }
            }
            if ($isGarbage) continue;

            // 3. Must have at least ONE contact method (Email OR Mobile)
            // Users typically don't want data without contact info
            if (empty($email) && empty($mobile)) {
                continue;
            }

            // DUPLICATE DATA CHECK
            // Check against Name+City, Email, or Mobile to prevent duplicates
            $dupConditions = ["(business_name = ? AND city = ?)"];
            $dupParams = [$title, $extractedCity];

            if (!empty($email)) {
                $dupConditions[] = "email_address = ?";
                $dupParams[] = $email;
            }
            
            if (!empty($mobile)) {
                $dupConditions[] = "mobile_number = ?";
                $dupParams[] = $mobile;
            }

            $dupSql = "SELECT COUNT(*) FROM data_scraper WHERE " . implode(' OR ', $dupConditions);
            $dupStmt = $pdo->prepare($dupSql);
            $dupStmt->execute($dupParams);
            
            if ($dupStmt->fetchColumn() > 0) {
                continue; // Skip Duplicate
            }

            $stmt = $pdo->prepare("INSERT INTO data_scraper (website_url, business_name, business_category, email_address, mobile_number, city, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $webUrl,
                $title,
                $category,
                $email, // Will be empty string if not found
                $mobile, // Will be empty string if not found
                ucfirst($extractedCity), 
                $country
            ]);
            $insertedCount++;
        }

        echo json_encode([
            'status' => 'success', 
            'message' => "Batch complete.",
            'count' => $insertedCount,
            'next_params' => $nextPageParams,
            'debug' => [
                'fetched_pages' => $fetchedPages,
                'links_found_raw' => count($links),
                'query' => $query ?? 'pagination'
            ]
        ]);
        exit;
    }

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
    }
}

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

    // Add status column if it doesn't exist - DEFAULT 0 (Unchecked)
    $checkStatusCol = $pdo->query("SHOW COLUMNS FROM data_scraper LIKE 'status'");
    if ($checkStatusCol->rowCount() == 0) {
        $pdo->exec("ALTER TABLE data_scraper ADD COLUMN status TINYINT(1) DEFAULT 0 AFTER country");
    } else {
        // Ensure default is 0 for existing column
        $pdo->exec("ALTER TABLE data_scraper MODIFY COLUMN status TINYINT(1) DEFAULT 0");
    }
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}


include 'inc/header.php';
include 'inc/sidebar.php';

// Fetch Data for Edit
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM data_scraper WHERE id=?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();
}

// Pagination Configuration
$limit = 20; // Entries per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get Total Count
$countSql = "SELECT COUNT(*) FROM data_scraper $searchQuery";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Get Paginated Data
$sql = "SELECT * FROM data_scraper $searchQuery ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

// Bind search params
foreach ($params as $key => $val) {
    $stmt->bindValue($key + 1, $val);
}
// Bind pagination params
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$dataList = $stmt->fetchAll();

// Adjust counter for pagination
$counter = $offset + 1;

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
          <!-- Table Column - Full Width -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Scraper Data List <span class="badge badge-info right"><?php echo $totalRecords; ?></span></h3>
                <div class="card-tools">
                    <div style="display:inline-block; margin-right: 10px;">
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
                    </div>
                    
                    <button type="button" class="btn btn-tool text-success" data-toggle="modal" data-target="#autoScraperModal" title="Auto Scraper Bot">
                        <i class="fas fa-robot"></i> Auto Scraper
                    </button>
                    <button type="button" class="btn btn-tool text-primary" data-toggle="modal" data-target="#addEditModal" id="btnAddData" title="Add New Data">
                        <i class="fas fa-plus"></i> Add New
                    </button>
                    
                    <a href="data_scraper.php?action=export_csv" id="exportBtn" class="btn btn-tool" title="Export to CSV">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                    <form method="POST" action="data_scraper.php" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to turn ALL statuses OFF?');">
                        <input type="hidden" name="action" value="turn_all_off">
                        <button type="submit" class="btn btn-tool text-danger" title="Turn All Off">
                            <i class="fas fa-power-off"></i> Turn All Off
                        </button>
                    </form>
                    <button type="button" class="btn btn-tool text-primary" id="btnTestAll" title="Test Page">
                        <i class="fas fa-check-double"></i> Test Page
                    </button>
                    <button type="button" class="btn btn-tool text-warning" id="btnCheckDb" title="Check Entire DB">
                        <i class="fas fa-database"></i> Check All DB
                    </button>
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
                      <td class="action-buttons-cell" data-id="<?php echo $data['id']; ?>">
                        <button type="button" class="btn btn-sm btn-info btn-test-url" title="Test URL"><i class="fas fa-stethoscope"></i></button>
                        <button type="button" class="btn btn-sm btn-warning btn-edit-item" data-id="<?php echo $data['id']; ?>"><i class="fas fa-edit"></i></button>
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
              <div class="card-footer clearfix" id="pagination-container">
                <ul class="pagination pagination-sm m-0 float-right">
                  <?php if($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo htmlspecialchars($search); ?>">&laquo;</a></li>
                  <?php else: ?>
                    <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                  <?php endif; ?>

                  <?php 
                  // Simple Pagination Range
                  $range = 2;
                  for ($i = 1; $i <= $totalPages; $i++): 
                    if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)):
                  ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php elseif ($i == $page - $range - 1 || $i == $page + $range + 1): ?>
                        <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
                    <?php endif; ?>
                  <?php endfor; ?>

                  <?php if($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo htmlspecialchars($search); ?>">&raquo;</a></li>
                  <?php else: ?>
                    <li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
</div>

<!-- Auto Scraper Modal -->
<div class="modal fade" id="autoScraperModal" tabindex="-1" role="dialog" aria-labelledby="autoScraperModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h5 class="modal-title" id="autoScraperModalLabel"><i class="fas fa-robot"></i> Auto Scraper Bot</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <form id="scraperForm">
            <div class="form-group">
                <label>Business Category</label>
                <input type="text" class="form-control" name="scrape_category" placeholder="e.g. Dentist, Plumber" required>
            </div>
            <div class="row">
                <div class="col-6">
                     <div class="form-group">
                        <label>City (Optional)</label>
                        <input type="text" class="form-control" name="scrape_city" placeholder="e.g. Toronto">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" class="form-control" name="scrape_country" placeholder="e.g. Canada" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-block" id="btnRunScraper">
                <i class="fas fa-search-plus"></i> Start Scraping
            </button>
            <p class="text-muted text-sm mt-2"><i class="fas fa-info-circle"></i> This adds data automatically to the list.</p>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Data Modal -->
<div class="modal fade" id="addEditModal" tabindex="-1" role="dialog" aria-labelledby="addEditModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title" id="addEditModalLabel">Add New Data</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form role="form" method="POST" action="data_scraper.php" id="addEditForm">
        <div class="modal-body">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
            <label for="website_url">Website URL</label>
            <input type="url" class="form-control" id="website_url" name="website_url" placeholder="Enter Website URL" required>
            </div>
            <div class="form-group">
            <label for="business_name">Business Name</label>
            <input type="text" class="form-control" id="business_name" name="business_name" placeholder="Enter Business Name" required>
            </div>
            <div class="form-group">
            <label for="business_category">Business Category</label>
            <input type="text" class="form-control" id="business_category" name="business_category" placeholder="Enter Business Category" required>
            </div>
            <div class="form-group">
            <label for="email_address">Email Address</label>
            <input type="email" class="form-control" id="email_address" name="email_address" placeholder="Enter Email Address" required>
            </div>
            <div class="form-group">
            <label for="mobile_number">Mobile Number</label>
            <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Enter Mobile Number" required>
            </div>
            <div class="form-group">
            <label for="city">City</label>
            <input type="text" class="form-control" id="city" name="city" placeholder="Enter City" required>
            </div>
            <div class="form-group">
            <label for="country">Country</label>
            <input type="text" class="form-control" id="country" name="country" placeholder="Enter Country" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Progress Modal -->
<div class="modal fade" id="progressModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Checking Database...</h5>
      </div>
      <div class="modal-body">
        <p id="progressText">Initializing...</p>
        <div class="progress">
          <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
        </div>
        <div class="mt-2 text-muted text-sm">
            <span id="deletedCount">0</span> deleted, <span id="validCount">0</span> valid
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="stopCheckBtn">Stop</button>
      </div>
    </div>
  </div>
</div>

<?php include 'inc/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        var searchTerm = $(this).val();
        
        // Update Export Link
        var exportUrl = 'data_scraper.php?action=export_csv&search=' + encodeURIComponent(searchTerm);
        $('#exportBtn').attr('href', exportUrl);

        // Hide pagination when searching
        if(searchTerm.length > 0) {
            $('#pagination-container').hide();
        } else {
            $('#pagination-container').show();
        }

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

    // Handle Edit Button Click (Open Modal & Populate)
    $(document).on('click', '.btn-edit-item', function(e) {
        e.preventDefault();
        var data = $(this).data('json'); // Assumes we will add data-json attribute
        
        // If data isn't in attribute, fetch it (preferred for larger datasets)
        // But for simplicity, let's parse the row or just use an AJAX fetch
        var id = $(this).data('id');
        
        // Reset form
        $('#addEditForm')[0].reset();
        $('input[name="action"]').val('update');
        $('#addEditModalLabel').text('Edit Data');
        
        // Set ID
        if ($('input[name="id"]').length === 0) {
             $('#addEditForm').append('<input type="hidden" name="id" value="'+id+'">');
        } else {
             $('input[name="id"]').val(id);
        }

        // Fetch details via AJAX to populate form
        $.ajax({
             url: 'data_scraper.php',
             type: 'GET',
             data: { edit: id, ajax_fetch_one: 1 }, // Need to implement ajax_fetch_one in PHP
             dataType: 'json',
             success: function(record) {
                 if(record) {
                     $('#website_url').val(record.website_url);
                     $('#business_name').val(record.business_name);
                     $('#business_category').val(record.business_category);
                     $('#email_address').val(record.email_address);
                     $('#mobile_number').val(record.mobile_number);
                     $('#city').val(record.city);
                     $('#country').val(record.country);
                     $('#addEditModal').modal('show');
                 }
             }
        });
    });

    $('#btnAddData').click(function() {
        $('#addEditForm')[0].reset();
        $('input[name="action"]').val('create');
        $('#addEditModalLabel').text('Add New Data');
        $('input[name="id"]').remove(); // Remove ID input if exists
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

    // Test One URL
    $(document).on('click', '.btn-test-url', function() {
        var btn = $(this);
        var row = btn.closest('tr');
        var id = row.find('.status-toggle').data('id'); // Get ID from toggle
        
        testWebsite(id, btn, row);
    });

    // Test All URLs (Current Page)
    $('#btnTestAll').click(function() {
        if(!confirm('This will verify ALL websites on this page and DELETE any that are down/invalid. This process happens one by one. Continue?')) {
            return;
        }

        var buttons = $('.btn-test-url');
        processQueue(buttons, 0);
    });

    // Test Entire Database logic
    var stopCheck = false;
    $('#stopCheckBtn').click(function(){ stopCheck = true; });

    $('#btnCheckDb').click(function() {
        if(!confirm('This will verify EVERY record in the database. It may take a long time. Invalid records will be DELETED. Continue?')) {
            return;
        }

        $('#progressModal').modal('show');
        $('#progressBar').css('width', '0%');
        $('#deletedCount').text('0');
        $('#validCount').text('0');
        stopCheck = false;

        // Fetch All IDs
        $.ajax({
            url: 'data_scraper.php',
            type: 'POST',
            data: { action: 'get_all_ids' },
            dataType: 'json',
            success: function(response) {
                var ids = response.ids;
                var total = ids.length;
                processIdQueue(ids, 0, total, 0, 0);
            },
            error: function() {
                alert('Error fetching IDs');
                $('#progressModal').modal('hide');
            }
        });
    });

    function processIdQueue(ids, index, total, deleted, valid) {
        if (stopCheck || index >= total) {
            $('#progressModal').modal('hide');
            alert('Process Completed! ' + deleted + ' records deleted, ' + valid + ' verified.');
            location.reload();
            return;
        }

        var id = ids[index];
        var percent = Math.round(((index + 1) / total) * 100);
        
        $('#progressBar').css('width', percent + '%');
        $('#progressText').text('Checking ' + (index + 1) + ' of ' + total);

        $.ajax({
            url: 'data_scraper.php',
            type: 'POST',
            dataType: 'json',
            data: { 
                action: 'validate_website', 
                id: id
            },
            success: function(response) {
                if (response.status === 'deleted') {
                    deleted++;
                    $('#deletedCount').text(deleted);
                } else {
                    valid++;
                    $('#validCount').text(valid);
                }
                // Determine speed (optional delay to prevent server overload, though PHP is serial)
                processIdQueue(ids, index + 1, total, deleted, valid);
            },
            error: function() {
                // If checking fails (network error), skip.
                processIdQueue(ids, index + 1, total, deleted, valid);
            }
        });
    }

    function processQueue(buttons, index) {
        if (index >= buttons.length) {
            alert('All websites checked!');
            return;
        }

        var btn = $(buttons[index]);
        var row = btn.closest('tr');
        var id = row.find('.status-toggle').data('id');

        // Scroll to row
        $('html, body').animate({
            scrollTop: row.offset().top - 100
        }, 200);

        testWebsite(id, btn, row, function() {
            processQueue(buttons, index + 1);
        });
    }

    function testWebsite(id, btn, row, callback) {
        var originalIcon = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: 'data_scraper.php',
            type: 'POST',
            dataType: 'json',
            data: { 
                action: 'validate_website', 
                id: id
            },
            success: function(response) {
                if (response.status === 'valid') {
                    btn.html('<i class="fas fa-check"></i>').removeClass('btn-info').addClass('btn-success');
                    setTimeout(function() {
                        btn.html(originalIcon).removeClass('btn-success').addClass('btn-info').prop('disabled', false);
                    }, 2000);
                } else if (response.status === 'deleted') {
                    row.fadeOut(500, function() { $(this).remove(); });
                } else {
                    btn.html('<i class="fas fa-exclamation"></i>').removeClass('btn-info').addClass('btn-secondary');
                    alert(response.message);
                }
                if (callback) callback();
            },
            error: function() {
                btn.html('<i class="fas fa-times"></i>').removeClass('btn-info').addClass('btn-danger');
                if (callback) callback();
            }
        });
    }
    // Auto Scraper Handler
    $('#scraperForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btnRunScraper');
        var originalText = 'Start Scraping'; // Hardcoded original text
        var category = $('input[name="scrape_category"]').val();
        var city = $('input[name="scrape_city"]').val();
        var country = $('input[name="scrape_country"]').val();
        
        var totalScraped = 0;
        var targetScraped = 100;

        btn.html('<i class="fas fa-cog fa-spin"></i> Initializing...').prop('disabled', true);

        function runScraper(nextParams = null) {
            var data = { 
                action: 'auto_scrape', 
                category: category,
                city: city,
                country: country
            };

            if (nextParams) {
                data.next_params = nextParams;
            }

            $.ajax({
                url: 'data_scraper.php',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response) {
                    if(response.status === 'success') {
                        totalScraped += response.count;
                        btn.html('<i class="fas fa-cog fa-spin"></i> Scraping... (' + totalScraped + '/' + targetScraped + ')');
                        
                        // Continue if target not reached and next page exists
                        if (totalScraped < targetScraped && response.next_params) {
                            // Small delay to be polite
                            setTimeout(function() {
                                runScraper(response.next_params);
                            }, 1000);
                        } else {
                            // Finished
                            btn.html('<i class="fas fa-check"></i> Done (' + totalScraped + ')').removeClass('btn-success').addClass('btn-primary');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    } else {
                         // Error or no more results
                         // If we have some data, just finish
                         if (totalScraped > 0) {
                             location.reload();
                         } else {
                            btn.html('<i class="fas fa-exclamation-triangle"></i> No Data Found');
                            // Alert removed as per request, just showing status on button
                            setTimeout(function() {
                                btn.html('<i class="fas fa-search-plus"></i> Start Scraping').prop('disabled', false).removeClass('btn-primary').addClass('btn-success');
                            }, 3000);
                         }
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Scraping error:", error);
                    // If we made some progress, reload to show it
                    if (totalScraped > 0) {
                        location.reload();
                    } else {
                        btn.html('<i class="fas fa-times"></i> Error').prop('disabled', false);
                    }
                }
            });
        }

        // Start the process
        runScraper();
    });
});
</script>
