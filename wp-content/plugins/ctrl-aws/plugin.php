<?php
/*
Plugin Name: Control AWS Core
Version: 0.2.2
Plugin URI: 
Description: DynamoDBでセッションをコントロールする。
Author: R-unit
Author URI: 
Text Domain: ctrl-aws
Domain Path: /languages/
*/
if ( !class_exists('S3_helper') )
	require(dirname(__FILE__).'/includes/class-S3_helper.php');
if ( !class_exists('Dynamo_helper') )
	require(dirname(__FILE__).'/includes/class-Dynamo_helper.php');
if ( !class_exists('ControlAws_Admin') )
	require(dirname(__FILE__).'/includes/class-ControlAws_Admin.php');
if ( !class_exists('ControlAws') )
	require(dirname(__FILE__).'/includes/class-ControlAws.php');
if ( !class_exists('ControlSession') )
	require(dirname(__FILE__).'/includes/class-ControlSession.php');


$ctrlaws = ControlSession::get_instance();
$ctrlaws->init(ControlAws_Admin::get_option());
$ctrlaws->add_hook();

if (is_admin()) {
        $ctrlaws_admin = ControlAws_Admin::get_instance();
        $ctrlaws_admin->init();
        $ctrlaws_admin->add_hook();
}
