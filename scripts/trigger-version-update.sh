#!/bin/bash

# Example script to trigger the version update workflow manually
# This demonstrates how to send a repository_dispatch event to test the workflow

# Configuration
REPO_OWNER="yiisoft-contrib"
REPO_NAME="yiiframework.com"
GITHUB_TOKEN="${GITHUB_TOKEN:-your_github_token_here}"

# Default values (can be overridden with command line arguments)
SOURCE_REPO_NAME="${1:-yii2}"
VERSION_TAG="${2:-2.0.54}"
RELEASE_URL="${3:-https://github.com/yiisoft/yii2/releases/tag/2.0.54}"

if [[ "$GITHUB_TOKEN" == "your_github_token_here" ]]; then
    echo "Error: Please set GITHUB_TOKEN environment variable"
    echo "Usage: GITHUB_TOKEN=your_token $0 [repo_name] [version_tag] [release_url]"
    echo "Example: GITHUB_TOKEN=ghp_xxx $0 yii2 2.0.54 https://github.com/yiisoft/yii2/releases/tag/2.0.54"
    exit 1
fi

echo "Triggering version update workflow..."
echo "Repository: $SOURCE_REPO_NAME"
echo "Version: $VERSION_TAG"
echo "Release URL: $RELEASE_URL"

# Prepare the payload
PAYLOAD=$(cat <<EOF
{
  "event_type": "yii-release",
  "client_payload": {
    "action": "published",
    "repository": {
      "name": "$SOURCE_REPO_NAME",
      "full_name": "yiisoft/$SOURCE_REPO_NAME"
    },
    "release": {
      "tag_name": "$VERSION_TAG",
      "html_url": "$RELEASE_URL"
    }
  }
}
EOF
)

# Send the dispatch event
response=$(curl -s -X POST \
  -H "Accept: application/vnd.github.v3+json" \
  -H "Authorization: token $GITHUB_TOKEN" \
  -H "Content-Type: application/json" \
  "https://api.github.com/repos/$REPO_OWNER/$REPO_NAME/dispatches" \
  -d "$PAYLOAD")

if [[ $? -eq 0 ]]; then
    echo "✓ Successfully triggered workflow"
    echo "Check the Actions tab at: https://github.com/$REPO_OWNER/$REPO_NAME/actions"
else
    echo "✗ Failed to trigger workflow"
    echo "Response: $response"
    exit 1
fi