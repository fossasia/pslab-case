<?php
/**
 * 			XMLRPC Helper Route Helper
 * @version		2.0.6
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

/**
 * XMLRPC Component Route Helper
 *
 * @static
 * @package		com_xmlrpc
 */
abstract class XMLRPCHelperRoute
{
	public static function getRsdRoute()
	{
		$link = 'index.php?option=com_xmlrpc&view=rsd&format=xml';

		return $link;
	}

	public static function getManifestRoute()
	{
		$link = 'index.php?option=com_xmlrpc&view=manifest&format=xml';

		return $link;
	}

	public static function getServiceRoute()
	{
		$link = 'index.php?option=com_xmlrpc&view=service&format=xml';

		return $link;
	}

	public static function getPreviewRoute()
	{
		$link = 'index.php?option=com_xmlrpc&task=webpreview';

		return $link;
	}

	public static function getWebLayoutRoute()
	{
		$link = 'index.php?option=com_xmlrpc&task=weblayout&tmpl=component';

		return $link;
	}
}