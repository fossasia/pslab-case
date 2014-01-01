<?php
/**
 * 			XMLRPC
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

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class XMLRPCViewConfiguration extends JViewLegacy
{
	public function display($tpl = null)
	{
		$style = '.icon-48-xmlrpc{background:url(components/com_xmlrpc/assets/images/xmlrpc48.png)}';
		JFactory::getDocument()->addStyleDeclaration($style);

		$xmlrpc_plugins = JPluginHelper::getPlugin('xmlrpc');
		$rsd_plugins = JPluginHelper::getPlugin('system', 'rsd');

		$this->assign('xmlrpc_plugins', $xmlrpc_plugins);
		$this->assign('rsd_plugins', $rsd_plugins);

		JToolBarHelper::title(JText::_('COM_XMLRPC_TITLE'), 'xmlrpc.png');
		JToolBarHelper::preferences('com_xmlrpc');
		parent::display($tpl);
	}
}