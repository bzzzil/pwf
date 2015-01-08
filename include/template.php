<?php
	if (!defined('NORMAL_START')) die();
/**
 * Simple Templates Processing Engine
 * 
 * @package Core
 * @subpackage Templates
 * 
 * @copyright Vasily Melenchuk
 */

	/**
	 * Empty page buffer and disable output buffering
	 */
	function cleanPageBuffer()
	{
		global $pageNoPostProcess;
		$pageNoPostProcess = 1;
		ob_end_clean();
	}

	ob_start("templatePostParse");

	// Initialize Smarty
	include('include/smarty/Smarty.class.php');

	$smarty = new Smarty();
	$smarty->template_dir = "templates/";
	$smarty->compile_dir  = "include/smarty/cache/";

	$smarty->force_compile = true;

	if ($pageParams)
	{
		$smarty->assign('path', implode('/',$pageParams).'/');
		$smarty->assign('lang', $lang);
	}

	$smarty->assign('i18n', $i18n);


	/**
	 * Render template with Smarty
	 *
	 * @param string $template_name
	 * @param mixed $template_vars
	 *
	 * @return none
	 */
	function DrawTemplate($template_name, $template_vars = array())
	{
		global $smarty, $pageParams, $pageNoPostProcess;
		$smarty->assign($template_vars);
		echo $smarty->fetch($template_name.".tpl");
	} 

	/**
	 * Final parsing of output file
	 *
	 * Called as callback for output buffering
	 *
	 * @param string $buffer
	 */
	function templatePostParse($output)
	{
		global $pageParameters, $pageNoPostProcess, $extraHeaders, $smarty;

		if ($pageNoPostProcess) return;

		// Prepare keywords list
		$Keywords = array();
		if (!is_array($pageParameters['keywords']) || !count($pageParameters['keywords']))
			$pageParameters['keywords'] = array();
		foreach ($pageParameters['keywords'] as $line) {
			$NewKeywords = preg_split("/[\s,]+/", $line, 0, PREG_SPLIT_NO_EMPTY);
			$Keywords = array_merge($Keywords, $NewKeywords);
		}

		if (!is_array($pageParameters['title']) || !count($pageParameters['title']))
			$pageParameters['title'] = array('');
		if (!is_array($pageParameters['description']) || !count($pageParameters['description']))
			$pageParameters['description'] = array('');

		// Step2 parsing
		$output = strtr(
			$output,
			array(
				'#Title#'       => end($pageParameters['title']),
				'#Description#' => end($pageParameters['description']),
				'#Keywords#'    => implode(" ",array_unique($Keywords)),
				'#ExtraHeaders#'=> $extraHeaders,
			)
		);
		
		// Check if the browser supports gzip encoding
		$acceptExcodings = explode(",",$_SERVER['HTTP_ACCEPT_ENCODING']);
		if (!headers_sent() && array_search('gzip', $acceptExcodings)!==false) {
			// Return GZipped content
			header("Content-Encoding: gzip");
			return gzencode($output);
		} else {
			// Return normal content
			return $output;
		}
	}
?>
