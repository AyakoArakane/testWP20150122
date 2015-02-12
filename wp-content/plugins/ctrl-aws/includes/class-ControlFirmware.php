<?php
/*
 * S3でファームウェアを管理するクラス
 */
class ControlFirmware {
	private static $instance;
	private static $options; // this plugin options
	private static $s3;                // S3 Object

	const FW_DOWNLOAD_URL    = '/fwdownload';

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
		add_action('init', array($this,'download_firmware'));
		#add_shortcode('download_firmware', array($this,'download_firmware'));

	}
	/* ファームウェアダウンロード機能
	 * URL: /fwdownload?session=xxxx&tab=xxxx
	 * パラメータ[session]:セッションID
	 * パラメータ[tab]:ダウンロードするタブのタイプ。gpio,button,acceleration,ledの4種類
	 */
	public function download_firmware(){
		//パラメータチェック
		$action = parse_url($_SERVER["REQUEST_URI"])['path'];
		$session =  (isset($_GET['session'])) ? $_GET['session'] : '';
		$tab =  (isset($_GET['tab'])) ? $_GET['tab'] : '';
		$args = ['gpio','button','acceleration','led'];
		if ($session =='' or $tab == '' or array_search($tab,$args) === FALSE){
	         $this->error_action('E01','Parameter is incorrect.');
		}
		try{
			//セッションチェック
			$cse = ControlSession::get_instance();
			$issess = $cse->is_dynamo_session($session);
			if ($issess == false){
				$this->error_action('E02','your session is invalid or expired');
			}	

			//ファイルダウンロード
			if ($action==self::FW_DOWNLOAD_URL){
				$s3_bucket = isset(self::$options['bucket']) ? self::$options['bucket'] : false;
				$s3_url = isset(self::$options['s3_url']) ? self::$options['s3_url'] : false;
				$s3_key = isset(self::$options['s3_firmfire_filename_'.$tab]) ? self::$options['s3_firmfire_filename_'.$tab] : false;
				$file = $s3_key;
				if (!$s3_bucket || !$s3_url)
					return $content;
				$this->s3_download($file, $s3_bucket, $s3_key);

				// ヘッダ
				header("Content-Type: application/octet-stream");
				// ダイアログボックスに表示するファイル名
				header("Content-Disposition: attachment; filename=$file");
				// 対象ファイルを出力する。
				readfile($file);
				// ローカルの対象ファイルを削除する。
				unlink($file);
				exit;
			}
		}catch(Exception $e){
			//var_dump($e);
			$this->error_action('E99',$e->getMessage());
			exit;
		}
	}	
	public function error_action($e_code,$e_message) {
		$args = [
			'ErrorCode'	=> $e_code,
			'ErrorMessage'	=> $e_message,
		];
		$json_value	= json_encode( $args );
		header( 'Content-Type: text/javascript; charset=utf-8' );
		echo $json_value;
		exit;
	}

	// Upload file to S3
	private function s3_upload($filename, $S3_bucket, $S3_key){
		if (!file_exists($filename))
			return false;

		$upload_result = false;
		if ($s3 = $this->s3($S3_bucket)) {
			if ($s3->object_exists($S3_key))
				return true;
			$upload_result = $s3->upload($filename, $S3_key);
		}
		return $upload_result;
	}

	// Download file to S3
	private function s3_download($filename, $S3_bucket, $S3_key){
		$download_result = false;
		if ($s3 = $this->s3($S3_bucket)) {
			if (!$s3->object_exists($S3_key))
				return false;
			$download_result = $s3->download($S3_key, $filename);
		}
		return $download_result;
	}

	// Delete S3 object
	private function s3_delete($S3_bucket, $S3_key){
		$delete_result = false;
		if ($s3 = $this->s3($S3_bucket)) {
			$delete_result =
				$s3->object_exists($S3_key)
				? $s3->delete($S3_key)
				: true;
		}
		return $delete_result;
	}


	// Initializing S3 object
	private function s3($S3_bucket = null){
		if (isset(self::$s3)) {
			if (isset($S3_bucket) && self::$s3->current_bucket() !== $S3_bucket)
				self::$s3->set_current_bucket($S3_bucket);
			return self::$s3;
		}
		if (self::$options) {
			$s3 = S3_helper::get_instance();
			$s3->init(
				isset(self::$options['access_key']) ? self::$options['access_key'] : null,
				isset(self::$options['secret_key']) ? self::$options['secret_key'] : null,
				isset(self::$options['region'])     ? self::$options['region']     : null
				);
			if ($s3 && isset($S3_bucket))
				$s3->set_current_bucket($S3_bucket);
			self::$s3 = $s3;
			return $s3;
		}
		return false;
	}





}
