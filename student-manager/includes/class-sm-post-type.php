<?php
/**
 * File: includes/class-sm-post-type.php
 * Mô tả: Đăng ký Custom Post Type "Sinh Viên" vào hệ thống WordPress.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SM_Post_Type {

    /**
     * Tên định danh của CPT.
     */
    const POST_TYPE = 'sinh_vien';

    /**
     * Đăng ký hook WordPress.
     */
    public function register() {
        add_action( 'init', [ $this, 'register_post_type' ] );
    }

    /**
     * Đăng ký Custom Post Type "sinh_vien".
     */
    public function register_post_type() {
        $labels = [
            'name'                  => _x( 'Sinh Viên',           'Post type general name', 'student-manager' ),
            'singular_name'         => _x( 'Sinh Viên',           'Post type singular name', 'student-manager' ),
            'menu_name'             => _x( 'Sinh Viên',           'Admin Menu text', 'student-manager' ),
            'name_admin_bar'        => _x( 'Sinh Viên',           'Add New on Toolbar', 'student-manager' ),
            'add_new'               => __( 'Thêm Mới',            'student-manager' ),
            'add_new_item'          => __( 'Thêm Sinh Viên Mới',  'student-manager' ),
            'new_item'              => __( 'Sinh Viên Mới',        'student-manager' ),
            'edit_item'             => __( 'Chỉnh Sửa Sinh Viên', 'student-manager' ),
            'view_item'             => __( 'Xem Sinh Viên',        'student-manager' ),
            'all_items'             => __( 'Tất Cả Sinh Viên',    'student-manager' ),
            'search_items'          => __( 'Tìm Kiếm Sinh Viên',  'student-manager' ),
            'not_found'             => __( 'Không tìm thấy sinh viên.', 'student-manager' ),
            'not_found_in_trash'    => __( 'Không tìm thấy sinh viên trong thùng rác.', 'student-manager' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'sinh-vien' ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-groups',
            'supports'           => [ 'title', 'editor' ],   // Họ tên + Tiểu sử/Ghi chú
            'show_in_rest'       => true,                    // Hỗ trợ Gutenberg
        ];

        register_post_type( self::POST_TYPE, $args );
    }
}
