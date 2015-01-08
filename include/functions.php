<?php
/**
 * Set of misc API functions
 * 
 * @package Core
 * @subpackage Service
 * 
 * @copyright Vasily Melenchuk
 */
	if (!defined('NORMAL_START')) die();
	/**
	 * Рекурсивная функция StripSlashes()
	 *
	 * @param mixed $src
	 * @return mixed
	 */
	function stripArray($src)
	{
		if (!is_array($src))
		{
			return stripslashes($src);
		}
		foreach ($src AS $key=>$val)
		{
			$src[$key] = stripArray($val);
		}
		return $src;
	}

	///////////////////////////////////////////////////////////////////////////////
	//
	// Generate security code
	//
	///////////////////////////////////////////////////////////////////////////////
	function generateSecurityCode($length=4) 
	{
		$CodeSet = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
		srand(date("s")*date("i"));

		// Generate new auth code
		$_SESSION['AUTH_CODE']="";
		for ($i=0;$i<$length;$i++)
			$_SESSION['AUTH_CODE'].=$CodeSet[rand(0, strlen($CodeSet)-1)];
	}

	/**
	 * Подмена переводов, если он имеется
	 * Строка может являться просто ссылкой на переводчик,
	 * в таком случае в неё подставляется перевод, в противном
	 * случае она остаётся без изменений
	 * 
	 * @param string
	 * 
	 * @return string
	 */
	function _t($string) 
	{
		$caption	=	array ();
		if (preg_match('/_T\((.*)\/(.*)\)/i', $string, $caption))
		{
			if ($caption[1] AND $caption[2])
			{
				return getTranslation($caption[1], $caption[2]);
			}
		}
		return $string;
	}

	/**
	 * send mail to user
	 *
	 * @param int/string $user_id
	 * @param string $subject
	 * @param string $body
	 * @param array $variables
	 *
	 * @return bool
	 */
	function sendMail($user_id, $subject, $body, $variables)
	{
		global $smarty;

		// Get recipient info
		if (is_numeric($user_id)) {
			global $db;
			$userInfo = $db->GetRow('SELECT * FROM users WHERE user_id = "'.intval($user_id).'"');

			$user_id = '"'.$userInfo['login'].'" <'.$userInfo['email'].'>';

			$variables2['username'] = $userInfo['login'];
		}

		$headers  = "From: \"".SITE_NAME."\" <support@".SITE_URL.">\r\n";
		$headers .= "X-Mailer: ".SITE_URL."\r\n";
		$headers .= "Content-type: text/plain; charset=utf-8";

		// Get translations
		$subject = _t($subject);
		$body    = _t(substr($body,0,-1).'_text)');

		// Insert variables
		$smarty->assign($variables);
		$variables2['subject'] = $smarty->fetch('string:'.$subject);
		$variables2['body']    = $smarty->fetch('string:'.$body);

		// Wrap e-mail
		$smarty->assign($variables2);
		$subject    = "=?utf-8?B?".base64_encode($smarty->fetch('string:'.getTranslation('EMail','subject_template')))."?=";
		$body_text  = $smarty->fetch('string:'.getTranslation('EMail','body_text_template'));

		return @mail($user_id, $subject, $body_text, $headers);
	}

	/**
	 * Debug dump variables
	 *
	 * @param mixed $var
	 *
	 * @return none
	 */
	function _p($var)
	{
		include_once('dBug/dBug.php');
		new dBug($var);
	}

	/**
	 * Проверяет, подходит ли заданное выражение под формат числа
	 *
	 * @param string $value
	 * @return bool
	 * 
	 * @static
	 */
	function isInt($value)
	{
		$regExp = "/^\d+$/";
		return preg_match($regExp, $value);
	}

	function __autoload($class_name) 
	{
		if (!class_exists($class_name))
			require_once 'classes/'.$class_name.'.php';
	}

	function Error_404()
	{
		global $smarty;
		header("HTTP/1.1 404 Not Found");
		ob_clean();
		DrawTemplate("common.page_top");
		DrawTemplate("page_error_404");
		DrawTemplate('common.page_bottom');
		exit();
	}

	function Error_501($message)
	{
		global $smarty;
		header("HTTP/1.1 501 Not Found");
		if (!DEBUG)
		{
			ob_clean();
		}
		DrawTemplate("common.page_top");
		DrawTemplate("page_error_501", array("message"=>$message));
		DrawTemplate('common.page_bottom');
		exit();
	}

	/**
	 * получение перевода термина
	 *
	 * @param string $group
	 * @param string $key
	 * @return string
	 */
	function getTranslation($group, $key)
	{
		global $db, $lang, $i18n;

		if (is_array($i18n[$group])) {
			return $i18n[$group][$key];
		} elseif (is_file('language/'.$lang.'/'.$group.'.php')) {
			require_once('language/'.$lang.'/'.$group.'.php');
			if (is_array($i18n[$group]))
				return $i18n[$group][$key];
/*		} else {
			return $db->GetOne('SELECT translation FROM translations WHERE 
				lang = "'.mysql_real_escape_string($lang).'" AND lang_group = "'.mysql_real_escape_string($group).'" AND lang_key = "'.mysql_real_escape_string($key).'"');*/
		}
	}

	function getTimestamp($date, $time="")
	{
		$date = preg_split("/[^\d]/",$date, -1, PREG_SPLIT_NO_EMPTY);
		if (!$date[2])
			$date[2] = date("Y");
		if (count($date)<3)
			return 0;
		if ($time)
			$time = preg_split("/[^\d]/",$time, -1, PREG_SPLIT_NO_EMPTY);
		else
			$time = array(0,0);

		return mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
	}

	/**
	 * Wrapper for Smarty modifier
	 *
	 * @return array
	 */

    /**
     * formatDate
     * 
     * @param int  $timestamp unixtime.
     * @param string $format  desired output format.
     *
     * @return string
     */
	function formatDate($timestamp, $format='datetime')
	{
		require_once('include/smarty/plugins/modifier.date.php');
		return smarty_modifier_date($timestamp, $format);
	}

    /**
     * open_image load image any type from file
     * 
     * @param string $file filename to open.
     *
     * @return image resource.
     */
	function open_image($file) 
	{
		$size = getimagesize($file);
		switch($size['mime'])
		{
			case "image/jpeg":
			$im = imagecreatefromjpeg($file); //jpeg file
			break;
			case "image/gif":
			$im = imagecreatefromgif($file); //gif file
			break;
			case "image/png":
			$im = imagecreatefrompng($file); //png file
			break;
			default: 
			$im=false;
			break;
		}
		return $im;
	}

    /**
     * resize_image proportional resize image to fit desired dimensions
     * 
     * @param mixed $image image resource.
     * @param mixed $w     desired width.
     * @param mixed $h     desired height.
     *
     * @return image resource.
     */
	function resize_image($image, $w, $h) 
	{
		if (!$w || !$h)
			return false;

		$dx = imagesx($image)/$w;
		$dy = imagesy($image)/$h;
		$d = max($dx, $dy);
		if ($d==0)
			return false;

		$resized = imagecreatetruecolor(imagesx($image)/$d, imagesy($image)/$d);
		if (!$resized)
			return false;

		if (!imagecopyresized($resized, $image, 0, 0, 0, 0,
			imagesx($resized), imagesy($resized),
		 	imagesx($image), imagesy($image)))
		 	return false;

		return $resized;
	}

	/**
	 * рабчик шиб рем ыпо.  сим  фигции, ыво рма  шиб  ран  рав   email .
	 *
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
	function globalErrorHandler($errno, $errstr, $errfile, $errline) 
	{
		global $AdministratorMail;

		$errorLogCacheFile = "include/error.cache";

		$dbg_dump	=	"";
		$dbg_arr	=	Array ();
		if ($errno == E_NOTICE || $errno == E_STRICT || $errno == E_USER_NOTICE)
		{
			/**
			 * уем NOTICE  STRICT
			 * <pre>Ignore NOTICE and STRICT</pre>
			 */
			return TRUE;
		}
		if (!function_exists('debug_backtrace')) 
		{
			$dbg_dump	.=	"[{$errno}] {$errstr} (@ line {$errline} in {$errfile}.\r\n";
		}
		else 
		{
			/**
			 *  рав соощен  email яем рма  рок 
			 * <pre>Add request information</pre>
			 */
			if (!DEBUG)
			{
				$dbg_dump	.=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\r\n\r\n";
			}
			/**
			 * @since PHP v4.3.0
			 */
			$dbg_dump	.=	"[{$errno}] {$errstr}\r\n";
			$dbg_idx	=	-1;
			$display_cache	=	array ();
			foreach (debug_backtrace() AS $dbg)
			{
				$dbg_idx++;
				if ($dbg_idx != 0)
				{
					$dbg_str	=	'';
					if (isset($dbg['file']))
					{
						$basename	=	basename($dbg['file']).' ';
						$parsed	=	'';
						if (DEBUG AND $dbg['file'] AND @file_exists($dbg['file']) AND @is_readable($dbg['file']))
						{
							if (0 && include_once('lib/Highlight/Highlight.php'))
							{
								$strings	=	file($dbg['file']);
								if ($dbg['line'] AND count($strings))
								{
									$start	=	($dbg['line'] > 5) ? $dbg['line'] - 5 : 1;
									$source	=	implode('', array_slice($strings, $start, 5 * 2 + 1));
									if (function_exists('iconv'))
									{
										$source	=	iconv('windows-1251', 'utf-8', $source);
									}
									$source	=	preg_replace("/\t/", "  ", $source);
									$source	=	preg_replace("/[\r\n]$/", '', $source);
									$highlight	=	new GeSHi($source, 'PHP');
									if ($highlight AND is_a($highlight, 'GeSHi'))
									{
										// чае уме рок (switch line numbering)
										$highlight->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS, 1);
										//  ректно чал  умеции (set numbering start)
										$highlight->start_line_numbers_at($start+1);
										//  тил  светки ужн рок (set highlight style)
										$highlight->set_highlight_lines_extra_style('background-color: #FFFF00;');
										//  рок  светки (set highlighted line number)
										$highlight->highlight_lines_extra(5);
										$parsed	=	$highlight->parse_code();
									}
								}
							}
						}
						$dbg_str	.=	$basename;
					}
					if (isset($dbg['class']))
					{
						$dbg_str	.=	$dbg['class'].$dbg['type'];
					}
					/**
					 * ропска сам ызо шиб
					 * <pre>Skip trigger_error()</pre>
					 */
					if ($dbg["function"] == 'trigger_error')
					{
						continue;
					}
					$dbg_str	.=	$dbg["function"];
					$secure		=	preg_match("/connect/i", $dbg["function"]);
					if (isset($dbg['args']) AND count($dbg['args']))
					{
						foreach ($dbg['args'] AS $key=>$arg)
						{
							if (is_object($arg))
							{
								$dbg['args'][$key]	=	get_class($arg)." Object";
							}
							elseif (is_array($arg)) 
							{
								$dbg['args'][$key]	=	'Array ('.count($arg).')';
							}
							elseif (!is_numeric($arg))
							{
								if ($secure)
								{
									$arg	=	'*';
								}
								$arg = preg_replace (array ("/^\'+/", "/\'+$/"), array ("'", "'"), $arg);
								$hash	=	md5($arg);
								if (!in_array($hash, $display_cache))
								{
									$display_cache[]	=	$hash;
									if (DEBUG AND preg_match("/[{](if.*)|(else)|(translate.*)|(foreach)[}]/", $arg))
									{
										if (0 && include_once('lib/Highlight/Highlight.php'))
										{
											$highlight	= new GeSHi($arg, 'Smarty');
											if ($highlight AND is_a($highlight, 'GeSHi'))
											{
												// чае уме рок
												$highlight->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS, 1);
												//  тил  светки ужн рок
												$highlight->set_highlight_lines_extra_style('background-color: #FFCC00;');
												if (preg_match("/line\s(\d+)\]/", $errstr, $err_match))
												{
													//  рок  светки
													$highlight->highlight_lines_extra($err_match[1]);
												}
												$arg	=	$highlight->parse_code();
											}
										}
									}
								}
								else 
								{
									$arg	=	'*';
								}
								$dbg['args'][$key]	=	"'{$arg}'";
							}
						}
						$args	=	implode (', ', $dbg['args']);
						$dbg_str	.=	"({$args})";
					}
					else
					{
						$dbg_str	.=	"()";
					}
					$dbg_str	.=	$parsed;
					$dbg_arr[$dbg['line']]	=	$dbg_str;
					$dbg_dump	.=	$dbg['line']." ".$dbg_str."\r\n";
				}
			}
		}
		if (!DEBUG)
		{
			// ровяем, равяли   соощен  шиб  (Check if the message has been sent earlier)
			$errorCode = md5($errno.$errfile.$errline);
			$sendError = true;
			if ($errorLogCacheFile && is_file($errorLogCacheFile) && is_readable($errorLogCacheFile) && 
				($errorsList = file($errorLogCacheFile)) && is_array($errorsList)) 
			{
				if (isset($_GET['reset_errorcache']))
				{
					$errorsList	=	array ();
				}
				//   шиб  (Parse error messages log)
				foreach ($errorsList as $line) 
				{
					$errorInfo = explode(";",trim($line));
					if (($errorInfo[0]==$errorCode) && ($errorInfo[1] > time() - 3600))
					{
						//  шиб  равяла
						$sendError = false;
						break;
					}
				}
			}
			if ($sendError) 
			{
				$env_vars	=	"_GET\r\n";
				$env_vars	.=	var_export($_GET, true);
				$env_vars	.=	"_POST\r\n";
				$env_vars	.=	var_export($_POST, true);
				$env_vars	.=	"_COOKIE\r\n";
				$env_vars	.=	var_export($_COOKIE, true);
				$env_vars	.=	"_SERVER\r\n";
				$env_vars	.=	var_export(array ('HTTP_REFERER' => $_SERVER['HTTP_REFERER']), true);
				
				// равяем соощен  шиб (send message)
				error_log ($dbg_dump."\r\n\r\n".$env_vars."\r\n".(function_exists('headers_list') ? implode ("\r\n", headers_list()) : ''), 1, $AdministratorMail);
				if ($errorLogCacheFile) 
				{
					// тмечае фак рав собщен  шиб  фай шиб (Update error log file)
					$fh = @fopen($errorLogCacheFile, "w");
					if ($fh)
					{
						fputs($fh, $errorCode.";".time()."\r\n");
						if (is_array($errorsList)) 
						{
							foreach ($errorsList as $line) 
								fputs($fh, $line);
						}
						fclose($fh);
					}
				}
			}
		}
		else 
		{
			if (DEBUG_HTML)
			{
				if (function_exists('_p'))
				{
					echo '<div style="background-color: #fff"><style type="text/css">PRE{margin: 4px 0px 0px 0px;}</style>';
					_p($errstr);
					_p(array_reverse($dbg_arr, TRUE));
					echo '</div>';
				}
				else
				{
					echo nl2br($dbg_dump);
				}
			}
			else 
			{
				echo $dbg_dump;
			}
		}
	}

	set_error_handler('globalErrorHandler');
?>