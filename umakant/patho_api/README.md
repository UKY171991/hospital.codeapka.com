Save doctor helper (PowerShell)
================================

What
----
This directory includes `save_doctor.ps1`, a small PowerShell helper that logs in to the patho_api and posts to `doctor.php?action=save` using the same PHP session so the API accepts the request.

Usage
-----
Open PowerShell (Windows) and run:

    cd c:\git\hospital.codeapka.com\umakant\patho_api
    .\save_doctor.ps1 -Username your_user -Name 'Dr Example' -Email 'dr@example.com'

The script will prompt for the password securely, perform login, then perform the save using the session cookie.

Options
-------
-BaseUrl: Base API URL (defaults to https://hospital.codeapka.com/umakant/patho_api)
-Username: login username (required)
-Password: if not provided, you'll be prompted (secure)
-Name, -Qualification, -Specialization, -Hospital, -ContactNo, -Phone, -Email, -Address, -RegistrationNo, -Percent

Troubleshooting
---------------
- If login fails, verify username/password and that the user is active in the `users` table.
- If save returns 401, ensure the login response included a `PHPSESSID` cookie and the script did not modify cookie handling. The script uses a PowerShell WebSession to store cookies.
- For programmatic non-session access, consider adding token-based auth to the API (I can help implement it).

Direct server-to-server insert (secret)
-------------------------------------
If you need the API to accept direct inserts without session/cookie or user credentials, you can use a shared secret.

1) Configure the server environment variables (recommended, do NOT commit secrets):
    - PATHO_API_SECRET=your-long-secret
    - PATHO_API_DEFAULT_USER_ID=1  # user id used as added_by for direct inserts

2) Call the save endpoint with header `X-Api-Key: your-long-secret` or include `secret_key=your-long-secret` in the request body.

Example:
curl -H "X-Api-Key: your-long-secret" -d "name=Dr Direct&percent=1&email=dr@direct.example" "https://hospital.codeapka.com/umakant/patho_api/doctor.php?action=save"

Security: Use HTTPS, rotate the secret periodically, and restrict the default user permissions if possible.

Security
--------
Don't store plaintext passwords in scripts. Use the prompt or a secure vault.
# Patho API - Notices

This folder contains small public API endpoints (PHP) for external clients.

New: `notice.php` - provides list/get/save/delete actions for notices.

Quick test with Postman:
- Import `notice_postman_collection.json` into Postman.
- Set the environment variable `baseUrl` to your local site root (e.g. `http://localhost/umakant`).
- Use the `Create Notice (POST)` request to create notices. Note: write actions require an authenticated session with role `admin` or `master` (browser cookie). If you don't have that, log into the app in a browser first and copy the session cookie to Postman.

Files added:
- `notice.php` - API endpoint
- `notice.txt` - endpoint documentation
- `notice_postman_collection.json` - Postman collection for import
