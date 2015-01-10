<?php
/**
 * Site Core entrypoint
 * 
 * @package Core
 * 
 * @copyright Vasily Melenchuk
 */
	DEFINE('NORMAL_START',1);

	require_once("include/init.php");

	// Render page
	DrawTemplate( 'common.page_top' );
	
	if ( !strlen($pageParams[0]) && file_exists('modules/index/index.php') )
	{
		// Load index module, if it exists
		require_once( 'modules/index/index.php' );
	}
	else if ( preg_match("/^[a-z]+$/",$pageParams[0]) && file_exists('modules/'.$pageParams[0].'/'.$pageParams[0].'.php') )
	{
		// Load desired module
		require_once('modules/'.$pageParams[0].'/'.$pageParams[0].'.php');
	} 
	else 
	{
		// Load static pages module
		require_once('modules/static/static.php');
	}

	DrawTemplate( 'common.page_bottom' );
?>