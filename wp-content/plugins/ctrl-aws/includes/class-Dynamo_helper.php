<?php
require_once(dirname(__FILE__).'/aws.phar');

use Aws\Common\Aws;
use Aws\Common\Enum\Region;
use Aws\DynamoDb\Enum\Type;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Session\SessionHandler;

class Dynamo_helper {
	private static $instance;

	private $dynamo;

	private $options = array(
		'SessionTable' => ''
		//'StorageClass' => 'STANDARD',
		//'ACL' => CannedAcl::PUBLIC_READ,
		);

	private function __construct() {}

	public static function get_instance() {
		if( !isset( self::$instance ) ) {
			$c = __CLASS__;
			self::$instance = new $c();    
		}

		return self::$instance;
	}

	public function init($access_key = null, $secret_key = null, $region = null) {
		if ($access_key && $secret_key) {
			$this->init_dynamo($access_key, $secret_key, $region);
		}
	}

	// get Dynamo object
	public function init_dynamo($access_key, $secret_key, $region = null){
		if ( !isset($region) )
			$region = Region::AP_NORTHEAST_1;
		$dynamo = DynamoDbClient::factory(array(
			'key' => $access_key,
			'secret' => $secret_key,
			'region' => $this->get_region($region),
			));
		$this->dynamo = $dynamo;
		return $dynamo;
	}
	public function set_session($session_table){
		$sessionHandler = SessionHandler::factory(array(
			'dynamodb_client' => $this->dynamo,
			'table_name'      => $session_table,
		));
		$sessionHandler->register();
	}
	public function get_session($session,$session_table){
		$result = $this->dynamo->getItem(array(
			'ConsistentRead' => true,
			'TableName' => $session_table,
			'Key' => array(
				'id'  => array( 'S' => $session)
			)
		));
		$rsession = $result['Item']['id']['S'];
		$rdata = $result['Item']['data']['S'];
		return $rsession;
	}

	public function get_region($region) {
		$region = str_replace('-','_',strtoupper($region));
		$regions = Region::values();
		return
			isset($regions[$region])
			? $regions[$region]
			: false;
	}
	// Initializing dynamo object
	/*
	private function dynamo(){
		if (isset(self::$dynamo)) {
			return self::$dynamo;
		}
		if (self::$options) {
			$dynamo = dynamo_helper::get_instance();
			$dynamo->init(
				isset(self::$options['access_key']) ? self::$options['access_key'] : null,
				isset(self::$options['secret_key']) ? self::$options['secret_key'] : null,
				isset(self::$options['region'])     ? self::$options['region']     : null
				);
			self::$dynamo = $dynamo;
			return $dynamo;
		}
		return false;
	}
	 */

}
