<?php

if (!ob_get_level()) {
	ob_start();
}

define('ENVIRONMENT', 'development');

switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1);
}

$system_path = 'system';
$application_folder = 'application';
$view_folder = '';
if (defined('STDIN'))
{
	chdir(dirname(__FILE__));
}
if (($_temp = realpath($system_path)) !== FALSE)
{
	$system_path = $_temp.'/';
}
else
{
	$system_path = rtrim($system_path, '/').'/';
}
if ( ! is_dir($system_path))
{
	header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
	echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
	exit(3);
}
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', str_replace('\\', '/', $system_path));
define('FCPATH', dirname(__FILE__).'/');
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));
if (is_dir($application_folder))
{
	if (($_temp = realpath($application_folder)) !== FALSE)
	{
		$application_folder = $_temp;
	}
	define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
}
else
{
	if ( ! is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3);
	}
	define('APPPATH', BASEPATH.$application_folder.DIRECTORY_SEPARATOR);
}
if ( ! is_dir($view_folder))
{
	if ( ! empty($view_folder) && is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR))
	{
		$view_folder = APPPATH.$view_folder;
	}
	elseif ( ! is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3);
	}
	else
	{
		$view_folder = APPPATH.'views';
	}
}
if (($_temp = realpath($view_folder)) !== FALSE)
{
	$view_folder = $_temp.DIRECTORY_SEPARATOR;
}
else
{
	$view_folder = rtrim($view_folder, '/\\').DIRECTORY_SEPARATOR;
}

define('VIEWPATH', $view_folder);

if (!defined('STDIN')) {
$lockFile = FCPATH.'application/config/installed.lock';
if (!is_file($lockFile)) {
	$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	if (stripos($requestUri, 'installation.php') === false) {
		$dbConfigPath = FCPATH.'application/config/database.php';
		$configText = @file_get_contents($dbConfigPath);
		$hostname = $username = $password = $database = null;
		if (is_string($configText)) {
			if (preg_match("/'hostname'\\s*=>\\s*'((?:\\\\\\\\'|[^'])*)'/", $configText, $m)) {
				$hostname = stripcslashes($m[1]);
			}
			if (preg_match("/'username'\\s*=>\\s*'((?:\\\\\\\\'|[^'])*)'/", $configText, $m)) {
				$username = stripcslashes($m[1]);
			}
			if (preg_match("/'password'\\s*=>\\s*'((?:\\\\\\\\'|[^'])*)'/", $configText, $m)) {
				$password = stripcslashes($m[1]);
			}
			if (preg_match("/'database'\\s*=>\\s*'((?:\\\\\\\\'|[^'])*)'/", $configText, $m)) {
				$database = stripcslashes($m[1]);
			}
		}
		$shouldRedirect = true;
		if ($hostname !== null && $username !== null && $password !== null && $database !== null) {
			mysqli_report(MYSQLI_REPORT_OFF);
			$mysqli = @mysqli_connect($hostname, $username, $password);
			if ($mysqli) {
				if (@mysqli_select_db($mysqli, $database)) {
					$res = @mysqli_query($mysqli, "SHOW TABLES LIKE 'active_pages'");
					if ($res && @mysqli_num_rows($res) > 0) {
						$shouldRedirect = false;
					}
					if ($res) {
						@mysqli_free_result($res);
					}
				}
				@mysqli_close($mysqli);
			}
		}
		if ($shouldRedirect) {
			header('Location: /installation.php');
			exit;
		}
	}
}
}
require_once BASEPATH.'core/CodeIgniter.php';