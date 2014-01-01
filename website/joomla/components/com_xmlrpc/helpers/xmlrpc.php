<?php
/**
 * 			XMLRPC Helper
 * @version		1.0.0
 * @package		XMLRPC for Joomla!
 * @copyright		Copyright (C) 2007 - 2012 Yoshiki Kozaki All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author		Yoshiki Kozaki  info@joomler.net
 * @link 			http://www.joomler.net/
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class XMLRPCHelper
{
	public static function log()
	{
		$args = func_get_args();

		print_r($args, true);
	}

	public static function write($text, $filename='xmlrpc.log.php')
	{

	}
}