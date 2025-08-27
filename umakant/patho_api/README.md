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
