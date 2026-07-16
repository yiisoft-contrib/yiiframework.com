# Automatic Version Updates

This repository includes a GitHub Action that automatically updates the framework versions when new releases are published to the Yii repositories.

## How it works

The workflow `.github/workflows/update-versions.yml` responds to `repository_dispatch` events with the type `yii-release`. When triggered, it:

1. Extracts release information from the webhook payload
2. Determines which Yii version (2.0 or 1.1) based on the repository name
3. Updates the `config/versions.php` file with the new version
4. Commits and pushes the changes

## Setting up webhooks

To enable automatic updates from Yii repositories, you need to configure webhooks in the source repositories that send `repository_dispatch` events to this repository.

### For repository maintainers

You can set up a webhook in the Yii repositories (yiisoft/yii2, yiisoft/yii) that triggers this workflow:

1. Go to the repository settings → Webhooks
2. Add a new webhook with:
   - **Payload URL**: `https://api.github.com/repos/yiisoft-contrib/yiiframework.com/dispatches`
   - **Content type**: `application/json`
   - **Secret**: Configure a secret token
   - **Events**: Choose "Releases" only

3. The webhook should send a POST request with this format:
```json
{
  "event_type": "yii-release",
  "client_payload": {
    "action": "published",
    "repository": {
      "name": "yii2",
      "full_name": "yiisoft/yii2"
    },
    "release": {
      "tag_name": "2.0.54",
      "html_url": "https://github.com/yiisoft/yii2/releases/tag/2.0.54"
    }
  }
}
```

### Manual triggering

You can also manually trigger the workflow using the GitHub API:

```bash
curl -X POST \
  -H "Accept: application/vnd.github.v3+json" \
  -H "Authorization: token YOUR_TOKEN" \
  https://api.github.com/repos/yiisoft-contrib/yiiframework.com/dispatches \
  -d '{
    "event_type": "yii-release",
    "client_payload": {
      "action": "published",
      "repository": {
        "name": "yii2",
        "full_name": "yiisoft/yii2"
      },
      "release": {
        "tag_name": "2.0.54",
        "html_url": "https://github.com/yiisoft/yii2/releases/tag/2.0.54"
      }
    }
  }'
```

## Supported repositories

- `yiisoft/yii2` → Updates Yii 2.0 versions
- `yiisoft/yii` → Updates Yii 1.1 versions

## Files modified

- `config/versions.php` - The main versions configuration file where new releases are added to the `minor-versions` array