# Categories Table Migration

This directory contains the database migration files for the test categories feature.

## Files

- `create_categories_table.sql` - Main migration script to create categories table
- `rollback_categories_table.sql` - Rollback script to undo the migration
- `README_categories_migration.md` - This documentation file

## Migration Process

### Step 1: Run the Migration

Navigate to your web browser and visit:
```
http://your-domain/umakant/migrate_categories.php
```

Or run via command line:
```bash
php migrate_categories.php
```

### Step 2: Verify the Migration

Visit the verification script:
```
http://your-domain/umakant/verify_categories.php
```

This will return a JSON response showing the status of all migration checks.

### Step 3: Check the Results

The migration will create:

1. **categories table** with the following structure:
   - `id` (Primary Key, Auto Increment)
   - `name` (VARCHAR(255), Unique, Not Null)
   - `description` (TEXT)
   - `is_active` (BOOLEAN, Default TRUE)
   - `created_at` (TIMESTAMP)
   - `updated_at` (TIMESTAMP)
   - `created_by` (INT, Foreign Key to users.id)

2. **Initial category data** including:
   - Hematology
   - Biochemistry
   - Microbiology
   - Immunology
   - Endocrinology
   - Cardiology
   - Urology
   - Radiology
   - Pathology
   - General

3. **Database indexes** for optimal performance:
   - Index on `name` column
   - Index on `is_active` column
   - Index on `created_by` column

4. **category_summary view** for easy management with test counts

## Rollback Process

If you need to undo the migration:

1. **Manual rollback** - Execute the rollback SQL:
   ```sql
   source sql/rollback_categories_table.sql
   ```

2. **Important**: The rollback will delete all category data and relationships

## Next Steps

After successful migration:

1. Update the tests table to add `category_id` column (Task 2)
2. Create category management API (Task 3)
3. Create category management interface (Task 5)

## Troubleshooting

### Common Issues

1. **Foreign Key Error**: Make sure the `users` table exists and has an `id` column
2. **Permission Error**: Ensure the database user has CREATE, INSERT, and INDEX privileges
3. **Duplicate Entry**: If running migration multiple times, existing data will be skipped

### Manual Fixes

If the migration fails partially, you can run individual SQL statements from the migration file manually.

## Database Schema

```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```