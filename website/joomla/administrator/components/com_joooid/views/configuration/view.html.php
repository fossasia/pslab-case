<?php
/**
 * 			JOOOID View Configuration
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

jimport('joomla.application.component.view');

class JOOOIDViewConfiguration extends JViewLegacy
{
	public function display($tpl = null)
	{
		$style = '.icon-48-joooid{background:url(components/com_joooid/assets/images/joooid48.png)}';
		JFactory::getDocument()->addStyleDeclaration($style);


		$joooid_plugins = array();
		$this->addArray($joooid_plugins ,JPluginHelper::getPlugin('xmlrpc'),'joooidContent');
		$this->addArray($joooid_plugins ,JPluginHelper::getPlugin('content'),'');
		$this->assign('joooid_plugins', $joooid_plugins);

		JToolBarHelper::title(JText::_('COM_JOOOID_TITLE'), 'joooid.png');
		JToolBarHelper::preferences('com_joooid');
		parent::display($tpl);
	}
	private function addArray(&$a,$val,$filter){
		for ($i=0;$i<count($val);$i++){
			if ($val[$i]->name=="joooid"||$val[$i]->name=="joooidcontent"){
				return array_push($a ,$val[$i]);
			}
		}
	}
}
?>
