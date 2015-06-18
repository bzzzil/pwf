<?php
/**
 * Show simple static pages from templates to user
 * 
 * @package StaticPage
 * 
 * @copyright Vasily Melenchuk
 */
	if ( !defined('NORMAL_START') ) die();

	if ( count( $pageParams ) < 2 )
	{
		$page_file = 'page_index';
	}
	else
	{
		$page_file = 'page_'.implode( '_',array_slice( $pageParams, 1 ) );
	}

	if ( !file_exists('templates/'.$lang.'/'.$page_file.'.tpl') )
	{
		Error_404();
		return;
	}

	// Show real modification time
	header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime('templates/'.$lang.'/'.$page_file.'.tpl')) . ' GMT');

	// Draw content
	DrawTemplate( $lang.'/'.$page_file );
?>
