<?php
/**
 * 			XMLRPC Component Template Model
 * @version		2.0.6
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

// No direct access
defined('_JEXEC') or die();

JFactory::getLanguage()->load('com_content', JPATH_SITE);

require_once JPATH_SITE . '/components/com_content/models/article.php';
/**
 * XMLRPC Component Template Model
 *
 * @package		com_xmlrpc
 */
class XMLRPCModelTemplate extends ContentModelArticle
{
	/**
	 * Method to get article data.
	 *
	 * @param	integer	The id of the article.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function &getItem ($pk = null)
	{
		$pk = 0;
		$this->setState('article.id', $pk);

		$this->_item[$pk] = $this->getTable('content', 'JTable');
		$this->_item[$pk]->load($pk);

		$registry = new JRegistry;
		$registry->loadArray(array());
		$this->_item[$pk]->category_alias = '';
		$this->_item[$pk]->parent_id = '';
		$this->_item[$pk]->author = '';
		$this->_item[$pk]->params = $registry;
		$this->_item[$pk]->attribs = $registry;
		$this->_item[$pk]->metadata = $registry;

		$this->_item[$pk]->title = '{post-title}';
		$this->_item[$pk]->introtext = '{post-body}';
		$this->_item[$pk]->fulltext = null;
		$this->_item[$pk]->state = 1;
		$this->_item[$pk]->parent_alias = '';

		$this->_item[$pk]->params->set('show_page_heading', 0);
		$this->_item[$pk]->params->set('show_title', 1);
		$this->_item[$pk]->params->set('access-view', true);
		$this->_item[$pk]->params->set('access-edit', 0);
		$this->_item[$pk]->params->set('show_print_icon', 0);
		$this->_item[$pk]->params->set('show_email_icon', 0);
		$this->_item[$pk]->params->set('show_author', 0);
		$this->_item[$pk]->params->set('show_category', 0);
		$this->_item[$pk]->params->set('show_parent_category', 0);
		$this->_item[$pk]->params->set('show_create_date', 0);
		$this->_item[$pk]->params->set('show_modify_date', 0);
		$this->_item[$pk]->params->set('show_publish_date', 0);
		$this->_item[$pk]->params->set('show_hits', 0);

		$this->getState('params')->set('show_page_heading', 0);

		return $this->_item[$pk];
	}

	/**
	 * Increment the hit counter for the article.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function hit ($pk = 0)
	{
		return true;
	}

	public function storeVote ($pk = 0, $rate = 0)
	{
		return true;
	}
}
