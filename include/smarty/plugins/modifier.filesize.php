<?php
/**
 * Smarty plugin - filesize modifier
 * 
 * @author Vasily Melenchuk
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Модификатор filesize: вывод размеров для файлов
 *
 * @param int/float $size
 * @param int $precision - требуемая точность (знаков после запятой)
 * @return string
 */
function smarty_modifier_filesize($size, $precision=2)
{
	if(!is_numeric($precision) || !is_numeric($size))
	{
		// Что-то явно не так
		trigger_error('Modifier filesize: Invalid input params');
		return '';
	}

	// Находим подходящую величину
	$result = '?';
	$multiplier = 1;
	while ($multiplier <= 1099511627776) // 1 Тб в байтах. Пока хватит
	{
		if ($size/$multiplier<1) 
		{
			break;
		} else {
			$result = round($size/$multiplier, $precision).'&nbsp;'.getTranslation('global','filesize_'.$multiplier);
		}

		$multiplier *= 1024;
	}
	
	// Финальный штрих: разделитель дробной части
	$result = str_replace(".", getTranslation('global','decimal_separator'), $result);

	return $result;
}
?>