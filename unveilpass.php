<?php
/**
 * Plugin Name: UnveilPass
 * Plugin URI: https://unveilpass.unveiltech.com/integrations.html
 * Description: Zero-knowledge password manager integration. Sign in with UnveilPass (OIDC SSO), credential management via Agent Gateway, security dashboard and 2FA enforcement.
 * Version: 1.0.0
 * Author: UnveilTech
 * Author URI: https://www.unveiltech.com/
 * License: MIT
 * Text Domain: unveilpass
 */

if (!defined('ABSPATH')) exit;

define('UNVEILPASS_VERSION', '1.0.0');
define('UNVEILPASS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UNVEILPASS_PLUGIN_URL', plugin_dir_url(__FILE__));

// ============================================================
// SETTINGS
// ============================================================

add_action('admin_menu', function () {
    add_options_page(
        'UnveilPass Settings',
        'UnveilPass',
        'manage_options',
        'unveilpass',
        'unveilpass_settings_page'
    );
});

add_action('admin_init', function () {
    register_setting('unveilpass_settings', 'unveilpass_server_url');
    register_setting('unveilpass_settings', 'unveilpass_client_id');
    register_setting('unveilpass_settings', 'unveilpass_client_secret');
    register_setting('unveilpass_settings', 'unveilpass_agent_key');
    register_setting('unveilpass_settings', 'unveilpass_sso_enabled');
    register_setting('unveilpass_settings', 'unveilpass_enforce_2fa');
    register_setting('unveilpass_settings', 'unveilpass_auto_create_users');
});

function unveilpass_settings_page() {
    $server_url = get_option('unveilpass_server_url', 'https://unveilpass.com');
    $client_id = get_option('unveilpass_client_id', '');
    $client_secret = get_option('unveilpass_client_secret', '');
    $agent_key = get_option('unveilpass_agent_key', '');
    $sso_enabled = get_option('unveilpass_sso_enabled', '0');
    $enforce_2fa = get_option('unveilpass_enforce_2fa', '0');
    $auto_create = get_option('unveilpass_auto_create_users', '0');
    $callback_url = site_url('/wp-login.php?unveilpass_callback=1');
    ?>
    <div class="wrap">
        <h1><img src="<?php echo esc_url(UNVEILPASS_PLUGIN_URL . 'assets/icon-48.png'); ?>" style="height:28px;vertical-align:middle;margin-right:8px">UnveilPass Settings</h1>

        <form method="post" action="options.php">
            <?php settings_fields('unveilpass_settings'); ?>

            <h2 class="title">Server</h2>
            <table class="form-table">
                <tr>
                    <th><label for="unveilpass_server_url">Server URL</label></th>
                    <td>
                        <input type="url" id="unveilpass_server_url" name="unveilpass_server_url" value="<?php echo esc_attr($server_url); ?>" class="regular-text">
                        <p class="description">Your UnveilPass server (default: https://unveilpass.com)</p>
                    </td>
                </tr>
            </table>

            <h2 class="title">Sign in with UnveilPass (OIDC SSO)</h2>
            <p>Allow users to sign in to WordPress using their UnveilPass account. Register an application in your <strong>Manager Console &rarr; Applications</strong> tab to get the credentials below.</p>
            <table class="form-table">
                <tr>
                    <th><label for="unveilpass_sso_enabled">Enable SSO</label></th>
                    <td><input type="checkbox" id="unveilpass_sso_enabled" name="unveilpass_sso_enabled" value="1" <?php checked($sso_enabled, '1'); ?>></td>
                </tr>
                <tr>
                    <th><label for="unveilpass_client_id">Client ID</label></th>
                    <td><input type="text" id="unveilpass_client_id" name="unveilpass_client_id" value="<?php echo esc_attr($client_id); ?>" class="regular-text" style="font-family:monospace"></td>
                </tr>
                <tr>
                    <th><label for="unveilpass_client_secret">Client Secret</label></th>
                    <td><input type="password" id="unveilpass_client_secret" name="unveilpass_client_secret" value="<?php echo esc_attr($client_secret); ?>" class="regular-text" style="font-family:monospace"></td>
                </tr>
                <tr>
                    <th>Callback URL</th>
                    <td>
                        <code><?php echo esc_html($callback_url); ?></code>
                        <p class="description">Add this URL as a Redirect URI in your UnveilPass application settings.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="unveilpass_auto_create_users">Auto-create users</label></th>
                    <td>
                        <input type="checkbox" id="unveilpass_auto_create_users" name="unveilpass_auto_create_users" value="1" <?php checked($auto_create, '1'); ?>>
                        <span class="description">When enabled, new WordPress accounts are created automatically for UnveilPass users who sign in for the first time (as Subscriber role).</span>
                    </td>
                </tr>
            </table>

            <h2 class="title">Agent Gateway (Pro)</h2>
            <p>Use an Agent Key to fetch credentials from your vault programmatically (e.g. for deployment scripts or automated tasks).</p>
            <table class="form-table">
                <tr>
                    <th><label for="unveilpass_agent_key">Agent Key</label></th>
                    <td>
                        <input type="password" id="unveilpass_agent_key" name="unveilpass_agent_key" value="<?php echo esc_attr($agent_key); ?>" class="regular-text" style="font-family:monospace">
                        <p class="description">Format: uvp_agent_xxxx. Create one in Manager Console &rarr; Agent Gateway.</p>
                    </td>
                </tr>
            </table>

            <h2 class="title">Security</h2>
            <table class="form-table">
                <tr>
                    <th><label for="unveilpass_enforce_2fa">Enforce 2FA for admins</label></th>
                    <td>
                        <input type="checkbox" id="unveilpass_enforce_2fa" name="unveilpass_enforce_2fa" value="1" <?php checked($enforce_2fa, '1'); ?>>
                        <span class="description">When enabled, administrators must sign in via UnveilPass SSO (which includes MFA). Direct WordPress password login is blocked for admin users.</span>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>

        <hr>
        <h2 class="title">Security Dashboard</h2>
        <?php unveilpass_security_dashboard(); ?>

        <hr>
        <p style="color:#888;font-size:12px">UnveilPass WordPress Plugin v<?php echo UNVEILPASS_VERSION; ?> &mdash; <a href="https://unveilpass.unveiltech.com/integrations.html" target="_blank">Documentation</a></p>
    </div>
    <?php
}

function unveilpass_security_dashboard() {
    $users = get_users(['role__in' => ['administrator', 'editor']]);
    echo '<table class="widefat striped"><thead><tr><th>User</th><th>Role</th><th>Last Login</th><th>Login Method</th></tr></thead><tbody>';
    foreach ($users as $user) {
        $last = get_user_meta($user->ID, 'unveilpass_last_login', true);
        $method = get_user_meta($user->ID, 'unveilpass_login_method', true) ?: 'password';
        echo '<tr>';
        echo '<td>' . esc_html($user->user_email) . '</td>';
        echo '<td>' . esc_html(implode(', ', $user->roles)) . '</td>';
        echo '<td>' . ($last ? esc_html(date('Y/m/d H:i', $last)) : 'Never') . '</td>';
        echo '<td>' . ($method === 'unveilpass' ? '<span style="color:#89A54E;font-weight:600">UnveilPass SSO</span>' : '<span style="color:#888">Password</span>') . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

// ============================================================
// OIDC SSO — SIGN IN WITH UNVEILPASS
// ============================================================

add_action('login_form', function () {
    if (get_option('unveilpass_sso_enabled') !== '1') return;
    $client_id = get_option('unveilpass_client_id');
    if (empty($client_id)) return;

    $server_url = rtrim(get_option('unveilpass_server_url', 'https://unveilpass.com'), '/');
    $redirect_uri = site_url('/wp-login.php?unveilpass_callback=1');

    // Generate PKCE
    $verifier = wp_generate_password(64, false);
    $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    $state = wp_generate_password(32, false);

    // Store in transient (5 min TTL)
    set_transient('unveilpass_pkce_' . $state, $verifier, 300);

    $auth_url = $server_url . '/api/oidc/authorize?' . http_build_query([
        'response_type' => 'code',
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'scope' => 'openid email',
        'state' => $state,
        'code_challenge' => $challenge,
        'code_challenge_method' => 'S256',
    ]);
    ?>
    <div style="margin:16px 0;text-align:center">
        <div style="border-top:1px solid #ddd;margin:12px 0;position:relative">
            <span style="background:#fff;padding:0 12px;position:relative;top:-10px;color:#888;font-size:13px">or</span>
        </div>
        <a href="<?php echo esc_url($auth_url); ?>" style="display:inline-flex;align-items:center;gap:8px;background:#4572A7;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;font-weight:600;font-size:14px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
            <img src="<?php echo esc_url(UNVEILPASS_PLUGIN_URL . 'assets/icon-24-white.png'); ?>" style="height:18px" alt="">
            Sign in with UnveilPass
        </a>
    </div>
    <?php
});

// Handle OIDC callback
add_action('init', function () {
    if (!isset($_GET['unveilpass_callback']) || !isset($_GET['code']) || !isset($_GET['state'])) return;

    $state = sanitize_text_field($_GET['state']);
    $code = sanitize_text_field($_GET['code']);
    $verifier = get_transient('unveilpass_pkce_' . $state);
    delete_transient('unveilpass_pkce_' . $state);

    if (!$verifier) {
        wp_die('Invalid or expired state. Please try again.', 'Authentication Error', ['back_link' => true]);
    }

    $server_url = rtrim(get_option('unveilpass_server_url', 'https://unveilpass.com'), '/');
    $client_id = get_option('unveilpass_client_id');
    $client_secret = get_option('unveilpass_client_secret');
    $redirect_uri = site_url('/wp-login.php?unveilpass_callback=1');

    // Exchange code for tokens
    $response = wp_remote_post($server_url . '/api/oidc/token', [
        'body' => [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirect_uri,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'code_verifier' => $verifier,
        ],
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        wp_die('Failed to contact UnveilPass server: ' . esc_html($response->get_error_message()), 'Authentication Error', ['back_link' => true]);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['access_token'])) {
        $error = isset($body['error_description']) ? $body['error_description'] : (isset($body['error']) ? $body['error'] : 'Unknown error');
        wp_die('Authentication failed: ' . esc_html($error), 'Authentication Error', ['back_link' => true]);
    }

    // Get user info
    $userinfo = wp_remote_get($server_url . '/api/oidc/userinfo', [
        'headers' => ['Authorization' => 'Bearer ' . $body['access_token']],
        'timeout' => 10,
    ]);

    if (is_wp_error($userinfo)) {
        wp_die('Failed to retrieve user info.', 'Authentication Error', ['back_link' => true]);
    }

    $user_data = json_decode(wp_remote_retrieve_body($userinfo), true);
    if (empty($user_data['email'])) {
        wp_die('No email returned from UnveilPass. Ensure the "email" scope is enabled.', 'Authentication Error', ['back_link' => true]);
    }

    $email = sanitize_email($user_data['email']);
    $sub = isset($user_data['sub']) ? sanitize_text_field($user_data['sub']) : '';

    // Find or create WordPress user
    $user = get_user_by('email', $email);

    if (!$user && get_option('unveilpass_auto_create_users') === '1') {
        $username = strstr($email, '@', true);
        $username = sanitize_user($username);
        if (username_exists($username)) {
            $username = $username . '_' . substr($sub, 0, 6);
        }
        $user_id = wp_create_user($username, wp_generate_password(32, true), $email);
        if (is_wp_error($user_id)) {
            wp_die('Failed to create user account.', 'Authentication Error', ['back_link' => true]);
        }
        $user = get_user_by('id', $user_id);
        update_user_meta($user_id, 'unveilpass_sub', $sub);
    }

    if (!$user) {
        wp_die('No WordPress account found for ' . esc_html($email) . '. Ask your administrator to create your account or enable auto-creation.', 'Authentication Error', ['back_link' => true]);
    }

    // Log in
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);
    update_user_meta($user->ID, 'unveilpass_sub', $sub);
    update_user_meta($user->ID, 'unveilpass_last_login', time());
    update_user_meta($user->ID, 'unveilpass_login_method', 'unveilpass');

    wp_safe_redirect(admin_url());
    exit;
});

// ============================================================
// 2FA ENFORCEMENT
// ============================================================

add_filter('authenticate', function ($user, $username, $password) {
    if (is_wp_error($user) || !$user) return $user;
    if (get_option('unveilpass_enforce_2fa') !== '1') return $user;
    if (!in_array('administrator', $user->roles)) return $user;

    // Block direct password login for admins when 2FA enforcement is on
    if (!empty($password)) {
        return new WP_Error('unveilpass_2fa_required',
            '<strong>Security policy:</strong> Administrators must sign in via UnveilPass SSO. Direct password login is disabled for admin accounts.'
        );
    }

    return $user;
}, 30, 3);

// Track password logins
add_action('wp_login', function ($user_login, $user) {
    if (!isset($_GET['unveilpass_callback'])) {
        update_user_meta($user->ID, 'unveilpass_last_login', time());
        update_user_meta($user->ID, 'unveilpass_login_method', 'password');
    }
}, 10, 2);

// ============================================================
// AGENT GATEWAY HELPER
// ============================================================

function unveilpass_get_credential($entry_id, $timeout = 30) {
    $agent_key = get_option('unveilpass_agent_key');
    $server_url = rtrim(get_option('unveilpass_server_url', 'https://unveilpass.com'), '/');

    if (empty($agent_key) || empty($entry_id)) return null;

    // Create request
    $response = wp_remote_post($server_url . '/api/agent/request', [
        'headers' => [
            'X-Agent-Key' => $agent_key,
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode(['entry_id' => $entry_id]),
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) return null;
    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['id'])) return null;

    $request_id = $body['id'];
    $start = time();

    // Poll for approval
    while (time() - $start < $timeout) {
        sleep(2);
        $status = wp_remote_get($server_url . '/api/agent/request/' . $request_id, [
            'headers' => ['X-Agent-Key' => $agent_key],
            'timeout' => 10,
        ]);
        if (is_wp_error($status)) continue;
        $data = json_decode(wp_remote_retrieve_body($status), true);
        if (isset($data['status']) && $data['status'] === 'approved') {
            // Fetch credential
            $cred = wp_remote_get($server_url . '/api/agent/credential/' . $request_id, [
                'headers' => ['X-Agent-Key' => $agent_key],
                'timeout' => 10,
            ]);
            if (is_wp_error($cred)) return null;
            return json_decode(wp_remote_retrieve_body($cred), true);
        }
        if (isset($data['status']) && in_array($data['status'], ['denied', 'expired'])) return null;
    }

    return null;
}

// ============================================================
// SETTINGS LINK IN PLUGINS LIST
// ============================================================

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $url = admin_url('options-general.php?page=unveilpass');
    array_unshift($links, '<a href="' . esc_url($url) . '">Settings</a>');
    return $links;
});

// ============================================================
// INTEGRATION TRACKING
// ============================================================

add_action('admin_init', function () {
    if (get_transient('unveilpass_tracked')) return;
    $server_url = rtrim(get_option('unveilpass_server_url', 'https://unveilpass.com'), '/');
    wp_remote_post($server_url . '/api/track/click', [
        'body' => json_encode(['module' => 'wordpress']),
        'headers' => ['Content-Type' => 'text/plain'],
        'timeout' => 3,
        'blocking' => false,
    ]);
    set_transient('unveilpass_tracked', 1, DAY_IN_SECONDS);
}, 999);
