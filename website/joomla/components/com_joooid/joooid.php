<?php
/**
 * 			JOOOID
 * @version			1.0.0
 * @package			JOOOID for Joomla!
 * @copyright			Copyright (C) 2007-2011 Joomler!.net. All rights reserved.
 * @license			http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author			Yoshiki Kozaki : joomlers@gmail.com
 * @link			http://www.joomler.net/
 *
 */

// Check to ensure this file is included in Joomla! and nobody else!
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT_SITE.'/'.'helpers/route.php';


$savedErrorReporting = error_reporting(E_ERROR);


function joooidHandleError($errno, $errstr, $errfile, $errline, array $errcontext)
	{
	if($errno>2 || $GLOBALS['joooid_debug']==="0") return false;
	echo "JOOOID EXCEPTION : ".$errno." - ".$errstr." - [file: ".$errfile.":".$errline."]\n";
	return true;
	}

$savedErrorHandler = set_error_handler("joooidHandleError");
$controller = JControllerLegacy::getInstance('joooid');
$view = JRequest::getCmd('view');
if(in_array($view, array('manifest'))){
	$task = 'display';
} else {
	$task = JRequest::getCmd('task', 'service');
}
if(function_exists('register_shutdown_function')){
	register_shutdown_function('joooid_shutdownFunction'); 
}
function joooid_shutDownFunction() { 
	$error = error_get_last(); 
	if(isset($error['file']) && stripos($error['file'],'joooid')!==false ){
	  if($GLOBALS['joooid_debug']==="0") return false; 
		echo "JOOOID EXCEPTION:\n";
		print_r($error);
	}

} 


$controller->execute($task);
$controller->redirect();
set_error_handler($savedErrorHandler,E_ALL);
error_reporting($savedErrorReporting);
