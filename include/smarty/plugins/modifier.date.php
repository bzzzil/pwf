<?php
/**
 * Smarty plugin - date modifier
 *
 * @author Vasily Melenchuk
 * @package Smarty
 * @subpackage plugins
 */

/**
 *  date: unix_timestamp, date, datetime =>    
 *
 * @param string $string
 * @param string $format - 'date', 'time', 'datetime'
 * @return string
 */
function smarty_modifier_date($string, $format='datetime')
{
	// All datetime => timestamp
	if (!is_numeric($string)) 
	{
		//   date  datetime
		$timestamp = strtotime($string);
		if ($timestamp!==FALSE && $timestamp!=-1 /*PHP<5.1*/) 
		{
			$string = $timestamp;
		}
	}

	// Relative date?
	if ($format=="relative")
	{
		return smarty_modifier_date_relative($string);
	}
	elseif ($format=="interval")
	{
		return smarty_modifier_date_interval($string);
	}

	$format = getTranslation('global', $format.'_format');
	if (!$format) 
	{
		trigger_error('Modifier date: invalid format specified!');
		return '';
	}

	//    unix_timestamp?
	if (!is_numeric($string)) 
	{
		//   ? - ...
		trigger_error('Invalid data for date modifier!');
		return '';
	}

	$format = str_replace('%B', getTranslation('Month',date('n', $string)), $format);	
	$format = str_replace('%e', date("j", $string), $format);	

	return strftime($format, $string);
}

/**
 *  Display date as interval
 *
 * @param string $string
 * @return string
 */
function smarty_modifier_date_interval($delta)
{
	require_once("modifier.declension.php");
	if ($delta<0)
	{
		trigger_error('Interval for future is not supported!');
		return '';
	}

	$out = "";

	$val = floor($delta/(365*24*60*60));
	if ($val > 0)
	{
		$delta -= $val * (365*24*60*60);
		$out .= $val." ".smarty_modifier_declension($val, getTranslation('global', 'date_interval_years'))." ";
	}

	$val = floor($delta/(30*24*60*60));
	if ($val > 0)
	{
		$delta -= $val * (30*24*60*60);
		$out .= $val." ".smarty_modifier_declension($val, getTranslation('global', 'date_interval_months'))." ";
	}

	$val = floor($delta/(24*60*60));
	if ($val > 0)
	{
		$delta -= $val * (24*60*60);
		$out .= $val." ".smarty_modifier_declension($val, getTranslation('global', 'date_interval_days'))." ";
	}

	$val = floor($delta/(60*60));
	if ($val > 0)
	{
		$delta -= $val * (60*60);
		$out .= $val." ".smarty_modifier_declension($val, getTranslation('global', 'date_interval_hours'))." ";
	}

	$val = floor($delta/(60));
	if ($val > 0)
	{
		$delta -= $val * (60);
		$out .= $val." ".smarty_modifier_declension($val, getTranslation('global', 'date_interval_minutes'))." ";
	}

	if ($delta > 0)
	{
		$out .= $delta." ".smarty_modifier_declension($delta, getTranslation('global', 'date_interval_seconds'))." ";
	}

	return $out;
}

/**
 *  Display date as relative
 *
 * @param string $string
 * @return string
 */
function smarty_modifier_date_relative($string)
{
	require_once("modifier.declension.php");
	$delta = time() - $string;
	if ($delta<0)
	{
		trigger_error('Relative date for future is not supported!');
		return '';
	}

	if ($delta<10)	// Just now
	{
		return getTranslation('global', 'date_relative_now');
	}
	elseif ($delta < 60)	// Couple of seconds
	{
		return ($delta==1?getTranslation('global', 'date_relative_one'):$delta." ").smarty_modifier_declension($delta, getTranslation('global', 'date_relative_seconds'))." ".getTranslation('global', 'date_relative_ago');
	}
	elseif ($delta<60*60)	// Couple of minutes
	{
		$m = round($delta/60);
		return ($m==1?getTranslation('global', 'date_relative_one'):$m." ").smarty_modifier_declension($m, getTranslation('global', 'date_relative_minutes'))." ".getTranslation('global', 'date_relative_ago');
	}
	elseif ($delta<24*60*60)	// Couple of hours
	{
		$h = round($delta/(60*60));
		return ($h==1?getTranslation('global', 'date_relative_one'):$h." ")." ".smarty_modifier_declension($h, getTranslation('global', 'date_relative_hours'))." ".getTranslation('global', 'date_relative_ago');
	}
	elseif ($delta < 7*24*60*60)
	{
		$d = round($delta/(24*60*60));	// Couple of days
		return ($d==1?getTranslation('global', 'date_relative_one'):$d." ").smarty_modifier_declension($d, getTranslation('global', 'date_relative_days'))." ".getTranslation('global', 'date_relative_ago');
	}
	elseif ($delta < 30*24*60*60)
	{
		$w = round($delta/(7*24*60*60));	// Couple of weeks
		return ($w==1?getTranslation('global', 'date_relative_one'):$w." ")." ".smarty_modifier_declension($w, getTranslation('global', 'date_relative_weeks'))." ".getTranslation('global', 'date_relative_ago');
	}
	elseif ($delta < 350*24*60*60)
	{
		$m = round($delta/(30*24*60*60));	// Couple of months
		return ($m==1?getTranslation('global', 'date_relative_one'):$m." ").smarty_modifier_declension($m, getTranslation('global', 'date_relative_months'))." ".getTranslation('global', 'date_relative_ago');
	}
	elseif ($delta < 10*365*24*60*60)
	{
		$y = round($delta/(365*24*60*60));	// Couple of years
		return ($y==1?getTranslation('global', 'date_relative_one'):$y." ").smarty_modifier_declension($y, getTranslation('global', 'date_relative_years'))." ".getTranslation('global', 'date_relative_ago');
	}
	else
	{
		return getTranslation('global', 'date_relative_old');
	}
	return '';
}
?>