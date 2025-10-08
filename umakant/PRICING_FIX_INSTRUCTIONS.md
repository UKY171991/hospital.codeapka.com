# Pricing Fields Fix - Test Instructions

## ✅ What Was Fixed

The pricing data (Subtotal, Discount Amount, Total Amount) was not being saved to the database because the save function wasn't properly handling the form input values.

### Changes Made:

1. **`umakant/ajax/entry_api_fixed.php`**:
   - Fixed the save function to properly read pricing fields from form data
   - Added priority: Form values > Calculated values from tests
   - Added extensive debug logging to track data flow
   - Fixed individual test price storage in `entry_tests` table

2. **`umakant/assets/js/entry-list.new.js`**:
   - Enhanced pricing field population logic
   - Added comprehensive field name checks (handles all API response variations)
   - Fixed null/undefined vs 0 value detection
   - Added debug console logging

---

## 🧪 How to Test

### Test 1: Create New Entry

1. **Clear browser cache** (Ctrl+Shift+Delete)
2. **Hard reload** page (Ctrl+F5 or Cmd+Shift+R)
3. Go to: https://hospital.codeapka.com/umakant/entry-list.php
4. Click **"Add Entry"** button
5. Fill in the form:
   - Select Owner/User
   - Select Patient
   - Select Doctor (optional)
   - **Select at least one Test** (this will auto-calculate subtotal)
   - **Enter Discount Amount** if needed (e.g., 50.00)
   - Verify **Total Amount** calculates correctly
6. Click **"Save Entry"**
7. Click **"Edit"** on the entry you just created
8. **Check**: All pricing fields should show the values you entered ✅

### Test 2: Edit Existing Entry

1. Click **"Edit"** on any existing entry
2. **Check browser console** (F12 → Console tab) for debug logs:
   ```
   === EDIT ENTRY DEBUG ===
   Entry pricing data: {...}
   Calculated pricing values: {...}
   ```
3. The pricing fields should now display:
   - **Subtotal**: Sum of test prices
   - **Discount Amount**: Any discount entered
   - **Total Amount**: Subtotal - Discount
4. Change the discount amount
5. Click **"Save Entry"**
6. Re-open the entry - new values should be saved ✅

### Test 3: Verify Database

Run this SQL query to check if data is being saved:

```sql
-- Check latest 5 entries with pricing
SELECT 
    id, 
    patient_id,
    subtotal, 
    discount_amount, 
    total_price,
    entry_date,
    status
FROM entries 
ORDER BY id DESC 
LIMIT 5;
```

**Expected Result**: New entries should have non-zero values in pricing columns.

---

## 🐛 Debugging

### If pricing still shows 0.00:

#### 1. Check Browser Console Logs
Press F12 → Console tab, look for:
```
Entry API pricing data received: subtotal=XXX, discount=XXX, total=XXX
```

#### 2. Check Server Logs
Location: `umakant/tmp/entry_api_debug.log`

Look for lines like:
```
[2025-10-08 12:00:00] SAVE_RECEIVED subtotal=100.00 discount=10.00 total=90.00
[2025-10-08 12:00:00] Entry save pricing: subtotal=100.00, discount=10.00, total=90.00
```

#### 3. Check PHP Error Logs
Look for any PHP errors that might prevent data from saving.

#### 4. Check Database Columns
Run:
```sql
DESCRIBE entries;
```

Verify these columns exist:
- `subtotal` DECIMAL(10,2)
- `discount_amount` DECIMAL(10,2)
- `total_price` DECIMAL(10,2)

---

## 📊 Debug Output Examples

### Successful Save
**Browser Console:**
```
Complete entry object: {
  "id": 10,
  "subtotal": "150.00",
  "discount_amount": "15.00",
  "total_price": "135.00",
  ...
}
Calculated pricing values: {
  subtotalValue: 150,
  discountValue: 15,
  totalValue: 135
}
```

**Server Log (`umakant/tmp/entry_api_debug.log`):**
```
[2025-10-08 12:00:00] SAVE_RECEIVED tests=2 subtotal=150.00 discount=15.00 total=135.00
[2025-10-08 12:00:00] CREATED_ENTRY id=10
[2025-10-08 12:00:00] INSERT_TEST entry=10 test_id=5 price=100.00
[2025-10-08 12:00:00] INSERT_TEST entry=10 test_id=8 price=50.00
[2025-10-08 12:00:00] COMMIT entry=10
```

---

## 🔄 Data Flow

### When Saving:
1. Form submits with pricing fields: `subtotal`, `discount_amount`, `total_price`
2. JavaScript includes these in FormData
3. PHP API receives and validates values
4. Values stored in `entries` table
5. Individual test prices stored in `entry_tests` table

### When Editing:
1. API fetches entry with all fields
2. JavaScript checks multiple field name variations
3. Values populated in form fields
4. User can modify and save
5. Process repeats

---

## ✅ Success Criteria

- ✅ New entries save with correct pricing
- ✅ Edit modal shows saved pricing values
- ✅ Modified pricing is saved correctly
- ✅ Database contains actual values (not 0.00)
- ✅ Calculations are correct (Total = Subtotal - Discount)

---

## 📞 Need Help?

If you still have issues:
1. Copy the browser console output
2. Copy the server log from `umakant/tmp/entry_api_debug.log`
3. Show me the database query results
4. I'll help debug further!

