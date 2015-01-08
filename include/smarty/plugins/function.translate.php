<?php
/**
 * Smarty function 'translate'
 * 
 * This function translates given term using current language
 * Term must be stored in format "<group>/<term>" and provided
 * via "term" parameter
 * 
 * @copyright Copyright (c) 2008, S & S Royal Limited
 * @author Vasily Melenchuk
 * @package Core
 * @subpackage Plugins
 * 
 * <code>
 * {translate term="Language/display_name" format="html"}
 * </code>
 */
/**
 * Реализация модуля
 *
 * @param array $params
 * @param Smarty $smarty
 * @return void
 */
function smarty_function_translate($params, &$smarty)
{
	global $Language, $lang;

	if (!is_a($smarty, 'Smarty'))
	{
		return;
	}

	if (!isset($params['term'])) {
		$smarty->trigger_error("translate: missing 'term' parameter"); 
		return;
	}
	if (!isset($params['format']))	// Default value;
		$params['format'] = 'text';
	if (!isset($params['insertas']))	// Default value;
		$params['insertas'] = 'default';

	//Parse current term
	$term = preg_split("/\//", $params['term']);
	if (!$term[0] || !$term[1]) {
		$smarty->trigger_error("translate: 'term' parameter is invalid. Parameter format must be: '[group]/[term]'"); 
		return;
	}

	if (isset($Language[$term[0]][$term[1]]))
		$string = $Language[$term[0]][$term[1]];
	else {
		// Trying to load language block
		if (!isset($Language[$term[0]]))
			include_once('language/'.$lang.'/'.$term[0].'.php');

		if (isset($Language[$term[0]][$term[1]]))
			$string = $Language[$term[0]][$term[1]];
		else
			$string = $term[0]."/".$term[1];
	}

	// Inserting variables
	if (isset($params['var1'])) {
		$search  = array();
		$replace = array();
		foreach ($params as $var=>$val) {
			if (preg_match("/^var(\d{1,3})$/", $var, $res)) {
				$search [$res[1]] = "/%".$res[1]."/";
				$replace[$res[1]] = $val;
			}
		}
		$string = preg_replace($search, $replace, $string);
	}
	
	// Applying format
	if ($params['format']=="html") {
		// Do nothing
	} elseif ($params['format']=="text") {
		$string = htmlspecialchars(strip_tags($string));
	} else {
		$smarty->trigger_error("translate: invalid 'format' parameter value provided!"); 
	}
	
	// Applying "insert as"
	
	if ($params['insertas'] == "default") {
		// Do nothing
	} elseif ($params['insertas'] == "js") {
		$string = strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
		// @todo implement this mode
	} elseif ($params['insertas'] == "escape") {
		$string = str_replace("\"", "&quot;", $string);
		// @todo implement this mode
	} elseif ($params['insertas'] == "quoted") {
		// @todo implement this mode
	}
	
	if (isset($params['var'])) {
		
		$smarty->_tpl_vars[$params['var']] = $string;
		return; 
	}
	
	
	return $string;
}
?>