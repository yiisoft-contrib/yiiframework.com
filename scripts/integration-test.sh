#!/bin/bash

# Integration test for the GitHub Action workflow
# This simulates the workflow steps without actually running the GitHub Action

set -e

echo "=== Integration Test for Version Update Workflow ==="

# Test data
TEST_REPO_NAME="yii2"
TEST_VERSION="2.0.99"
TEST_DATE="$(date +'%b %d, %Y')"
TEST_RELEASE_URL="https://github.com/yiisoft/yii2/releases/tag/$TEST_VERSION"

echo "Testing with:"
echo "  Repository: $TEST_REPO_NAME"
echo "  Version: $TEST_VERSION"
echo "  Date: $TEST_DATE"
echo "  URL: $TEST_RELEASE_URL"

# Step 1: Validate payload (simulate)
echo "Step 1: Validating payload..."
if [[ -z "$TEST_REPO_NAME" || -z "$TEST_VERSION" ]]; then
    echo "✗ Payload validation failed"
    exit 1
fi
echo "✓ Payload validation passed"

# Step 2: Extract release information (simulate)
echo "Step 2: Extracting release information..."
if [[ "$TEST_REPO_NAME" == "yii2" ]]; then
    YII_VERSION="2.0"
elif [[ "$TEST_REPO_NAME" == "yii" ]]; then
    YII_VERSION="1.1"
else
    echo "✗ Unsupported repository: $TEST_REPO_NAME"
    exit 1
fi
echo "✓ Determined Yii version: $YII_VERSION"

# Step 3: Update versions.php
echo "Step 3: Updating versions.php..."
# Create backup
cp config/versions.php config/versions.php.integration-backup

# Run the update script
php scripts/update-versions.php "$YII_VERSION" "$TEST_VERSION" "$TEST_DATE"

# Step 4: Verify changes
echo "Step 4: Verifying changes..."
if grep -q "'$TEST_VERSION' => '$TEST_DATE'" config/versions.php; then
    echo "✓ Version $TEST_VERSION successfully added"
else
    echo "✗ Version $TEST_VERSION not found in file"
    exit 1
fi

# Show the diff
echo "Changes made:"
diff config/versions.php.integration-backup config/versions.php || true

# Step 5: Simulate commit preparation
echo "Step 5: Simulating commit preparation..."
git add config/versions.php

if git diff --staged --quiet; then
    echo "ℹ No staged changes (this shouldn't happen in this test)"
else
    echo "✓ Changes staged for commit"
    
    # Show what would be committed
    echo "Staged changes:"
    git diff --staged --name-only
fi

# Clean up - restore original file and unstage changes
cp config/versions.php.integration-backup config/versions.php
rm config/versions.php.integration-backup
git reset HEAD config/versions.php > /dev/null 2>&1

echo "✓ Integration test completed successfully!"
echo "The workflow would work correctly with these inputs."