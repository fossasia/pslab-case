<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JFactory::getLanguage()->load('com_menu', JPATH_ADMINISTRATOR);

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_menus/models/menu.php';

class JOOOIDModelMenus extends MenusModelMenu
{
	public function getIds(){
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select(
				$this->getState(
					'list.select',
					'a.id'
					)
			      );
		$query->from('#__menu_types AS a');
		$db->setQuery($query);
		$options = $db->loadAssocList();
		$ret = array();
		for($i=0;$i<count($options);$i++){
			$ret[] = $this->getItem($options[$i]);
		}
		return $ret;



	}

}

