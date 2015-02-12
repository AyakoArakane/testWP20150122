<?php
/*
 * Dynamo DB でセッションをコントロールするクラス
 */
class ControlSession {
	private static $instance;
	private static $options; // this plugin options
	private static $s3;                // S3 Object

	const META_KEY    = '_dynamo_session_ids';
	const DEBUG_MODE  = false;
	const TEXT_DOMAIN = 'ctrl-aws';
	const LIMIT       = 100;

	private function __construct() {}

	public static function get_instance() {
		if( !isset( self::$instance ) ) {
			$c = __CLASS__;
			self::$instance = new $c();    
		}

		return self::$instance;
	}

	public function init($options){
		self::$options = $options;
	}

	public function add_hook(){
		add_action('init', array($this,'init_dynamo_sessions'));

	}

	static public function plugin_basename() {
		return plugin_basename(dirname(dirname(__FILE__)).'/plugin.php');
	}
	public function init_dynamo_sessions() {
		$session_table = isset(self::$options['session_table']) ? self::$options['session_table'] : false;
		$access_key = isset(self::$options['access_key']) ? self::$options['access_key'] : false;
		$secret_key = isset(self::$options['secret_key']) ? self::$options['secret_key'] : false;
		$region = isset(self::$options['region']) ? self::$options['region'] : false;

		$dynamo = Dynamo_helper::get_instance();
		$dynamo->init(
			$access_key, 
			$secret_key, 
			$region 
		);
		#die($region);
		if ($dynamo) {  
			$dynamo->set_session($access_key,$secret_key,$dynamo,$session_table); 
		}


		if (!session_id()) {
			/* ユーザーがブラウザを閉じるまで
			 * ルート以下すべての階層でセッションを開始
			 * サブドメイン間でセッションを継続
			 */
			//session_set_cookie_params (0 , '/', '.'.DOMAIN_CURRENT_SITE);
			session_start();
			if (isset($_SESSION["username"])) {
				echo $_SESSION["username"];
			} else {
				$_SESSION["username"] = "arakane";
			}
			session_write_close();
		}
	}
	public function is_dynamo_session($session) {
		$session_table = isset(self::$options['session_table']) ? self::$options['session_table'] : false;
		$access_key = isset(self::$options['access_key']) ? self::$options['access_key'] : false;
		$secret_key = isset(self::$options['secret_key']) ? self::$options['secret_key'] : false;
		$region = isset(self::$options['region']) ? self::$options['region'] : false;

		$dynamo = Dynamo_helper::get_instance();
		$dynamo->init(
			$access_key, 
			$secret_key, 
			$region 
		);
		if ($dynamo) {  
			$rsession = $dynamo->get_session($session,$session_table); 
			if (isset($rsession)) {
				return true;
			}	
		}
		return false;


        }




}
