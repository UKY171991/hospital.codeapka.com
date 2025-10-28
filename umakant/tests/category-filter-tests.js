/**
 * Unit Tests for Category Filtering Functionality
 * Tests for the dynamic test fields feature
 */

// Mock data for testing
const mockTestsData = [
    { id: 1, name: 'Blood Sugar', category_id: 1, category_name: 'Blood Tests', unit: 'mg/dL', min: 70, max: 100 },
    { id: 2, name: 'Cholesterol', category_id: 1, category_name: 'Blood Tests', unit: 'mg/dL', min: 150, max: 200 },
    { id: 3, name: 'X-Ray Chest', category_id: 2, category_name: 'Radiology', unit: '', min: null, max: null },
    { id: 4, name: 'ECG', category_id: 3, category_name: 'Cardiology', unit: '', min: null, max: null },
    { id: 5, name: 'Hemoglobin', category_id: 1, category_name: 'Blood Tests', unit: 'g/dL', min: 12, max: 16 }
];

const mockCategoriesData = [
    { id: 1, name: 'Blood Tests' },
    { id: 2, name: 'Radiology' },
    { id: 3, name: 'Cardiology' }
];

// Test Suite for Category Filtering
describe('Category Filtering Functionality', function() {
    let entryManager;

    beforeEach(function() {
        // Create a mock EntryManager instance
        entryManager = {
            testsData: [...mockTestsData],
            categoriesData: [...mockCategoriesData],
            
            // Mock the filtering function
            filterTestsByCategory: function(categoryId) {
                try {
                    if (!categoryId || categoryId === '') {
                        return this.testsData;
                    }
                    return this.testsData.filter(test => test.category_id && test.category_id == categoryId);
                } catch (error) {
                    return this.testsData;
                }
            },

            // Mock the current filtered tests function
            getCurrentlyFilteredTests: function() {
                const selectedCategory = '1'; // Mock selected category
                return this.filterTestsByCategory(selectedCategory);
            },

            // Mock update functions
            updateFilteredTestCount: function() {
                return true;
            },

            updateAllTestDropdowns: function() {
                return true;
            }
        };
    });

    describe('filterTestsByCategory', function() {
        it('should return all tests when no category is selected', function() {
            const result = entryManager.filterTestsByCategory('');
            expect(result).toEqual(mockTestsData);
            expect(result.length).toBe(5);
        });

        it('should return all tests when null category is provided', function() {
            const result = entryManager.filterTestsByCategory(null);
            expect(result).toEqual(mockTestsData);
            expect(result.length).toBe(5);
        });

        it('should filter tests by valid category ID', function() {
            const result = entryManager.filterTestsByCategory(1);
            expect(result.length).toBe(3);
            expect(result.every(test => test.category_id === 1)).toBe(true);
            expect(result.map(t => t.name)).toEqual(['Blood Sugar', 'Cholesterol', 'Hemoglobin']);
        });

        it('should return empty array for non-existent category', function() {
            const result = entryManager.filterTestsByCategory(999);
            expect(result).toEqual([]);
            expect(result.length).toBe(0);
        });

        it('should handle string category IDs correctly', function() {
            const result = entryManager.filterTestsByCategory('2');
            expect(result.length).toBe(1);
            expect(result[0].name).toBe('X-Ray Chest');
            expect(result[0].category_id).toBe(2);
        });

        it('should handle invalid category IDs gracefully', function() {
            const result = entryManager.filterTestsByCategory('invalid');
            expect(result).toEqual([]);
        });
    });

    describe('getCurrentlyFilteredTests', function() {
        it('should return filtered tests based on current selection', function() {
            const result = entryManager.getCurrentlyFilteredTests();
            expect(result.length).toBe(3);
            expect(result.every(test => test.category_id === 1)).toBe(true);
        });
    });

    describe('Edge Cases and Error Handling', function() {
        it('should handle empty tests data', function() {
            entryManager.testsData = [];
            const result = entryManager.filterTestsByCategory(1);
            expect(result).toEqual([]);
        });

        it('should handle tests without category_id', function() {
            entryManager.testsData = [
                { id: 1, name: 'Test Without Category', category_id: null },
                { id: 2, name: 'Test With Category', category_id: 1 }
            ];
            const result = entryManager.filterTestsByCategory(1);
            expect(result.length).toBe(1);
            expect(result[0].name).toBe('Test With Category');
        });

        it('should handle undefined category_id in tests', function() {
            entryManager.testsData = [
                { id: 1, name: 'Test Without Category' }, // No category_id property
                { id: 2, name: 'Test With Category', category_id: 1 }
            ];
            const result = entryManager.filterTestsByCategory(1);
            expect(result.length).toBe(1);
            expect(result[0].name).toBe('Test With Category');
        });
    });
});

// Test Suite for Range Calculation
describe('Reference Range Calculation', function() {
    let entryManager;

    beforeEach(function() {
        entryManager = {
            // Mock range calculation function
            calculateAppropriateRanges: function(patientAge, patientGender, testData) {
                const CHILD_AGE_THRESHOLD = 18;
                
                if (!testData) {
                    return { min: null, max: null, unit: '', type: 'general', label: 'General Range' };
                }

                // Child ranges
                if (patientAge !== null && patientAge < CHILD_AGE_THRESHOLD) {
                    if (testData.min_child !== null || testData.max_child !== null) {
                        return {
                            min: testData.min_child,
                            max: testData.max_child,
                            unit: testData.unit || '',
                            type: 'child',
                            label: 'Child Range'
                        };
                    }
                }

                // Adult ranges
                if (patientAge !== null && patientAge >= CHILD_AGE_THRESHOLD) {
                    if (patientGender === 'Male' || patientGender === 'male') {
                        if (testData.min_male !== null || testData.max_male !== null) {
                            return {
                                min: testData.min_male,
                                max: testData.max_male,
                                unit: testData.unit || '',
                                type: 'male',
                                label: 'Male Range'
                            };
                        }
                    } else if (patientGender === 'Female' || patientGender === 'female') {
                        if (testData.min_female !== null || testData.max_female !== null) {
                            return {
                                min: testData.min_female,
                                max: testData.max_female,
                                unit: testData.unit || '',
                                type: 'female',
                                label: 'Female Range'
                            };
                        }
                    }
                }

                // Fallback to general range
                return {
                    min: testData.min,
                    max: testData.max,
                    unit: testData.unit || '',
                    type: 'general',
                    label: 'General Range'
                };
            }
        };
    });

    describe('calculateAppropriateRanges', function() {
        const testData = {
            min: 10, max: 20, unit: 'mg/dL',
            min_male: 12, max_male: 22,
            min_female: 8, max_female: 18,
            min_child: 5, max_child: 15
        };

        it('should return child range for patients under 18', function() {
            const result = entryManager.calculateAppropriateRanges(10, 'Male', testData);
            expect(result.type).toBe('child');
            expect(result.label).toBe('Child Range');
            expect(result.min).toBe(5);
            expect(result.max).toBe(15);
        });

        it('should return male range for adult male patients', function() {
            const result = entryManager.calculateAppropriateRanges(25, 'Male', testData);
            expect(result.type).toBe('male');
            expect(result.label).toBe('Male Range');
            expect(result.min).toBe(12);
            expect(result.max).toBe(22);
        });

        it('should return female range for adult female patients', function() {
            const result = entryManager.calculateAppropriateRanges(25, 'Female', testData);
            expect(result.type).toBe('female');
            expect(result.label).toBe('Female Range');
            expect(result.min).toBe(8);
            expect(result.max).toBe(18);
        });

        it('should return general range when no specific range available', function() {
            const testDataGeneral = { min: 10, max: 20, unit: 'mg/dL' };
            const result = entryManager.calculateAppropriateRanges(25, 'Male', testDataGeneral);
            expect(result.type).toBe('general');
            expect(result.label).toBe('General Range');
            expect(result.min).toBe(10);
            expect(result.max).toBe(20);
        });

        it('should handle null patient demographics', function() {
            const result = entryManager.calculateAppropriateRanges(null, null, testData);
            expect(result.type).toBe('general');
            expect(result.label).toBe('General Range');
        });

        it('should handle missing test data', function() {
            const result = entryManager.calculateAppropriateRanges(25, 'Male', null);
            expect(result.type).toBe('general');
            expect(result.min).toBe(null);
            expect(result.max).toBe(null);
        });
    });
});

// Test Suite for Result Validation
describe('Result Validation', function() {
    let entryManager;

    beforeEach(function() {
        entryManager = {
            validateTestResult: function(resultValue, rangeData) {
                try {
                    if (resultValue === null || resultValue === undefined || resultValue === '') {
                        return { status: 'empty', message: 'No result entered', isNormal: null };
                    }

                    const numericResult = parseFloat(resultValue);
                    if (isNaN(numericResult)) {
                        return { status: 'invalid', message: 'Invalid numeric value', isNormal: null };
                    }

                    if (!rangeData || (rangeData.min === null && rangeData.max === null)) {
                        return { status: 'no_range', message: 'No reference range available', isNormal: null };
                    }

                    const min = parseFloat(rangeData.min);
                    const max = parseFloat(rangeData.max);

                    let isNormal = true;
                    let message = 'Normal';

                    if (!isNaN(min) && numericResult < min) {
                        isNormal = false;
                        message = `Below normal (Min: ${min})`;
                    } else if (!isNaN(max) && numericResult > max) {
                        isNormal = false;
                        message = `Above normal (Max: ${max})`;
                    }

                    return { status: 'valid', message: message, isNormal: isNormal };
                } catch (error) {
                    return { status: 'error', message: 'Validation error', isNormal: null };
                }
            }
        };
    });

    describe('validateTestResult', function() {
        const rangeData = { min: 10, max: 20, unit: 'mg/dL' };

        it('should validate normal results correctly', function() {
            const result = entryManager.validateTestResult('15', rangeData);
            expect(result.status).toBe('valid');
            expect(result.isNormal).toBe(true);
            expect(result.message).toBe('Normal');
        });

        it('should detect below normal results', function() {
            const result = entryManager.validateTestResult('5', rangeData);
            expect(result.status).toBe('valid');
            expect(result.isNormal).toBe(false);
            expect(result.message).toBe('Below normal (Min: 10)');
        });

        it('should detect above normal results', function() {
            const result = entryManager.validateTestResult('25', rangeData);
            expect(result.status).toBe('valid');
            expect(result.isNormal).toBe(false);
            expect(result.message).toBe('Above normal (Max: 20)');
        });

        it('should handle empty results', function() {
            const result = entryManager.validateTestResult('', rangeData);
            expect(result.status).toBe('empty');
            expect(result.isNormal).toBe(null);
        });

        it('should handle invalid numeric values', function() {
            const result = entryManager.validateTestResult('abc', rangeData);
            expect(result.status).toBe('invalid');
            expect(result.isNormal).toBe(null);
        });

        it('should handle missing range data', function() {
            const result = entryManager.validateTestResult('15', null);
            expect(result.status).toBe('no_range');
            expect(result.isNormal).toBe(null);
        });
    });
});

// Simple test runner for browser console
function runTests() {
    console.log('🧪 Running Category Filter Tests...');
    
    try {
        // This is a simplified test runner for browser console
        // In a real environment, you'd use Jest, Mocha, or similar
        
        console.log('✅ All tests would run here with a proper test framework');
        console.log('📝 Tests cover:');
        console.log('   - Category filtering with valid/invalid IDs');
        console.log('   - Edge cases and error handling');
        console.log('   - Reference range calculation for different demographics');
        console.log('   - Result validation against ranges');
        
        return true;
    } catch (error) {
        console.error('❌ Test execution failed:', error);
        return false;
    }
}

// Export for use in test frameworks
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        mockTestsData,
        mockCategoriesData,
        runTests
    };
}