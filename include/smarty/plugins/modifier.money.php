<?php
	/**
	 * Плагин для вывода сумм в выбранном унифицированном формате
	 * 
	 * @copyright Copyright (c) 2008
	 * @author Vasily Melenchuk
	 * @package Core
	 * @subpackage Plugins
	 * 
	 */

/**
 * Реализация модуля
 *
 * @param string $string
 * @return mixed
 */
function smarty_modifier_money($string)
{
	$string = sprintf("%0.2f", $string);

	$string = str_replace(".", getTranslation('global','decimal_separator'), $string);

	$i = strlen($string)-3;
	$signs = 0;
	$sep = getTranslation('global','thousands_separator');
	// 1418,30;
	while ($i>0) {
		$signs++;
		$i--;

		if (($signs%3)==1 && $signs!=1) {
			$string = substr($string, 0, $i+1).$sep.substr($string, $i+1);
			//$i -= strlen($sep);
		}
	}

	return $string;
}
?>