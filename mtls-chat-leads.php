<?php
/**
 * Plugin Name: MTLS Chat Leads
 * Description: A lightweight and essential click-to-chat button for your website.
 * Version:     1.0.3
 * Author:      MTLS
 * License:     GPLv2 or later
 * Text Domain: mtls-chat-leads
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// 1. STYLES
add_action( 'wp_head', function() {
    $data = get_option('mtls_chat_lite_data');
    $btn_color = !empty($data['color']) ? $data['color'] : '#25D366';
    ?>
    <style>
    .mtls-btn { background-color: <?php echo esc_attr($btn_color); ?> !important; color: #fff !important; font-family: sans-serif; font-weight: bold; text-decoration: none !important; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center; }
    .mtls-float { position: fixed; bottom: 20px; right: 20px; border-radius: 50px; padding: 15px 25px; z-index: 9999; }
    .mtls-inline { display: inline-flex; padding: 12px 24px; border-radius: 50px; }
    .mtls-btn:hover { opacity: 0.85; transform: scale(1.03); }
    </style>
    <?php
});

// 2. LITE DASHBOARD
add_action('admin_menu', function() {
    add_menu_page('MTLS Chat Leads', 'MTLS Chat Leads', 'manage_options', 'mtls-chat-leads', 'mtls_chat_render_lite', 'dashicons-format-chat', 30);
});

function mtls_chat_render_lite() {
    // SECURITY FIX: Nonce check
    if ( isset($_POST['mtls_save_lite']) ) {
        if ( ! isset( $_POST['mtls_lite_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mtls_lite_nonce'] ) ), 'mtls_lite_save_action' ) ) {
            wp_die( 'Security check failed.' );
        }

        // SECURITY FIX: Input Validation & Sanitization
        update_option('mtls_chat_lite_data', array(
            'num'   => isset($_POST['num']) ? sanitize_text_field(wp_unslash($_POST['num'])) : '',
            'label' => isset($_POST['label']) ? sanitize_text_field(wp_unslash($_POST['label'])) : '',
            'color' => isset($_POST['color']) ? sanitize_hex_color(wp_unslash($_POST['color'])) : '#25D366',
            'float' => isset($_POST['float']) ? 'yes' : 'no'
        ));
        echo '<div class="updated"><p>Settings Saved! ✅</p></div>';
    }
    $data = get_option('mtls_chat_lite_data');
    ?>
    <div class="wrap">
        <h1>MTLS Chat Leads</h1>
        
        <div style="background: #fff8e5; border-left: 4px solid #ffb900; padding: 20px; margin: 20px 0;">
            <h3 style="margin-top:0;">🚀 Want Lead Analytics?</h3>
            <p>Upgrade to the <strong>Pro Version</strong> to track daily leads, use date filters, and see which pages are performing best!</p>
            <a href="https://mindtrain5.gumroad.com/l/mtls-chatleads-pro" target="_blank" class="button button-primary" style="background:#128C7E; border-color:#128C7E;">Upgrade to Analytics Version 🚀</a>
        </div>

        <form method="post">
            <?php wp_nonce_field( 'mtls_lite_save_action', 'mtls_lite_nonce' ); ?>
            <table class="form-table">
                <tr><th>Chat Number (with Country Code)</th><td><input type="text" name="num" value="<?php echo esc_attr($data['num'] ?? ''); ?>" class="regular-text" placeholder="919876543210"></td></tr>
                <tr><th>Button Color</th><td><input type="color" name="color" value="<?php echo esc_attr($data['color'] ?? '#25D366'); ?>"></td></tr>
                <tr><th>Button Label</th><td><input type="text" name="label" value="<?php echo esc_attr($data['label'] ?? 'Chat Here'); ?>" class="regular-text"></td></tr>
                <tr><th>Enable Sticky Button</th><td><input type="checkbox" name="float" value="yes" <?php checked($data['float'] ?? 'no', 'yes'); ?>> Show floating button</td></tr>
            </table>
            <?php submit_button('Save Settings', 'primary', 'mtls_save_lite'); ?>
        </form>
    </div>
    <?php
}

// 3. DISPLAY
add_action('wp_footer', function() {
    $data = get_option('mtls_chat_lite_data');
    if ( ($data['float'] ?? 'no') !== 'yes' || empty($data['num']) ) return;
    $url = 'https://wa.me/'.preg_replace('/[^0-9]/','',$data['num']);
    echo '<a href="'.esc_url($url).'" target="_blank" class="mtls-btn mtls-float">💬 '.esc_html($data['label'] ?? 'Chat').'</a>';
});

add_shortcode('mtls_chat_button', function($atts) {
    $data = get_option('mtls_chat_lite_data');
    if(empty($data['num'])) return '';
    $url = 'https://wa.me/'.preg_replace('/[^0-9]/','',$data['num']);
    return '<a href="'.esc_url($url).'" target="_blank" class="mtls-btn mtls-inline">💬 '.esc_html($data['label'] ?? 'Connect').'</a>';
});
