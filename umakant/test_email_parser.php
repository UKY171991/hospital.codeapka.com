<?php
/**
 * Test Email Parser Logic
 * This script tests the email parsing without connecting to Gmail
 * Run: php test_email_parser.php
 */

// Test email samples
$test_emails = [
    [
        'subject' => 'Payment Received - Rs. 1500.00',
        'body' => 'Dear Customer, Your payment of Rs. 1500.00 has been credited to your account via UPI. Transaction ID: 123456789. Thank you for your payment.',
        'from' => 'payments@bank.com',
        'expected' => 'income'
    ],
    [
        'subject' => 'Bill Payment Successful',
        'body' => 'Your electricity bill payment of INR 2,500.00 has been debited from your account. Reference: ELEC123456',
        'from' => 'utility@powercompany.com',
        'expected' => 'expense'
    ],
    [
        'subject' => 'UPI Payment Credited',
        'body' => 'Amount ₹3,250.50 credited to your account from consultation fees. UPI Ref: 987654321',
        'from' => 'upi@paytm.com',
        'expected' => 'income'
    ],
    [
        'subject' => 'Purchase Confirmation',
        'body' => 'Your purchase of medical supplies for Rs 5000 has been completed. Amount debited via Card.',
        'from' => 'orders@medsupply.com',
        'expected' => 'expense'
    ],
    [
        'subject' => 'NEFT Credit Alert',
        'body' => 'NEFT credit of Rs. 10,000.00 received in your account. Sender: Patient Payment',
        'from' => 'alerts@bank.com',
        'expected' => 'income'
    ],
    [
        'subject' => 'Regular Email',
        'body' => 'This is just a regular email without any payment information.',
        'from' => 'friend@example.com',
        'expected' => 'none'
    ]
];

echo "===========================================\n";
echo "EMAIL PARSER LOGIC TEST\n";
echo "===========================================\n\n";

$passed = 0;
$failed = 0;

foreach ($test_emails as $index => $email) {
    echo "Test #" . ($index + 1) . "\n";
    echo "Subject: " . $email['subject'] . "\n";
    echo "Expected: " . $email['expected'] . "\n";
    
    $result = parseTransactionEmail(
        $email['subject'],
        $email['body'],
        $email['from'],
        date('Y-m-d')
    );
    
    if ($result) {
        echo "Result: " . $result['type'] . "\n";
        echo "Amount: ₹" . $result['amount'] . "\n";
        echo "Payment Method: " . $result['payment_method'] . "\n";
        echo "Category: " . $result['category'] . "\n";
        echo "Description: " . $result['description'] . "\n";
        
        if ($result['type'] === $email['expected']) {
            echo "✅ PASSED\n";
            $passed++;
        } else {
            echo "❌ FAILED (Expected: {$email['expected']}, Got: {$result['type']})\n";
            $failed++;
        }
    } else {
        if ($email['expected'] === 'none') {
            echo "Result: Not a transaction email\n";
            echo "✅ PASSED\n";
            $passed++;
        } else {
            echo "Result: Not detected\n";
            echo "❌ FAILED (Expected: {$email['expected']}, Got: none)\n";
            $failed++;
        }
    }
    
    echo "\n" . str_repeat("-", 43) . "\n\n";
}

echo "===========================================\n";
echo "TEST SUMMARY\n";
echo "===========================================\n";
echo "Total Tests: " . count($test_emails) . "\n";
echo "Passed: $passed ✅\n";
echo "Failed: $failed " . ($failed > 0 ? "❌" : "") . "\n";
echo "Success Rate: " . round(($passed / count($test_emails)) * 100, 2) . "%\n";
echo "===========================================\n";

/**
 * Parse email for transaction information
 */
function parseTransactionEmail($subject, $body, $from, $date) {
    $subject_lower = strtolower($subject);
    $body_lower = strtolower($body);
    $combined = $subject_lower . ' ' . $body_lower;
    
    // Payment keywords for income
    $income_keywords = [
        'payment received', 'payment credited', 'money received', 'credited to',
        'payment successful', 'transaction successful', 'amount credited',
        'upi credit', 'imps credit', 'neft credit', 'rtgs credit',
        'paytm payment', 'phonepe payment', 'gpay payment', 'google pay'
    ];
    
    // Payment keywords for expense
    $expense_keywords = [
        'payment debited', 'amount debited', 'payment made', 'transaction debited',
        'purchase', 'bill payment', 'recharge', 'subscription',
        'upi debit', 'imps debit', 'neft debit', 'rtgs debit'
    ];
    
    $is_income = false;
    $is_expense = false;
    
    foreach ($income_keywords as $keyword) {
        if (strpos($combined, $keyword) !== false) {
            $is_income = true;
            break;
        }
    }
    
    if (!$is_income) {
        foreach ($expense_keywords as $keyword) {
            if (strpos($combined, $keyword) !== false) {
                $is_expense = true;
                break;
            }
        }
    }
    
    if (!$is_income && !$is_expense) {
        return null; // Not a transaction email
    }
    
    // Extract amount
    $amount = extractAmount($combined);
    if (!$amount) {
        return null; // No amount found
    }
    
    // Extract payment method
    $payment_method = extractPaymentMethod($combined);
    
    // Extract description
    $description = extractDescription($subject, $body);
    
    // Determine category
    $category = determineCategory($combined, $is_income);
    
    return [
        'type' => $is_income ? 'income' : 'expense',
        'date' => $date,
        'category' => $category,
        'description' => $description,
        'amount' => $amount,
        'payment_method' => $payment_method,
        'source_email' => $from,
        'notes' => 'Auto-imported from email'
    ];
}

/**
 * Extract amount from text
 */
function extractAmount($text) {
    // Pattern for Indian currency (₹ or Rs or INR)
    $patterns = [
        '/(?:rs\.?|inr|₹)\s*([0-9,]+(?:\.[0-9]{2})?)/i',
        '/([0-9,]+(?:\.[0-9]{2})?)\s*(?:rs\.?|inr|₹)/i',
        '/amount[:\s]+(?:rs\.?|inr|₹)?\s*([0-9,]+(?:\.[0-9]{2})?)/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $amount = str_replace(',', '', $matches[1]);
            return floatval($amount);
        }
    }
    
    return null;
}

/**
 * Extract payment method
 */
function extractPaymentMethod($text) {
    if (strpos($text, 'upi') !== false) return 'UPI';
    if (strpos($text, 'card') !== false || strpos($text, 'debit') !== false || strpos($text, 'credit') !== false) return 'Card';
    if (strpos($text, 'neft') !== false) return 'Bank Transfer';
    if (strpos($text, 'rtgs') !== false) return 'Bank Transfer';
    if (strpos($text, 'imps') !== false) return 'Bank Transfer';
    if (strpos($text, 'cheque') !== false || strpos($text, 'check') !== false) return 'Cheque';
    if (strpos($text, 'cash') !== false) return 'Cash';
    
    return 'Bank Transfer'; // Default
}

/**
 * Extract description
 */
function extractDescription($subject, $body) {
    // Clean subject
    $description = trim($subject);
    
    // Limit length
    if (strlen($description) > 200) {
        $description = substr($description, 0, 197) . '...';
    }
    
    return $description;
}

/**
 * Determine category based on keywords
 */
function determineCategory($text, $is_income) {
    if ($is_income) {
        if (strpos($text, 'consultation') !== false) return 'Consultation';
        if (strpos($text, 'lab') !== false || strpos($text, 'test') !== false) return 'Lab Tests';
        if (strpos($text, 'pharmacy') !== false || strpos($text, 'medicine') !== false) return 'Pharmacy';
        if (strpos($text, 'surgery') !== false || strpos($text, 'operation') !== false) return 'Surgery';
        if (strpos($text, 'room') !== false || strpos($text, 'bed') !== false) return 'Room Charges';
        return 'Other Services';
    } else {
        if (strpos($text, 'medical supply') !== false || strpos($text, 'supplies') !== false) return 'Medical Supplies';
        if (strpos($text, 'equipment') !== false) return 'Equipment';
        if (strpos($text, 'electricity') !== false || strpos($text, 'water') !== false || strpos($text, 'utility') !== false) return 'Utilities';
        if (strpos($text, 'salary') !== false || strpos($text, 'wage') !== false) return 'Salaries';
        if (strpos($text, 'rent') !== false) return 'Rent';
        if (strpos($text, 'maintenance') !== false || strpos($text, 'repair') !== false) return 'Maintenance';
        if (strpos($text, 'marketing') !== false || strpos($text, 'advertisement') !== false) return 'Marketing';
        if (strpos($text, 'transport') !== false || strpos($text, 'fuel') !== false) return 'Transportation';
        return 'Other';
    }
}
?>
