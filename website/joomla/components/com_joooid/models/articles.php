<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JFactory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR);

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_content/models/articles.php';

class JOOOIDModelArticles extends ContentModelArticles
{
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		//$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$search = JRequest::getVar('search');
		$this->setState('filter.search', $search);

		//$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$access = JRequest::getVar('filter_access');
		$this->setState('filter.access', $access);

		//$authorId = $app->getUserStateFromRequest($this->context.'.filter.author_id', 'filter_author_id');
		$authorId = JRequest::getVar('filter_author_id');
		$this->setState('filter.author_id', $authorId);


		//$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$published = JRequest::getVar('published');
		$this->setState('filter.published', $published);

		//$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$categoryId = JRequest::getVar('category_id');
		$this->setState('filter.category_id', $categoryId);

		//$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$language = JRequest::getVar('language');
		$this->setState('filter.language', $language);
		

		if(JRequest::getVar('order_key')!==0 && JRequest::getVar('order_filter')!==0){
			//print_r(JRequest::getVar('order_key'));print_r(JRequest::getVar('order_filter'));die;
			$orderCol = JRequest::getVar('order_key');
			$orderDirn = JRequest::getVar('order_filter');
		}
		//print_r($orderCol);print_r($orderDirn);die;

		parent::populateState('a.'.$orderCol, $orderDirn);

	}


	public function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

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
		$access = JRequest::getVar('filter_access');
		if(isset($access)){
			if ($access = $this->getState('filter.access')) {
				$query->where('a.access = ' . (int) $access);
			}
		}

		// Filter by published state
		$published = JRequest::getVar('filter_published');
		if(isset($published)){
			$published = $this->getState('filter.published');
			if (is_numeric($published)) {
				$query->where('a.state = ' . (int) $published);
			}
			else if ($published === '') {
				$query->where('(a.state = 0 OR a.state = 1 OR a.state = 2 OR a.state = -2 )');
			}
		}

		// Filter by a single or group of categories.
		$categoryId = JRequest::getVar('filter_category_id');
		if(isset($categoryId)){
			$categoryId = $this->getState('filter.category_id');
			if (is_numeric($categoryId)) {
				$query->where('a.catid = '.(int) $categoryId);
			}
			else if (is_array($categoryId)) {
				JArrayHelper::toInteger($categoryId);
				$categoryId = implode(',', $categoryId);
				$query->where('a.catid IN ('.$categoryId.')');
			}
		}

		// Filter by author
		$authorId = JRequest::getVar('filter_author_id');
		if(isset($authorId)){
			$authorId = $this->getState('filter.author_id');
			if (is_numeric($authorId)) {
				$type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
				$query->where('a.created_by '.$type.(int) $authorId);
			}
		}


		// Filter by search in title.
		$search = JRequest::getVar('filter_search');
		if(isset($search)){
			$search = $this->getState('filter.search');
			if (!empty($search)) {
				if (stripos($search, 'id:') === 0) {
					$query->where('a.id = '.(int) substr($search, 3));
				}
				else if (stripos($search, 'author:') === 0) {
					$search = $db->Quote('%'.$db->escape(substr($search, 7), true).'%');
					$query->where('(ua.name LIKE '.$search.' OR ua.username LIKE '.$search.')');
				}
				else {
					$search = $db->Quote('%'.$db->escape($search, true).'%');
					$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
				}
			}
		}

		$language = JRequest::getVar('filter_language');
		if(isset($language)){
			// Filter on the language.
			if ($language = $this->getState('filter.language')) {
				$query->where('a.language = '.$db->quote($language));
			}
		}

		// Add the list ordering clause.
		$orderCol	= 'a.created';
		$orderDirn	= 'desc';
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', a.ordering';
		}

		if(JRequest::getVar('order_key')!==0 && JRequest::getVar('order_filter')!==0){
			//print_r(JRequest::getVar('order_key'));print_r(JRequest::getVar('order_filter'));die;
			$orderCol = JRequest::getVar('order_key');
			$orderDirn = JRequest::getVar('order_filter');
		}
		//print_r($orderCol);print_r($orderDirn);die;
		$query->order($db->escape($orderCol.' '.$orderDirn));
		//$query->setLimit(0,1);

		return $query;

	}

}
