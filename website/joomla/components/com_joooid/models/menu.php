<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JFactory::getLanguage()->load('com_menu', JPATH_ADMINISTRATOR);

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_menus/models/menu.php';

class JOOOIDModelMenu extends MenusModelMenu
{
	function allowAdd($data){
		return true;
	}
	function allowEdit($data){
		return true;
	}

}


