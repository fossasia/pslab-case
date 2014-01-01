<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JFactory::getLanguage()->load('com_categories', JPATH_ADMINISTRATOR);

require_once(JPATH_ADMINISTRATOR.'/components/com_categories/models/categories.php');

class JOOOIDModelCategories extends CategoriesModelCategories
{
	protected $context = 'com_joooid.categories';

	function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$this->setState('list.limit', 0);

		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
				'a.id, a.title, a.alias, a.description, a.note, a.published, a.access' .
				', a.checked_out, a.checked_out_time, a.created_user_id' .
				', a.path, a.parent_id, a.level, a.lft, a.rgt' .
				', a.language, a.created_time'
		);
		$query->from('#__categories AS a');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_user_id');

		$query->where('a.extension = '.$db->quote('com_content'));

		$query->where('a.access IN(' . implode(',', $user->getAuthorisedViewLevels()).')');

		$query->where('(a.published IN (0, 1))');

		// Add the list ordering clause.
		$query->order('a.lft ASC');

		return $query;
	}
}
