<?php
/**
 * Smarty function 'set'
 * 
 * @copyright Copyright (c) 2008, S & S Royal Limited
 * @author Vasily Melenchuk
 * @package Core
 * @subpackage Plugins
 * 
 */
/**
 * 'set' function realization
 *
 * @param array $params
 * @param Smarty $smarty
 * @return void
 */
function smarty_function_set($params, &$smarty)
{
	global $pageParameters;
	if (!is_a($smarty, 'Smarty_Internal_Template'))
	{
		return;
	}

	if (isset($params['title'])) {
		$pageParameters['title'][] = $params['title'];
	}
	if (isset($params['description'])) {
		$pageParameters['description'][] = $params['description'];
	}
	if (isset($params['keywords'])) {
		$pageParameters['keywords'][] = $params['keywords'];
	}
}
?>