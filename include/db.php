<?php
	if (!defined('NORMAL_START')) die();
	
	require_once("adodb5/adodb.inc.php");
	
	$db = NewADOConnection('mysql');

	if (DEBUG)
	{
		// Enable debugging
	//	$db->debug = true;
	}

	if (!$db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
	{
		Error_501("SQL connection failed");
		exit();
	}
	$db->SetCharSet('utf8');
?>
