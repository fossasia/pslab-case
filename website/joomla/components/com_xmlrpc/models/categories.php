<?php
/**
 * 			XMLRPC Component Categories Model
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JFactory::getLanguage()->load('com_categories', JPATH_ADMINISTRATOR);

require_once(JPATH_ADMINISTRATOR.'/components/com_categories/models/categories.php');

/**
 * XMLRPC Component Categories Model
 *
 * @package		com_xmlrpc
 */
class XMLRPCModelCategories extends CategoriesModelCategories
{
	protected $context = 'com_xmlrpc.categories';

	/**
	 * @return	string
	 * @since	1.6
	 */
	function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$this->setState('list.limit', 0);

		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
				'a.id, a.title, a.alias, a.note, a.published, a.access' .
				', a.checked_out, a.checked_out_time, a.created_user_id' .
				', a.path, a.parent_id, a.level, a.lft, a.rgt' .
				', a.language'
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
