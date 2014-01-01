<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

function JOOOIDBuildRoute(&$query)
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

function JOOOIDParseRoute($segments)
{
	$vars = array();

	// Count route segments
	$count = count($segments);

	if($count){
		$vars['view'] = $segments[0];
	}

	return $vars;
}
