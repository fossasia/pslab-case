<?php
/**
 * 			XMLRPC
 * @version		2.0.6
 * @package		XMLRPC for Joomla!
 * @copyright		Copyright (C) 2007 - 2013 Yoshiki Kozaki All rights reserved.
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

/**
 * ABOUT jMT_API
 * @package jMT_API
 * @version 1.0a
 * @copyright Copyright (C) 2006 dex_stern. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die();

class plgXMLRPCJoomla extends JPlugin
{

	protected $beforewrapid = '(';
	protected $afterwrapid  = ')';

	public function __construct(&$subject, $config)
	{
		$this->writeLog('__construct');

		parent::__construct($subject, $config);
		$this->loadLanguage('', JPATH_ADMINISTRATOR);

		$this->beforewrapid = ' ' . trim($this->params->get('beforewrapid', $this->beforewrapid));
		$this->afterwrapid = trim($this->params->get('afterwrapid', $this->afterwrapid));
	}

	public function onGetWebServices()
	{
		$this->writeLog('getCatTitle');

		return array
			(
			'blogger.getUsersBlogs' => array('function' => array($this, 'blogger_getUserBlogs'), 'signature'  => null),
			'blogger.getUserInfo' => array('function' => array($this, 'blogger_getUserInfo'), 'signature' => null),
			'blogger.getRecentPosts' => array('function' => array($this, 'blogger_getRecentPosts'), 'signature'  => null),
			'blogger.newPost' => array('function' => array($this, 'blogger_newPost'), 'signature' => null),
			'blogger.deletePost' => array('function' => array($this, 'blogger_deletePost'), 'signature' => null),
			'blogger.editPost' => array('function' => array($this, 'blogger_editPost'), 'signature'  => null),
			/*	* * new * */
			'blogger.getTemplate' => array('function' => array($this, 'blogger_editPost'), 'signature' => null),
			'metaWeblog.getUsersBlogs' => array('function' => array($this, 'blogger_getUserBlogs'), 'signature' => null),
			'metaWeblog.getUserInfo' => array('function' => array($this, 'blogger_getUserInfo'), 'signature' => null),
			'metaWeblog.deletePost' => array('function' => array($this, 'blogger_deletePost'), 'signature' => null),
			'wp.getUsersBlogs' => array('function' => array($this, 'wp_getUserBlogs'), 'signature' => null),
			'wp.getAuthors' => array('function' => array($this, 'wp_getAuthors'), 'signature' => null),
			/** end new * */
			'metaWeblog.newPost' => array('function' => array($this, 'mw_newPost'), 'signature'  => null),
			'metaWeblog.editPost' => array('function' => array($this, 'mw_editPost'), 'signature' => null),
			'metaWeblog.getPost' => array('function' => array($this, 'mw_getPost'), 'signature' => null),
			'metaWeblog.newMediaObject' => array('function' => array($this, 'mw_newMediaObject'), 'signature' => null),
			'metaWeblog.getRecentPosts' => array('function' => array($this, 'mw_getRecentPosts'), 'signature' => null),
			'metaWeblog.getCategories' => array('function' => array($this, 'mw_getCategories'), 'signature' => null),
			'mt.getCategoryList' => array('function' => array($this, 'mt_getCategoryList'), 'signature' => null),
			'mt.getPostCategories' => array('function' => array($this, 'mt_getPostCategories'), 'signature' => null),
			'mt.setPostCategories' => array('function' => array($this, 'mt_setPostCategories'), 'signature' => null),
			'mt.getRecentPostTitles' => array('function' => array($this, 'mt_getRecentPostTitles'), 'signature'  => null),
			'mt.supportedTextFilters' => array('function' => array($this, 'mt_supportedTextFilters'), 'signature' => null),
			'mt.publishPost' => array('function' => array($this, 'mt_publishPost'), 'signature' => null),
			'mt.getTrackbackPings' => array('function' => array($this, 'mt_getTrackbackPings'), 'signature'  => null),
			'mt.supportedMethods' => array('function' => array($this, 'mt_supportedMethods'), 'signature' => null),
			'wp.getCategories' => array('function' => array($this, 'wp_getCategories'), 'signature' => null),
			'wp.newCategory' => array('function' => array($this, 'wp_newCategory'), 'signature' => null),
			'wp.getTags' => array( 'function' => array($this, 'wp_getTags'), 'signature' => null )
		);
	}

	public function wp_getAuthors()
	{
		$this->writeLog('wp_getAuthors');

		global $xmlrpcerruser;

		$args = func_get_args();

		if(func_num_args() != 3)
		{
			return new xmlrpcresp(0, $xmlrpcerruser + 1, JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$username = $args[1];
		$password = $args[2];

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		//Check permission
		if(!$user->authorise('com_xmlrpc', 'core.edit'))
		{
			return new xmlrpcresp(new xmlrpcval(array(), 'array'));
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from('#__usergroups');
		$query->order('id');
		$db->setQuery($query);
		$groups = $db->loadColumn();

		$gids = array();
		foreach($groups as $gid)
		{
			if(JAccess::checkGroup($gid, 'core.edit', 'com_content') || JAccess::checkGroup($gid, 'core.admin')){
				$gids[] = $gid;
			}
		}

		if(count($gids) < 1)
		{
			return new xmlrpcresp(new xmlrpcval(array(), 'array'));
		}

		$query = $db->getQuery(true);

		$query->select('a.id, a.name, a.username');
		$query->from('#__users AS a');
		$query->innerJoin('#__user_usergroup_map AS b ON b.user_id = a.id');
		$query->innerJoin('#__usergroups AS c ON c.id = b.group_id');
		$query->where('c.id IN('. implode(', ', $gids). ')');

		$db->setQuery($query);

		$users = $db->loadObjectList();

		if(count($users) < 1){
			return new xmlrpcresp(new xmlrpcval(array(), 'array'));
		}

		$structs = array();
		$array = array();
		foreach($users as $u)
		{
			//Own
			if($user->id == $u->id) continue;

			$array['user_id'] = new xmlrpcval($u->id, 'string');
			$array['user_login'] = new xmlrpcval(0, 'string');//ignore
			$array['display_name'] = new xmlrpcval($u->name, 'string');
			$array['meta_value'] = new xmlrpcval('', 'string');
			$structs[] = new xmlrpcval($array, 'struct');
		}

		return new xmlrpcresp(new xmlrpcval($structs, 'array'));
	}

	public function wp_getCategories()
	{
		$this->writeLog('wp_getCategories');

		global $xmlrpcerruser;

		$args = func_get_args();

		if (func_num_args() < 3)
		{
			return new xmlrpcresp(0, $xmlrpcerruser + 1, JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$username = strval($args[1]);
		$password = strval($args[2]);

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$structarray = array();

		JRequest::setVar('limit', 0);
		$model = $this->getModel('Categories');
		$categories = $model->getItems();

		if (empty($categories))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CATEGORY_WAS_NOT_FOUND'));
		}

		$array = array();

		//Featured
		$array['categoryId'] = new xmlrpcval('-1', 'string');
		$array['parentId'] = new xmlrpcval('0', 'string');
		$array['description'] = new xmlrpcval(JText::_('PLG_XMLRPC_JOOMLA_FEATURED_DESCRIPTION'), 'string');
		$array['categoryDescription'] = new xmlrpcval('PLG_XMLRPC_JOOMLA_FEATURED_DESCRIPTION', 'string');
		$array['categoryName'] = new xmlrpcval($this->buildCategoryTitle(JText::_('PLG_XMLRPC_JOOMLA_FEATURED_TITLE'), 0, true), 'string');
//		$array['categoryName'] = new xmlrpcval( JText::_('PLG_XMLRPC_JOOMLA_FEATURED_TITLE'), 'string' );
		$array['htmlUrl'] = new xmlrpcval(JURI::root(), 'string');
		$array['rssUrl']  = new xmlrpcval(JURI::root() . '/index.php?format=feed', 'string');
		$structarray[] = new xmlrpcval($array, 'struct');

		foreach ($categories as $row)
		{
			if ($row->published < 1)
			{
				if (!$user->authorise('core.edit.state', 'com_content.category.' . $row->id))
				{
					continue;
				}

				if (!$user->authorise('core.admin', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id'))
				{
					continue;
				}
			}

			$array = array();

			if(!isset($row->description))
			{
				$row->description = '';
			}
			$array['categoryId']  = new xmlrpcval($row->id, 'string');
			$array['parentId'] = new xmlrpcval($row->parent_id, 'string');
			$array['description'] = new xmlrpcval($row->description, 'string');
			$array['categoryDescription'] = new xmlrpcval($row->description, 'string');
//			$array['categoryName'] = new xmlrpcval( $row->title, 'string' );
			$array['categoryName'] = new xmlrpcval($this->buildCategoryTitle($row->title, $row->id), 'string');
			$array['htmlUrl'] = new xmlrpcval(JRoute::_(ContentHelperRoute::getCategoryRoute($row->id)), 'string');
			$array['rssUrl']  = new xmlrpcval(JRoute::_(ContentHelperRoute::getCategoryRoute($row->id) . '&format=feed'), 'string');

			$structarray[] = new xmlrpcval($array, 'struct');
		}

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
	}

	public function wp_newCategory()
	{
		$this->writeLog('wp_newCategory');

		global $xmlrpcerruser;

		$args = func_get_args();

		if (func_num_args() < 4)
		{
			return new xmlrpcresp(0, $xmlrpcerruser + 1, JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$username = strval($args[1]);
		$password = strval($args[2]);
		$category = $args[3];

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		if (!$user->authorise('core.create', 'com_content'))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if (empty($category['name']))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CATEGORY_MUST_HAVE_TITLE'));
		}

		$category['title'] = $category['name'];
		unset($category['name']);

		$category['extension'] = 'com_content';
		$category['published'] = 1;
		$category['language'] = $this->params->get('language', '*');

		$model = $this->getModel('Category');
		if (!$model->save($category))
		{
			return $this->response($model->getError());
		}

		return (new xmlrpcresp(new xmlrpcval($model->getState('category.id'), 'string')));
	}

	public function wp_getUserBlogs()
	{
		$this->writeLog('wp_getUserBlogs');

		$args = func_get_args();

		if (func_num_args() != 2)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		return $this->blogger_getUserBlogs('wp', $args[0], $args[1]);
	}

	public function wp_getTags()
	{
		$this->writeLog('wp_getTags');

		$args = func_get_args();
		if(func_num_args() != 3){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$article_id = (int)$args[0];
		$username = strval( $args[1] );
		$password = strval( $args[2] );

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id, a.title AS name, a.hits AS count');
		$query->select('CONCAT(a.id, ":", a.alias) AS slug');
		$query->from('#__tags AS a');
		$query->join('LEFT', '#__contentitem_tag_map AS b ON b.tag_id = a.id');
		//WLW sent blogid always
		if($article_id > 1){
			$query->where('b.content_item_id = '. $article_id);
		}

		$query->where('a.id > 1');//no root

		$query->order('a.title');

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		$array = array();
		if(count($rows)){
			JLoader::register('TagsHelperRoute', JPATH_ROOT.'/components/com_tags/helpers/route.php');

			foreach($rows as $row){
				$struct = array();
				$struct['tag_id'] = new xmlrpcval($row->id, 'int');
				$struct['name'] = new xmlrpcval( $row->name, 'string');
				$struct['count'] = new xmlrpcval($row->count, 'int');
				$struct['html_url'] = new xmlrpcval(JRoute::_(TagsHelperRoute::getTagRoute($row->slug)), 'string');
				$struct['rss_url'] = new xmlrpcval( JRoute::_(TagsHelperRoute::getTagRoute($row->slug). '&format=feed'), 'string');
				$array[] = new xmlrpcval($struct, 'struct');
			}
		}

		return new xmlrpcresp(new xmlrpcval($array, 'array'), 'array');
	}

	public function blogger_getUserBlogs()
	{
		$this->writeLog('blogger_getUserBlogs');

		$args = func_get_args();

		if (func_num_args() < 3)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

//		$wp   = ($args[0] === 'wp');
		$username = $args[1];
		$password = $args[2];

		$mt = false;

		if (isset($args[3]))
		{
			$mt = (boolean) $args[3];
		}

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

//		$db =JFactory::getDbo();
		$app = JFactory::getApplication();

		$structarray = array();

		if (!$mt)
		{
			$site_name = $app->getCfg('sitename');
			$structarray[] = new xmlrpcval(
							array(
								'url'  => new xmlrpcval(JURI::root(), 'string'),
								'blogid'   => new xmlrpcval(0, 'string'),
								'blogName' => new xmlrpcval($site_name, 'string'))
							, 'struct');
			return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
		}

		$model  = $this->getModel('Categories');
		$categories = $model->getItems();

		if (empty($categories))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CATEGORY_WAS_NOT_FOUND'));
		}

		$structarray[] = new xmlrpcval(
			array(
				'categoryId'   => new xmlrpcval('-1', 'string'),
				'categoryName' => new xmlrpcval(
						$this->buildCategoryTitle(JText::_('PLG_XMLRPC_JOOMLA_FEATURED_TITLE'), 0, true), 'string')
			),
			'struct'
		);

		foreach ($categories as $row)
		{
			if ($row->published < 1)
			{
				if (!$user->authorise('core.edit.state', 'com_content.category.' . $row->id))
				{
					continue;
				}

				if (!$user->authorise('core.admin', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id'))
				{
					continue;
				}
			}

			$row->title = str_repeat(' ...', $row->level - 1) . $row->title;
			$structarray[] = new xmlrpcval(
				array(
					'categoryId'   => new xmlrpcval($row->id, 'string'),
					'categoryName' => new xmlrpcval($this->buildCategoryTitle($row->title, $row->id), 'string')
					//				'categoryName' => new xmlrpcval($row->title, 'string')
				),
				'struct');
		}

		if (empty($structarray))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CATEGORY_WAS_NOT_FOUND'));
		}

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
	}

	public function blogger_getUserInfo()
	{
		$this->writeLog('blogger_getUserInfo');

		global $xmlrpcStruct;

		$args = func_get_args();

		if (func_num_args() < 3)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$username = strval($args[1]);
		$password = strval($args[2]);

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$name = $user->name;
		if (function_exists('mb_convert_kana'))
		{
			$name = mb_convert_kana($user->name, 's');
		}

		$names = explode(' ', $name);
		$firstname = $names[0];
		$lastname  = trim(str_replace($firstname, '', $name));

		$struct = new xmlrpcval(
						array(
							'nickname'  => new xmlrpcval($user->username),
							'userid' => new xmlrpcval($user->id),
							'url'   => new xmlrpcval(JURI::root()),
							'email' => new xmlrpcval($user->email),
							'lastname'  => new xmlrpcval($lastname),
							'firstname' => new xmlrpcval($firstname)
						), $xmlrpcStruct);

		return new xmlrpcresp($struct);
	}

	public function blogger_newPost()
	{
		$this->writeLog('blogger_newPost');

		$args = func_get_args();

		if (func_num_args() < 6)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid   = (int) $args[1];
		$username = strval($args[2]);
		$password = strval($args[3]);
		$content  = $args[4];
		$publish  = (int) $args[5];

		return $this->mw_newPost($blogid, $username, $password, $content, $publish, true);
	}

	public function blogger_editPost()
	{
		$this->writeLog('blogger_editPost');

		$args = func_get_args();

		if (func_num_args() < 6)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid   = (int) $args[1];
		$username = strval($args[2]);
		$password = strval($args[3]);
		$content  = $args[4];
		$publish  = (int) $args[5];

		return $this->mw_editPost($postid, $username, $password, $content, $publish, true);
	}

	public function blogger_deletePost()
	{
		$this->writeLog('blogger_deletePost');

		global $xmlrpcBoolean;

		$args = func_get_args();

		if (func_num_args() < 5)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid   = (int) $args[1];
		$username = $args[2];
		$password = $args[3];
//		$publish = (int)$args[4];

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$userid = intval($user->get('id'));

		JRequest::setVar('id', $postid);
		$model  = $this->getModel('Article');
		$model->set('option', 'com_content');

		$row = $model->getTable();
		$result = $row->load($postid);
		if (!$result)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		if (!$model->canEditState($row))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $userid)
		{
			return $this->response(JText::sprintf('PLG_XMLRPC_JOOMLA_EDITING_OTHER_USER', $row->title));
		}

		$model->checkout();

		$row->ordering = 0;
		$row->state = -2; //to trash

		if (!$row->check())
		{
			return $this->response($row->getError());
		}

		if (!$row->store())
		{
			return $this->response($row->getError());
		}

		$model->checkin();

		return new xmlrpcresp(new xmlrpcval('true', $xmlrpcBoolean));
	}

	public function blogger_getRecentPosts()
	{
		$this->writeLog('blogger_getRecentPosts');

		$args = func_get_args();

		if (func_num_args() < 5)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid   = (int) $args[1];
		$username = strval($args[2]);
		$password = strval($args[3]);
		$numposts = (int) $args[4];

		return $this->mw_getRecentPosts($blogid, $username, $password, $numposts);
	}

	public function mw_newPost()
	{
		$this->writeLog('mw_newPost');

		$args = func_get_args();

		if (func_num_args() < 4)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid   = (int) $args[0];
		$username = $args[1];
		$password = $args[2];
		$content  = $args[3];
		if (isset($args[4]))
			$publish  = $args[4];
		$blogger  = false;
		if (isset($args[5]))
		{
			$blogger = $args[5];
		}

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$content['catid'] = (int) $blogid;

		$data = $this->buildData($content, $publish, $blogger);

		if ($this->params->get('featured', 0))
		{
			$data['featured'] = 1;
		}

		$this->assignCategory($data);

		JRequest::setVar('id', 0);
		$model = $this->getModel('Article');
		$model->set('option', 'com_content');

		if ($model->allowAdd($data) !== true)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if (!$model->save($data))
		{
			return $this->response($model->getError());
		}

		return (new xmlrpcresp(new xmlrpcval($model->getState('article.id'), 'string')));
	}

	public function mw_editPost()
	{
		$this->writeLog('mw_editPost');

		$args = func_get_args();

		if (func_num_args() < 4)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid   = (int) $args[0];
		$username = $args[1];
		$password = $args[2];
		$content  = $args[3];
		$publish  = (int) $args[4];

		$blogger = false;

		if (isset($args[5]))
		{
			$blogger = $args[5];
		}

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$content['id'] = $postid;
		JRequest::setVar('id', $postid);

		$data = $this->buildData($content, $publish, $blogger);

		$this->assignCategory($data);

		JRequest::setVar('id', $postid);
		$model = $this->getModel('Article');
		$model->set('option', 'com_content');

		if ($model->allowEdit($data) !== true)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if($model->getItem()->featured && (!isset($data['featured']) || !$data['featured'])){
			$data['featured'] = 0;
		}

		if (!$model->save($data))
		{
			return $this->response($model->getError());
		}

		return (new xmlrpcresp(new xmlrpcval('1', 'boolean')));
	}

	public function mw_getPost()
	{
		$this->writeLog('mw_getPost');

		$args = func_get_args();

		if (func_num_args() < 3)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid   = (int) $args[0];
		$username = strval($args[1]);
		$password = strval($args[2]);

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		JRequest::setVar('id', $postid);
		$model = $this->getModel('Article');
		$model->set('option', 'com_content');

		$data = array();
		$data['id'] = $postid;

		if ($model->allowEdit($data) !== true)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		$row = $model->getItem($postid);
		if (empty($row))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		$ret = $this->buildStruct($row);

		if (!$ret[0])
		{
			return $this->response($ret[1]);
		}

		return new xmlrpcresp($ret[1]);
	}

	public function mw_getRecentPosts()
	{
		$this->writeLog('mw_getRecentPosts');

		global $xmlrpcArray;

		$args = func_get_args();

		if (func_num_args() < 3)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid   = (int) $args[0];
		$username = $args[1];
		$password = $args[2];

		$limit = 0;

		if (isset($args[3]))
		{
			$limit = (int) $args[3];
		}

		$mt = false;

		if (isset($args[5]))
		{
			$mt = (boolean) $args[5];
		}

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		if ($blogid > 0)
		{
			JRequest::setVar('filter_category_id', $blogid);
		}

		JRequest::setVar('limit', $limit);
		JRequest::setVar('filter_order', 'a.created');
		JRequest::setVar('filter_order_Dir', 'desc');
		$model = $this->getModel('Articles');
//		$model->setState('list.limit', $limit);

		$userid = (int) $user->get('id');

		$temp = $model->getItems();
		$articles = array();
		if (count($temp))
		{
			foreach ($temp as $row)
			{
				$canEdit = $user->authorise('core.edit', 'com_content.article.' . $row->id);
				$canCheckin = $user->authorise('core.manage', 'com_checkin') || $row->checked_out == $userid || $row->checked_out == 0;
				$canEditOwn = $user->authorise('core.edit.own', 'com_content.article.' . $row->id) && $row->created_by == $userid;

				if (($canEdit || $canEditOwn) && $canCheckin)
				{
					$res = $this->buildStruct($row, $mt);

					if ($res[0])
					{
						$articles[] = $res[1];
					}
				}
			}
		}

		return new xmlrpcresp(new xmlrpcval($articles, $xmlrpcArray));
	}

	public function mt_getPostCategories()
	{
		$this->writeLog('mt_getPostCategories');

		$args = func_get_args();

		if (func_num_args() < 3)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid   = (int) $args[0];
		$username = strval($args[1]);
		$password = strval($args[2]);

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		JRequest::setVar('id', $postid);
		$model = $this->getModel('Article');
		$model->set('option', 'com_content');

		$row   = $model->getItem($postid);
		if (!$row)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		$data = array();
		$data['id'] = $row->id;
		$data['created_by'] = $row->created_by;
		if ($model->allowEdit($data) !== true)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if (empty($row->catid))
		{
			return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
		}
		else
		{
			$cmodel   = $this->getModel('Category');
			$category = $cmodel->getItem((int) $row->catid);
			if (empty($category))
			{
				return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CATEGORY_WAS_NOT_FOUND'));
			}

			if (!$cmodel->canEditState($category) && $category->published < 1)
			{
				return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
			}
		}

		$structarray = array();

		//featured article
		if ($row->featured)
		{
			$structarray[] = $this->getFeatureStruct(true);
		}

		$structarray[] = new xmlrpcval(
						array('categoryName' => new xmlrpcval($category->title, 'string'),
							'categoryId'   => new xmlrpcval($category->id, 'string'),
							'isPrimary' => new xmlrpcval(1, 'boolean')),
						'struct');

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
	}

	public function mt_setPostCategories()
	{
		$this->writeLog('mt_setPostCategories');

		$args = func_get_args();

		if (func_num_args() < 4)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid = (int) $args[0];
		$username   = strval($args[1]);
		$password   = strval($args[2]);
		$categories = $args[3];

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		JRequest::setVar('id', $blogid);
		$model  = $this->getModel('Article');
		$model->set('option', 'com_content');

		$row = $model->getTable();
		$result = $row->load($blogid);
		if (!$result)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id'))
		{
			return $this->response(JText::sprintf('PLG_XMLRPC_JOOMLA_EDITING_OTHER_USER', $row->title));
		}

		$data = array();
		$data['id'] = $row->id;
		$data['created_by'] = $row->created_by;
		if ($model->allowEdit($data) !== true)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

//		$cmodel = $this->getModel('Category');

		if ($blogid && is_array($categories) && count($categories))
		{
			$model->checkout();

			$catid = 0;
			$primary_catid = 0;
			for ($i = 0; $i < count($categories); $i++)
			{
				if (!isset($categories[$i]['categoryId']))
				{
					continue;
				}

				if((int) $categories[$i]['categoryId'] < 1){
					continue;
				}

				$tempcatid = (int) $categories[$i]['categoryId'];

				if ($catid == 0)
				{
					$catid = $tempcatid;
				}

				if (isset($categories[$i]['isPrimary']) && $categories[$i]['isPrimary'])
				{
					$primary_catid = $tempcatid;
				}
			}

			if ($catid && $primary_catid && $primary_catid !== $catid)
			{
				$catid = $primary_catid;
			}

			if (!$catid)
			{
				return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CORRECT_CATEGORY'));
			}

			$row->catid = $catid;

			$data = $row->getProperties(true);

			if(!$model->save($data))
			{
				return $this->response($model->getError());
			}

			$model->checkin();
		}

		return (new xmlrpcresp(new xmlrpcval('1', 'boolean')));
	}

	public function mt_getRecentPostTitles()
	{
		$this->writeLog('mt_getRecentPostTitles');

		$args = func_get_args();

		if (func_num_args() < 4)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid   = (int) $args[0];
		$username = strval($args[1]);
		$password = strval($args[2]);

		$limit = 0;

		if (isset($args[4]))
		{
			$limit = (int) $args[4];
		}

		return $this->mw_getRecentPosts($blogid, $username, $password, $limit, true);
	}

	public function mt_getCategoryList()
	{
		$this->writeLog('mt_getCategoryList');

		$args = func_get_args();

		if (func_num_args() < 3)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid   = (int) $args[0];
		$username = strval($args[1]);
		$password = strval($args[2]);

		return $this->blogger_getUserBlogs($blogid, $username, $password, true);
	}

	public function mt_supportedTextFilters()
	{
		$this->writeLog('mt_supportedTextFilters');

		return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
	}

	public function mt_publishPost()
	{
		$this->writeLog('mt_publishPost');

		$args = func_get_args();

		if (func_num_args() < 3)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid   = (int) $args[0];
		$username = strval($args[1]);
		$password = strval($args[2]);

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		JRequest::setVar('id', $postid);
		$model  = $this->getModel('Article');
		$model->set('option', 'com_content');

		$row = $model->getTable();
		$result = $row->load($postid);
		if (!$result)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		if (!$user->authorise('core.edit.state', 'com_content.article.' . $row->id))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id'))
		{
			return $this->response(JText::sprintf('PLG_XMLRPC_JOOMLA_EDITING_OTHER_USER', $row->title));
		}

		$data = array();
		$data['id'] = $row->id;
		$data['created_by'] = $row->created_by;
		if ($model->allowEdit($data) !== true)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		$model->checkout();

		$row->state = 1;
		if (!$row->check())
		{
			return $this->response($row->getError());
		}

		$row->version++;

		if (!$row->store())
		{
			return $this->response($row->getError());
		}

		$model->checkin();

		return (new xmlrpcresp(new xmlrpcval('1', 'boolean')));
	}

	public function mt_getTrackbackPings()
	{
		$this->writeLog('mt_getTrackbackPings');

//		$args = func_get_args();

		if (func_num_args() < 1)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

//		$blogid = (int)$args[0];
		//pingIP, pingURL, pingTitle
		return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
	}

	public function mt_supportedMethods()
	{
		$this->writeLog('mt_supportedMethods');

		return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
	}

	public function mw_newMediaObject()
	{
		$this->writeLog('mw_newMediaObject');

		$args = func_get_args();

		if (func_num_args() < 4)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid  = (int) $args[0];
		$username = strval($args[1]);
		$password = strval($args[2]);
		$file_struct = $args[3];

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		/**
		 * It seems that WLW may be uploaded first. (The article is not registered.)
		 */
//		JRequest::setVar('id', $blogid);
//		$model  = $this->getModel('Article');
//		$model->set('option', 'com_content');
//
//		$row = $model->getTable();
//		$result = $row->load($blogid);
//		if ($result)
//		{
//			if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id'))
//			{
//				return $this->response(JText::sprintf('PLG_XMLRPC_JOOMLA_EDITING_OTHER_USER', $row->title));
//			}
//
//			$data = array();
//			$data['id'] = $row->id;
//			$data['created_by'] = $row->created_by;
//			if ($model->allowEdit($data) !== true)
//			{
//				return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
//			}
//		}
//		else
//		{
//			//no check
//		}

		$file  = $file_struct['bits'];

		$params = JComponentHelper::getParams('com_media');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		if (empty($file))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_FILE_EMPTY'));
		}
		//File size check
		$maxSize = (int) ($params->get('upload_maxsize', 0) * 1024 * 1024);
		if ($maxSize && strlen($file) > $maxSize)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_NOT_ALLOWED_FILE_SIZE'));
		}

		if (empty($file_struct['name']))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_FILE_EMPTY'));
		}

		//filename check
		$temp  = pathinfo($file_struct['name']);
		$file_name = strtolower(JFile::makeSafe(str_replace(' ', '_', trim($temp['basename']))));
		if (empty($file_name))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_FILENAME_EMPTY'));
		}

		$ext   = JFile::getExt($file_name);

		$allowable = explode(',', $params->get('upload_extensions'));
		$ignored   = explode(',', $params->get('ignore_extensions'));
		$images = explode(',', $params->get('image_extensions'));

		if (!in_array($ext, $allowable) && !in_array($ext, $ignored))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_NOT_ALLOWED_FILETYPE'));
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_media/helpers/media.php';

		$images_path = str_replace(array('/', '\\'), '/', JPATH_ROOT . '/' . $params->get('image_path', 'images'));
		$file_path   = str_replace(array('/', '\\'), '/', JPATH_ROOT . '/' . $params->get('file_path', 'images'));

		if (in_array($ext, $images))
		{
			$destination = $images_path;
		}
		else
		{
			$destination = $file_path;
		}

		$destination .= '/';

		if ($this->params->get('userfolder'))
		{
			$userfolder = JFile::makeSafe($username);
			if (!empty($userfolder))
			{
				$destination .= $userfolder;
				if (!JFolder::exists($destination))
				{
					if (!JFolder::create($destination))
					{
						return $this->response(JText::_('PLG_XMLRPC_JOOMLA_NOT_ABLE_TO_CREATE_FOLDER'));
					}
				}

				if (!JFile::exists($destination . '/' . 'index.html'))
				{
					$html = '<html><body></body></html>';
					JFile::write($destination . '/' . 'index.html', $html);
				}

				$destination .= '/';
			}
		}

		if (file_exists($destination . $file_name) && (/* !isset($file_struct['overwrite']) || !$file_struct['overwrite'] || */!$this->params->get('overwrite')))
		{
			$nameonly  = str_replace(strrchr($file_name, '.'), '', $file_name); //for 1.5.10 or under
			$nameonly .= '_' . JApplication::getHash(microtime() * 1000000);
			$file_name = JFile::makeSafe($nameonly . '.' . $ext);
		}

		if (!JFile::write($destination . $file_name, $file))
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_NOT_ABLE_TO_UPLOAD_FILE'));
		}

		if (!file_exists($destination . $file_name))
		{
			return $this->response(JText::sprintf('PLG_XMLRPC_JOOMLA_NOT_ABLE_TO_UPLOAD_FILE'));
		}

		//Change default to 0
		if($this->params->get('absolute_link', 0)){
			$url = rtrim(JURI::root(), '/');
		} else {
			$url = JURI::root(true);
		}

		$root_path = str_replace(DIRECTORY_SEPARATOR, '/', JPATH_ROOT);
		$url .= str_replace(array($root_path, '/'), array('', '/'), $destination . $file_name);

		$responce_struct = array('url' => new xmlrpcval($url, 'string'));

		return (new xmlrpcresp(new xmlrpcval($responce_struct, 'struct')));
	}

	protected function authenticateUser($username, $password)
	{
		$this->writeLog('authenticateUser');

		jimport('joomla.user.authentication');
		$auth = JAuthentication::getInstance();
		$credentials['username'] = $username;
		$credentials['password'] = $password;
		$authuser = $auth->authenticate($credentials, null);

		if ($authuser->status == JAUTHENTICATE_STATUS_FAILURE || empty($authuser->username) || empty($authuser->password) || empty($authuser->email))
		{
			return false;
		}

		$user = JUser::getInstance($authuser->username);
		//Check Status
		if (empty($user->id) || $user->block || !empty($user->activation))
		{
			return false;
		}

		JFactory::getSession()->set('user', $user);

		return $user;
	}

	protected function getCatTitle($id)
	{
		$this->writeLog('getCatTitle');

		$db = JFactory::getDBO();
		if (!$id)
		{
			return;
		}
		$query = 'SELECT title'
				. ' FROM #__categories'
				. ' WHERE id = ' . (int) $id
		;
		$db->setQuery($query);
		return $db->loadResult();
	}

	protected function GoogleDocsToContent(&$content)
	{
		$this->writeLog('GoogleDocsToContent');

		if (is_array($content) || (is_string($content) && strpos($content, 'google_header') === false))
		{
			return;
		}

		//Header title
		$headerregex = '/<div.+?google_header[^>]+>(.+?)<\/div>/is';
		//Old page break;
		$oldpbregex  = '/<p.+?page-break-after[^>]+>.*?<\/p>/is';
		//Horizontal line
		$hrizonregex = '/<hr\s+?size="2"[^>]*?>/is';
		//New page break;
		$newpbregex  = '/<hr\s+?class="pb"[^>]*?>/is';

		$match = array();
		if (preg_match($headerregex, $content, $match))
		{
			$title = trim($match[1]);
			$introandfull = preg_replace($headerregex, '', $content);
		}
		else
		{
			$title = JString::substr($content, 0, 30);
			$introandfull = str_replace($title, '', $content);
		}

		$text  = preg_split($oldpbregex, $introandfull, 2, PREG_SPLIT_NO_EMPTY);
		$introtext = '';
		$fulltext  = '';
		if (count($text) > 1)
		{
			$introtext = trim($text[0]);
			$fulltext  = trim($text[1]);
		}
		else
		{

			//new
			if (!$this->params->get('readmore'))
			{
				//Horizontal line
				$regex = $hrizonregex;
			}
			else
			{
				//Page break
				$regex = $newpbregex;
			}

			//first horizontal line or pagebreak
			$text = preg_split($regex, $introandfull, 2, PREG_SPLIT_NO_EMPTY);
			if (count($text) > 1)
			{
				$introtext = trim($text[0]);
				$fulltext  = trim($text[1]);
			}
			else
			{
				$introtext = trim($introandfull);
			}
		}

		if ($this->params->get('pagebreak'))
		{
			$count = 2;
			//for pagebreak
			$text  = preg_split($newpbregex, $introtext, -1, PREG_SPLIT_NO_EMPTY);
			if (count($text) > 1)
			{
				$introtext = '';
				for ($i = 0, $total = count($text); $i < $total; $i++)
				{
					$alt = JText::sprintf('PAGEBREAK', $count);
					$count++;
					$introtext .= $text[$i];
					if ($i < ($total - 1))
					{
						$introtext .= '<hr title="' . $alt . '" alt="' . $alt . '" class="system-pagebreak" />';
					}
				}
			}

			if (!empty($fulltext))
			{
				$text = preg_split($newpbregex, $fulltext, -1, PREG_SPLIT_NO_EMPTY);
				if (count($text) > 1)
				{
					$fulltext = '';
					for ($i = 0, $total = count($text); $i < $total; $i++)
					{
						$alt = JText::sprintf('PAGEBREAK', $count);
						$count++;
						$fulltext .= $text[$i];
						if ($i < ($total - 1))
						{
							$fulltext .= '<hr title="' . $alt . '" alt="' . $alt . '" class="system-pagebreak" />';
						}
					}
				}
			}
		}

		//b to br and escape
		$replace_from = array('<b>', '</b>', '<br>');
		$replace_to = array('<strong>', '</strong>', '<br />');
		$title = htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8');
		$introtext = str_replace($replace_from, $replace_to, $introtext);
		$fulltext  = str_replace($replace_from, $replace_to, $fulltext);

		$content = array();
		$content['title'] = $title;
		$content['description']  = $introtext;
		$content['mt_text_more'] = $fulltext;
		return;
	}

	protected function buildStruct($row, $mt = false)
	{
		$this->writeLog('buildStruct');

		$date = iso8601_encode(strtotime($row->created), 0);

		if ($mt)
		{
			$xmlArray = array(
				'userid'  => new xmlrpcval($row->created_by, 'string'),
				'dateCreated' => new xmlrpcval($date, 'dateTime.iso8601'),
				'postid'  => new xmlrpcval($row->id, 'string'),
				'title'   => new xmlrpcval($row->title, 'string'),
			);
		}
		else
		{
			if(!isset($row->category_title))
			{
				$row->category_title = $this->getCatTitle($row->catid);
			}

			$link = JRoute::_(ContentHelperRoute::getArticleRoute($row->id, $row->catid), false, 2);
			$xmlArray = array(
				'userid'  => new xmlrpcval($row->created_by, 'string'),
				'dateCreated' => new xmlrpcval($date, 'dateTime.iso8601'),
				'postid'  => new xmlrpcval($row->id, 'string'),
				'description' => new xmlrpcval($row->introtext, 'string'),
				'title'   => new xmlrpcval($row->title, 'string'),
				'wp_slug' => new xmlrpcval($row->alias, 'string'),
				'mt_basename' => new xmlrpcval($row->alias, 'string'),
				'categories'  => new xmlrpcval(array(new xmlrpcval($row->category_title, 'string')), 'array'),
				'link'  => new xmlrpcval($link, 'string'),
				'permaLink' => new xmlrpcval($link, 'string'),
				'mt_excerpt' => new xmlrpcval(
						(isset($row->metadesc)? $row->metadesc: '')
						, 'string'),
				'mt_text_more'  => new xmlrpcval(
						(isset($row->fulltext)? $row->fulltext:'')
						, 'string'),
				'mt_allow_comments' => new xmlrpcval('1', 'int'),
				'mt_allow_pings' => new xmlrpcval('0', 'int'),
				'mt_convert_breaks' => new xmlrpcval('', 'string'),
				'mt_keywords'   => new xmlrpcval(
						(isset($row->metakey)? $row->metakey:'')
						, 'string')
			);
		}

		$xmlObject = new xmlrpcval($xmlArray, 'struct');
		return array(true, $xmlObject);
	}

	protected function buildData($content, $publish, $blogger = false)
	{
		$this->writeLog('buildData');

		if ($blogger)
		{
			$this->GoogleDocsToContent($content);
		}

		if (!isset($content['description']))
		{
			$content['description'] = '';
		}

		$content['articletext'] = $content['description'];
		unset($content['description']);

		//alias
		if (isset($content['mt_basename']) && !empty($content['mt_basename']))
		{
			$content['alias'] = $content['mt_basename'];
			unset($content['mt_basename']);
		}
		else if (isset($content['wp_slug']) && !empty($content['wp_slug']))
		{
			$content['alias'] = $content['wp_slug'];
			unset($content['wp_slug']);
		}

		if (!isset($content['mt_text_more']))
		{
			$content['mt_text_more'] = '';
		}

		$content['mt_text_more'] = trim($content['mt_text_more']);

		if (JString::strlen($content['mt_text_more']) < 1)
		{
			$temp = explode('<!--more-->', $content['articletext']); //for MetaWeblog
			if (count($temp) > 1)
			{
				$content['articletext'] = $temp[0] . '<hr id="system-readmore" />';
				$content['articletext'] .= $temp[1];
			}
		}
		else
		{
			$content['articletext'] .= '<hr id="system-readmore" />';
			$content['articletext'] .= $content['mt_text_more'];
		}

		unset($content['mt_text_more']);

		if (!isset($content['mt_keywords']))
		{
			$content['mt_keywords'] = '';
		}

		$content['metakey'] = $content['mt_keywords'];

		//build tags
		$tags = $this->getAssignedTags($content['metakey']);
		if($tags){
			$content['metadata'] = array();
			$content['metadata']['tags'] = $tags;
		}

		if (!isset($content['mt_excerpt']))
		{
			$content['mt_excerpt'] = '';
		}

		$content['metadesc'] = $content['mt_excerpt'];

		$content['state'] = 0;

		if ($publish)
		{
			$content['state'] = 1;
		}

		$content['language'] = $this->params->get('language', '*');

		//date
		$basedate = null;
		switch(true)
		{
			case (isset($content['date_created_gmt'])):
				$basedate = $content['date_created_gmt'];
				break;
			case (isset($content['dateCreated_gmt'])):
				$basedate = $content['dateCreated_gmt'];
				break;

			case (isset($content['dateCreated'])):
				$basedate = $content['dateCreated'];
				break;
		}

		if($basedate){
			$timezone = new DateTimeZone(JFactory::getConfig()->get('offset'));
			$now = new DateTime('now', $timezone);
			$offsetsecond = $timezone->getOffset($now);

			$offset = 0;
			if($offsetsecond){
				$offset = $offsetsecond / 3600;
			}

			$date = JFactory::getDate(iso8601_decode($basedate, $offset));
//			$date->setTimeZone(new DateTimeZone(JFactory::getConfig()->get('offset')));
			$content['created'] = $content['publish_up'] = $date->toSql();
		}

		if (empty($content['id']) && empty($content['created']))
		{
			$content['created'] = $content['publish_up'] = JFactory::getDate()->toSql();
		}

		$content['created_by_alias'] = '';
		if(isset($content['wp_author_id']) && $content['wp_author_id'] > 0)
		{
			$author = JFactory::getUser($content['wp_author_id']);
			if($author)
			{
				$content['created_by_alias'] = $author->get('name');
			}
		}

		return $content;
	}

	protected function buildCategoryTitle($title, $id, $featured = false)
	{
		$this->writeLog('buildCategoryTitle');

		if ($featured)
		{
			return $title;
		}

		$base = '%s' . $this->beforewrapid . '%s' . $this->afterwrapid;

		return sprintf($base, $title, $id);
	}

	protected function getCatId($title)
	{
		$this->writeLog('getCatId');

		if (strpos($title, $this->beforewrapid) === false)
		{
			return null;
		}

		$title = explode($this->beforewrapid, $title);

		if (count($title) == 2)
		{
			return intval(str_replace($this->afterwrapid, '', $title[1]));
		}

		return 0;
	}

	protected function assignCategory(& $content)
	{
		$this->writeLog('assignCategory');

		static $assigned = false;

		if (isset($content['categories']) && count($content['categories']))
		{
			foreach ($content['categories'] as $title)
			{
				$catid = $this->getCatId($title);

				if (is_null($catid))
				{
					$content['featured'] = 1;
					continue;
				}

				if (!$assigned && $catid > 0)
				{
					$content['catid'] = $catid;
					$assigned = true;
				}
			}
		}
	}

	protected function getFeatureStruct($mt = false, $isPrimary = 0)
	{
		$this->writeLog('getFeatureStruct');

		if ($mt)
		{
			return new xmlrpcval(
				array(
					'categoryName' => new xmlrpcval($this->buildCategoryTitle(JText::_('PLG_XMLRPC_JOOMLA_FEATURED_TITLE'), 0, true), 'string'),
					'categoryId'   => new xmlrpcval('-1', 'string'),
					'isPrimary' => new xmlrpcval($isPrimary, 'boolean')
				), 'struct'
			);
		}

		return new xmlrpcval(
			array(
				'categoryId'   => new xmlrpcval('-1', 'string'),
				'categoryName' => new xmlrpcval(
						$this->buildCategoryTitle(JText::_('PLG_XMLRPC_JOOMLA_FEATURED_TITLE'), 0, true)
						, 'string')
			), 'struct'
		);
	}

	/**
	 *  Assign tag_id from keywords
	 * @param string $text
	 * @return array|string
	 */
	protected function getAssignedTags($text)
	{
		$result = array();

		$text = trim($text);
		if(empty($text)){
			return $result;
		}

		$tags = explode(',', $text);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id, title');
		$query->from('#__tags');
//		$query->where('published = 1');

		jimport('joomla.filter.input');
		$filter = JFilterInput::getInstance();

		$cleans = array();
		$wheres = array();
		foreach($tags as $tag){
			$temp = trim($filter->clean($tag));
			if(empty($temp)){
				continue;
			}

			$result[] = '#new#'. $temp;
			$cleans[] = $temp;
			$wheres[] = 'title LIKE '. $db->q($temp);
		}

		if(count($wheres) < 1){
			$db->getQuery(true);
			return $result;
		}

		$query->where(implode(' OR ', $wheres));

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		//all new tags
		if(count($rows) < 1){
			if(count($result)){
//				$post = array();
//				$post['metadata'] = array();
//				$post['metadata']['tags'] = $result;
//				JRequest::set($post);
			}
			return $result;
		}

		$result = array();
		$cleanflip = array_flip($cleans);
		foreach($rows as $row){
			$result[] = $row->id;
			if(isset($cleanflip[$row->title])){
				unset($cleans[$cleanflip[$row->title]]);
			}
		}

		if(count($cleans)){
			foreach($cleans as $clean){
				$result[] = '#new#'. $clean;
			}
		}

//		$post = array();
//		$post['metadata'] = array();
//		$post['metadata']['tags'] = $result;
//		JRequest::set($post);

		return $result;
	}

	protected function getModel($type, $prefix = 'XMLRPCModel', $config = array())
	{
		$this->writeLog('getModel');

		if(version_compare(JVERSION, '3.0.0', '>=')){
			return JModelLegacy::getInstance($type, $prefix, $config);
		} else {
			return JModel::getInstance($type, $prefix, $config);
		}
	}

	protected function response($msg)
	{
		$this->writeLog('response');

		global $xmlrpcerruser;
		return new xmlrpcresp(0, $xmlrpcerruser + 1, $msg);
	}

	private function writeLog($message)
	{
		if(!JDEBUG){
			return;
		}

		jimport('joomla.log.log');

		static $log = null;

		if(is_null($log)){
			$options['text_file'] = 'xmlrpc.info.php';
			$options['format'] = "{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}";
			JLog::addLogger($options, JLog::ALL, array('xmlrpc'));
		}

		if(!is_string($message)){
			$message = print_r($message, true);
		}

		JLog::add($message, JLog::INFO, 'xmlrpc');
	}
}
