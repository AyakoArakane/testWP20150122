
<?php
/*
Plugin Name: MainMagazineOff
Plugin URI: http://www.example.com/plugin
Description: メルマガ購読をオフにするプラグイン
Author: R-unit
Version: 0.1
Author URI: http://www.example.com
*/

//テスト用 
add_shortcode('hello','hello_wp');
function hello_wp() {
	return 'Hello wordpress';
}
// [mailoff]というショートコードで呼出し
add_shortcode('mailoff','mail_off');

/* 呼出し方
 * 作成したページに[mailoff]というショートコードを登録して、パラメータにuser_idを付けて呼び出す。
  サンプルURL：http://dev.mesh.jp/index.php/mailoff/?user_id=2
*/
function mail_off() {
     global $wpdb;
     $user_id = htmlspecialchars($_GET['user_id']);
     $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->usermeta WHERE meta_key = 'mailmagazine' AND user_id = %d", $user_id));

     $wpdb->update(
      $wpdb->usermeta,
      array( 'meta_value' => 0 ),
      array( 'umeta_id' => $result->umeta_id )
     );
     return 'メルマガをオフにしました';
}
