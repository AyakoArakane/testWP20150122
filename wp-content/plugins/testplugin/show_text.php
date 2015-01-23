
<?php
/*
Plugin Name: Show Text
Plugin URI: http://www.example.com/plugin
Description: テキストを表示するだけのプラグイン
Author: my name
Version: 0.1
Author URI: http://www.example.com
*/
 
/*function show_text() {
    echo 'おはようございます。';
}*/

class ShowText {
    function __construct() {
      add_action('admin_menu', array($this, 'add_pages'));
    }
    function add_pages() {
      add_menu_page('テキスト設定','テキスト設定',  'level_8', __FILE__, array($this,'show_text_option_page'), '', 26);
    }


function show_text_option_page() {
    //$_POST['showtext_options'])があったら保存
    if ( isset($_POST['showtext_options'])) {
        check_admin_referer('shoptions');
        $opt = $_POST['showtext_options'];
        update_option('showtext_options', $opt);
	?><div class="updated fade"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
    <?php
    }
    ?>
    <div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div><h2>テキスト設定</h2>
        <form action="" method="post">
            <?php
            wp_nonce_field('shoptions');
            $opt = get_option('showtext_options');
            $show_text = isset($opt['text']) ? $opt['text']: null;
            ?> 
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="inputtext">テキスト</label></th>
                    <td><input name="showtext_options[text]" type="text" id="inputtext" value="<?php  echo $show_text ?>" class="regular-text" /></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
        </form>
    <!-- /.wrap --></div>
    <?php
}
// テンプレートに表示するためのメソッド 
// 呼出し方：<?php echo esc_html($showtext->get_text()); 
function get_text() {
  $opt = get_option('showtext_options');
  return isset($opt['text']) ? $opt['text']: null;
}


}
$showtext = new ShowText;


