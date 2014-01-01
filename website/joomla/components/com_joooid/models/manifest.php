<?php
/**
 * 			JOOOID Model Manifest
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



class JOOOIDModelManifest extends JModel
{
	public function getXML()
	{
		$xml = null;

		$params = JComponentHelper::getParams('com_joooid');
		$plugin = $params->get('plugin', $params->get('specified'));
		if(empty($plugin)){
			return $xml;
		}

		$path = JPATH_PLUGINS.'/xmlrpc/'.$plugin.'/xml/manifest.php';

		if(file_exists($path)){
			JFactory::getLanguage()->load('plg_'.$plugin, JPATH_ADMINISTRATOR);
			require_once $path;
			$class = 'JOOOIDManifest'. ucfirst($plugin);
			$xml = call_user_func(array($class, 'buildXML'), $params);
//			$xml = $class::buildXML($params); //for 5.3 or higher
		}

		return $xml;
	}
}