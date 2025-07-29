# GitHub Webhook for Automatic Version Updates

This feature allows the website to automatically update framework version information when new releases are published on GitHub.

## Setup

### 1. Configure Webhook Secret

Add the webhook secret to your configuration:

```php
// config/params-local.php
return [
    // ... other params
    'github-webhook-secret' => 'your-secret-key-here',
];
```

Or set via environment variable and reference it in params.

### 2. Configure GitHub Webhooks

For each repository you want to track (e.g., `yiisoft/yii2`, `yiisoft/yii`):

1. Go to repository Settings → Webhooks
2. Click "Add webhook"
3. Configure:
   - **Payload URL**: `https://www.yiiframework.com/site/github-webhook`
   - **Content type**: `application/json`
   - **Secret**: Use the same secret as configured above
   - **Events**: Select "Releases"
   - **Active**: ✓ Checked

### 3. Supported Repositories

Currently supported repositories:
- `yiisoft/yii2` → Updates `2.0` version series
- `yiisoft/yii` → Updates `1.1` version series

### 4. How It Works

When a new release is published:

1. GitHub sends a webhook to `/site/github-webhook`
2. The webhook validates the signature for security
3. If the release is from a supported repository and matches the expected version format:
   - The new version is added to the top of the appropriate array in `config/versions.php`
   - The date is formatted as "Month Day, Year"

### 5. Security

- Webhook signatures are validated using HMAC-SHA256
- Only "published" release events are processed
- Only releases from whitelisted repositories are accepted
- Only versions matching expected patterns are processed

### 6. Testing

To test the webhook:

```bash
# Send a test payload (replace with actual secret)
curl -X POST https://www.yiiframework.com/site/github-webhook \
  -H "Content-Type: application/json" \
  -H "X-GitHub-Event: ping" \
  -d '{"zen": "Design for failure."}'
```

Expected response: "pong"