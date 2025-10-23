<?php
/**
 * Pathology Dashboard API
 * Provides comprehensive dashboard data and statistics
 * 
 * @author Hospital Management System
 * @version 1.0
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../inc/connection.php';

// Security check
$secret_key = $_GET['secret_key'] ?? $_POST['secret_key'] ?? '';
if ($secret_key !== 'hospital-api-secret-2024') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Valid secret key required.',
        'error_code' => 'UNAUTHORIZED'
    ]);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'overview';

try {
    switch ($action) {
        case 'overview':
            echo json_encode(getDashboardOverview($pdo));
            break;
            
        case 'stats':
            echo json_encode(getDashboardStats($pdo));
            break;
            
        case 'recent_activities':
            echo json_encode(getRecentActivities($pdo));
            break;
            
        case 'charts_data':
            echo json_encode(getChartsData($pdo));
            break;
            
        case 'quick_stats':
            echo json_encode(getQuickStats($pdo));
            break;
            
        case 'revenue_stats':
            echo json_encode(getRevenueStats($pdo));
            break;
            
        case 'test_performance':
            echo json_encode(getTestPerformance($pdo));
            break;
            
        case 'patient_demographics':
            echo json_encode(getPatientDemographics($pdo));
            break;
            
        case 'monthly_trends':
            echo json_encode(getMonthlyTrends($pdo));
            break;
            
        case 'top_tests':
            echo json_encode(getTopTests($pdo));
            break;
            
        case 'doctor_performance':
            echo json_encode(getDoctorPerformance($pdo));
            break;
            
        case 'alerts':
            echo json_encode(getSystemAlerts($pdo));
            break;
            
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action specified',
                'available_actions' => [
                    'overview', 'stats', 'recent_activities', 'charts_data',
                    'quick_stats', 'revenue_stats', 'test_performance',
                    'patient_demographics', 'monthly_trends', 'top_tests',
                    'doctor_performance', 'alerts'
                ]
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error: ' . $e->getMessage(),
        'error_code' => 'SERVER_ERROR'
    ]);
}

/**
 * Get comprehensive dashboard overview
 */
function getDashboardOverview($pdo) {
    try {
        $overview = [
            'success' => true,
            'data' => [
                'counts' => getEntityCounts($pdo),
                'recent_stats' => getRecentStats($pdo),
                'quick_metrics' => getQuickMetrics($pdo),
                'system_health' => getSystemHealth($pdo),
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];
        
        return $overview;
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch dashboard overview: ' . $e->getMessage()
        ];
    }
}

/**
 * Get entity counts for dashboard cards
 */
function getEntityCounts($pdo) {
    $counts = [
        'doctors' => 0,
        'patients' => 0,
        'entries' => 0,
        'tests' => 0,
        'test_categories' => 0,
        'users' => 0,
        'owners' => 0,
        'notices' => 0,
        'plans' => 0
    ];
    
    try {
        $counts['doctors'] = (int) $pdo->query('SELECT COUNT(*) FROM doctors')->fetchColumn();
        $counts['patients'] = (int) $pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();
        $counts['entries'] = (int) $pdo->query('SELECT COUNT(*) FROM entries')->fetchColumn();
        $counts['tests'] = (int) $pdo->query('SELECT COUNT(*) FROM tests')->fetchColumn();
        
        // Handle categories table (might be named differently)
        try {
            $counts['test_categories'] = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
        } catch (Exception $e) {
            try {
                $counts['test_categories'] = (int) $pdo->query('SELECT COUNT(*) FROM test_categories')->fetchColumn();
            } catch (Exception $e2) {
                $counts['test_categories'] = 0;
            }
        }
        
        $counts['users'] = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $counts['owners'] = (int) $pdo->query('SELECT COUNT(*) FROM owners')->fetchColumn();
        $counts['notices'] = (int) $pdo->query('SELECT COUNT(*) FROM notices')->fetchColumn();
        $counts['plans'] = (int) $pdo->query('SELECT COUNT(*) FROM plans')->fetchColumn();
        
    } catch (Exception $e) {
        // Return zeros if tables don't exist
    }
    
    return $counts;
}

/**
 * Get recent statistics (last 30 days)
 */
function getRecentStats($pdo) {
    $stats = [
        'new_patients_30d' => 0,
        'new_entries_30d' => 0,
        'completed_tests_30d' => 0,
        'revenue_30d' => 0
    ];
    
    try {
        // New patients in last 30 days
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
        $stmt->execute();
        $stats['new_patients_30d'] = (int) $stmt->fetchColumn();
        
        // New entries in last 30 days
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
        $stmt->execute();
        $stats['new_entries_30d'] = (int) $stmt->fetchColumn();
        
        // Completed tests in last 30 days (assuming status field exists)
        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE status = "completed" AND updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
            $stmt->execute();
            $stats['completed_tests_30d'] = (int) $stmt->fetchColumn();
        } catch (Exception $e) {
            $stats['completed_tests_30d'] = 0;
        }
        
        // Revenue calculation (if price/amount field exists)
        try {
            $stmt = $pdo->prepare('SELECT SUM(total_amount) FROM entries WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
            $stmt->execute();
            $stats['revenue_30d'] = (float) $stmt->fetchColumn() ?: 0;
        } catch (Exception $e) {
            $stats['revenue_30d'] = 0;
        }
        
    } catch (Exception $e) {
        // Return zeros if queries fail
    }
    
    return $stats;
}

/**
 * Get quick metrics for dashboard
 */
function getQuickMetrics($pdo) {
    $metrics = [
        'avg_tests_per_patient' => 0,
        'completion_rate' => 0,
        'active_doctors' => 0,
        'pending_entries' => 0
    ];
    
    try {
        // Average tests per patient
        $stmt = $pdo->query('SELECT AVG(test_count) FROM (SELECT COUNT(*) as test_count FROM entries GROUP BY patient_id) as subq');
        $metrics['avg_tests_per_patient'] = round((float) $stmt->fetchColumn() ?: 0, 2);
        
        // Completion rate (if status field exists)
        try {
            $total = (int) $pdo->query('SELECT COUNT(*) FROM entries')->fetchColumn();
            $completed = (int) $pdo->query('SELECT COUNT(*) FROM entries WHERE status = "completed"')->fetchColumn();
            $metrics['completion_rate'] = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
        } catch (Exception $e) {
            $metrics['completion_rate'] = 0;
        }
        
        // Active doctors (doctors with entries in last 30 days)
        try {
            $stmt = $pdo->prepare('SELECT COUNT(DISTINCT doctor_id) FROM entries WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
            $stmt->execute();
            $metrics['active_doctors'] = (int) $stmt->fetchColumn();
        } catch (Exception $e) {
            $metrics['active_doctors'] = 0;
        }
        
        // Pending entries
        try {
            $stmt = $pdo->query('SELECT COUNT(*) FROM entries WHERE status = "pending" OR status = "in_progress"');
            $metrics['pending_entries'] = (int) $stmt->fetchColumn();
        } catch (Exception $e) {
            $metrics['pending_entries'] = 0;
        }
        
    } catch (Exception $e) {
        // Return zeros if queries fail
    }
    
    return $metrics;
}

/**
 * Get system health indicators
 */
function getSystemHealth($pdo) {
    $health = [
        'database_status' => 'healthy',
        'last_backup' => null,
        'disk_usage' => 0,
        'active_sessions' => 0
    ];
    
    try {
        // Database connection test
        $pdo->query('SELECT 1');
        $health['database_status'] = 'healthy';
        
        // Get database size (approximate)
        try {
            $stmt = $pdo->query("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'DB Size in MB' FROM information_schema.tables WHERE table_schema = DATABASE()");
            $health['disk_usage'] = (float) $stmt->fetchColumn() ?: 0;
        } catch (Exception $e) {
            $health['disk_usage'] = 0;
        }
        
        // Active sessions (if sessions table exists)
        try {
            $stmt = $pdo->query('SELECT COUNT(*) FROM user_sessions WHERE expires_at > NOW()');
            $health['active_sessions'] = (int) $stmt->fetchColumn();
        } catch (Exception $e) {
            $health['active_sessions'] = 0;
        }
        
    } catch (Exception $e) {
        $health['database_status'] = 'error';
    }
    
    return $health;
}

/**
 * Get detailed dashboard statistics
 */
function getDashboardStats($pdo) {
    try {
        return [
            'success' => true,
            'data' => [
                'daily_stats' => getDailyStats($pdo),
                'weekly_stats' => getWeeklyStats($pdo),
                'monthly_stats' => getMonthlyStats($pdo),
                'yearly_stats' => getYearlyStats($pdo)
            ]
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch statistics: ' . $e->getMessage()
        ];
    }
}

/**
 * Get daily statistics
 */
function getDailyStats($pdo) {
    $stats = [];
    
    try {
        // Get last 7 days data
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            
            $dayStats = [
                'date' => $date,
                'patients' => 0,
                'entries' => 0,
                'tests' => 0,
                'revenue' => 0
            ];
            
            // Patients registered on this day
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE DATE(created_at) = ?');
            $stmt->execute([$date]);
            $dayStats['patients'] = (int) $stmt->fetchColumn();
            
            // Entries created on this day
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE DATE(created_at) = ?');
            $stmt->execute([$date]);
            $dayStats['entries'] = (int) $stmt->fetchColumn();
            
            // Tests completed on this day
            try {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE DATE(updated_at) = ? AND status = "completed"');
                $stmt->execute([$date]);
                $dayStats['tests'] = (int) $stmt->fetchColumn();
            } catch (Exception $e) {
                $dayStats['tests'] = 0;
            }
            
            // Revenue for this day
            try {
                $stmt = $pdo->prepare('SELECT SUM(total_amount) FROM entries WHERE DATE(created_at) = ?');
                $stmt->execute([$date]);
                $dayStats['revenue'] = (float) $stmt->fetchColumn() ?: 0;
            } catch (Exception $e) {
                $dayStats['revenue'] = 0;
            }
            
            $stats[] = $dayStats;
        }
    } catch (Exception $e) {
        // Return empty array if queries fail
    }
    
    return $stats;
}

/**
 * Get weekly statistics
 */
function getWeeklyStats($pdo) {
    $stats = [];
    
    try {
        // Get last 4 weeks data
        for ($i = 3; $i >= 0; $i--) {
            $startDate = date('Y-m-d', strtotime("-" . (($i + 1) * 7) . " days"));
            $endDate = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            
            $weekStats = [
                'week' => "Week " . (4 - $i),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'patients' => 0,
                'entries' => 0,
                'revenue' => 0
            ];
            
            // Patients registered in this week
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE DATE(created_at) BETWEEN ? AND ?');
            $stmt->execute([$startDate, $endDate]);
            $weekStats['patients'] = (int) $stmt->fetchColumn();
            
            // Entries created in this week
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE DATE(created_at) BETWEEN ? AND ?');
            $stmt->execute([$startDate, $endDate]);
            $weekStats['entries'] = (int) $stmt->fetchColumn();
            
            // Revenue for this week
            try {
                $stmt = $pdo->prepare('SELECT SUM(total_amount) FROM entries WHERE DATE(created_at) BETWEEN ? AND ?');
                $stmt->execute([$startDate, $endDate]);
                $weekStats['revenue'] = (float) $stmt->fetchColumn() ?: 0;
            } catch (Exception $e) {
                $weekStats['revenue'] = 0;
            }
            
            $stats[] = $weekStats;
        }
    } catch (Exception $e) {
        // Return empty array if queries fail
    }
    
    return $stats;
}

/**
 * Get monthly statistics
 */
function getMonthlyStats($pdo) {
    $stats = [];
    
    try {
        // Get last 6 months data
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-$i months"));
            $monthName = date('M Y', strtotime($date));
            
            $monthStats = [
                'month' => $monthName,
                'date' => $date,
                'patients' => 0,
                'entries' => 0,
                'revenue' => 0,
                'tests_completed' => 0
            ];
            
            // Patients registered in this month
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE YEAR(created_at) = YEAR(?) AND MONTH(created_at) = MONTH(?)');
            $stmt->execute([$date, $date]);
            $monthStats['patients'] = (int) $stmt->fetchColumn();
            
            // Entries created in this month
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE YEAR(created_at) = YEAR(?) AND MONTH(created_at) = MONTH(?)');
            $stmt->execute([$date, $date]);
            $monthStats['entries'] = (int) $stmt->fetchColumn();
            
            // Tests completed in this month
            try {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE YEAR(updated_at) = YEAR(?) AND MONTH(updated_at) = MONTH(?) AND status = "completed"');
                $stmt->execute([$date, $date]);
                $monthStats['tests_completed'] = (int) $stmt->fetchColumn();
            } catch (Exception $e) {
                $monthStats['tests_completed'] = 0;
            }
            
            // Revenue for this month
            try {
                $stmt = $pdo->prepare('SELECT SUM(total_amount) FROM entries WHERE YEAR(created_at) = YEAR(?) AND MONTH(created_at) = MONTH(?)');
                $stmt->execute([$date, $date]);
                $monthStats['revenue'] = (float) $stmt->fetchColumn() ?: 0;
            } catch (Exception $e) {
                $monthStats['revenue'] = 0;
            }
            
            $stats[] = $monthStats;
        }
    } catch (Exception $e) {
        // Return empty array if queries fail
    }
    
    return $stats;
}

/**
 * Get yearly statistics
 */
function getYearlyStats($pdo) {
    $currentYear = date('Y');
    $stats = [];
    
    try {
        // Get last 3 years data
        for ($i = 2; $i >= 0; $i--) {
            $year = $currentYear - $i;
            
            $yearStats = [
                'year' => $year,
                'patients' => 0,
                'entries' => 0,
                'revenue' => 0,
                'doctors' => 0
            ];
            
            // Patients registered in this year
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE YEAR(created_at) = ?');
            $stmt->execute([$year]);
            $yearStats['patients'] = (int) $stmt->fetchColumn();
            
            // Entries created in this year
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE YEAR(created_at) = ?');
            $stmt->execute([$year]);
            $yearStats['entries'] = (int) $stmt->fetchColumn();
            
            // Revenue for this year
            try {
                $stmt = $pdo->prepare('SELECT SUM(total_amount) FROM entries WHERE YEAR(created_at) = ?');
                $stmt->execute([$year]);
                $yearStats['revenue'] = (float) $stmt->fetchColumn() ?: 0;
            } catch (Exception $e) {
                $yearStats['revenue'] = 0;
            }
            
            // Doctors registered in this year
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM doctors WHERE YEAR(created_at) = ?');
            $stmt->execute([$year]);
            $yearStats['doctors'] = (int) $stmt->fetchColumn();
            
            $stats[] = $yearStats;
        }
    } catch (Exception $e) {
        // Return empty array if queries fail
    }
    
    return $stats;
}

/**
 * Get recent activities
 */
function getRecentActivities($pdo) {
    try {
        $activities = [];
        
        // Recent patients
        $stmt = $pdo->prepare('SELECT id, name, created_at FROM patients ORDER BY created_at DESC LIMIT 5');
        $stmt->execute();
        $recentPatients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($recentPatients as $patient) {
            $activities[] = [
                'type' => 'patient_registered',
                'title' => 'New Patient Registered',
                'description' => 'Patient: ' . $patient['name'],
                'timestamp' => $patient['created_at'],
                'icon' => 'user-plus',
                'color' => 'success'
            ];
        }
        
        // Recent entries
        $stmt = $pdo->prepare('
            SELECT e.id, e.created_at, p.name as patient_name 
            FROM entries e 
            LEFT JOIN patients p ON e.patient_id = p.id 
            ORDER BY e.created_at DESC LIMIT 5
        ');
        $stmt->execute();
        $recentEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($recentEntries as $entry) {
            $activities[] = [
                'type' => 'entry_created',
                'title' => 'New Test Entry',
                'description' => 'Entry for: ' . ($entry['patient_name'] ?: 'Unknown Patient'),
                'timestamp' => $entry['created_at'],
                'icon' => 'file-medical',
                'color' => 'info'
            ];
        }
        
        // Sort activities by timestamp
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return [
            'success' => true,
            'data' => array_slice($activities, 0, 10) // Return top 10 activities
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch recent activities: ' . $e->getMessage()
        ];
    }
}

/**
 * Get charts data for dashboard
 */
function getChartsData($pdo) {
    try {
        return [
            'success' => true,
            'data' => [
                'patient_growth' => getPatientGrowthChart($pdo),
                'revenue_chart' => getRevenueChart($pdo),
                'test_distribution' => getTestDistributionChart($pdo),
                'doctor_performance' => getDoctorPerformanceChart($pdo)
            ]
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch charts data: ' . $e->getMessage()
        ];
    }
}

/**
 * Get patient growth chart data
 */
function getPatientGrowthChart($pdo) {
    $chartData = [
        'labels' => [],
        'datasets' => [
            [
                'label' => 'New Patients',
                'data' => [],
                'borderColor' => '#4f46e5',
                'backgroundColor' => 'rgba(79, 70, 229, 0.1)'
            ]
        ]
    ];
    
    try {
        // Get last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-$i months"));
            $monthName = date('M Y', strtotime($date));
            
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE YEAR(created_at) = YEAR(?) AND MONTH(created_at) = MONTH(?)');
            $stmt->execute([$date, $date]);
            $count = (int) $stmt->fetchColumn();
            
            $chartData['labels'][] = $monthName;
            $chartData['datasets'][0]['data'][] = $count;
        }
    } catch (Exception $e) {
        // Return empty chart data if queries fail
    }
    
    return $chartData;
}

/**
 * Get revenue chart data
 */
function getRevenueChart($pdo) {
    $chartData = [
        'labels' => [],
        'datasets' => [
            [
                'label' => 'Revenue (₹)',
                'data' => [],
                'borderColor' => '#10b981',
                'backgroundColor' => 'rgba(16, 185, 129, 0.1)'
            ]
        ]
    ];
    
    try {
        // Get last 12 months revenue
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-$i months"));
            $monthName = date('M Y', strtotime($date));
            
            $stmt = $pdo->prepare('SELECT SUM(total_amount) FROM entries WHERE YEAR(created_at) = YEAR(?) AND MONTH(created_at) = MONTH(?)');
            $stmt->execute([$date, $date]);
            $revenue = (float) $stmt->fetchColumn() ?: 0;
            
            $chartData['labels'][] = $monthName;
            $chartData['datasets'][0]['data'][] = $revenue;
        }
    } catch (Exception $e) {
        // Return empty chart data if queries fail
    }
    
    return $chartData;
}

/**
 * Get test distribution chart data
 */
function getTestDistributionChart($pdo) {
    $chartData = [
        'labels' => [],
        'datasets' => [
            [
                'data' => [],
                'backgroundColor' => [
                    '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                    '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6b7280'
                ]
            ]
        ]
    ];
    
    try {
        // Get top 10 most ordered tests
        $stmt = $pdo->prepare('
            SELECT t.name, COUNT(*) as count 
            FROM tests t 
            INNER JOIN entry_tests et ON t.id = et.test_id 
            GROUP BY t.id, t.name 
            ORDER BY count DESC 
            LIMIT 10
        ');
        $stmt->execute();
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($tests as $test) {
            $chartData['labels'][] = $test['name'];
            $chartData['datasets'][0]['data'][] = (int) $test['count'];
        }
    } catch (Exception $e) {
        // Return empty chart data if queries fail
    }
    
    return $chartData;
}

/**
 * Get doctor performance chart data
 */
function getDoctorPerformanceChart($pdo) {
    $chartData = [
        'labels' => [],
        'datasets' => [
            [
                'label' => 'Entries Handled',
                'data' => [],
                'backgroundColor' => 'rgba(79, 70, 229, 0.8)'
            ]
        ]
    ];
    
    try {
        // Get top 10 doctors by entries handled
        $stmt = $pdo->prepare('
            SELECT d.name, COUNT(e.id) as entry_count 
            FROM doctors d 
            LEFT JOIN entries e ON d.id = e.doctor_id 
            GROUP BY d.id, d.name 
            ORDER BY entry_count DESC 
            LIMIT 10
        ');
        $stmt->execute();
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($doctors as $doctor) {
            $chartData['labels'][] = $doctor['name'];
            $chartData['datasets'][0]['data'][] = (int) $doctor['entry_count'];
        }
    } catch (Exception $e) {
        // Return empty chart data if queries fail
    }
    
    return $chartData;
}

/**
 * Get quick stats for widgets
 */
function getQuickStats($pdo) {
    try {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        $stats = [
            'today' => [
                'patients' => 0,
                'entries' => 0,
                'revenue' => 0
            ],
            'yesterday' => [
                'patients' => 0,
                'entries' => 0,
                'revenue' => 0
            ],
            'growth' => [
                'patients' => 0,
                'entries' => 0,
                'revenue' => 0
            ]
        ];
        
        // Today's stats
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE DATE(created_at) = ?');
        $stmt->execute([$today]);
        $stats['today']['patients'] = (int) $stmt->fetchColumn();
        
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE DATE(created_at) = ?');
        $stmt->execute([$today]);
        $stats['today']['entries'] = (int) $stmt->fetchColumn();
        
        try {
            $stmt = $pdo->prepare('SELECT SUM(total_amount) FROM entries WHERE DATE(created_at) = ?');
            $stmt->execute([$today]);
            $stats['today']['revenue'] = (float) $stmt->fetchColumn() ?: 0;
        } catch (Exception $e) {
            $stats['today']['revenue'] = 0;
        }
        
        // Yesterday's stats
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE DATE(created_at) = ?');
        $stmt->execute([$yesterday]);
        $stats['yesterday']['patients'] = (int) $stmt->fetchColumn();
        
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE DATE(created_at) = ?');
        $stmt->execute([$yesterday]);
        $stats['yesterday']['entries'] = (int) $stmt->fetchColumn();
        
        try {
            $stmt = $pdo->prepare('SELECT SUM(total_amount) FROM entries WHERE DATE(created_at) = ?');
            $stmt->execute([$yesterday]);
            $stats['yesterday']['revenue'] = (float) $stmt->fetchColumn() ?: 0;
        } catch (Exception $e) {
            $stats['yesterday']['revenue'] = 0;
        }
        
        // Calculate growth percentages
        foreach (['patients', 'entries', 'revenue'] as $metric) {
            if ($stats['yesterday'][$metric] > 0) {
                $growth = (($stats['today'][$metric] - $stats['yesterday'][$metric]) / $stats['yesterday'][$metric]) * 100;
                $stats['growth'][$metric] = round($growth, 2);
            } else {
                $stats['growth'][$metric] = $stats['today'][$metric] > 0 ? 100 : 0;
            }
        }
        
        return [
            'success' => true,
            'data' => $stats
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch quick stats: ' . $e->getMessage()
        ];
    }
}

/**
 * Get revenue statistics
 */
function getRevenueStats($pdo) {
    try {
        $stats = [
            'total_revenue' => 0,
            'monthly_revenue' => 0,
            'daily_average' => 0,
            'top_revenue_sources' => []
        ];
        
        // Total revenue
        try {
            $stmt = $pdo->query('SELECT SUM(total_amount) FROM entries');
            $stats['total_revenue'] = (float) $stmt->fetchColumn() ?: 0;
        } catch (Exception $e) {
            $stats['total_revenue'] = 0;
        }
        
        // Monthly revenue (current month)
        try {
            $stmt = $pdo->prepare('SELECT SUM(total_amount) FROM entries WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())');
            $stmt->execute();
            $stats['monthly_revenue'] = (float) $stmt->fetchColumn() ?: 0;
        } catch (Exception $e) {
            $stats['monthly_revenue'] = 0;
        }
        
        // Daily average (last 30 days)
        try {
            $stmt = $pdo->prepare('SELECT AVG(daily_revenue) FROM (SELECT DATE(created_at) as date, SUM(total_amount) as daily_revenue FROM entries WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(created_at)) as daily_stats');
            $stmt->execute();
            $stats['daily_average'] = round((float) $stmt->fetchColumn() ?: 0, 2);
        } catch (Exception $e) {
            $stats['daily_average'] = 0;
        }
        
        // Top revenue sources (by test type)
        try {
            $stmt = $pdo->prepare('
                SELECT t.name, SUM(e.total_amount) as revenue 
                FROM tests t 
                INNER JOIN entry_tests et ON t.id = et.test_id 
                INNER JOIN entries e ON et.entry_id = e.id 
                GROUP BY t.id, t.name 
                ORDER BY revenue DESC 
                LIMIT 5
            ');
            $stmt->execute();
            $stats['top_revenue_sources'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $stats['top_revenue_sources'] = [];
        }
        
        return [
            'success' => true,
            'data' => $stats
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch revenue stats: ' . $e->getMessage()
        ];
    }
}

/**
 * Get test performance metrics
 */
function getTestPerformance($pdo) {
    try {
        $performance = [
            'most_ordered' => [],
            'completion_times' => [],
            'success_rates' => []
        ];
        
        // Most ordered tests
        try {
            $stmt = $pdo->prepare('
                SELECT t.name, COUNT(*) as order_count 
                FROM tests t 
                INNER JOIN entry_tests et ON t.id = et.test_id 
                GROUP BY t.id, t.name 
                ORDER BY order_count DESC 
                LIMIT 10
            ');
            $stmt->execute();
            $performance['most_ordered'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $performance['most_ordered'] = [];
        }
        
        // Average completion times (if timestamps available)
        try {
            $stmt = $pdo->prepare('
                SELECT t.name, AVG(TIMESTAMPDIFF(HOUR, e.created_at, e.updated_at)) as avg_hours 
                FROM tests t 
                INNER JOIN entry_tests et ON t.id = et.test_id 
                INNER JOIN entries e ON et.entry_id = e.id 
                WHERE e.status = "completed" 
                GROUP BY t.id, t.name 
                ORDER BY avg_hours ASC 
                LIMIT 10
            ');
            $stmt->execute();
            $performance['completion_times'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $performance['completion_times'] = [];
        }
        
        return [
            'success' => true,
            'data' => $performance
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch test performance: ' . $e->getMessage()
        ];
    }
}

/**
 * Get patient demographics
 */
function getPatientDemographics($pdo) {
    try {
        $demographics = [
            'age_groups' => [],
            'gender_distribution' => [],
            'location_distribution' => []
        ];
        
        // Age groups (if age/birth_date field exists)
        try {
            $stmt = $pdo->query("
                SELECT 
                    CASE 
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18 THEN 'Under 18'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 30 THEN '18-30'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 31 AND 50 THEN '31-50'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 51 AND 70 THEN '51-70'
                        ELSE 'Over 70'
                    END as age_group,
                    COUNT(*) as count
                FROM patients 
                WHERE birth_date IS NOT NULL
                GROUP BY age_group
                ORDER BY age_group
            ");
            $demographics['age_groups'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $demographics['age_groups'] = [];
        }
        
        // Gender distribution (if gender field exists)
        try {
            $stmt = $pdo->query('SELECT gender, COUNT(*) as count FROM patients WHERE gender IS NOT NULL GROUP BY gender');
            $demographics['gender_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $demographics['gender_distribution'] = [];
        }
        
        // Location distribution (if city/address field exists)
        try {
            $stmt = $pdo->query('SELECT city, COUNT(*) as count FROM patients WHERE city IS NOT NULL GROUP BY city ORDER BY count DESC LIMIT 10');
            $demographics['location_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $demographics['location_distribution'] = [];
        }
        
        return [
            'success' => true,
            'data' => $demographics
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch patient demographics: ' . $e->getMessage()
        ];
    }
}

/**
 * Get monthly trends
 */
function getMonthlyTrends($pdo) {
    try {
        return [
            'success' => true,
            'data' => getMonthlyStats($pdo)
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch monthly trends: ' . $e->getMessage()
        ];
    }
}

/**
 * Get top tests
 */
function getTopTests($pdo) {
    try {
        $topTests = [];
        
        // Most popular tests
        $stmt = $pdo->prepare('
            SELECT t.id, t.name, t.price, COUNT(et.test_id) as order_count,
                   SUM(t.price) as total_revenue
            FROM tests t 
            LEFT JOIN entry_tests et ON t.id = et.test_id 
            GROUP BY t.id, t.name, t.price 
            ORDER BY order_count DESC 
            LIMIT 20
        ');
        $stmt->execute();
        $topTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $topTests
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch top tests: ' . $e->getMessage()
        ];
    }
}

/**
 * Get doctor performance
 */
function getDoctorPerformance($pdo) {
    try {
        $performance = [];
        
        // Doctor performance metrics
        $stmt = $pdo->prepare('
            SELECT d.id, d.name, d.specialization,
                   COUNT(e.id) as total_entries,
                   COUNT(CASE WHEN e.status = "completed" THEN 1 END) as completed_entries,
                   AVG(TIMESTAMPDIFF(HOUR, e.created_at, e.updated_at)) as avg_completion_time
            FROM doctors d 
            LEFT JOIN entries e ON d.id = e.doctor_id 
            GROUP BY d.id, d.name, d.specialization 
            ORDER BY total_entries DESC 
            LIMIT 20
        ');
        $stmt->execute();
        $performance = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate completion rate for each doctor
        foreach ($performance as &$doctor) {
            if ($doctor['total_entries'] > 0) {
                $doctor['completion_rate'] = round(($doctor['completed_entries'] / $doctor['total_entries']) * 100, 2);
            } else {
                $doctor['completion_rate'] = 0;
            }
            $doctor['avg_completion_time'] = round((float) $doctor['avg_completion_time'], 2);
        }
        
        return [
            'success' => true,
            'data' => $performance
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch doctor performance: ' . $e->getMessage()
        ];
    }
}

/**
 * Get system alerts
 */
function getSystemAlerts($pdo) {
    try {
        $alerts = [];
        
        // Check for pending entries older than 24 hours
        try {
            $stmt = $pdo->prepare('
                SELECT COUNT(*) as count 
                FROM entries 
                WHERE status IN ("pending", "in_progress") 
                AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ');
            $stmt->execute();
            $pendingCount = (int) $stmt->fetchColumn();
            
            if ($pendingCount > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Pending Entries Alert',
                    'message' => "$pendingCount entries have been pending for more than 24 hours",
                    'action' => 'Review pending entries',
                    'priority' => 'medium'
                ];
            }
        } catch (Exception $e) {
            // Skip this alert if query fails
        }
        
        // Check for low test inventory (if inventory tracking exists)
        try {
            $stmt = $pdo->prepare('
                SELECT COUNT(*) as count 
                FROM tests 
                WHERE stock_quantity < min_stock_level 
                AND stock_quantity IS NOT NULL 
                AND min_stock_level IS NOT NULL
            ');
            $stmt->execute();
            $lowStockCount = (int) $stmt->fetchColumn();
            
            if ($lowStockCount > 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'title' => 'Low Stock Alert',
                    'message' => "$lowStockCount tests are running low on stock",
                    'action' => 'Reorder supplies',
                    'priority' => 'high'
                ];
            }
        } catch (Exception $e) {
            // Skip this alert if query fails
        }
        
        // Check for inactive doctors (no entries in last 30 days)
        try {
            $stmt = $pdo->prepare('
                SELECT COUNT(*) as count 
                FROM doctors d 
                WHERE NOT EXISTS (
                    SELECT 1 FROM entries e 
                    WHERE e.doctor_id = d.id 
                    AND e.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                )
            ');
            $stmt->execute();
            $inactiveDoctors = (int) $stmt->fetchColumn();
            
            if ($inactiveDoctors > 0) {
                $alerts[] = [
                    'type' => 'info',
                    'title' => 'Inactive Doctors',
                    'message' => "$inactiveDoctors doctors have not created any entries in the last 30 days",
                    'action' => 'Review doctor activity',
                    'priority' => 'low'
                ];
            }
        } catch (Exception $e) {
            // Skip this alert if query fails
        }
        
        return [
            'success' => true,
            'data' => $alerts
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to fetch system alerts: ' . $e->getMessage()
        ];
    }
}
?>