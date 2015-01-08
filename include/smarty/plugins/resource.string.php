<?php
/**
 * Расширение Smarty для обработки строковых шаблонов
 * Использование:
 * <code>
 * 		$smarty->register_resource("string", array("str_get_template", "str_get_timestamp", "str_get_secure", "str_get_trusted")); 
 * 		$smarty->assign ('var', 'value');
 * 		$smarty->display('string:The var value is {$var}'); 
 * </code>
 *  
 * @package Core
 * @subpackage Plugins
 */
/**
 * Возвращает данные шаблона
 * 
 * @param string $tpl_name
 * @param mixed $tpl_source
 * @param Smarty $smarty_obj
 * @return bool
 */
	function smarty_resource_string_source ($tpl_name, &$tpl_source, &$smarty_obj) 
	{
		if ($tpl_name)
		{
			$tpl_source	=	$tpl_name;
			return  true;
		}
		else
		{
			$tpl_source	=	'';
			return true;
		}
	} 	
	/**
	 * Получает дату/время последней модификации шаблона.
	 *
	 * @param string $tpl_name
	 * @param mixed $tpl_source
	 * @param Smarty $smarty_obj
	 * @return bool
	 */
	function smarty_resource_string_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj) 
	{ 
		$tpl_timestamp	=	CMF_TIME;
		return true;
        } 
        /**
         * Определяет безопасность шаблона. По умолчанию, все шаблоны считаются безопасными
         *
         * @param string $tpl_name
         * @param Smarty $smarty_obj
         * @return bool
         */
	function smarty_resource_string_secure($tpl_name, &$smarty_obj) 
	{ 
	    return true; 
	} 
	/**
	 * Не используется в шаблонах
	 *
	 * @param string $tpl_name
	 * @param Smarty $smarty_obj
	 */
	function smarty_resource_string_trusted($tpl_name, &$smarty_obj) 
	{ 
	}
?>