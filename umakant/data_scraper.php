<?php
// c:\git\hospital.codeapka.com\umakant\data_scraper.php
// RE-WRITTEN FROM SCRATCH - DATA SCRAPER V2.0
// Author: Antigravity (Agent)
// Date: 2026-02-16

// --------------------------------------------------------------------------
// 1. CONFIGURATION & DATABASE SETUP
// --------------------------------------------------------------------------

// Include system headers (assumes $pdo and session are initialized here)
require_once 'inc/connection.php';
include 'inc/header.php'; 
include 'inc/sidebar.php';

// Ensure table exists
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
        status TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    // Silent fail or log if strictly needed
}

// --------------------------------------------------------------------------
// 2. BACKEND LOGIC (AJAX HANDLER)
// --------------------------------------------------------------------------

if (isset($_POST['action'])) {
    
    // Clean buffer
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');
    set_time_limit(300); // 5 minutes max

    $action = $_POST['action'];

    // --- HELPER: HTTP REQUEST ---
    function fetch_url($url, $postData = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        
        // Cookie Handling (Important for sessions)
        $cookieFile = sys_get_temp_dir() . '/scraper_cookie_' . date('Ymd') . '.txt';
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

        if ($postData) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return ['code' => $httpCode, 'content' => $response];
    }

    // --- HELPER: LINK EXTRACTOR (REGEX Power) ---
    function extract_links($html) {
        $links = [];
        // Match http/https URLs inside href attributes or plain text
        preg_match_all('/href=["\'](https?:\/\/[^"\']+)["\']/', $html, $matches);
        if (!empty($matches[1])) $links = array_merge($links, $matches[1]);
        
        // Match "hidden" DuckDuckGo / Bing redirect urls
        preg_match_all('/uddg=([^&]+)/', $html, $ddgMatches); // DDG
        if (!empty($ddgMatches[1])) {
            foreach($ddgMatches[1] as $m) $links[] = urldecode($m);
        }
        
        return array_unique($links);
    }

    // --- HELPER: DATA MINER ---
    function mine_data($html) {
        $data = ['email' => '', 'phone' => '', 'title' => ''];
        
        // Title
        if (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
            $data['title'] = trim(strip_tags($m[1]));
        }
        
        // Email (Strict)
        $text = strip_tags($html);
        if (preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $emails)) {
            // Filter junk emails
            foreach($emails[0] as $email) {
                if (strpos($email, '.png') !== false || strpos($email, '.jpg') !== false) continue;
                $data['email'] = $email;
                break; // Take first valid
            }
        }
        
        // Phone (North America & International loosely)
        if (preg_match('/(\+?1?[-.]?\s?\(?\d{3}\)?[-.]?\s?\d{3}[-.]?\s?\d{4})/', $text, $phones)) {
            $data['phone'] = trim($phones[0]);
        }
        
        // Footprint Check
        if (stripos($html, 'elfsight') !== false) {
            $data['elfsight'] = true;
        }
        
        return $data;
    }

    // --- ACTION: AUTO SCRAPE ---
    if ($action === 'start_scrape') {
        $category = $_POST['category'];
        $city = $_POST['city'];
        $country = $_POST['country'];
        
        // 1. Build Search Queries (Multi-Engine)
        $queries = [];
        $baseQ = urlencode("$category in $city $country");
        
        // DuckDuckGo Lite (Best for scraping headers)
        $queries[] = "https://lite.duckduckgo.com/lite/?q=$baseQ";
        
        // Bing (Fallback)
        $queries[] = "https://www.bing.com/search?q=$baseQ";
        
        // Yahoo (Deep Fallback)
        $queries[] = "https://search.yahoo.com/search?p=$baseQ";

        $foundLinks = [];
        $debugLog = [];

        // 2. Search Phase
        foreach ($queries as $searchUrl) {
            $resp = fetch_url($searchUrl);
            if ($resp['code'] == 200 && !empty($resp['content'])) {
                $rawLinks = extract_links($resp['content']);
                $debugLog[] = "Engine " . substr($searchUrl, 0, 20) . "... returned " . count($rawLinks) . " potential links.";
                
                foreach ($rawLinks as $link) {
                    $link = trim($link);
                    // Filter Trash
                    $excludes = ['yahoo.com', 'bing.com', 'duckduckgo', 'google', 'facebook', 'youtube', 'twitter', 'instagram', 'yelp', 'yellowpages', 'linkedin', 'tripadvisor', 'mapquest', 'pinterest'];
                    $isBad = false;
                    foreach ($excludes as $ex) {
                        if (stripos($link, $ex) !== false) { $isBad = true; break; }
                    }
                    
                    if (!$isBad && filter_var($link, FILTER_VALIDATE_URL)) {
                        $foundLinks[] = $link;
                    }
                }
            }
            if (count($foundLinks) > 20) break; // Optimization
        }
        
        $foundLinks = array_unique($foundLinks);
        
        if (empty($foundLinks)) {
            echo json_encode(['status' => 'warning', 'message' => 'No new businesses found. Try a broader category.', 'debug' => $debugLog]);
            exit;
        }

        // 3. Mining Phase
        $savedCount = 0;
        foreach ($foundLinks as $url) {
            if ($savedCount >= 15) break; // Batch limit
            
            // Duplicate Check
            $stmt = $pdo->prepare("SELECT id FROM data_scraper WHERE website_url = ?");
            $stmt->execute([$url]);
            if ($stmt->fetch()) continue;
            
            // Visit
            $siteData = fetch_url($url);
            if ($siteData['code'] != 200) continue;
            
            $mined = mine_data($siteData['content']);
            
            // Criteria: Must have a Title
            if (empty($mined['title'])) continue;
            
            // Tagging
            $finalCat = $category;
            if (isset($mined['elfsight']) && $mined['elfsight']) $finalCat .= " [Elfsight]";
            
            // Insert
            $stmt = $pdo->prepare("INSERT INTO data_scraper (website_url, business_name, business_category, email_address, mobile_number, city, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$url, $mined['title'], $finalCat, $mined['email'], $mined['phone'], $city, $country]);
            $savedCount++;
        }
        
        $msg = $savedCount > 0 ? "Successfully scraped $savedCount businesses!" : "Scanned " . count($foundLinks) . " sites but mostly duplicates or unreadable.";
        echo json_encode(['status' => 'success', 'message' => $msg, 'count' => $savedCount, 'debug' => $debugLog]);
        exit;
    }
    
    // --- ACTION: CLEAR DATA ---
    if ($action === 'clear_data') {
        $pdo->exec("TRUNCATE TABLE data_scraper");
        echo json_encode(['status' => 'success']);
        exit;
    }
    
    // --- ACTION: DELETE ROW ---
    if ($action === 'delete_row') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM data_scraper WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // --- ACTION: GET ROW DETAILS ---
    if ($action === 'get_row') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT * FROM data_scraper WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $row]);
        exit;
    }

    // --- ACTION: TEST ROW (VERIFY & UPDATE/DELETE) ---
    if ($action === 'test_row') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT * FROM data_scraper WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo json_encode(['status' => 'error', 'message' => 'Row not found']);
            exit;
        }

        $url = $row['website_url'];
        // Validation: Ensure URL has protocol
        if (strpos($url, 'http') !== 0) {
            $url = 'http://' . $url;
        }

        $res = fetch_url($url);

        // 1. Dead URL -> Delete
        if ($res['code'] < 200 || $res['code'] >= 400) {
            $pdo->prepare("DELETE FROM data_scraper WHERE id = ?")->execute([$id]);
            echo json_encode(['status' => 'warning', 'message' => "URL Unreachable (Code {$res['code']}). Row Deleted."]);
            exit;
        }

        // 2. Re-mine data
        $mined = mine_data($res['content']);

        // 3. No Data Found -> Delete
        if (empty($mined['title']) && empty($mined['email']) && empty($mined['phone'])) {
             $pdo->prepare("DELETE FROM data_scraper WHERE id = ?")->execute([$id]);
             echo json_encode(['status' => 'warning', 'message' => "Page active but no valid business data found. Row Deleted."]);
             exit;
        }

        // 4. Update if Data Changed
        $updateFields = [];
        $params = [];
        $changes = [];

        if (!empty($mined['title']) && $mined['title'] !== $row['business_name']) {
            $updateFields[] = "business_name = ?";
            $params[] = $mined['title'];
            $changes[] = "Name";
        }
        if (!empty($mined['email']) && $mined['email'] !== $row['email_address']) {
            $updateFields[] = "email_address = ?";
            $params[] = $mined['email'];
            $changes[] = "Email";
        }
        if (!empty($mined['phone']) && $mined['phone'] !== $row['mobile_number']) {
            // Only update phone if new one is found; strict length check roughly
            if(strlen($mined['phone']) > 6) {
                $updateFields[] = "mobile_number = ?";
                $params[] = $mined['phone'];
                $changes[] = "Phone";
            }
        }
        
        // Check for Elfsight tag update
        if (isset($mined['elfsight']) && $mined['elfsight']) {
             if (strpos($row['business_category'], 'Elfsight') === false) {
                 $updateFields[] = "business_category = ?";
                 $params[] = $row['business_category'] . ' [Elfsight]';
                 $changes[] = "Category (Elfsight)";
             }
        }

        if (!empty($updateFields)) {
            $params[] = $id;
            $sql = "UPDATE data_scraper SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $pdo->prepare($sql)->execute($params);
            $msg = "Updated: " . implode(', ', $changes);
            echo json_encode(['status' => 'success', 'message' => $msg]);
        } else {
            echo json_encode(['status' => 'success', 'message' => "Verified: Data is up to date."]);
        }
        exit;
    }
}
?>

<!-- ------------------ FRONTEND UI ------------------ -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Data Scraper Bot 2.0</h1></div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- CONTROLS -->
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">Scraper Controls</h3></div>
                <div class="card-body">
                    <form id="scraperForm" onsubmit="return false;">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Category</label>
                                <input type="text" id="catInput" class="form-control" placeholder="e.g. Dentist" required>
                            </div>
                            <div class="col-md-3">
                                <label>City</label>
                                <input type="text" id="cityInput" class="form-control" placeholder="e.g. New York">
                            </div>
                            <div class="col-md-3">
                                <label>Country</label>
                                <input type="text" id="countryInput" class="form-control" placeholder="e.g. USA" value="USA">
                            </div>
                            <div class="col-md-3">
                                <label>Action</label><br>
                                <button class="btn btn-success" onclick="startScrape()">Start Scraping</button>
                                <button class="btn btn-danger" onclick="clearData()">Clear All</button>
                            </div>
                        </div>
                    </form>
                    <div id="statusArea" class="mt-3 text-info font-weight-bold">Status: Ready</div>
                </div>
            </div>

            <!-- RESULTS TABLE -->
            <div class="card">
                <div class="card-header"><h3 class="card-title">Scraped Data</h3></div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="dataTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Business Name</th>
                                <th>Category</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM data_scraper ORDER BY id DESC LIMIT 500");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td><a href='{$row['website_url']}' target='_blank'>{$row['business_name']}</a></td>
                                    <td>{$row['business_category']}</td>
                                    <td>{$row['email_address']}</td>
                                    <td>{$row['mobile_number']}</td>
                                    <td>{$row['city']}</td>
                                    <td>{$row['city']}</td>
                                    <td>
                                        <button class='btn btn-sm btn-info' onclick='viewRow({$row['id']})' title='View'><i class='fas fa-eye'></i></button>
                                        <button class='btn btn-sm btn-warning' onclick='testRow({$row['id']})' title='Test & Verify'><i class='fas fa-sync-alt'></i></button>
                                        <button class='btn btn-sm btn-danger' onclick='deleteRow({$row['id']})' title='Delete'><i class='fas fa-trash'></i></button>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateStatus(msg) {
        $('#statusArea').text("Status: " + msg);
    }

    function startScrape() {
        var cat = $('#catInput').val();
        var city = $('#cityInput').val();
        var country = $('#countryInput').val();
        
        if(!cat) { alert("Please enter a category"); return; }
        
        updateStatus("Searching engines for " + cat + "...");
        
        $.post('data_scraper.php', {
            action: 'start_scrape',
            category: cat,
            city: city,
            country: country
        }, function(resp) {
            console.log(resp);
            if(resp.status === 'success') {
                updateStatus(resp.message);
                setTimeout(function(){ location.reload(); }, 1500);
            } else {
                updateStatus("Error: " + resp.message);
                if(resp.debug) console.log(resp.debug);
            }
        }, 'json').fail(function() {
            updateStatus("Server Failure. Check console.");
        });
    }

    function clearData() {
        if(confirm("Are you sure?")) {
            $.post('data_scraper.php', { action: 'clear_data' }, function() { location.reload(); });
        }
    }
    
    function deleteRow(id) {
        if(confirm("Delete this row?")) {
            $.post('data_scraper.php', { action: 'delete_row', id: id }, function() { location.reload(); });
        }
    }

    function viewRow(id) {
        $.post('data_scraper.php', { action: 'get_row', id: id }, function(resp) {
            if(resp.status === 'success') {
                var d = resp.data;
                var html = `
                    <table class="table table-bordered">
                        <tr><th>ID</th><td>${d.id}</td></tr>
                        <tr><th>Business Name</th><td>${d.business_name}</td></tr>
                        <tr><th>Category</th><td>${d.business_category}</td></tr>
                        <tr><th>Email</th><td>${d.email_address}</td></tr>
                        <tr><th>Phone</th><td>${d.mobile_number}</td></tr>
                        <tr><th>Website</th><td><a href="${d.website_url}" target="_blank">${d.website_url}</a></td></tr>
                        <tr><th>City</th><td>${d.city}</td></tr>
                        <tr><th>Country</th><td>${d.country}</td></tr>
                        <tr><th>Scraped At</th><td>${d.created_at}</td></tr>
                    </table>
                `;
                $('#globalViewModalBody').html(html);
                $('#globalViewModal').modal('show');
            } else {
                alert("Failed to fetch details.");
            }
        }, 'json');
    }

    function testRow(id) {
        var btn = $(event.target).closest('button');
        var originalIcon = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        updateStatus("Testing ID " + id + "...");
        
        $.post('data_scraper.php', { action: 'test_row', id: id }, function(resp) {
            btn.html(originalIcon).prop('disabled', false);
            if(resp.status === 'success') {
                alert(resp.message);
                updateStatus("ID " + id + ": " + resp.message);
                if(resp.message.indexOf('Updated') !== -1) location.reload();
            } else if (resp.status === 'warning') {
                alert(resp.message);
                updateStatus("ID " + id + " Deleted.");
                location.reload(); // Row deleted
            } else {
                alert("Error: " + resp.message);
                updateStatus("Error testing ID " + id);
            }
        }, 'json').fail(function() {
            btn.html(originalIcon).prop('disabled', false);
            alert("Verification Request Failed");
            updateStatus("Network Error");
        });
    }
</script>

<?php include 'inc/footer.php'; ?>
