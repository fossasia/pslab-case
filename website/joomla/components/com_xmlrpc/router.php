<?php
/**
 * 			XMLRPC
 * @version		2.0.0
 * @package		XMLRPC for Joomla!
 * @copyright		Copyright (C) 2007-20121 Joomler!.net. All rights reserved.
 * @license		GNU/GPL 2.0 or higher
 * @author		Joomler!.net  joomlers@gmail.com
 * @link			http://www.joomler.net
 */

/**
* @package		Joomla
* @copyright		Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

function XMLRPCBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		$view = $query['view'];

		if (empty($query['Itemid'])) {
			$segments[] = $query['view'];
		}

		unset($query['view']);
	}

	return $segments;
}

function XMLRPCParseRoute($segments)
{
	$vars = array();

	// Count route segments
	$count = count($segments);

	if($count){
		$vars['view'] = $segments[0];
	}

	return $vars;
}