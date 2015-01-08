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

	// Determine page language
	$languages = array('en', 'ru');	// Supported interface languages list, first language - default

	if (!in_array($pageParams[0], $languages)) {
		// Determine user's browser language
		$userLangs = preg_split("/[,]/", $_SERVER['HTTP_ACCEPT_LANGUAGE'], -1, PREG_SPLIT_NO_EMPTY);
		if (is_array($userLangs)) {
			foreach ($userLangs AS $lang_id=>$langInfo)
			{
				$langData	=	Preg_Split ("/[;]/", Preg_Replace ("/^\s{0,}/", "", $langInfo));
				$langCode	=	SubStr($langData[0], 0, 2);
				if (in_array($langCode, $languages))
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

	// Render page
	DrawTemplate('common.page_top', $page_top_params);
	
	if (!strlen($pageParams[0]))
	{
		// Load index module
		require_once('modules/index/index.php');
	}
	else if (preg_match("/^[a-z]+$/",$pageParams[0]) && file_exists('modules/'.$pageParams[0].'/'.$pageParams[0].'.php'))
	{
		// Load desired module
		require_once('modules/'.$pageParams[0].'/'.$pageParams[0].'.php');
	} 
	else 
	{
		// Load static pages module
		require_once('modules/static/static.php');
	}

	DrawTemplate('common.page_bottom');
?>