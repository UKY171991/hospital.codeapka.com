Project example helpers

- inc/connection.php: DB connection (update creds if needed)
- inc/seed_admin.php: create default admin/user (admin/admin123, user/user123)
- ajax/doctor_api.php: example AJAX CRUD endpoint for doctors
- doctors_example.php: demo page showing modal CRUD + toastr alerts

Place these files on your PHP host and open `doctors_example.php`. Ensure the DB is reachable and the `users` and `doctors` tables exist.
