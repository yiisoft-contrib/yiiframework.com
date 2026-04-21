#!/bin/bash

# Test script for update-versions.php
# This script tests the version update functionality

set -e

echo "=== Testing update-versions.php script ==="

# Create a backup of the original versions file
cp config/versions.php config/versions.php.test-backup

echo "✓ Created backup of versions.php"

# Test 1: Add a new version to Yii 2.0
echo "Test 1: Adding version 2.0.99 to Yii 2.0..."
php scripts/update-versions.php "2.0" "2.0.99" "Test Date 2025"

# Verify the version was added
if grep -q "'2.0.99' => 'Test Date 2025'" config/versions.php; then
    echo "✓ Test 1 passed: Version 2.0.99 added successfully"
else
    echo "✗ Test 1 failed: Version 2.0.99 not found"
    exit 1
fi

# Test 2: Add a new version to Yii 1.1
echo "Test 2: Adding version 1.1.99 to Yii 1.1..."
php scripts/update-versions.php "1.1" "1.1.99" "Test Date 2025"

# Verify the version was added
if grep -q "'1.1.99' => 'Test Date 2025'" config/versions.php; then
    echo "✓ Test 2 passed: Version 1.1.99 added successfully"
else
    echo "✗ Test 2 failed: Version 1.1.99 not found"
    exit 1
fi

# Test 3: Try to add the same version again (should not duplicate)
echo "Test 3: Attempting to add duplicate version 2.0.99..."
output=$(php scripts/update-versions.php "2.0" "2.0.99" "Test Date 2025" 2>&1)

if [[ "$output" == *"already exists"* ]]; then
    echo "✓ Test 3 passed: Duplicate version handling works correctly"
else
    echo "✗ Test 3 failed: Duplicate version not detected"
    exit 1
fi

# Test 4: Verify versions are added at the beginning (most recent first)
echo "Test 4: Verifying version order..."
line_2099=$(grep -n "'2.0.99'" config/versions.php | head -1 | cut -d: -f1)
line_2053=$(grep -n "'2.0.53'" config/versions.php | head -1 | cut -d: -f1)

if [[ $line_2099 -lt $line_2053 ]]; then
    echo "✓ Test 4 passed: New versions are added at the beginning"
else
    echo "✗ Test 4 failed: Version order is incorrect"
    exit 1
fi

# Show the diff to see what changed
echo "=== Changes made to versions.php ==="
diff config/versions.php.test-backup config/versions.php || true

# Restore the original file
cp config/versions.php.test-backup config/versions.php
rm config/versions.php.test-backup

echo "✓ Restored original versions.php"
echo "=== All tests passed! ==="