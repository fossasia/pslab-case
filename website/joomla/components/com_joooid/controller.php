<?php
/**
 * 			JOOOID Controller
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

function joooid_log($data){
}



class JOOOIDController extends JControllerLegacy
{
	public function service()
	{
		try{
			error_reporting(0);
			$app = JFactory::getApplication();

			$params = JComponentHelper::getParams('com_joooid');

			$GLOBALS['joooid_debug']=$params->get('debug', JDEBUG);
			joooid_log("########## ".date(DATE_RFC822)."\nRichiesta:\n ###########\n");

			$plugin = $params->get('plugin', 'movabletype');

			JPluginHelper::importPlugin('xmlrpc');
			$allCalls = $app->triggerEvent('onGetWebServices');
			if(count($allCalls) < 1){
				joooid_log("-------------------\n");
				joooid_log("[ERRORE]:".JText::_('COM_JOOOID_SERVICE_WAS_NOT_FOUND')."\n");
				if (isset($caller[1]['function']))
					joooid_log("Class:".$caller[1]['class']."\n");
				joooid_log("Function:".$caller[1]['function']."\n");
				joooid_log("File:".$caller[0]['file']."\n");
				joooid_log("Line:".$caller[0]['line']."\n");
				joooid_log("-------------------\n");
						
				JError::raiseError(404, JText::_('COM_JOOOID_SERVICE_WAS_NOT_FOUND'));
			}

			$methodsArray = array();

			foreach ($allCalls as $calls) {
				$methodsArray = array_merge($methodsArray, $calls);
			}

			@mb_regex_encoding('UTF-8');
			@mb_internal_encoding('UTF-8');

			require_once dirname(__FILE__).'/'.'libraries'.'/'.'phpxmlrpc'.'/'.'xmlrpc.php';
			require_once dirname(__FILE__).'/'.'libraries'.'/'.'phpxmlrpc'.'/'.'xmlrpcs.php';
			require_once (JPATH_SITE.'/'.'components'.'/'.'com_content'.'/'.'helpers'.'/'.'route.php');
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_content/tables');

			$xmlrpc = new xmlrpc_server($methodsArray, false);
			$xmlrpc->functions_parameters_type = 'phpvals';

			$encoding = 'UTF-8';

			$xmlrpc->xml_header($encoding);
			$GLOBALS['xmlrpc_internalencoding'] = $encoding;
			$xmlrpc->setDebug($params->get('debug', JDEBUG));

			$data = file_get_contents('php://input');
			

			if (count($_FILES)>0&& isset($_POST['request'])){
				$data = $_POST['request'];
				
			}
			else if(empty($data)){
			print_r(count($_FILES));die;
				joooid_log("-------------------\n");
				joooid_log("[ERRORE]:". JText::_('COM_JOOOID_INVALID_REQUEST')."\n");
				if (isset($caller[1]['function']))
					joooid_log("Class:".$caller[1]['class']."\n");
				joooid_log("Function:".$caller[1]['function']."\n");
				joooid_log("File:".$caller[0]['file']."\n");
				joooid_log("Line:".$caller[0]['line']."\n");
				joooid_log("-------------------\n");
				
				JError::raiseError(403, JText::_('COM_JOOOID_INVALID_REQUEST'));
			}

			$xmlrpc->service($data);
		}
		catch (Exception $e){
				joooid_log($e->getMessage());
				return $this->response($e->getMessage());
		}

		jexit();
	}

	public function weblayout($preview=false)
	{
		require_once (JPATH_SITE.'/'.'components'.'/'.'com_content'.'/'.'helpers'.'/'.'route.php');

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
