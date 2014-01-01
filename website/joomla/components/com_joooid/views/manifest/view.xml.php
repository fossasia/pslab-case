<?php
/**
 * 			JOOOID View Manifest
 * @version			1.0.0
 * @package			JOOOID for Joomla!
 * @copyright			Copyright (C) 2007-2011 Joomler!.net. All rights reserved.
 * @license			http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author			Yoshiki Kozaki : joomlers@gmail.com
 * @link			http://www.joomler.net/
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * XML View class for the JOOOID component
 *
 * @package		Joomla.Site
 * @subpackage	com_joooid
 * @since		1.5
 */
class JOOOIDViewManifest extends JView
{
	function display($tpl = null)
	{
		if(!JComponentHelper::getParams('com_joooid')->get('show_manifest')){
			JFactory::getApplication()->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$xml		= $this->get('xml');

		$this->assign('xml', $xml);

		parent::display($tpl);
	}
}
