<?php
/**
 * 			JOOOID Helper Route
 * @version			1.0.0
 * @package			JOOOID for Joomla!
 * @copyright			Copyright (C) 2007-2011 Joomler!.net. All rights reserved.
 * @license			http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author			Yoshiki Kozaki : joomlers@gmail.com
 * @link			http://www.joomler.net/
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * JOOOID Component Route Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	JOOOID
 * @since 1.5
 */
abstract class JOOOIDHelperRoute
{

	public static function getManifestRoute()
	{
		$link = 'index.php?option=com_joooid&view=manifest&format=xml';

		return $link;
	}

	public static function getServiceRoute()
	{
		$link = 'index.php?option=com_joooid';

		return $link;
	}
}
