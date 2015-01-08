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

	if (get_magic_quotes_gpc())
	{
		$_POST   = stripArray($_POST);
		$_GET    = stripArray($_GET);
		$_COOKIE = stripArray($_COOKIE);
	}

	include "include/db.php";
?>