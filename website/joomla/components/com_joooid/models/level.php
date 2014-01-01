<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

JFactory::getLanguage()->load('com_users', JPATH_ADMINISTRATOR);

require_once JPATH_ADMINISTRATOR.'/components/com_users/models/level.php';

class JOOOIDModelLevel extends UsersModelLevel
{

}

