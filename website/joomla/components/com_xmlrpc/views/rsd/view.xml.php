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

/**
 * XML View class for the XMLRPC component
 *
 * @package		Joomla.Site
 * @subpackage	com_xmlrpc
 * @since		1.5
 */
class XMLRPCViewRSD extends JViewLegacy
{
	function display($tpl = null)
	{
		if(!JComponentHelper::getParams('com_xmlrpc')->get('show_rsd', 1)){
			JFactory::getApplication()->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$xml		= $this->get('xml');

		$this->assign('xml', $xml);

		parent::display($tpl);
	}
}
