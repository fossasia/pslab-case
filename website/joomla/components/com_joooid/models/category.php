<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JFactory::getLanguage()->load('com_categories', JPATH_ADMINISTRATOR);
// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_categories/models/category.php';

class JOOOIDModelCategory extends CategoriesModelCategory
{

	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');

		$parentId = JRequest::getInt('parent_id');
		$this->setState('category.parent_id', $parentId);

		// Load the User state.
		$pk = (int) JRequest::getInt('id');
		$this->setState($this->getName().'.id', $pk);

		//$extension = 'com_categories';
		$extension = 'com_content';
		$this->setState('category.extension', $extension);
		$parts = explode('.',$extension);

		// Extract the component name
		$this->setState('category.component', $parts[0]);

		// Extract the optional section name
		$this->setState('category.section', (count($parts)>1)?$parts[1]:null);

		// Load the parameters.
		$params	= JComponentHelper::getParams('com_category');
		$this->setState('params', $params);
	}

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
