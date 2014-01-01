<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

JFactory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR);

require_once JPATH_ADMINISTRATOR.'/components/com_content/models/article.php';

class JOOOIDModelArticle extends ContentModelArticle
{
	protected $text_prefix = 'COM_CONTENT';

	public function canEditState($record)
	{
		return parent::canEditState($record);
	}

	public function getItem($pk = null)
	{
		$getTemplate = false;
		if(in_array(JRequest::getCmd('task'), array('weblayout', 'webpreview'))){
			$params = JComponentHelper::getParams('com_joooid');
			$pk = intval($params->get('template_content_id', 8));
			JRequest::setVar('id', $pk);
			$getTemplate = true;

			$params->set('access-view', true);
			$this->setState('params', $params);
		}

		$item = parent::getItem($pk);
		if ($item) {
			if($getTemplate){
				$item->introtext = '{post-body}';
				$item->fulltext = '';
				$item->title = '{post-title}';
			}
		}

		return $item;
	}

	public function getForm($data = array(), $loadData = true)
	{}

	public function allowAdd($data = array())
	{
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', 0, 'int');
		$allow		= null;

		if ($categoryId) {
			$allow	= $user->authorise('core.create', 'com_content.category.'.$categoryId);
		}

		if ($allow === null) {
			return $user->authorise('core.create', 'com_content') || (count($user->getAuthorisedCategories('com_content', 'core.create')));
		}
		else {
			return $allow;
		}
	}

	public function allowEdit($data = array(), $key = 'id')
	{
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		if ($user->authorise('core.edit', 'com_content.article.'.$recordId)) {
			return true;
		}

		if ($user->authorise('core.edit.own', 'com_content.article.'.$recordId)) {
			$ownerId	= (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId) {
				$record		= $this->getItem($recordId);

				if (empty($record)) {
					return false;
				}

				$ownerId = $record->created_by;
			}

			if ($ownerId == $userId) {
				return true;
			}
		}

		return $user->authorise('core.edit', 'com_content');
	}

}
