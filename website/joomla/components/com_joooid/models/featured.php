<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

JFactory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR);

require_once JPATH_BASE.'/components/com_contact/models/featured.php';

class JOOOIDModelFeatured extends ContactModelFeatured
{

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$authorId = $app->getUserStateFromRequest($this->context.'.filter.author_id', 'filter_author_id');
		$this->setState('filter.author_id', $authorId);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// List state information.
		parent::populateState('a.title', 'asc');
	}



	public function saveFront($list){

		$db =& JFactory::getDBO();

		$query='SELECT ordering '
			.'FROM #__content_frontpage';
		$db->setQuery( $query );
		$number = count($db->loadRowList());

		if ($number>0){

			$query='DELETE FROM #__content_frontpage ';
			$db->setQuery( $query );
			$aa = $db->loadRowList();
			if (!isset($aa)){
				return -1; 
			}
		}
		for($i=0;$i<count($list);$i++){
			$query='INSERT INTO #__content_frontpage (content_id, ordering) values("'.$list[$i].'","'.$i.'")';

			$db->setQuery( $query );
			$aa = $db->loadRowList();
			if (!isset($aa)){
				return -2; 
			}

		}

		return 0;

	}


	public function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
				$this->getState(
					'list.select',
					'a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.catid' .
					', a.state, a.access, a.created, a.created_by, a.ordering, a.featured, a.language, a.hits' .
					', a.publish_up, a.publish_down'.
					', a.introtext, a.fulltext, a.metakey, a.metadesc'
					)
			      );
		$query->from('#__content AS a');

		// Join over the featured
		$query->select('front.ordering AS frontpage_order');
		$query->join('LEFT', '`#__content_frontpage` AS front ON front.content_id = a.id');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = ' . (int) $access);
		}

		$query->where('a.featured = 1');

		// Add the list ordering clause.
		$orderCol	= 'front.ordering';//$this->state->get('list.ordering');
		$orderDirn	= 'asc';//$this->state->get('list.direction');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', a.ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}

}
