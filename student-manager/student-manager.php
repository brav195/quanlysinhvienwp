<?php
/**
 * Plugin Name:       Student Manager
 * Plugin URI:        https://github.com/your-username/student-manager
 * Description:       Plugin quản lý sinh viên – Đăng ký CPT, Meta Boxes và Shortcode hiển thị danh sách sinh viên.
 * Version:           1.0.0
 * Author:            Student Developer
 * Author URI:        #
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       student-manager
 * Domain Path:       /languages
 */

// Ngăn truy cập trực tiếp vào file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Định nghĩa hằng số plugin
define( 'SM_VERSION',     '1.0.0' );
define( 'SM_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'SM_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );
define( 'SM_PLUGIN_FILE', __FILE__ );

/**
 * Load các module con
 */
require_once SM_PLUGIN_DIR . 'includes/class-sm-post-type.php';
require_once SM_PLUGIN_DIR . 'includes/class-sm-meta-box.php';
require_once SM_PLUGIN_DIR . 'includes/class-sm-shortcode.php';

/**
 * Khởi động plugin
 */
function sm_init_plugin() {
    $post_type = new SM_Post_Type();
    $post_type->register();

    $meta_box = new SM_Meta_Box();
    $meta_box->register();

    $shortcode = new SM_Shortcode();
    $shortcode->register();
}
add_action( 'plugins_loaded', 'sm_init_plugin' );

/**
 * Enqueue CSS ở frontend
 */
function sm_enqueue_assets() {
    if ( ! is_admin() ) {
        wp_enqueue_style(
            'student-manager-style',
            SM_PLUGIN_URL . 'assets/style.css',
            [],
            SM_VERSION
        );
    }
}
add_action( 'wp_enqueue_scripts', 'sm_enqueue_assets' );
