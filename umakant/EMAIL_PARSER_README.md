# Email Parser for Inventory Management

## Overview
Automatic email parsing system that reads payment/transaction emails from Gmail and automatically creates Income and Expense records in the Inventory module.

## Features
- **Automatic Email Processing**: Reads Gmail inbox for payment notifications
- **Smart Transaction Detection**: Identifies income vs expense transactions
- **Amount Extraction**: Automatically extracts amounts from emails (supports ₹, Rs, INR formats)
- **Payment Method Detection**: Identifies UPI, Card, Bank Transfer, etc.
- **Category Assignment**: Auto-categorizes transactions based on keywords
- **Duplicate Prevention**: Tracks processed emails to avoid duplicates
- **Cron Job Support**: Can run automatically via cron or manually via web interface
- **Detailed Logging**: Comprehensive logs for debugging and monitoring

## Files Created

### 1. Core Files
- **umakant/cron_email_parser.php** - Main cron job script
- **umakant/email_parser_settings.php** - Web interface for management
- **umakant/ajax/email_parser_api.php** - API for settings and stats

### 2. Database Tables (in inventory_tables.sql)
- **processed_emails** - Tracks processed emails to prevent duplicates
- **system_config** - Stores Gmail password securely

## Installation Steps

### Step 1: Import Database Tables
```sql
-- Already included in inventory_tables.sql
-- Just import that file if you haven't already
```

### Step 2: Configure Gmail App Password
1. Go to: https://myaccount.google.com/apppasswords
2. Create a new App Password for "Mail"
3. Copy the 16-character password
4. Go to: Inventory → Email Parser in the admin panel
5. Paste the App Password and click "Save Password"

### Step 3: Setup Cron Job

#### Option A: Linux/Unix Cron
Add this line to your crontab (runs every 5 minutes):
```bash
*/5 * * * * php /path/to/umakant/cron_email_parser.php
```

To edit crontab:
```bash
crontab -e
```

#### Option B: Web-based Cron (with secret key)
```bash
*/5 * * * * curl "https://yourdomain.com/umakant/cron_email_parser.php?cron_key=your_secret_cron_key_12345"
```

**Important:** Change the secret key in `cron_email_parser.php` line 8!

#### Option C: Windows Task Scheduler
1. Open Task Scheduler
2. Create Basic Task
3. Trigger: Daily, repeat every 5 minutes
4. Action: Start a program
5. Program: `php.exe`
6. Arguments: `C:\path\to\umakant\cron_email_parser.php`

### Step 4: Test the Parser
1. Go to: Inventory → Email Parser
2. Click "Run Parser Now (Manual)"
3. Check the logs for results

## How It Works

### Email Detection
The parser looks for these keywords:

**Income Keywords:**
- payment received
- payment credited
- money received
- credited to
- payment successful
- transaction successful
- amount credited
- upi credit, imps credit, neft credit, rtgs credit

**Expense Keywords:**
- payment debited
- amount debited
- payment made
- transaction debited
- purchase
- bill payment
- recharge
- subscription
- upi debit, imps debit, neft debit, rtgs debit

### Amount Extraction
Supports multiple formats:
- Rs. 1500.00
- ₹1,500.00
- INR 1500
- Amount: Rs. 1500

### Payment Method Detection
- UPI → if email contains "upi"
- Card → if email contains "card", "debit", "credit"
- Bank Transfer → if email contains "neft", "rtgs", "imps"
- Cheque → if email contains "cheque", "check"
- Cash → if email contains "cash"

### Category Assignment

**Income Categories:**
- Consultation
- Lab Tests
- Pharmacy
- Surgery
- Room Charges
- Other Services

**Expense Categories:**
- Medical Supplies
- Equipment
- Utilities
- Salaries
- Rent
- Maintenance
- Marketing
- Transportation
- Other

## Web Interface Features

### Dashboard Stats
- Total Processed Emails
- Income Records Created
- Expense Records Created
- Last Run Time

### Manual Controls
- Run Parser Now (manual execution)
- Test Email Parsing (test with sample data)
- View Processing Logs
- View Recently Processed Emails

### Configuration
- Gmail Password Management
- Password Status Check
- Cron Job Setup Instructions

## Logs

Logs are stored in: `umakant/logs/email_parser.log`

Log format:
```
[2024-01-15 10:30:00] === Email Parser Cron Job Started ===
[2024-01-15 10:30:01] Connecting to Gmail IMAP...
[2024-01-15 10:30:02] Successfully connected to Gmail
[2024-01-15 10:30:03] Found 5 emails to process
[2024-01-15 10:30:04] Created INCOME record: Payment Received - ₹1500.00
[2024-01-15 10:30:05] === Processing Complete ===
```

## Security Considerations

1. **Gmail App Password**: Never use your actual Gmail password. Always use App Passwords.
2. **Secret Key**: Change the default secret key in `cron_email_parser.php`
3. **Database**: Gmail password is stored in the database (consider encryption for production)
4. **Access Control**: Only authenticated users can access the Email Parser settings

## Troubleshooting

### Parser Not Running
- Check if Gmail password is configured
- Verify cron job is set up correctly
- Check logs for error messages
- Ensure PHP IMAP extension is enabled

### No Emails Being Processed
- Verify Gmail IMAP is enabled in Gmail settings
- Check if emails contain recognized keywords
- Test with sample emails using "Test Email Parsing"
- Review logs for parsing errors

### Duplicate Records
- The system tracks processed emails by message_id
- If duplicates occur, check the processed_emails table
- Clear old entries if needed

### IMAP Connection Errors
- Verify Gmail App Password is correct
- Check if IMAP is enabled in Gmail
- Ensure firewall allows IMAP connections (port 993)

## Customization

### Adding New Keywords
Edit `cron_email_parser.php` around line 150:
```php
$income_keywords = [
    'payment received',
    'your_custom_keyword',
    // Add more...
];
```

### Changing Categories
Edit the `determineCategory()` function in `cron_email_parser.php`

### Adjusting Time Range
Default: Last 24 hours
Change in `cron_email_parser.php` line 75:
```php
$since_date = date('d-M-Y', strtotime('-24 hours')); // Change to -48 hours, -7 days, etc.
```

## API Endpoints

### Get Stats
```
GET ajax/email_parser_api.php?action=get_stats
```

### Save Password
```
POST ajax/email_parser_api.php
action=save_password&password=YOUR_APP_PASSWORD
```

### Run Parser
```
POST ajax/email_parser_api.php
action=run_parser
```

### Get Logs
```
GET ajax/email_parser_api.php?action=get_logs&lines=50
```

## Cron URL Access

For web-based cron services:
```
https://yourdomain.com/umakant/cron_email_parser.php?cron_key=your_secret_cron_key_12345
```

## Support

For issues or questions:
1. Check the logs first
2. Test with sample emails
3. Verify Gmail configuration
4. Review the troubleshooting section

## Future Enhancements

Potential improvements:
- Email attachment processing
- Multiple email account support
- Advanced filtering rules
- Email templates for auto-responses
- Machine learning for better categorization
- SMS/WhatsApp notifications
- Export processed emails report
