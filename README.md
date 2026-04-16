# UnveilPass for WordPress

Zero-knowledge password manager integration for WordPress. Sign in with UnveilPass (OIDC SSO), credential management via Agent Gateway, 2FA enforcement and security dashboard.

## Features

- **Sign in with UnveilPass** — OIDC SSO button on wp-login.php with PKCE (S256)
- **Auto-create users** — optionally create WordPress accounts for new UnveilPass users
- **2FA enforcement** — block direct password login for administrators
- **Agent Gateway** — `unveilpass_get_credential($entry_id)` helper for deployment scripts
- **Security dashboard** — admin/editor login history and method tracking

## Installation

1. Download and upload to `/wp-content/plugins/unveilpass/`
2. Activate in WordPress admin
3. Go to **Settings > UnveilPass**
4. Configure your Server URL, Client ID and Client Secret

### Getting Client Credentials

1. Log in to [UnveilPass](https://unveilpass.com)
2. Go to **Manager Console > Applications**
3. Click **+ New Application**
4. Set the Redirect URI to: `https://yoursite.com/wp-login.php?unveilpass_callback=1`
5. Copy the Client ID and Client Secret to the plugin settings

## Agent Gateway Usage

```php
// In your theme or plugin code:
$cred = unveilpass_get_credential('your-entry-uuid');
if ($cred) {
    $db_user = $cred['username'];
    $db_pass = $cred['password'];
}
```

## Requirements

- WordPress 5.6+
- PHP 7.4+
- UnveilPass Pro plan (for SSO and Agent Gateway)

## License

MIT License. Copyright 2026 UnveilTech.
