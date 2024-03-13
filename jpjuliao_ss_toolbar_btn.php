<?php
/**
 * Plugin Name: Simply Static Generator toolbar button
 * Description: Adds a button to the toolbar to generate static site with Simply Static plugin.
 * Version: 1.0
 * Author: Juan Pablo Juliao
 */

/**
 * Enqueue JavaScript file for handling AJAX request
 */
function jpjuliao_toolbar_button_enqueue_scripts() {
    wp_enqueue_script(
        'jpjuliao_ss_toolbar_btn-js',
        plugin_dir_url(__FILE__) . 'jpjuliao_ss_toolbar_btn.js',
        array('jquery'),
        '1.0',
        true
    );

    wp_localize_script(
        'jpjuliao_ss_toolbar_btn-js',
        'jpjuliao_ajax_object',
        array('ajaxurl' => admin_url('admin-ajax.php'))
    );
}
add_action('admin_enqueue_scripts', 'jpjuliao_toolbar_button_enqueue_scripts');
add_action('wp_enqueue_scripts', 'jpjuliao_toolbar_button_enqueue_scripts');

/**
 * Add a button to the admin toolbar
 *
 * @param WP_Admin_Bar $wp_admin_bar WordPress admin toolbar instance.
 */
function jpjuliao_toolbar_button($wp_admin_bar) {
    if (
        current_user_can('manage_options') &&
        is_plugin_active('simply-static/simply-static.php')
    ) {
        $wp_admin_bar->add_node(array(
            'id'    => 'jpjuliao_ss_toolbar_btn',
            'title' => 'Generate Static Site',
            'href'  => '#',
            'meta'  => array(
                'class' => 'jpjuliao_ss_toolbar_btn',
            ),
        ));
    }
}
add_action('admin_bar_menu', 'jpjuliao_toolbar_button', 999);

/**
 * Check if Simply Static plugin is active
 *
 * @return bool True if Simply Static plugin is active, false otherwise.
 */
function is_simply_static_active() {
    return is_plugin_active('simply-static/simply-static.php');
}

/**
 * Display admin notice if Simply Static plugin is not active
 */
function jpjuliao_toolbar_button_admin_notice() {
    if (!is_simply_static_active() && current_user_can('activate_plugins')) {
?>
        <div class="notice notice-error">
            <p><?php esc_html_e(
                    'Custom Toolbar Button requires Simply Static plugin to be active.',
                    'jpjuliao_ss_toolbar_btn'
                ); ?>
            </p>
        </div>
<?php
    }
}
add_action('admin_notices', 'jpjuliao_toolbar_button_admin_notice');

/**
 * Handle the AJAX request to trigger static export
 */
function trigger_static_export_action() {
    if (
        isset($_POST['action']) &&
        $_POST['action'] === 'jpjuliao_ss_toolbar_btn' &&
        is_simply_static_active()
    ) {
        $plugin = \Simply_Static\Plugin::instance();
        $error = null;
        try {
            $plugin->run_static_export();
            wp_send_json_success('Static export process initiated successfully.');
        } catch (Exception $e) {
            $error = new WP_Error(
                'export_error',
                'Error occurred during static export: ' . $e->getMessage()
            );
            wp_send_json_error($error->get_error_message());
        }
    }
}
add_action('wp_ajax_jpjuliao_ss_toolbar_btn', 'trigger_static_export_action');
