<?php
/**
 * 			JOOOID
 * @version		1.0.0
 * @package		JOOOID for Joomla!
 * @copyright		Copyright (C) 2007-2011 Joomler!.net. All rights reserved.
 * @license		GNU/GPL 2.0 or higher
 * @author		Joomler!.net  joomlers@gmail.com
 * @link			http://www.joomler.net
 */

/**
* @package		Joomla
* @copyright		Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL
*/

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_joooid')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
JRequest::setVar('view', 'configuration');
// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('joooid');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

?>
