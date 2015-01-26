
<?php
/*
Plugin Name: AwsTest
Plugin URI: http://www.example.com/plugin
Description: テキストを表示するだけのプラグイン
Author: my name
Version: 0.1
Author URI: http://www.example.com
*/
//define('__DIR__', ABSPATH.'aws-php/vendor');
//require __DIR__.'/autoload.php';
include ABSPATH.'aws-php/sample.php';


class AwsTest {
    function __construct() {
      add_action('admin_menu', array($this, 'add_pages'));
    }
    function add_pages() {
      add_menu_page('AWS設定','AWS設定',  'level_8', __FILE__, array($this,'show_text_option_page'), '', 27);
    }


function show_text_option_page() {
    ?>
    <div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div><h2>AWS設定</h2>
        <form action="" method="post">
            <?php
            wp_nonce_field('shoptions');
            $opt = get_option('showtext_options');
            //$show_text = isset($opt['text']) ? $opt['text']: null;
            $show_text = __DIR__;
            ?> 
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="inputtext">テキスト</label></th>
                    <td><?php  echo $show_text ?></td>
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
$awstest = new AwsTest;


