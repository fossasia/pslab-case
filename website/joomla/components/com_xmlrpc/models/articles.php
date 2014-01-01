<?php
/**
 * 			XMLRPC Component Articles Model
 * @version		2.0.7
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

JFactory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR);

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_content/models/articles.php';

/**
 * This models supports retrieving lists of articles.
 *
 * @package		com_xmlrpc
 */
class XMLRPCModelArticles extends ContentModelArticles
{
	protected function getListQuery()
	{
		$db = $this->getDbo();

		$query  = parent::getListQuery();

		$select = (string)$query->__get('select');

		if(strpos($select, 'introtext') === false)
		{
			$query->select('a.'. $db->qn('introtext'));
		}

		if(strpos($select, 'fulltext') === false)
		{
			$query->select('a.'. $db->qn('fulltext'));
		}

//		$query->select('CASE WHEN CHAR_LENGTH(a.'. $db->qn('introtext').') > 0 THEN a.'
//				. $db->qn('introtext'). ' ELSE a.'. $db->qn('fulltext'). ' END AS description');

		return $query;
	}
}
