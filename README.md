# UnveilPass for WordPress

Zero-knowledge password manager integration for WordPress.

## Free Features

- **Password Generator** — Dashboard widget with length, character toggles and strength indicator (12 chars max)
- **Admin Bar Button** — Quick password generation from anywhere in WordPress admin
- **REST API** — `GET /wp-json/unveilpass/v1/generate?length=12`

## Pro Features

- **Sign in with UnveilPass** — OIDC SSO button on wp-login.php with PKCE (S256)
- **Auto-create users** — optionally create WordPress accounts for new UnveilPass users
- **2FA enforcement** — block direct password login for administrators
- **Agent Gateway** — `unveilpass_get_credential($entry_id)` helper for deployment scripts
- **Security dashboard** — admin/editor login history and method tracking
- **Extended generator** — up to 128 characters

## Installation

1. Download and upload to `/wp-content/plugins/unveilpass/`
2. Activate in WordPress admin
3. Go to **Settings > UnveilPass**

### Free Setup

No configuration needed. The password generator widget appears on your dashboard automatically.

### Pro Setup

1. Log in to [UnveilPass](https://unveilpass.com)
2. Go to **Manager Console > Applications**
3. Click **+ New Application**
4. Set the Redirect URI to: `https://yoursite.com/wp-login.php?unveilpass_callback=1`
5. Copy the Client ID and Client Secret to the plugin settings

## Agent Gateway Usage

```php
$cred = unveilpass_get_credential('your-entry-uuid');
if ($cred) {
    $db_user = $cred['username'];
    $db_pass = $cred['password'];
}
```

## Requirements

- WordPress 5.6+
- PHP 7.4+
- UnveilPass Pro plan (for SSO, Agent Gateway and 2FA enforcement)

## License

MIT License. Copyright 2026 UnveilTech.
