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
if ( !class_exists('ControlSession') )
	require(dirname(__FILE__).'/includes/class-ControlSession.php');
if ( !class_exists('ControlFirmware') )
	require(dirname(__FILE__).'/includes/class-ControlFirmware.php');


$ctrlsession = ControlSession::get_instance();
$ctrlsession->init(ControlAws_Admin::get_option());
$ctrlsession->add_hook();

$ctrlfw = ControlFirmware::get_instance();
$ctrlfw->init(ControlAws_Admin::get_option());
$ctrlfw->add_hook();

if (is_admin()) {
        $ctrlaws_admin = ControlAws_Admin::get_instance();
        $ctrlaws_admin->init();
        $ctrlaws_admin->add_hook();
}
