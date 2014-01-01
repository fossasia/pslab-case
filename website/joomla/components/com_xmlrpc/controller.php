<?php
/**
 * 			XMLRPC
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class XMLRPCController extends JControllerLegacy
{
	public function weblayout($preview=false)
	{
		require_once (JPATH_SITE.'/components/com_content/helpers/route.php');

		$model = $this->getModel('Template');
		$this->addViewPath(JPATH_SITE.'/components/com_content/views');
		$view = $this->getView('Article', 'html', 'ContentView');
		$view->setModel($model, true);
		$doc = JFactory::getDocument();
		$view->assignRef('document', $doc);
		$view->addTemplatePath(JPATH_SITE.'/components/com_content/views/article/tmpl');
		$view->display();
		$view->document->setMetaData('title', '');
		return;
	}

	public function webpreview()
	{
		$this->weblayout(true);
	}
}