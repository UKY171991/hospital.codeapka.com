# 🧪 Test Pricing Fields - Step by Step

## What to Do Now

### Step 1: Clear Everything
1. **Clear browser cache**: Press `Ctrl+Shift+Delete` → Clear cache
2. **Hard reload page**: Press `Ctrl+F5` (Windows) or `Cmd+Shift+R` (Mac)
3. **Open browser console**: Press `F12` → Go to "Console" tab

### Step 2: Test Adding a New Entry

1. Go to: https://hospital.codeapka.com/umakant/entry-list.php
2. Click **"Add Entry"** button
3. **Watch the console** - you should see:
   ```
   Tests loaded successfully: X tests
   First test sample: {id: 1, name: "...", price: 980, ...}
   ```

4. Fill in the form:
   - Select Owner/User
   - Select Patient
   - Select Doctor (optional)
   - **Select a Test**

5. **When you select a test**, watch console for:
   ```
   Test selection change: {
     testId: "1",
     testName: "Aubrey Reyes g",
     price: 980,  <-- THIS SHOULD SHOW A NUMBER, NOT 0
     ...
   }
   
   Pricing calculated: {
     subtotal: "980.00",  <-- THIS SHOULD MATCH THE TEST PRICE
     discount: "0.00",
     total: "980.00"
   }
   ```

6. **Check the form** - The fields should show:
   - Subtotal (₹): `980.00`
   - Discount Amount (₹): `0.00`
   - Total Amount (₹): `980.00`

7. **If you add a discount** (e.g., 50), you should see:
   ```
   Pricing calculated: {
     subtotal: "980.00",
     discount: "50.00",
     total: "930.00"
   }
   ```

8. Click **"Save Entry"**

9. **Watch console for save debug**:
   ```
   === SAVING ENTRY - PRICING DEBUG ===
   Pricing field values: {
     subtotal: "980.00",  <-- SHOULD NOT BE "0" OR EMPTY
     discount: "50.00",
     total: "930.00"
   }
   FormData contents:
   subtotal: 980.00
   discount_amount: 50.00
   total_price: 930.00
   ```

10. After save, click **"Edit"** on the entry you just created

11. **Check if pricing fields are populated** - they should show the values you entered!

---

## 🔍 What to Check

### ✅ If Everything Works:

**Console logs show:**
- Test price is NOT 0 when selected
- Pricing calculated with real values
- FormData includes non-zero values
- Edit modal shows saved values

**Database shows:**
```sql
SELECT id, subtotal, discount_amount, total_price 
FROM entries 
ORDER BY id DESC LIMIT 1;
```
Should show: `980.00`, `50.00`, `930.00` (or your values)

### ❌ If Price is Still 0:

**Check console logs - find where it breaks:**

#### Problem 1: Test price is 0 when selected
```
Test selection change: {
  price: 0  <-- PROBLEM HERE
}
```
**Cause**: Tests don't have prices in database or not loading correctly
**Fix**: Check if tests have prices: `SELECT id, name, price FROM tests;`

#### Problem 2: Pricing not calculating
```
Test selection change: { price: 980 }  <-- OK
Pricing calculated: {
  subtotal: "0.00"  <-- PROBLEM HERE
}
```
**Cause**: `updatePricingFields()` not finding the test price
**Fix**: Check if `.test-select` elements exist and have data-price attribute

#### Problem 3: FormData has 0 values
```
Pricing calculated: { subtotal: "980.00" }  <-- OK
FormData contents:
subtotal: 0  <-- PROBLEM HERE
```
**Cause**: Form fields not getting updated or being cleared
**Fix**: Check if `$('#subtotal').val()` returns the right value before save

#### Problem 4: Server not saving
```
FormData contents: subtotal: 980.00  <-- OK in browser
```
But database still shows 0.00
**Cause**: PHP not receiving or not saving the data
**Fix**: Check server log: `umakant/tmp/entry_api_debug.log`

---

## 📊 Server Logs

Check: `umakant/tmp/entry_api_debug.log`

You should see:
```
[2025-10-08 12:00:00] SAVE_RECEIVED tests=1 subtotal=980.00 discount=50.00 total=930.00
[2025-10-08 12:00:00] CREATED_ENTRY id=11
[2025-10-08 12:00:00] INSERT_TEST entry=11 test_id=1 price=980.00
[2025-10-08 12:00:00] COMMIT entry=11
```

If you see `subtotal=NONE` or `subtotal=0`, the data is not reaching the server!

---

##  📝 Tell Me What You See

After testing, please tell me:

1. **What does the console show** when you select a test?
   - Is the price showing as a number or 0?
   
2. **What do the pricing fields show** in the form?
   - Are they calculating correctly?
   
3. **What does the save debug show**?
   - Are the FormData values correct?

4. **What does the database show**?
   ```sql
   SELECT id, subtotal, discount_amount, total_price 
   FROM entries 
   ORDER BY id DESC LIMIT 1;
   ```

This will help me identify EXACTLY where the problem is!

