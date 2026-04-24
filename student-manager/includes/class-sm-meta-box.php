<?php
/**
 * File: includes/class-sm-meta-box.php
 * Mô tả: Tạo và xử lý Custom Meta Box cho CPT "Sinh Viên".
 *        Gồm các trường: MSSV, Lớp/Chuyên ngành, Ngày sinh.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SM_Meta_Box {

    // Tên meta keys lưu trong database
    const META_MSSV    = '_sm_mssv';
    const META_LOP     = '_sm_lop';
    const META_NGAYSINH = '_sm_ngay_sinh';

    // Nonce action & name để bảo mật
    const NONCE_ACTION = 'sm_save_meta_box';
    const NONCE_NAME   = 'sm_meta_box_nonce';

    // Danh sách lớp/chuyên ngành
    const CHUYEN_NGANH = [
        'CNTT'        => 'Công nghệ thông tin',
        'KTPM'        => 'Kỹ thuật phần mềm',
        'KHMT'        => 'Khoa học máy tính',
        'ATTT'        => 'An toàn thông tin',
        'Kinh_te'     => 'Kinh tế',
        'QTKD'        => 'Quản trị kinh doanh',
        'Marketing'   => 'Marketing',
        'Ke_toan'     => 'Kế toán - Kiểm toán',
        'Luat'        => 'Luật',
        'Ngoai_ngu'   => 'Ngoại ngữ',
    ];

    /**
     * Đăng ký hook WordPress.
     */
    public function register() {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
        add_action( 'save_post',      [ $this, 'save_meta_box' ] );
    }

    /**
     * Thêm meta box vào màn hình chỉnh sửa CPT sinh_vien.
     */
    public function add_meta_box() {
        add_meta_box(
            'sm_thong_tin_sinh_vien',               // ID
            __( 'Thông Tin Sinh Viên', 'student-manager' ), // Tiêu đề
            [ $this, 'render_meta_box' ],           // Callback render HTML
            SM_Post_Type::POST_TYPE,                // Post type
            'normal',                               // Context
            'high'                                  // Priority
        );
    }

    /**
     * Render HTML form trong meta box.
     *
     * @param WP_Post $post Đối tượng post hiện tại.
     */
    public function render_meta_box( WP_Post $post ) {
        // Nonce field để bảo mật khi submit
        wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

        // Lấy giá trị hiện tại từ database (nếu có)
        $mssv      = get_post_meta( $post->ID, self::META_MSSV,     true );
        $lop       = get_post_meta( $post->ID, self::META_LOP,      true );
        $ngay_sinh = get_post_meta( $post->ID, self::META_NGAYSINH, true );
        ?>
        <style>
            .sm-meta-table { width: 100%; border-collapse: collapse; }
            .sm-meta-table th,
            .sm-meta-table td { padding: 10px 12px; vertical-align: middle; }
            .sm-meta-table th { width: 200px; text-align: left; font-weight: 600; color: #1d2327; }
            .sm-meta-table input[type="text"],
            .sm-meta-table input[type="date"],
            .sm-meta-table select { width: 100%; padding: 6px 10px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px; }
            .sm-meta-table tr:nth-child(even) { background: #f6f7f7; }
            .sm-meta-required { color: #d63638; margin-left: 3px; }
        </style>

        <table class="sm-meta-table">
            <tbody>

                <!-- Mã số sinh viên -->
                <tr>
                    <th>
                        <label for="sm_mssv">
                            <?php esc_html_e( 'Mã số sinh viên (MSSV)', 'student-manager' ); ?>
                            <span class="sm-meta-required">*</span>
                        </label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="sm_mssv"
                            name="sm_mssv"
                            value="<?php echo esc_attr( $mssv ); ?>"
                            placeholder="VD: SV2024001"
                            maxlength="20"
                        />
                    </td>
                </tr>

                <!-- Lớp / Chuyên ngành -->
                <tr>
                    <th>
                        <label for="sm_lop">
                            <?php esc_html_e( 'Lớp / Chuyên ngành', 'student-manager' ); ?>
                            <span class="sm-meta-required">*</span>
                        </label>
                    </th>
                    <td>
                        <select id="sm_lop" name="sm_lop">
                            <option value="">-- <?php esc_html_e( 'Chọn chuyên ngành', 'student-manager' ); ?> --</option>
                            <?php foreach ( self::CHUYEN_NGANH as $value => $label ) : ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $lop, $value ); ?>>
                                    <?php echo esc_html( $label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <!-- Ngày sinh -->
                <tr>
                    <th>
                        <label for="sm_ngay_sinh">
                            <?php esc_html_e( 'Ngày sinh', 'student-manager' ); ?>
                        </label>
                    </th>
                    <td>
                        <input
                            type="date"
                            id="sm_ngay_sinh"
                            name="sm_ngay_sinh"
                            value="<?php echo esc_attr( $ngay_sinh ); ?>"
                        />
                    </td>
                </tr>

            </tbody>
        </table>
        <?php
    }

    /**
     * Lưu dữ liệu meta box vào database.
     * Áp dụng: kiểm tra Nonce, quyền hạn, autosave, và Sanitize dữ liệu.
     *
     * @param int $post_id ID của post đang được lưu.
     */
    public function save_meta_box( int $post_id ) {

        // 1. Kiểm tra Nonce – bảo vệ khỏi CSRF
        if ( ! isset( $_POST[ self::NONCE_NAME ] )
             || ! wp_verify_nonce( $_POST[ self::NONCE_NAME ], self::NONCE_ACTION )
        ) {
            return;
        }

        // 2. Bỏ qua autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // 3. Kiểm tra quyền chỉnh sửa post
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // 4. Kiểm tra đúng post type
        if ( SM_Post_Type::POST_TYPE !== get_post_type( $post_id ) ) {
            return;
        }

        // 5. Sanitize và lưu MSSV
        if ( isset( $_POST['sm_mssv'] ) ) {
            $mssv = sanitize_text_field( wp_unslash( $_POST['sm_mssv'] ) );
            update_post_meta( $post_id, self::META_MSSV, $mssv );
        }

        // 6. Sanitize và lưu Lớp/Chuyên ngành (chỉ chấp nhận giá trị trong whitelist)
        if ( isset( $_POST['sm_lop'] ) ) {
            $lop_raw = sanitize_text_field( wp_unslash( $_POST['sm_lop'] ) );
            $lop     = array_key_exists( $lop_raw, self::CHUYEN_NGANH ) ? $lop_raw : '';
            update_post_meta( $post_id, self::META_LOP, $lop );
        }

        // 7. Sanitize và lưu Ngày sinh (validate định dạng YYYY-MM-DD)
        if ( isset( $_POST['sm_ngay_sinh'] ) ) {
            $ngay_raw = sanitize_text_field( wp_unslash( $_POST['sm_ngay_sinh'] ) );
            // Kiểm tra định dạng date hợp lệ
            $date = DateTime::createFromFormat( 'Y-m-d', $ngay_raw );
            $ngay = ( $date && $date->format( 'Y-m-d' ) === $ngay_raw ) ? $ngay_raw : '';
            update_post_meta( $post_id, self::META_NGAYSINH, $ngay );
        }
    }

    /**
     * Helper – lấy tên chuyên ngành từ key.
     *
     * @param string $key
     * @return string
     */
    public static function get_chuyen_nganh_label( string $key ): string {
        return self::CHUYEN_NGANH[ $key ] ?? $key;
    }
}
