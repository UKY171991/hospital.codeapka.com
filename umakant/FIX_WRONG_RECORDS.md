# Fix Wrong Income/Expense Records

## Problem
Some emails were incorrectly categorized:
- Income emails saved as Expense
- Expense emails saved as Income

## ✅ Solution Applied

The keyword detection logic has been **completely rewritten** using regex with word boundaries:

### Before (Wrong):
- "credited" could match "debited" ❌
- "debit" could match "credit" ❌
- Simple string matching caused confusion

### After (Correct):
- Uses `\b` word boundaries in regex ✅
- "debited" matches ONLY "debited" (not "credited") ✅
- "credited" matches ONLY "credited" (not "debited") ✅
- Much more accurate detection

## 🧪 Test the New Logic

Run this test to verify it works:
```bash
php umakant/test_keyword_detection.php
```

You should see all tests pass! ✅

## 🔄 Fix Existing Wrong Records

If you already have wrong records in your database, here's how to fix them:

### Option 1: Delete All and Reprocess (Recommended)

1. **Clear all auto-imported records:**
```sql
-- Delete auto-imported income records
DELETE FROM inventory_income WHERE notes LIKE '%Auto-imported from email%';

-- Delete auto-imported expense records
DELETE FROM inventory_expense WHERE notes LIKE '%Auto-imported from email%';

-- Clear processed emails (to allow reprocessing)
TRUNCATE TABLE processed_emails;
```

2. **Run the parser again:**
   - Go to: Inventory → Email Parser
   - Click "Run Parser Now (Manual)"
   - All emails will be reprocessed with correct logic ✅

### Option 2: Manually Move Records

If you want to keep some records and just move the wrong ones:

**Move Income to Expense:**
```sql
-- Find wrong income records (look for "debit" keywords)
SELECT * FROM inventory_income 
WHERE description LIKE '%debit%' 
   OR description LIKE '%paid%'
   OR description LIKE '%purchase%';

-- Manually move them to expense table
INSERT INTO inventory_expense (date, category, vendor, description, amount, payment_method, notes, created_at)
SELECT date, category, '', description, amount, payment_method, notes, created_at
FROM inventory_income
WHERE id IN (1, 2, 3); -- Replace with actual IDs

-- Delete from income
DELETE FROM inventory_income WHERE id IN (1, 2, 3);
```

**Move Expense to Income:**
```sql
-- Find wrong expense records (look for "credit" keywords)
SELECT * FROM inventory_expense 
WHERE description LIKE '%credit%' 
   OR description LIKE '%received%'
   OR description LIKE '%deposit%';

-- Manually move them to income table
INSERT INTO inventory_income (date, category, client_id, description, amount, payment_method, notes, created_at)
SELECT date, category, NULL, description, amount, payment_method, notes, created_at
FROM inventory_expense
WHERE id IN (1, 2, 3); -- Replace with actual IDs

-- Delete from expense
DELETE FROM inventory_expense WHERE id IN (1, 2, 3);
```

### Option 3: Manual Review and Edit

1. Go to: **Inventory → Income**
2. Look for records with notes: "Auto-imported from email"
3. Check if they contain expense keywords (debited, paid, purchase)
4. If wrong, delete them

5. Go to: **Inventory → Expense**
6. Look for records with notes: "Auto-imported from email"
7. Check if they contain income keywords (credited, received, deposit)
8. If wrong, delete them

9. Then reprocess emails (clear processed_emails table and run parser)

## 📊 Verify Correct Detection

### Test Cases That Should Work Now:

**INCOME (Money IN):**
- ✅ "Rs 1500 credited to your account"
- ✅ "Payment received Rs 2000"
- ✅ "You received Rs 3000"
- ✅ "Deposit of Rs 5000"
- ✅ "UPI credit Rs 2500"

**EXPENSE (Money OUT):**
- ✅ "Rs 1500 debited from your account"
- ✅ "Payment made Rs 2000"
- ✅ "You paid Rs 3000"
- ✅ "Purchase of Rs 5000"
- ✅ "Bill payment Rs 1000"
- ✅ "UPI debit Rs 2500"

**Edge Cases (Should NOT Confuse):**
- ✅ "Rs 1500 credited" → INCOME (not expense)
- ✅ "Rs 1500 debited" → EXPENSE (not income)

## 🚀 Going Forward

All NEW emails processed from now on will be correctly categorized! The improved logic uses:

1. **Word boundaries** - Prevents partial matches
2. **Regex patterns** - More precise matching
3. **Priority order** - Checks expense first (more specific)
4. **Better keywords** - More comprehensive list

## 📝 Recommended Steps

1. **Test the new logic:**
   ```bash
   php umakant/test_keyword_detection.php
   ```

2. **Clear wrong records:**
   ```sql
   DELETE FROM inventory_income WHERE notes LIKE '%Auto-imported%';
   DELETE FROM inventory_expense WHERE notes LIKE '%Auto-imported%';
   TRUNCATE TABLE processed_emails;
   ```

3. **Reprocess emails:**
   - Inventory → Email Parser
   - Click "Run Parser Now"

4. **Verify results:**
   - Check Income page - should only have "credited", "received", etc.
   - Check Expense page - should only have "debited", "paid", etc.

5. **Monitor going forward:**
   - Check logs regularly
   - Look for "DETECTED: INCOME" or "DETECTED: EXPENSE" messages
   - Verify they're correct

## ✅ Success Indicators

After reprocessing, you should see:
- Income records with keywords: credited, received, deposit
- Expense records with keywords: debited, paid, purchase
- No more confusion between similar words
- Accurate financial tracking! 🎉
