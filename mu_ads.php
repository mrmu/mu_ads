<?php
/*
Plugin Name: MU ADS
Plugin URI:
Description: 廣告版位
Version: 1.0.1
Author: Audi Lu
Author URI: http://audilu.com/
*/

class MU_ADS {
    static $add_script;

    static function init() {
        add_shortcode('show_ads', array(__CLASS__, 'the_show_ads_func'));

        add_action( 'admin_menu', array(__CLASS__, 'reg_ads_menu_page' ));

        add_action( 'wp_ajax_load_avatar_func', array(__CLASS__, 'load_avatar_func') ); // 針對已登入的使用者
        add_action( 'wp_ajax_nopriv_load_avatar_func', array(__CLASS__, 'load_avatar_func') ); // 針對未登入的使用者

        add_action( 'wp_ajax_do_login_func', array(__CLASS__, 'do_login_func') ); // 針對已登入的使用者
        add_action( 'wp_ajax_nopriv_do_login_func', array(__CLASS__, 'do_login_func') ); // 針對未登入的使用者

        add_action( 'wp_ajax_load_funny_name_func', array(__CLASS__, 'load_funny_name_func') ); // 針對已登入的使用者
        add_action( 'wp_ajax_nopriv_load_funny_name_func', array(__CLASS__, 'load_funny_name_func') ); // 針對未登入的使用者
    }

    function reg_ads_menu_page() {
        $ads_set_page = add_menu_page( 'Banner 設定', 'Banner 設定', 'manage_options', 'mu_ads/mu_ads-admin.php', array(__CLASS__,'ads_settings_func'), '', 91 );
    }

    static function ads_settings_func() 
    {
        $ary_settings = array();

        if (isset($_POST['submit_frm']))
        {
            $ary_settings['lnk_banner_top_1'] = esc_attr($_POST['lnk_banner_top_1']);
            $ary_settings['lnk_single_top_1'] = esc_attr($_POST['lnk_single_top_1']);

            $ary_settings['img_banner_top_1'] = esc_attr($_POST['img_banner_top_1']);
            $ary_settings['img_single_top_1'] = esc_attr($_POST['img_single_top_1']);
            $ary_settings['img_m_banner_top_1'] = esc_attr($_POST['img_m_banner_top_1']);
            $ary_settings['img_m_single_top_1'] = esc_attr($_POST['img_m_single_top_1']);

            update_option("ads_settings", $ary_settings);
            $showmsg = '已儲存變更。';
        }
        $ary_settings = get_option( 'ads_settings' );

        ?>
        <div class="wrap">
            <?php screen_icon('themes'); ?> <h2>ADS 設定</h2>
            <?php
            if ($showmsg){ ?>
                    <div id="message" class="updated below-h2"><p><?php echo $showmsg; ?> </p></div>
            <?php
            }
            ?>
            <form method="POST" action="">
                <input type="hidden" name="submit_frm" value="1">
                <table class="form-table">
                    <?php
                    $ary_setting_itms = array(
                        'lnk_banner_top_1' => 'Top Banner 連結',
                        'img_banner_top_1' => 'Top Banner 圖片 <br>(1170x138)',
                        'img_m_banner_top_1' => 'Top Banner 行動版圖片 <br>(370x98)',
                        'lnk_single_top_1'=>'單篇文章頁 Top版位 的連結',
                        'img_single_top_1' => '單文頁 Top版位圖片 <br>(1170x138)',
                        'img_m_single_top_1' => '單文頁 Top版位行動版圖片<br>(370x98)'
                    );

                    foreach ($ary_setting_itms as $key => $val){
                        ?>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo $key?>"><?php echo $val?> </label>
                            </th>
                            <td>
                                <input type="text" name="<?php echo $key?>" size="105" value="<?php echo $ary_settings[$key];?>" />
                            </td>
                        </tr>
                        <?php
                    }

                    ?>
                    <script>
                    var $ = jQuery.noConflict();
                    $(function(){
                        var custom_uploader;

                        $('.select_img').click(function(e) {

                            e.preventDefault();

                            //If the uploader object has already been created, reopen the dialog
                            if (custom_uploader) {
                                custom_uploader.open();
                                return;
                            }

                            //Extend the wp.media object
                            custom_uploader = wp.media.frames.file_frame = wp.media({
                                title: 'Choose Image',
                                button: {
                                    text: 'Choose Image'
                                },
                                multiple: false
                            });

                            //When a file is selected, grab the URL and set it as the text field's value
                            custom_uploader.on('select', function() {
                                attachment = custom_uploader.state().get('selection').first().toJSON();
                                $('#upload_image').val(attachment.url);
                            });

                            //Open the uploader dialog
                            custom_uploader.open();

                        });
                    });
                    </script>
                    <tr valign="top">
                        <th scope="row"></th>
                        <td><input class="button button-primary" type="submit" value="儲存變更"></td>
                    </tr>
                </table>
            </form>
        </div>
        <?php
    }

    // [show_ads]
    static function the_show_ads_func( $atts )
    {
        self::$add_script = true;

        $ary_settings = get_option( 'ads_settings' );
        $lnk_banner_top_1 = $ary_settings['lnk_banner_top_1'];
        $lnk_single_top_1 = $ary_settings['lnk_single_top_1'];
        $img_banner_top_1 = $ary_settings['img_banner_top_1'];
        $img_m_banner_top_1 = $ary_settings['img_m_banner_top_1'];
        $img_single_top_1 = $ary_settings['img_single_top_1'];
        $img_m_single_top_1 = $ary_settings['img_m_single_top_1'];

        $set = shortcode_atts( array(
                'mode' => '',
                'size' => ''
        ), $atts );

        if ($set['mode']=='single_top_1') {
            if ($set['size']=='mobile'){
                if ($img_m_single_top_1){
                        echo '<a href="'.$lnk_single_top_1.'"><img src="'.$img_m_single_top_1.'" style="width:100%"></a>';
                }
            }else{
                if ($img_single_top_1){
                        echo '<a href="'.$lnk_single_top_1.'"><img src="'.$img_single_top_1.'" style="width:100%"></a>';
                }
            }
        }

        if ($set['mode']=='banner_top_1') {
            if ($set['size']=='mobile'){
                if ($img_m_banner_top_1){
                        echo '<a href="'.$lnk_banner_top_1.'"><img src="'.$img_m_banner_top_1.'" style="width:100%"></a>';
                }
            }else{
                if ($img_banner_top_1){
                        echo '<a href="'.$lnk_banner_top_1.'"><img src="'.$img_banner_top_1.'" style="width:100%"></a>';
                }
            }
        }

    }

}
MU_ADS::init();