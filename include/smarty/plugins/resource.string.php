<?php
/**
 * ���������� Smarty ��� ��������� ��������� ��������
 * �������������:
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
 * ���������� ������ �������
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
	 * �������� ����/����� ��������� ����������� �������.
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
         * ���������� ������������ �������. �� ���������, ��� ������� ��������� �����������
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
	 * �� ������������ � ��������
	 *
	 * @param string $tpl_name
	 * @param Smarty $smarty_obj
	 */
	function smarty_resource_string_trusted($tpl_name, &$smarty_obj) 
	{ 
	}
?>