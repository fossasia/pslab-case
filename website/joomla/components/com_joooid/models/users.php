<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JFactory::getLanguage()->load('com_users', JPATH_ADMINISTRATOR);

require_once(JPATH_ADMINISTRATOR.'/components/com_users/models/users.php');

class JOOOIDModelUsers extends UsersModelUsers
{
	protected $context = 'com_joooid.users';

	protected function populateState($ordering = null, $direction = null) {
	    parent::populateState(JRequest::getVar('order_key'), JRequest::getVar('order_filter'));
	    $this->setState('list.ordering',JRequest::getVar('order_key'));
	    $this->setState('list.direction',JRequest::getVar('order_filter'));
	}

}

