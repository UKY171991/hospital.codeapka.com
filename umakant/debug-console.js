/**
 * Debug Console Script - Run this in browser console to diagnose issues
 */

console.log('=== DIAGNOSTIC SCRIPT START ===');

// Check jQuery
console.log('1. jQuery Check:');
console.log('jQuery loaded:', typeof $ !== 'undefined');
console.log('jQuery version:', typeof $ !== 'undefined' ? $.fn.jquery : 'N/A');

// Check DataTables
console.log('2. DataTables Check:');
console.log('DataTables loaded:', typeof $.fn.DataTable !== 'undefined');

// Check Bootstrap
console.log('3. Bootstrap Check:');
console.log('Bootstrap loaded:', typeof $.fn.modal !== 'undefined');

// Check table element
console.log('4. Table Element Check:');
console.log('Table exists:', $('#entriesTable').length > 0);
console.log('Table HTML:', $('#entriesTable')[0]);

// Check API endpoint
console.log('5. API Endpoint Check:');
fetch('patho_api/entry.php?action=list')
    .then(response => {
        console.log('API Response Status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('API Response Text:', text.substring(0, 200) + '...');
        try {
            const json = JSON.parse(text);
            console.log('API Response JSON:', json);
        } catch (e) {
            console.log('API Response is not valid JSON');
        }
    })
    .catch(error => {
        console.error('API Request Failed:', error);
    });

// Check global variables
console.log('6. Global Variables Check:');
console.log('entriesTable:', typeof entriesTable);
console.log('testsData:', typeof testsData, testsData ? testsData.length : 'N/A');
console.log('categoriesData:', typeof categoriesData, categoriesData ? categoriesData.length : 'N/A');
console.log('patientsData:', typeof patientsData, patientsData ? patientsData.length : 'N/A');
console.log('doctorsData:', typeof doctorsData, doctorsData ? doctorsData.length : 'N/A');

// Check functions
console.log('7. Function Check:');
const functions = [
    'initializeDataTable',
    'loadInitialData', 
    'bindEvents',
    'openAddModal',
    'refreshTable',
    'exportEntries',
    'validateForm',
    'saveEntry'
];

functions.forEach(funcName => {
    console.log(`${funcName}:`, typeof window[funcName]);
});

// Check for errors in console
console.log('8. Console Errors:');
console.log('Check the console above for any red error messages');

console.log('=== DIAGNOSTIC SCRIPT END ===');

// Try to initialize DataTable manually
console.log('9. Manual DataTable Test:');
try {
    if ($('#entriesTable').length > 0) {
        const testTable = $('#entriesTable').DataTable({
            data: [
                {
                    id: 1,
                    patient_name: 'Test Patient',
                    doctor_name: 'Test Doctor',
                    test_names: 'Test Name',
                    status: 'pending',
                    priority: 'normal',
                    total_price: '100.00',
                    entry_date: '2024-01-01'
                }
            ],
            columns: [
                { data: 'id' },
                { data: 'patient_name' },
                { data: 'doctor_name' },
                { data: 'test_names' },
                { data: 'status' },
                { data: 'priority' },
                { data: 'total_price' },
                { data: 'entry_date' },
                { data: null, render: () => 'Actions' }
            ]
        });
        console.log('Manual DataTable test: SUCCESS');
        testTable.destroy();
    }
} catch (error) {
    console.error('Manual DataTable test: FAILED', error);
}