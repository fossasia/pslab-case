<?php
/**
 * 			XMLRPC Component Manifest Model
 * @version		2.0.4
 * @package		XMLRPC for Joomla!
 * @copyright		Copyright (C) 2007 - 2012 Yoshiki Kozaki All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author		Yoshiki Kozaki  info@joomler.net
 * @link 			http://www.joomler.net/
 */

/**
* @package		Joomla
* @copyright		Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class XMLRPCModelManifest extends JModelLegacy
{
	public function getXML()
	{
		$xml = null;

		$params = JComponentHelper::getParams('com_xmlrpc');
		$plugin = $params->get('plugin', 'joomla');
		if(empty($plugin)){
			return $xml;
		}

		$path = JPATH_PLUGINS.'/xmlrpc/'.$plugin.'/xml/manifest.php';

		if(file_exists($path)){
			JFactory::getLanguage()->load('plg_xmlrpc_'.$plugin, JPATH_ADMINISTRATOR);
			require_once $path;
			$class = 'XMLRPCManifest'. ucfirst($plugin);
			$xml = call_user_func(array($class, 'buildXML'), $params);
//			$xml = $class::buildXML($params); //for 5.3 or higher
		}

		return $xml;
	}
}