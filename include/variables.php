<?php
	if (!defined('NORMAL_START')) die();

	define("DEBUG", true);

	// Database parameters to pass to ADOdb
	define("DB_HOST",     DEBUG?"localhost":"localhost");
	define("DB_USER",     DEBUG?"mysql":"mysql");
	define("DB_PASSWORD", DEBUG?"mysql":"mysql");
	define("DB_NAME",     DEBUG?"pwf":"pwf");

	define("SITE_PROTO",  DEBUG?"http":"http");
	define("SITE_URL",    $_SERVER['SERVER_NAME']);
	define("SITE_NAME",   "bzzzil.io");
	define("SITE_EMAIL",  "bzzzil@gmailcom");
?>
