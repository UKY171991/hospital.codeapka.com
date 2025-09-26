<?php
/**
 * Smart Upsert Helper - Prevents duplicates and updates based on updated_at timestamp
 * 
 * This function:
 * 1. Checks for existing records based on unique criteria
 * 2. If found, compares updated_at timestamps
 * 3. Updates only if new data is newer or different
 * 4. If not found, creates new record
 * 5. Returns action taken and record ID
 */

/**
 * Smart upsert with timestamp-based updates
 * 
 * @param PDO $pdo Database connection
 * @param string $table Table name
 * @param array $uniqueWhere Unique criteria to find existing record
 * @param array $data Data to insert/update
 * @param array $options Options for upsert behavior
 * @return array ['action' => 'created|updated|skipped', 'id' => int, 'message' => string]
 */
function smartUpsert($pdo, $table, $uniqueWhere, $data, $options = []) {
    // Default options
    $options = array_merge([
        'compare_timestamps' => true,
        'force_update' => false,
        'exclude_fields' => ['id', 'created_at'], // Fields to exclude from comparison
        'timestamp_field' => 'updated_at'
    ], $options);
    
    try {
        // Build WHERE clause for finding existing record
        $whereParts = [];
        $whereParams = [];
        foreach ($uniqueWhere as $col => $val) {
            if ($val !== null) {
                $whereParts[] = "$col = ?";
                $whereParams[] = $val;
            } else {
                $whereParts[] = "$col IS NULL";
            }
        }
        $whereClause = implode(' AND ', $whereParts);
        
        // Check if record exists
        $checkSql = "SELECT *, {$options['timestamp_field']} FROM $table WHERE $whereClause";
        $stmt = $pdo->prepare($checkSql);
        $stmt->execute($whereParams);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Record exists - decide whether to update
            $shouldUpdate = $options['force_update'];
            
            if (!$shouldUpdate && $options['compare_timestamps']) {
                // Compare timestamps if both have timestamp field
                if (isset($data[$options['timestamp_field']]) && isset($existing[$options['timestamp_field']])) {
                    $newTimestamp = strtotime($data[$options['timestamp_field']]);
                    $existingTimestamp = strtotime($existing[$options['timestamp_field']]);
                    $shouldUpdate = $newTimestamp > $existingTimestamp;
                } else {
                    // If no timestamp in new data, assume it's newer
                    $shouldUpdate = !isset($data[$options['timestamp_field']]);
                }
            }
            
            if (!$shouldUpdate) {
                // Compare data to see if anything changed
                $shouldUpdate = hasDataChanged($existing, $data, $options['exclude_fields']);
            }
            
            if ($shouldUpdate) {
                // Update existing record
                $updateFields = [];
                $updateParams = [];
                
                foreach ($data as $field => $value) {
                    if (!in_array($field, $options['exclude_fields'])) {
                        $updateFields[] = "$field = ?";
                        $updateParams[] = $value;
                    }
                }
                
                // Always update timestamp
                if (!isset($data[$options['timestamp_field']])) {
                    $updateFields[] = "{$options['timestamp_field']} = NOW()";
                }
                
                $updateParams = array_merge($updateParams, $whereParams);
                $updateSql = "UPDATE $table SET " . implode(', ', $updateFields) . " WHERE $whereClause";
                
                $stmt = $pdo->prepare($updateSql);
                $stmt->execute($updateParams);
                
                return [
                    'action' => 'updated',
                    'id' => $existing['id'],
                    'message' => 'Record updated successfully'
                ];
            } else {
                return [
                    'action' => 'skipped',
                    'id' => $existing['id'],
                    'message' => 'Record already up to date'
                ];
            }
        } else {
            // Record doesn't exist - create new one
            $insertFields = array_keys($data);
            $insertPlaceholders = array_fill(0, count($data), '?');
            
            // Add timestamp if not provided
            if (!isset($data[$options['timestamp_field']])) {
                $insertFields[] = $options['timestamp_field'];
                $insertPlaceholders[] = 'NOW()';
            }
            
            // Add created_at if not provided
            if (!isset($data['created_at']) && !in_array('created_at', $options['exclude_fields'])) {
                $insertFields[] = 'created_at';
                $insertPlaceholders[] = 'NOW()';
            }
            
            $insertSql = "INSERT INTO $table (" . implode(', ', $insertFields) . ") VALUES (" . implode(', ', $insertPlaceholders) . ")";
            
            $stmt = $pdo->prepare($insertSql);
            $stmt->execute(array_values($data));
            
            return [
                'action' => 'created',
                'id' => $pdo->lastInsertId(),
                'message' => 'Record created successfully'
            ];
        }
    } catch (Exception $e) {
        return [
            'action' => 'error',
            'id' => null,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

/**
 * Check if data has changed compared to existing record
 * 
 * @param array $existing Existing record from database
 * @param array $newData New data to compare
 * @param array $excludeFields Fields to exclude from comparison
 * @return bool True if data has changed
 */
function hasDataChanged($existing, $newData, $excludeFields = []) {
    foreach ($newData as $field => $value) {
        if (in_array($field, $excludeFields)) {
            continue;
        }
        
        if (!isset($existing[$field]) || $existing[$field] != $value) {
            return true;
        }
    }
    return false;
}

/**
 * Get unique criteria for different entity types
 * 
 * @param string $entityType Type of entity (user, patient, doctor, test, etc.)
 * @param array $data Data array
 * @return array Unique criteria for finding duplicates
 */
function getUniqueWhere($entityType, $data) {
    switch (strtolower($entityType)) {
        case 'user':
            $unique = [];
            if (!empty($data['username'])) {
                $unique['username'] = $data['username'];
            }
            return $unique;
            
        case 'patient':
            $unique = [];
            if (!empty($data['mobile'])) {
                $unique['mobile'] = $data['mobile'];
            } elseif (!empty($data['phone'])) {
                $unique['mobile'] = $data['phone']; // Map phone to mobile
            } elseif (!empty($data['uhid'])) {
                $unique['uhid'] = $data['uhid'];
            } else {
                // Fallback to name + address
                if (!empty($data['name'])) {
                    $unique['name'] = $data['name'];
                    if (!empty($data['address'])) {
                        $unique['address'] = $data['address'];
                    }
                }
            }
            return $unique;
            
        case 'doctor':
            $unique = [];
            if (!empty($data['registration_no'])) {
                $unique['registration_no'] = $data['registration_no'];
            } elseif (!empty($data['email'])) {
                $unique['email'] = $data['email'];
            } else {
                // Fallback to name + hospital
                if (!empty($data['name'])) {
                    $unique['name'] = $data['name'];
                    if (!empty($data['hospital'])) {
                        $unique['hospital'] = $data['hospital'];
                    }
                }
            }
            return $unique;
            
        case 'test':
            $unique = [];
            if (!empty($data['name']) && !empty($data['category_id'])) {
                $unique['name'] = $data['name'];
                $unique['category_id'] = $data['category_id'];
            }
            return $unique;
            
        case 'category':
            $unique = [];
            if (!empty($data['name'])) {
                $unique['name'] = $data['name'];
            }
            return $unique;
            
        case 'entry':
            $unique = [];
            if (!empty($data['patient_id']) && !empty($data['test_id']) && !empty($data['entry_date'])) {
                $unique['patient_id'] = $data['patient_id'];
                $unique['test_id'] = $data['test_id'];
                $unique['entry_date'] = $data['entry_date'];
            }
            return $unique;
            
        default:
            // Generic fallback - use name if available
            $unique = [];
            if (!empty($data['name'])) {
                $unique['name'] = $data['name'];
            }
            return $unique;
    }
}

/**
 * Validate required fields for entity
 * 
 * @param string $entityType Type of entity
 * @param array $data Data to validate
 * @return array Array of error messages (empty if valid)
 */
if (!function_exists('validateEntityData')) {
function validateEntityData($entityType, $data) {
    $errors = [];
    
    switch (strtolower($entityType)) {
        case 'user':
            if (empty($data['username'])) $errors[] = 'Username is required';
            if (empty($data['full_name'])) $errors[] = 'Full name is required';
            break;
            
        case 'patient':
            if (empty($data['name'])) $errors[] = 'Patient name is required';
            if (empty($data['mobile']) && empty($data['phone'])) $errors[] = 'Mobile number is required';
            break;
            
        case 'doctor':
            if (empty($data['name'])) $errors[] = 'Doctor name is required';
            break;
            
        case 'test':
            if (empty($data['name'])) $errors[] = 'Test name is required';
            if (empty($data['category_id'])) $errors[] = 'Category is required';
            break;
            
        case 'category':
            if (empty($data['name'])) $errors[] = 'Category name is required';
            break;
            
        case 'entry':
            if (empty($data['patient_id'])) $errors[] = 'Patient is required';
            break;
    }
    
    return $errors;
}
}
?>
