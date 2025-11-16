<?php
/**
 * Test Keyword Detection Logic
 * This verifies that income/expense detection works correctly
 */

echo "===========================================\n";
echo "KEYWORD DETECTION TEST\n";
echo "===========================================\n\n";

$test_cases = [
    // INCOME cases
    ['text' => 'Rs 1500 credited to your account', 'expected' => 'income', 'reason' => 'Contains "credited"'],
    ['text' => 'Payment received Rs 2000', 'expected' => 'income', 'reason' => 'Contains "received"'],
    ['text' => 'You received Rs 3000 from John', 'expected' => 'income', 'reason' => 'Contains "you received"'],
    ['text' => 'Deposit of Rs 5000 successful', 'expected' => 'income', 'reason' => 'Contains "deposit"'],
    ['text' => 'Money added Rs 1000', 'expected' => 'income', 'reason' => 'Contains "money added"'],
    ['text' => 'UPI credit Rs 2500', 'expected' => 'income', 'reason' => 'Contains "credit"'],
    
    // EXPENSE cases
    ['text' => 'Rs 1500 debited from your account', 'expected' => 'expense', 'reason' => 'Contains "debited"'],
    ['text' => 'Payment made Rs 2000', 'expected' => 'expense', 'reason' => 'Contains "payment made"'],
    ['text' => 'You paid Rs 3000 to vendor', 'expected' => 'expense', 'reason' => 'Contains "you paid"'],
    ['text' => 'Purchase of Rs 5000', 'expected' => 'expense', 'reason' => 'Contains "purchase"'],
    ['text' => 'Bill payment Rs 1000', 'expected' => 'expense', 'reason' => 'Contains "bill payment"'],
    ['text' => 'UPI debit Rs 2500', 'expected' => 'expense', 'reason' => 'Contains "debit"'],
    ['text' => 'Withdrawn Rs 3000', 'expected' => 'expense', 'reason' => 'Contains "withdrawn"'],
    
    // Edge cases (should NOT confuse)
    ['text' => 'Rs 1500 credited', 'expected' => 'income', 'reason' => 'Should detect "credited" not "debited"'],
    ['text' => 'Rs 1500 debited', 'expected' => 'expense', 'reason' => 'Should detect "debited" not "credited"'],
];

$passed = 0;
$failed = 0;

foreach ($test_cases as $index => $test) {
    echo "Test #" . ($index + 1) . ": " . $test['text'] . "\n";
    echo "Expected: " . $test['expected'] . " (" . $test['reason'] . ")\n";
    
    $result = detectTransactionType($test['text']);
    
    if ($result === $test['expected']) {
        echo "✅ PASSED\n";
        $passed++;
    } else {
        echo "❌ FAILED (Got: $result)\n";
        $failed++;
    }
    echo "\n";
}

echo "===========================================\n";
echo "TEST SUMMARY\n";
echo "===========================================\n";
echo "Total: " . count($test_cases) . "\n";
echo "Passed: $passed ✅\n";
echo "Failed: $failed " . ($failed > 0 ? "❌" : "") . "\n";
echo "===========================================\n";

function detectTransactionType($text) {
    $combined = strtolower($text);
    
    // EXPENSE patterns (check first - money going OUT)
    $expense_patterns = [
        '/\bdebit(ed)?\b/i',
        '/\bwithdraw(n|al)?\b/i',
        '/\bpurchase(d)?\b/i',
        '/\bpaid\b/i',
        '/\bspent\b/i',
        '/\bbill payment\b/i',
        '/\brecharge\b/i',
        '/\bsubscription\b/i',
        '/\bdeducted\b/i',
        '/\bpayment made\b/i',
        '/\bsent to\b/i',
        '/\border placed\b/i',
        '/\byou paid\b/i',
        '/\bpayment sent\b/i'
    ];
    
    // INCOME patterns (check second - money coming IN)
    $income_patterns = [
        '/\bcredit(ed)?\b/i',
        '/\bdeposit(ed)?\b/i',
        '/\breceived\b/i',
        '/\bincoming\b/i',
        '/\bmoney added\b/i',
        '/\bpayment received\b/i',
        '/\breceived from\b/i',
        '/\byou received\b/i',
        '/\byou got\b/i',
        '/\btransfer received\b/i'
    ];
    
    // Check EXPENSE first
    foreach ($expense_patterns as $pattern) {
        if (preg_match($pattern, $combined)) {
            return 'expense';
        }
    }
    
    // Check INCOME second
    foreach ($income_patterns as $pattern) {
        if (preg_match($pattern, $combined)) {
            return 'income';
        }
    }
    
    return 'none';
}
?>
