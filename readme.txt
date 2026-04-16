=== UnveilPass ===
Contributors: unveiltech
Tags: password manager, sso, oidc, security, 2fa, credentials
Requires at least: 5.6
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: MIT

Zero-knowledge password manager integration for WordPress. SSO login, credential management and security enforcement.

== Description ==

UnveilPass is a zero-knowledge password manager with end-to-end encryption. This plugin integrates UnveilPass with your WordPress site:

**Sign in with UnveilPass (OIDC SSO)**
Add a "Sign in with UnveilPass" button to your WordPress login page. Users authenticate via UnveilPass with master password + MFA, then are automatically logged into WordPress. No passwords stored in WordPress.

**Agent Gateway**
Fetch credentials from your vault programmatically using the `unveilpass_get_credential()` helper function. Ideal for deployment scripts, database migrations or any automated task that needs secure access to credentials.

**2FA Enforcement**
Force administrators to sign in via UnveilPass SSO instead of direct password login. Since UnveilPass supports TOTP, passkeys and device trust, this effectively enforces multi-factor authentication.

**Security Dashboard**
View all admin and editor accounts with their last login time and login method (UnveilPass SSO vs password).

== Installation ==

1. Upload the `unveilpass` folder to `/wp-content/plugins/`
2. Activate the plugin through the Plugins menu
3. Go to Settings > UnveilPass
4. Enter your Server URL (default: https://unveilpass.com)
5. For SSO: enter Client ID and Client Secret from your UnveilPass Manager Console > Applications tab
6. Add the Callback URL shown on the settings page as a Redirect URI in your UnveilPass application

== Frequently Asked Questions ==

= Do my users need an UnveilPass account? =
Yes, users who want to sign in via UnveilPass SSO need an UnveilPass account. You can enable auto-creation to automatically create WordPress accounts for new UnveilPass users.

= Is this free? =
The SSO feature requires an UnveilPass organization (Pro plan). The plugin itself is free.

= What data does UnveilPass receive? =
Only the email address associated with the WordPress account is matched. Vault data and passwords are never shared with WordPress.

== Changelog ==

= 1.0.0 =
* Initial release
* Sign in with UnveilPass (OIDC SSO with PKCE)
* Agent Gateway helper function
* 2FA enforcement for administrators
* Security dashboard
* Integration tracking
