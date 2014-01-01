<?php
/**
 * 			XMLRPC Component Category Model
 * @version		2.0.0
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

// No direct access.
defined('_JEXEC') or die;

JFactory::getLanguage()->load('com_categories', JPATH_ADMINISTRATOR);
// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_categories/models/category.php';
require_once JPATH_ADMINISTRATOR.'/components/com_categories/tables/category.php';

/**
 * XMLRPC Component Category Model
 *
 * @package		com_xmlrpc
 */
class XMLRPCModelCategory extends CategoriesModelCategory
{
	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');

		$parentId = JRequest::getInt('parent_id');
		$this->setState('category.parent_id', $parentId);

		// Load the User state.
		$pk = (int) JRequest::getInt('id');
		$this->setState($this->getName().'.id', $pk);

		$extension = 'com_content';
		$this->setState('category.extension', $extension);
		$parts = explode('.',$extension);

		// Extract the component name
		$this->setState('category.component', $parts[0]);

		// Extract the optional section name
		$this->setState('category.section', (count($parts)>1)?$parts[1]:null);

		// Load the parameters.
		$params	= JComponentHelper::getParams('com_categories');
		$this->setState('params', $params);
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	public function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check for existing category.
		if (!empty($record->id)) {
			return $user->authorise('core.edit.state', $record->extension.'.category.'.(int) $record->id);
		}
		// New category, so check against the parent.
		else if (!empty($record->parent_id)) {
			return $user->authorise('core.edit.state', $record->extension.'.category.'.(int) $record->parent_id);
		}
		// Default to component settings if neither category nor parent known.
		else {
			return $user->authorise('core.edit.state', $record->extension);
		}
	}
}
