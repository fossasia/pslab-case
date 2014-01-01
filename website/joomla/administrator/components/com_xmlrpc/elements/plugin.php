<?php
/**
 * 			Form Field Plugin
 * @version		2.0.0
 * @package		XMLRPC for Joomla!
 * @copyright		Copyright (C) 2007 - 2012 Yoshiki Kozaki All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author		Yoshiki Kozaki  info@joomler.net
 * @link 			http://www.joomler.net/
 */

// No direct access.
defined('JPATH_BASE') or die;

/**
 * Renders a filelist element
 *
 * @package		Joomla.Framework
 * @subpackage	Parameter
 * @since		1.5
 */

class JFormFieldPlugin extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $type = 'Plugin';

	protected function getInput()
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);
		$query->select('element AS value, element AS text');
		$query->from('`#__extensions`');
		$query->where('`type` = '. $db->quote('plugin'));
		$query->where('`folder`= '. $db->quote('xmlrpc'));
		$query->where('`access` IN ('. implode(',', $user->getAuthorisedViewLevels()). ')');
		$query->where('`enabled` = 1');
		$query->order('`extension_id` DESC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if(count($rows) < 1){
			return JText::_('COM_XMLRPC_WARNING_NOTHAVE_PLUGIN');
		}

		return JHtml::_('select.genericlist', $rows, $this->getName($this->fieldname),
			array(
				'id' => $this->id,
				'list.attr' => 'class="inputbox" size="1"',
				'list.select' => $this->value
			)
		);
	}
}