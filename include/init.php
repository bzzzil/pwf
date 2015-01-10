<?php
/**
 * Initialization routines called by index.php, ajax.php
 * 
 * @package Core
 * 
 * @copyright Vasily Melenchuk
 */
	if (!defined('NORMAL_START')) die();

	mb_internal_encoding("UTF-8");

	require_once("variables.php");

	// debug info
	if (DEBUG)
	{
		ini_set('display_errors',1);
	}
	else
	{
		ini_set('display_errors',0);
	}
	error_reporting(E_ALL ^ E_NOTICE);

	require_once("functions.php");

	header("HTTP/1.1 200 OK");
	header('Server:');
	header('X-Powered-By:');
	header('Cache-Control:');
	header('Pragma:');
	header('Content-Type: text/html; charset=utf-8');

	$pageParams = preg_split("/\//",$_SERVER['REQUEST_URI'], -1, PREG_SPLIT_NO_EMPTY);
	if ($pageParams[count($pageParams)-1][0] == '?')
		unset($pageParams[count($pageParams)-1]);

	if ( get_magic_quotes_gpc() )
	{
		$_POST   = stripArray($_POST);
		$_GET    = stripArray($_GET);
		$_COOKIE = stripArray($_COOKIE);
	}


	// Determine page language
	$languages = array('en', 'ru');	// Supported interface languages list, first language - default

	if ( !in_array($pageParams[0], $languages) ) 
	{
		// Determine user's browser language
		$userLangs = preg_split("/[,]/", $_SERVER['HTTP_ACCEPT_LANGUAGE'], -1, PREG_SPLIT_NO_EMPTY);
		if ( is_array($userLangs) ) 
		{
			foreach ( $userLangs AS $lang_id=>$langInfo )
			{
				$langData	=	Preg_Split ("/[;]/", Preg_Replace ("/^\s{0,}/", "", $langInfo));
				$langCode	=	SubStr($langData[0], 0, 2);
				if (in_array( $langCode, $languages) )
				{
					Header("Location: /".$langCode."/".implode('/',$pageParams).(count($pageParams)?"/":""));
					exit();
				}
			}
		}
		Header("Location: /".$languages[0]."/".implode('/',$pageParams).(count($pageParams)?"/":""));
		exit();
	}
	$lang = substr(preg_replace("/[^a-z]/","",$pageParams[0]),0,2);	// Useless, but...

	require_once("lang/".$lang."/global.php");
	require_once('include/template.php');

	// basically we do not connect to db, only on demand
	//require_once("include/db.php");
?>