# Email Parser Troubleshooting Guide

## Issue: All Emails Skipped (0 Processed)

If you see logs like:
```
[2025-11-16 12:00:17] Total Emails: 18
[2025-11-16 12:00:17] Processed: 0
[2025-11-16 12:00:17] Skipped: 18
```

### Possible Causes & Solutions:

### 1. Emails Don't Contain Transaction Keywords

**Check the logs for:**
```
SKIP: No transaction keywords found in: [subject]
```

**Solution:** The emails might not be payment-related. Check if your emails contain these keywords:

**Income Keywords:**
- payment received, credited, deposit, received
- payment successful, transaction successful
- upi credit, neft credit, imps credit
- account credited, money added
- payment confirmation

**Expense Keywords:**
- payment debited, debited, withdrawn
- purchase, bill payment, paid
- upi debit, neft debit, imps debit
- account debited, money deducted
- order placed

### 2. No Amount Found in Emails

**Check the logs for:**
```
SKIP: No amount found in: [subject]
```

**Solution:** The parser looks for amounts in these formats:
- Rs. 1500
- ₹1,500.00
- INR 1500
- Amount: Rs. 1500
- Credited Rs. 1500

Make sure your emails contain amounts in one of these formats.

### 3. View Detailed Logs

**To see what's happening:**

1. Go to: Inventory → Email Parser
2. Scroll to "Processing Logs" section
3. Look for lines starting with:
   - `Processing email:` - Shows which emails are being checked
   - `SKIP:` - Shows why emails were skipped
   - `DETECTED:` - Shows successfully detected transactions
   - `Created INCOME/EXPENSE record:` - Shows created records

### 4. Test with Sample Email

**Send yourself a test email:**

**Subject:** Payment Received - Rs. 1500
**Body:** 
```
Dear Customer,

Your payment of Rs. 1500.00 has been credited to your account via UPI.

Transaction ID: 123456789
Date: 2025-11-16

Thank you!
```

Then run the parser manually and check if it's detected.

### 5. Check Email Content

**The parser needs BOTH:**
1. ✅ Transaction keyword (credited, debited, payment, etc.)
2. ✅ Amount with currency (Rs., ₹, INR)

**Example of emails that WILL work:**
- "Payment of Rs. 1500 credited to your account"
- "Amount debited: ₹2,500 for electricity bill"
- "UPI payment received INR 3000"

**Example of emails that WON'T work:**
- "Your payment was successful" (no amount)
- "Amount: 1500" (no currency symbol)
- "Meeting reminder for tomorrow" (not a transaction)

## Issue: Table Not Found Error

```
FATAL ERROR: Table 'system_config' doesn't exist
```

**Solution:** This is now fixed! The latest version auto-creates tables. Just:
1. Save your Gmail password again in Email Parser Settings
2. Run the parser manually
3. Tables will be created automatically

## Issue: Gmail Connection Failed

```
ERROR: Failed to connect to Gmail
```

**Solutions:**

1. **Check Gmail App Password:**
   - Go to: https://myaccount.google.com/apppasswords
   - Generate new App Password
   - Save it in Email Parser Settings

2. **Enable IMAP in Gmail:**
   - Gmail Settings → Forwarding and POP/IMAP
   - Enable IMAP
   - Save changes

3. **Check PHP IMAP Extension:**
   ```bash
   php -m | grep imap
   ```
   If not installed:
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php-imap
   sudo service apache2 restart
   
   # CentOS/RHEL
   sudo yum install php-imap
   sudo service httpd restart
   ```

## Debugging Steps

### Step 1: Check Logs
```bash
tail -f /path/to/umakant/logs/email_parser.log
```

### Step 2: Run Test Script
```bash
php /path/to/umakant/test_email_parser.php
```

This tests the parsing logic without connecting to Gmail.

### Step 3: Run Parser Manually
1. Go to: Inventory → Email Parser
2. Click "Run Parser Now (Manual)"
3. Watch the logs in real-time

### Step 4: Test Email Parsing
1. Go to: Inventory → Email Parser
2. Click "Test Email Parsing"
3. See how sample emails are parsed

## Common Email Formats

### Bank Transaction Alerts

**HDFC Bank:**
```
Subject: HDFC Bank: Rs 1500.00 credited to A/c XX1234
Body: Your A/c XX1234 is credited with Rs.1500.00 on 16-11-2025
```
✅ Will be detected as INCOME

**SBI:**
```
Subject: SBI: Debit Alert
Body: Rs 2500.00 debited from A/c XX5678 for bill payment
```
✅ Will be detected as EXPENSE

### UPI Payments

**Google Pay:**
```
Subject: You received Rs 3000 from John
Body: Payment of Rs 3000.00 received via UPI
```
✅ Will be detected as INCOME

**PhonePe:**
```
Subject: Payment Successful
Body: You paid Rs 500 to Electricity Board via PhonePe
```
✅ Will be detected as EXPENSE

### Credit Card

```
Subject: Credit Card Transaction Alert
Body: Rs 5000.00 debited from your card XX1234 for purchase
```
✅ Will be detected as EXPENSE

## Enhanced Logging

The latest version includes detailed logging:

```
[2025-11-16 12:00:08] Processing email: Payment Received (from: bank@example.com)
[2025-11-16 12:00:08] DETECTED: INCOME - Amount: ₹1500 - Keyword: payment received
[2025-11-16 12:00:08] Created INCOME record: Payment Received - ₹1500.00
```

Or if skipped:
```
[2025-11-16 12:00:09] Processing email: Meeting Reminder (from: colleague@example.com)
[2025-11-16 12:00:09] SKIP: No transaction keywords found in: Meeting Reminder
```

## Still Having Issues?

1. **Check the full log file:**
   ```bash
   cat /path/to/umakant/logs/email_parser.log
   ```

2. **Verify database tables exist:**
   ```sql
   SHOW TABLES LIKE 'inventory_%';
   SHOW TABLES LIKE 'system_config';
   SHOW TABLES LIKE 'processed_emails';
   ```

3. **Test with known working email:**
   Forward a bank transaction alert to your Gmail and run the parser

4. **Check PHP version:**
   ```bash
   php -v
   ```
   Requires PHP 7.4 or higher

## Quick Fixes

### Reset Everything
```sql
-- Clear processed emails (to reprocess)
TRUNCATE TABLE processed_emails;

-- Reset Gmail password
DELETE FROM system_config WHERE config_key = 'gmail_password';
```

Then reconfigure and try again.

### Force Reprocess Last 24 Hours
The parser only checks emails from the last 24 hours. To change this, edit `cron_email_parser.php` line ~75:
```php
// Change from 24 hours to 7 days
$since_date = date('d-M-Y', strtotime('-7 days'));
```

## Success Indicators

You'll know it's working when you see:
```
[2025-11-16 12:00:17] === Processing Complete ===
[2025-11-16 12:00:17] Total Emails: 18
[2025-11-16 12:00:17] Processed: 5
[2025-11-16 12:00:17] Income Records: 3
[2025-11-16 12:00:17] Expense Records: 2
[2025-11-16 12:00:17] Skipped: 13
```

And in your Inventory → Income/Expense pages, you'll see the auto-created records with notes: "Auto-imported from email"
