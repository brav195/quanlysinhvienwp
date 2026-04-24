<?php
/**
 * File: includes/class-sm-shortcode.php
 * Mô tả: Đăng ký shortcode [danh_sach_sinh_vien] để hiển thị
 *        danh sách sinh viên dưới dạng bảng HTML.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SM_Shortcode {

    /**
     * Đăng ký shortcode với WordPress.
     */
    public function register() {
        add_shortcode( 'danh_sach_sinh_vien', [ $this, 'render_shortcode' ] );
    }

    /**
     * Xử lý và trả về HTML bảng danh sách sinh viên.
     *
     * @param array  $atts    Thuộc tính shortcode (nếu có mở rộng sau).
     * @param string $content Nội dung bên trong shortcode (không dùng).
     * @return string         HTML của bảng sinh viên.
     */
    public function render_shortcode( $atts = [], $content = null ): string {

        // Merge thuộc tính mặc định
        $atts = shortcode_atts(
            [
                'so_luong'    => -1,       // -1 = hiển thị tất cả
                'sap_xep'     => 'ASC',    // ASC | DESC
                'sap_xep_theo'=> 'title',  // title | date | meta_value
            ],
            $atts,
            'danh_sach_sinh_vien'
        );

        // Sanitize các tham số
        $posts_per_page = intval( $atts['so_luong'] );
        $order          = in_array( strtoupper( $atts['sap_xep'] ), [ 'ASC', 'DESC' ], true )
                          ? strtoupper( $atts['sap_xep'] )
                          : 'ASC';

        // Truy vấn sinh viên
        $query_args = [
            'post_type'      => SM_Post_Type::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
            'orderby'        => 'title',
            'order'          => $order,
            'no_found_rows'  => false,
        ];

        $sinh_vien_query = new WP_Query( $query_args );

        // Bắt đầu output buffering
        ob_start();

        if ( ! $sinh_vien_query->have_posts() ) {
            echo '<p class="sm-empty">' . esc_html__( 'Chưa có sinh viên nào được thêm vào hệ thống.', 'student-manager' ) . '</p>';
        } else {
            $tong_so = $sinh_vien_query->found_posts;
            ?>
            <div class="sm-wrapper">
                <div class="sm-header">
                    <h3 class="sm-title">
                        <?php esc_html_e( 'Danh Sách Sinh Viên', 'student-manager' ); ?>
                    </h3>
                    <span class="sm-count">
                        <?php
                        printf(
                            /* translators: %d: total students */
                            esc_html__( 'Tổng số: %d sinh viên', 'student-manager' ),
                            intval( $tong_so )
                        );
                        ?>
                    </span>
                </div>

                <div class="sm-table-wrap">
                    <table class="sm-table">
                        <thead>
                            <tr>
                                <th class="sm-col-stt"><?php esc_html_e( 'STT',      'student-manager' ); ?></th>
                                <th class="sm-col-mssv"><?php esc_html_e( 'MSSV',    'student-manager' ); ?></th>
                                <th class="sm-col-hoten"><?php esc_html_e( 'Họ tên', 'student-manager' ); ?></th>
                                <th class="sm-col-lop"><?php esc_html_e( 'Lớp / Chuyên ngành', 'student-manager' ); ?></th>
                                <th class="sm-col-ngaysinh"><?php esc_html_e( 'Ngày sinh', 'student-manager' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stt = 1;
                            while ( $sinh_vien_query->have_posts() ) :
                                $sinh_vien_query->the_post();
                                $post_id   = get_the_ID();
                                $mssv      = get_post_meta( $post_id, SM_Meta_Box::META_MSSV,     true );
                                $lop_key   = get_post_meta( $post_id, SM_Meta_Box::META_LOP,      true );
                                $ngay_sinh = get_post_meta( $post_id, SM_Meta_Box::META_NGAYSINH, true );

                                // Format ngày sinh sang dd/mm/yyyy
                                $ngay_hien_thi = '';
                                if ( $ngay_sinh ) {
                                    $date = DateTime::createFromFormat( 'Y-m-d', $ngay_sinh );
                                    $ngay_hien_thi = $date ? $date->format( 'd/m/Y' ) : $ngay_sinh;
                                }

                                // Lấy tên chuyên ngành đầy đủ
                                $lop_label = SM_Meta_Box::get_chuyen_nganh_label( $lop_key );
                            ?>
                            <tr class="<?php echo ( $stt % 2 === 0 ) ? 'sm-row-even' : 'sm-row-odd'; ?>">
                                <td class="sm-col-stt"><?php echo esc_html( $stt ); ?></td>
                                <td class="sm-col-mssv">
                                    <strong><?php echo esc_html( $mssv ?: '—' ); ?></strong>
                                </td>
                                <td class="sm-col-hoten">
                                    <?php the_title(); ?>
                                </td>
                                <td class="sm-col-lop">
                                    <?php if ( $lop_key ) : ?>
                                        <span class="sm-badge sm-badge--<?php echo esc_attr( strtolower( $lop_key ) ); ?>">
                                            <?php echo esc_html( $lop_label ); ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="sm-empty-cell">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="sm-col-ngaysinh">
                                    <?php echo esc_html( $ngay_hien_thi ?: '—' ); ?>
                                </td>
                            </tr>
                            <?php
                                $stt++;
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </tbody>
                    </table>
                </div><!-- .sm-table-wrap -->
            </div><!-- .sm-wrapper -->
            <?php
        }

        return ob_get_clean();
    }
}
