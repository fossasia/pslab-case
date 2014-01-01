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

defined('_JEXEC') or die;

/**
 * XML View class for the XMLRPC component
 *
 * @package		com_xmlrpc
 */
class XMLRPCViewService extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$params = JComponentHelper::getParams('com_xmlrpc');

		$plugin = $params->get('plugin', 'joomla');

		JPluginHelper::importPlugin('xmlrpc', strtolower($plugin));
		$allCalls = $app->triggerEvent('onGetWebServices');
		if(count($allCalls) < 1){
			JError::raiseError(404, JText::_('COM_XMLRPC_SERVICE_WAS_NOT_FOUND'));
		}

		$methodsArray = array();

		foreach ($allCalls as $calls) {
			$methodsArray = array_merge($methodsArray, $calls);
		}

		@mb_regex_encoding('UTF-8');
		@mb_internal_encoding('UTF-8');

		require_once JPATH_COMPONENT_SITE.'/libraries/phpxmlrpc/xmlrpc.php';
		require_once JPATH_COMPONENT_SITE.'/libraries/phpxmlrpc/xmlrpcs.php';
		require_once (JPATH_SITE.'/components/com_content/helpers/route.php');
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_content/tables');

		$xmlrpc = new xmlrpc_server($methodsArray, false);
		$xmlrpc->functions_parameters_type = 'phpvals';

		$encoding = 'UTF-8';

		$xmlrpc->xml_header($encoding);
		$GLOBALS['xmlrpc_internalencoding'] = $encoding;
		$xmlrpc->setDebug($params->get('debug', JDEBUG));
		@ini_set( 'display_errors', $params->get('display_errors', 0));

		$data = file_get_contents('php://input');

		if(empty($data)){
			JError::raiseError(403, JText::_('COM_XMLRPC_INVALID_REQUEST'));
		}

		$xmlrpc->service($data);
	}
}
