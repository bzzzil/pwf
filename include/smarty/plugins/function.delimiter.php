<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {delimiter} plugin
 *
 * Type:     function<br>
 * Name:     delimiter<br>
 * Purpose:  draw delimiter line
 * @return string
 */
function smarty_function_delimiter($params, &$smarty)
{
	return "<div class=\"blueline\"></div>";
}
?>
