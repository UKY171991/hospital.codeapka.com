# Cron Job Setup for Shared Hosting

## Important: exec() Function Disabled

Your shared hosting has the `exec()` function disabled for security. This is normal and we've adapted the system to work without it.

## ✅ Manual Execution (Works Now!)

You can run the parser manually from the web interface:
1. Go to: Inventory → Email Parser
2. Click "Run Parser Now (Manual)"
3. The script will execute directly and show results

## 🔄 Automatic Execution via Cron Job

For automatic execution every 5 minutes, use one of these methods:

### Method 1: Web-Based Cron (Recommended for Shared Hosting)

Most shared hosting providers offer a cron job panel in cPanel or similar.

**Setup Steps:**

1. **Change the Secret Key** (Important for security!)
   - Edit `umakant/cron_email_parser.php`
   - Line 10: Change `'your_secret_cron_key_12345'` to something unique
   - Example: `'my_hospital_secret_key_xyz789'`

2. **Get Your Cron URL:**
   ```
   https://hospital.codeapka.com/umakant/cron_email_parser.php?cron_key=YOUR_SECRET_KEY
   ```
   Replace `YOUR_SECRET_KEY` with the key you set in step 1.

3. **Add Cron Job in cPanel:**
   - Login to cPanel
   - Find "Cron Jobs" section
   - Add new cron job:
     - **Minute:** */5 (every 5 minutes)
     - **Hour:** * (every hour)
     - **Day:** * (every day)
     - **Month:** * (every month)
     - **Weekday:** * (every weekday)
     - **Command:** 
       ```
       curl -s "https://hospital.codeapka.com/umakant/cron_email_parser.php?cron_key=YOUR_SECRET_KEY"
       ```
       OR
       ```
       wget -q -O /dev/null "https://hospital.codeapka.com/umakant/cron_email_parser.php?cron_key=YOUR_SECRET_KEY"
       ```

4. **Save and Test:**
   - Save the cron job
   - Wait 5 minutes
   - Check logs: Inventory → Email Parser → Processing Logs

### Method 2: External Cron Service (Alternative)

If your hosting doesn't support cron jobs, use a free external service:

**Services:**
- https://cron-job.org (Free, reliable)
- https://www.easycron.com (Free tier available)
- https://console.cron-job.org (Free)

**Setup:**
1. Register for free account
2. Add new cron job
3. URL: `https://hospital.codeapka.com/umakant/cron_email_parser.php?cron_key=YOUR_SECRET_KEY`
4. Interval: Every 5 minutes
5. Save and activate

### Method 3: PHP CLI (If Available)

Some shared hosting allows PHP CLI access via SSH:

```bash
*/5 * * * * /usr/bin/php /home/u902379465/domains/codeapka.com/public_html/hospital/umakant/cron_email_parser.php
```

**To check if PHP CLI is available:**
```bash
which php
php -v
```

## 🔒 Security Notes

### Why Use a Secret Key?

The secret key prevents unauthorized people from running your cron job. Without it, anyone could trigger the email parser by visiting the URL.

### Best Practices:

1. **Use a Strong Secret Key:**
   - At least 20 characters
   - Mix of letters, numbers, and symbols
   - Example: `hosp_email_parser_2024_xyz789_secure`

2. **Don't Share the Key:**
   - Keep it private
   - Don't commit it to public repositories
   - Change it if compromised

3. **Monitor Logs:**
   - Check logs regularly for suspicious activity
   - Look for unexpected execution times

## 📊 Verify Cron Job is Working

### Check 1: Logs
```
Inventory → Email Parser → Processing Logs
```
Look for entries every 5 minutes:
```
[2025-11-16 12:00:00] === Email Parser Cron Job Started ===
[2025-11-16 12:05:00] === Email Parser Cron Job Started ===
[2025-11-16 12:10:00] === Email Parser Cron Job Started ===
```

### Check 2: Stats
```
Inventory → Email Parser → Dashboard
```
"Last Run" should update every 5 minutes

### Check 3: Records
```
Inventory → Income or Expense
```
New records should appear automatically when payment emails arrive

## 🐛 Troubleshooting

### Cron Job Not Running

**Check 1: URL is Correct**
- Test the URL in your browser
- You should see log output
- If you see "Access denied", the secret key is wrong

**Check 2: Cron Job is Active**
- In cPanel, check if cron job is enabled
- Look for any error emails from cron

**Check 3: Server Time**
- Cron jobs run based on server time
- Check if server timezone is correct

### Cron Job Running But No Records

**Check 1: Gmail Password**
- Verify Gmail App Password is saved
- Go to: Inventory → Email Parser
- Check "Gmail Password Status"

**Check 2: Email Content**
- Emails must contain transaction keywords
- Emails must have amounts with currency symbols
- See TROUBLESHOOTING.md for details

**Check 3: Logs**
- Check why emails are being skipped
- Look for "SKIP:" messages in logs

## 📝 Example cPanel Cron Job Setup

**Common Path (adjust for your hosting):**
```
/usr/bin/curl -s "https://hospital.codeapka.com/umakant/cron_email_parser.php?cron_key=YOUR_SECRET_KEY"
```

**Timing Options:**

Every 5 minutes:
```
*/5 * * * *
```

Every 10 minutes:
```
*/10 * * * *
```

Every 15 minutes:
```
*/15 * * * *
```

Every hour:
```
0 * * * *
```

Every day at 9 AM:
```
0 9 * * *
```

## 🎯 Recommended Setup

For most users, we recommend:

1. **Frequency:** Every 5 minutes
2. **Method:** Web-based cron with curl
3. **Secret Key:** Strong, unique key
4. **Monitoring:** Check logs daily for first week

**Example Command:**
```bash
*/5 * * * * curl -s "https://hospital.codeapka.com/umakant/cron_email_parser.php?cron_key=hosp_email_parser_2024_xyz789_secure" > /dev/null 2>&1
```

## ✅ Success Checklist

- [ ] Changed secret key in cron_email_parser.php
- [ ] Added cron job in cPanel or external service
- [ ] Tested URL in browser (should show logs)
- [ ] Waited 5 minutes and checked logs
- [ ] Verified "Last Run" updates in Email Parser page
- [ ] Sent test payment email and verified it was processed
- [ ] Checked Income/Expense pages for auto-created records

## 📞 Need Help?

If you're still having issues:

1. Check TROUBLESHOOTING.md
2. Review the logs carefully
3. Test with the manual "Run Parser Now" button first
4. Verify Gmail App Password is correct
5. Send a test payment email to yourself

The system is designed to work on shared hosting without exec() or shell access!
