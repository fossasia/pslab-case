<?php
/**
 * 			XMLRPC Plugin Joomla
 * @version		1.0.0
 * @package		XMLRPC
 * @copyright		Copyright (C) 2007-2011 Joomler!.net. All rights reserved.
 * @license		GNU/GPL 2.0 or higher
 * @author		Joomler!.net  joomlers@gmail.com
 * @link			http://www.joomler.net
 */

/**
 * To Joomla!1.6
 * Change Name to XMLRPC Joomla
 * version 1.0.0
 *
 * Updated 2.3.4
 * fix : PHP 5.3
 * support user folder
 *
 * Updated 2.3.3
 * fix : pass-by-reference
 * support ftp mode upload
 *
 * Updated 2.3.2
 * fix : new post at restricted categories
 * fix : MTMail date for Japanese Famous Service (MTMail)
 * change : screen style of setting parameters
 *
 * Updated 2.3.1
 * fix : Same filename
 * add : overwrite parameter
 *
 * Updated 2.3.0
 * fix : Google Docs
 * add : filter user groups
 * add : support plugins of aftersave and beforesave
 *
 * Updated to 2.2.1
 * fix : Undefined Property
 *
 * Updated to 2.2.0
 * Add : Single Category Mode
 * fix : modified_by, modified
 * Supported ScribeFire of version 2.3.2
 *
 * Updated to 2.1.0
 * Add : Support Google Docs
 * Add : html_entity_decode method
 *
 * Updated to 2.0.1
 * change : Joomla! version check and call date for 1.5.x All
 *
 * Updated to 2.0.0
 * Support more movable Type XML-RPC API
 * fix access : cotent, category, section
 *
 * Thanks! Great Developers.
 */

/**
 * ABOUT jMT_API
 * @package jMT_API
 * @version 1.0a
 * @copyright Copyright (C) 2006 dex_stern. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
function createString($var){
	$string="";
	$c=count($var);
	for ($i=0;$i<$c;$i++){
		$string .="---\n";
		$tmp = $var[i];
		$u = array_keys ($var[i]);
		$string .= count($u) . " ". $u[0]. " ". $u[1]. " ". $u[2]."\n";
		$string .= "file ".$tmp[0]."\n" ;
		$string .= "function ".$tmp[1]."\n" ;

	}
	return $string;
}
class plgXmlrpcJoooid extends JPlugin
{
	
	private $version = "2.0";

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage( '', JPATH_ADMINISTRATOR );
	}

	public function onGetWebServices()
	{


		return array
			(
			 'blogger.newCategory' => array( 'function' => array($this, 'blogger_newCategory'), 'signature' => null ),
			 'blogger.deleteCategory' => array( 'function' => array($this, 'blogger_deleteCategory'), 'signature' => null ),
			 'blogger.getUsersBlogs' => array( 'function' => array($this, 'blogger_getUserBlogs'), 'signature' => null ),
			 'blogger.getUserInfo' => array( 'function' => array($this, 'blogger_getUserInfo'), 'signature' => null ),
			 'blogger.getRecentPosts' => array( 'function' => array($this, 'blogger_getRecentPosts'), 'signature' => null ),
			 'blogger.newPost' => array( 'function' => array($this, 'blogger_newPost'), 'signature' => null ),
			 //'blogger.deletePost' => array( 'function' => array($this, 'blogger_deletePost'), 'signature' => null ),
			 'blogger.updatePost' => array( 'function' => array($this, 'blogger_updatePost'), 'signature' => null ),
			 'blogger.editPost' => array( 'function' => array($this, 'blogger_editPost'), 'signature' => null ),
			 //'metaWeblog.newPost' => array( 'function' => array($this, 'mw_newPost'), 'signature' => null ),
			 //'metaWeblog.editPost' => array( 'function' => array($this, 'mw_editPost'), 'signature' => null ),
			 'metaWeblog.getPost' => array( 'function' => array($this, 'mw_getPost'), 'signature' => null ),
			 'metaWeblog.newMediaObject' => array( 'function' => array($this, 'mw_newMediaObject'), 'signature' => null ),
			 //'metaWeblog.getRecentPosts' => array( 'function' => array($this, 'mw_getRecentPosts'), 'signature' => null ),
			 //'metaWeblog.getCategories' => array( 'function' => array($this, 'mw_getCategories'), 'signature' => null ),
			 //'mt.getCategoryList' => array( 'function' => array($this, 'mt_getCategoryList'), 'signature' => null ),
			 //'mt.getPostCategories' => array( 'function' => array($this, 'mt_getPostCategories'), 'signature' => null ),
			 //'mt.setPostCategories' => array( 'function' => array($this, 'mt_setPostCategories'), 'signature' => null ),
			 //'mt.getRecentPostTitles' => array( 'function' => array($this, 'mt_getRecentPostTitles'), 'signature' => null ),
			 //'mt.supportedTextFilters' => array( 'function' => array($this, 'mt_supportedTextFilters'), 'signature' => null ),
			 //'mt.publishPost' => array( 'function' => array($this, 'mt_publishPost'), 'signature' => null ),
			 //'mt.getTrackbackPings' => array( 'function' => array($this, 'mt_getTrackbackPings'), 'signature' => null ),
			 //'mt.supportedMethods' => array( 'function' => array($this, 'mt_supportedMethods'), 'signature' => null ),
			 'wp.getCategories' => array( 'function' => array($this, 'wp_getCategories'), 'signature' => null ),
			 'phimpme.findCategory' => array( 'function' => array($this, 'phimpme_findCategory'), 'signature' => null ),
			 //'wp.newCategory' => array( 'function' => array($this, 'wp_newCategory'), 'signature' => null ),
			 'logger.on' => array( 'function' => array($this, 'loggerOn'), 'signature' => null ),
			 'logger.off' => array( 'function' => array($this, 'loggerOff'), 'signature' => null ),
			 'joooid.addUser' => array( 'function' => array($this, 'addUser'), 'signature' => null ),
			 'joooid.getUsersList' => array( 'function' => array($this, 'getUsersList'), 'signature' => null ),
			 
			 'blogger.version' => array( 'function' => array($this, 'mw_version'), 'signature' => null )
				 );
	}



	public function addUser ($myusername,$mypassword,$username,$password,$name,$email,$block,$group_count, $group_names,$id){
		$user = $this->authenticateUser($myusername, $mypassword);
		

		// Only Super User can manage Users	
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		if (!isset($user->groups["Super Users"])	 ){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}else if (isset($user->groups["Super Users"]) && ($user->groups["Super Users"])!=8 ){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		$model = $this->getModel("User");	
		$data = array();
		if (isset($id) && $id!=''){
			$data['userId']=$id;
		}
		//print_r($model);die();
		$data['name']=$name;
		$data['username']=$username;
		$data['password']=$password;
		$data['email']=$email;
		$data['block']=$block;
		$data['groups_count']=$group_count;
		$data['group_names']=$group_names;
		if(!$model->save($data)){
			return $this->response($model->getError());
		}

		return new xmlrpcresp(new xmlrpcval(0, 'int'));
	}



	public function getUsersList ($username,$password){
		
		$user = $this->authenticateUser($username, $password);

		// Only Super User can manage Users	
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		if (!isset($user->groups["Super Users"])	 ){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}else if (isset($user->groups["Super Users"]) && ($user->groups["Super Users"])!=8 ){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}

		$model = $this->getModel("Users");	

		$users = $model->getItems();
		
		if(empty($users)){
			return $this->response(JText::_('PLG_JOOOID_CATEGORY_WAS_NOT_FOUND'));
		}

		foreach($users as $row){

			$array = array();
			$array['userId']		= new xmlrpcval( $row->id, 'string' );
			$array['name']			= new xmlrpcval( $row->name, 'string' );
			$array['username']		= new xmlrpcval( $row->username, 'string' );
			$array['email']			= new xmlrpcval( $row->email, 'string' );
			$array['block']			= new xmlrpcval( $row->block, 'string' );
			$array['group_count']		= new xmlrpcval( $row->group_count, 'string' );
			$array['group_names']		= new xmlrpcval( $row->group_names, 'string' );

			$structarray[] = new xmlrpcval( $array, 'struct' );
		}

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));


}

	public function loggerOn($username,$password){

		joooid_log("--------------------------------Logger On");
		$user = $this->authenticateUser($username, $password);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		$db =& JFactory::getDBO();

		$db->setQuery(
				' SELECT params FROM #__extensions WHERE' .
				' name="com_joooid"'
			     );
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
		}
		$row = $db->loadObject();
		$params = json_decode($row->params);
		$params->debug=1;
		$paramsString = json_encode($params);

		$db->setQuery('UPDATE #__extensions SET params="'.$db->escape($paramsString).'" WHERE name="com_joooid"');
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
		}

		return (new xmlrpcresp(new xmlrpcval(0, 'i4')));

	//	$params = JComponentHelper::getParams('com_joooid');
	//	$params->set('debug', true, JDEBUG);
	//	return $params->get('debug', JDEBUG);
	}

	public function loggerOff($username,$password){
		joooid_log("Logger Off");
		print_r($_REQUEST);die;
		$user = $this->authenticateUser($username, $password);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		$db =& JFactory::getDBO();

		$db->setQuery(
				' SELECT params FROM #__extensions WHERE' .
				' name="com_joooid"'
			     );
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
		}
		$row = $db->loadObject();
		$params = json_decode($row->params);
		$params->debug=0;
		$paramsString = json_encode($params);

		$db->setQuery('UPDATE #__extensions SET params="'.$db->escape($paramsString).'" WHERE name="com_joooid"');
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return (new xmlrpcresp(new xmlrpcval(-1, 'i4')));
		}

		return (new xmlrpcresp(new xmlrpcval(0, 'i4')));
	}

	public function mw_version()
	{
		$args = func_get_args();

		$username = $args[0];
		$password = $args[1];
		$version = $args[2];

		if(func_num_args() >3 ){
			return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
		}


		$user = $this->authenticateUser($username, $password);
		
		// Only Super User with priv = 8 can make a plugin update
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		if (!isset($user->groups["Super Users"])	 ){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}else if (isset($user->groups["Super Users"]) && ($user->groups["Super Users"])!=8 ){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		$this->do_version($version);
	}

	protected function do_version($version){

		// TODO determinare  se la versione e' corretta

		// TODO stiamo nella versione 1.7 scaricare i file dall'url giusto uno alla volta e metterli nella posizione giusta

		// TODO SE non si possono scaricare, ritorna errore
		// TODO SE non si possono scrivere, ritorna errore

		//joooid_log($user);
		
		// TODO PLG_JOOOID_OK_UPDATE: Server Update Successful
		return $this->response(JText::_('PLG_JOOOID_OK_UPDATE'));


	}
	protected function buildCategory($title,$alias,$description,$parent_id,$published,$access,$created_user_id,$language,$id)
	{
		$date = JFactory::getDate();
		$created = $date->toMySQL();

		$user = JFactory::getUser();
		$userid = intval( $user->get('id') );

		if(!isset($content['description'])){
			$content['description'] = '';
		}
		$content['id'] = $id;
		$content['title'] = $title;
		if (isset($alias)){
			$content['alias'] = $alias;
		}else{
			$content['alias'] = $title;
		}
		$content['extension'] = "com_content";
		$content['published'] = $published;
		$content['access'] = $access;
		$content['parent_id'] = $parent_id;
		$content['created_user_id'] = $userid;
		$content['language'] = "*";

		$content['language'] = $this->params->get('language', '*');


		// 		if ($publish){
		// 			$content['state'] = 1;
		// 		}


		//date
		if(isset($content['dateCreated_gmt'])){
			$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
			$content['created']  = $content['publish_up'] = $date->toMySQL();
		} else if(isset($content['dateCreated'])){
			$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
			$content['created']  = $content['publish_up'] = $date->toMySQL();
		}

		if(empty($content['id']) && empty($content['created'])){
			$content['created'] = JFactory::getDate()->toMySQL();
		}

		return $content;
	}

	public function blogger_newCategory()
	{
		$args		= func_get_args();
		//joooid_log($args);

		if(func_num_args() < 4){
			return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
		}

		$username	= strval( $args[1] );
		$password	= strval( $args[2] );
		$title		= $args[3];
		$alias		= $args[4];
		$description	= $args[5];
		$parent_id	= $args[6];
		$published	= $args[7];
		$access		= $args[8];
		$created_user_id = $args[9];
		$language	= $args[10];
		$id		= (int)$args[11];


		//$alias		= $args[5];
		//$intro		= $args[6];
		//$full		= $args[7];
		//$publish	= (int)$args[8];
		//$access		= (int)$args[9];
		//$front		= (int)$args[10];


		return $this->mw_newCategory($username, $password, $title,$alias,$description,$parent_id, $published, $access, $created_user_id,$language,$id);
	}



public function blogger_deleteCategory()
{
	global $xmlrpcBoolean;

	$args		= func_get_args();


	$categoryid	= (int)$args[0];
	$username	= $args[1];
	$password	= $args[2];

	$user = $this->authenticateUser($username, $password);

	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}

	$userid = intval($user->get('id'));

	$model = $this->getModel('Category');
	$row = $model->getTable();
	$result = $row->load($categoryid);


	if(!$result){
		return $this->response(JText::_('PLG_JOOOID_ITEM_WAS_NOT_FOUND'));
	}

	if(!$model->canEditState($row)){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $userid)
	{
		return $this->response(JText::sprintf('PLG_JOOOID_EDITING_OTHER_USER', $row->title));
	}
	
	if (!$row->publish($row->id,-2)){
		return $this->response($row->getError());
	}


	$row->checkout((int)$userid);

	//$row->ordering = 0;
	$row->published = -2;//to trash

	if (!$row->store()){
		return $this->response($row->getError());
	}

	$row->checkin();


	//clear cache
	$cache = & JFactory::getCache('com_content');
	$cache->clean();

	return new xmlrpcresp(new xmlrpcval('true', $xmlrpcBoolean));
}


	public function mw_newCategory()
	{
		$args		= func_get_args();
		//joooid_log($args);

		if(func_num_args() < 3){
			return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
		}

		$username	= $args[0];
		$password	= $args[1];
		$title		= $args[2];
		$alias		= $args[3];
		$description	= $args[4];
		$parent_id	= $args[5];
		$published	= $args[6];
		$access		= $args[7];
		$created_user_id= $args[8];
		$language	= $args[9];
		$id		= $args[10];

		$user = $this->authenticateUser($username, $password);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		//print_r($user);die;

		$data  = $this->buildCategory($title,$alias,$description,$parent_id,$published,$access,$created_user_id,$language,$id);
		//joooid_log($data);
		$model = $this->getModel('Category');
		/*if($model->allowAdd($data) == true){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}		*/

		
		if(!$model->save($data)){				
			joooid_log($model->getError());
			return $this->response($model->getError());
		}
		$row = $model->getTable();
		if ($id == 0)
			$id = (int)$model->getState($model->getName().'.id');		
		joooid_log($id);
		$result = $row->load($id);
		if (!$row->publish($id,$published)){
			return $this->response($row->getError());
		}

		return (new xmlrpcresp(new xmlrpcval($model->getState($model->getName().'.id'), 'string')));
	}

	public function wp_getCategories()
	{
		global $xmlrpcerruser;

		$args		= func_get_args();

		if(func_num_args() < 3){
			return new xmlrpcresp(0, $xmlrpcerruser + 1,  JText::_('The request is illegal.'));
		}

		$username	= strval( $args[1] );
		$password	= strval( $args[2] );

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		$structarray = array();

		JRequest::setVar('limit', 0);
		$model = $this->getModel('Categories');
		$categories = $model->getItems();

		if(empty($categories)){
			return $this->response(JText::_('PLG_JOOOID_CATEGORY_WAS_NOT_FOUND'));
		}

		foreach($categories as $row){
			if($row->published < 1){
				if(!$user->authorise('core.edit.state', 'com_content.category.'. $row->id)){
					continue;
				}

				if(!$user->authorise('core.admin', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
					continue;
				}
			}

			$array = array();
			$array['categoryId']		= new xmlrpcval( $row->id, 'string' );
			$array['parentId']		= new xmlrpcval( $row->parent_id, 'string' );
			$array['description']		= new xmlrpcval( $row->description, 'string' );
			$array['categoryDescription']	= new xmlrpcval( $row->description, 'string' );
			$array['categoryName']		= new xmlrpcval( $row->title, 'string' );
			$array['htmlUrl']		= new xmlrpcval( JRoute::_(ContentHelperRoute::getCategoryRoute($row->id)), 'string' );
			$array['rssUrl']		= new xmlrpcval( JRoute::_(ContentHelperRoute::getCategoryRoute($row->id). '&format=feed'), 'string' );

			$structarray[] = new xmlrpcval( $array, 'struct' );
		}


		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));

	}
	public function phimpme_findCategory(){
		global $xmlrpcerruser;

		$args		= func_get_args();

		if(func_num_args() < 4){
			return new xmlrpcresp(0, $xmlrpcerruser + 1,  JText::_('The request is illegal.'));
		}

		$username	= strval( $args[1] );
		$password	= strval( $args[2] );
		$name = $args[3];
		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		$structarray = array();

		JRequest::setVar('limit', 0);
		$model = $this->getModel('Categories');
		$categories = $model->getItems();

		if(empty($categories)){
			return $this->response(JText::_('PLG_JOOOID_CATEGORY_WAS_NOT_FOUND'));
		}
		$id = '0';
		foreach($categories as $row){
			if ($row->title == $name) {
			$id = $row->id;
			break;
			}
		}


		return new xmlrpcresp(new xmlrpcval($id, 'string'));
		
		}
	public function wp_newCategory()
	{
		global $xmlrpcerruser;

		$args		= func_get_args();
		if(func_num_args() < 4){
			return new xmlrpcresp(0, $xmlrpcerruser + 1,  JText::_('The request is illegal.'));
		}

		$username	= strval( $args[1] );
		$password	= strval( $args[2] );
		//$category	= $args[3];

		$user = $this->authenticateUser($username, $password);
		$category['name'] = 'phimpme123';
		//joooid_log($args);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		if(!$user->authorise('core.create', 'com_content')){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}

		if(empty($category['name'])){
			return $this->response(JText::_('PLG_JOOOID_CATEGORY_MUST_HAVE_TITLE'));
		}

		$category['title'] = $category['name'];
		unset($category['name']);

		$category['extension'] = 'com_content';
		$category['published'] = 1;
		$category['language'] = $this->params->get('language', '*');
		//joooid_log($args);
	/*if (!$row->publish($row->id,-2)){
		return $this->response($row->getError());
	}*/
		$model = $this->getModel('Category');
		joooid_log($args);
		if(!$model->save($category)){
			return $this->response($model->getError());
		}


		return (new xmlrpcresp(new xmlrpcval($model->getState('category.id'), 'string')));

	}

	public function blogger_getUserBlogs()
	{
		$args		= func_get_args();
		if(func_num_args() < 3){
			return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
		}


		$username	= $args[1];
		$password	= $args[2];

		$mt	= false;

		if(isset($args[3])){
			$mt = (boolean)$args[3];
		}

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		$db =& JFactory::getDbo();
		$app = JFactory::getApplication();

		$structarray = array();

		// 		if(!$mt){
		// 			$site_name = $app->getCfg('sitename');
		// 			$structarray[] = new xmlrpcval(
		// 				array('url' => new xmlrpcval(JURI::root(), 'string'),
		// 				'blogid' => new xmlrpcval(0, 'string'),
		// 				'blogName' => new xmlrpcval($site_name, 'string')),
		// 				'struct');
		// 			return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
		// 		}

		$model = $this->getModel('Categories');
		//$model = JModel::getInstance("Categories", "CategoriesModel" );
		//$model->setState('filter.published','*');
		$categories = $model->getItems();

		//print_r($categories);
		//die;

		if(empty($categories)){
			return $this->response(JText::_('PLG_JOOOID_CATEGORY_WAS_NOT_FOUND'));
		}

		foreach($categories as $row){
		
			// check if it can publish
			if($row->published < 1){
				if(!$user->authorise('core.edit.state', 'com_content.category.'. $row->id)){
					continue;
				}

				if(!$user->authorise('core.admin', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
					continue;
				}
			}

			if(!($user->authorise('core.edit.state', 'com_content.category.'. $row->id)||$user->authorise('core.create', 'com_content.category.'. $row->id)||$user->authorise('core.edit', 'com_content.category.'. $row->id))){
				continue;
			}

			
			//joooid_log($row);
			//joooid_log("BBBB");
			//joooid_log($row);
			//$row->title = str_repeat(' ...', $row->level-1). $row->title;
			$structarray[] = new xmlrpcval(
					array(
						'id' => new xmlrpcval($row->id, 'string'),
						'title' => new xmlrpcval($row->title, 'string'),
						'alias' => new xmlrpcval($row->alias, 'string'),
						'description' => new xmlrpcval($row->description, 'string'),
						'level' => new xmlrpcval($row->level, 'string'),
						'parent_id' => new xmlrpcval($row->parent_id, 'string'),
						'path' => new xmlrpcval($row->path, 'string'),
						'date' => new xmlrpcval($row->created_time,'string'),
						'access' => new xmlrpcval($row->access, 'string'),
						'language' => new xmlrpcval($row->language, 'string'),
						'created_user_id' => new xmlrpcval($row->created_user_id, 'string'),
						'published' => new xmlrpcval($row->published, 'string')
					     ),'struct');
		}
		if(empty($structarray)){
			return $this->response(JText::_('PLG_JOOOID_CATEGORY_WAS_NOT_FOUND'));
		}
		//joooid_log($structarray);

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
	}

	public function blogger_getUserInfo()
	{
		global $xmlrpcStruct;

		$args		= func_get_args();

		if(func_num_args() < 3){
			return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
		}

		$username	= strval( $args[1] );
		$password	= strval( $args[2] );

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		$name = $user->name;
		if(function_exists('mb_convert_kana')){
			$name = mb_convert_kana($user->name, 's');
		}

		$names = explode(' ', $name);
		$firstname = $names[0];
		$lastname = trim(str_replace($firstname, '', $name));

		$struct = new xmlrpcval(
				array(
					'nickname'	=> new xmlrpcval($user->username),
					'userid'	=> new xmlrpcval($user->id),
					'url'		=> new xmlrpcval(JURI::root()),
					'email'		=> new xmlrpcval($user->email),
					'lastname'	=> new xmlrpcval($lastname),
					'firstname'	=> new xmlrpcval($firstname),
					'version'	=> new xmlrpcval($this->version)
				     ), $xmlrpcStruct);

		return new xmlrpcresp($struct);

	}

	public function blogger_newPost()
	{
		$args		= func_get_args();
		//joooid_log($args);

		if(func_num_args() < 6){
			return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[1];
		$username	= strval( $args[2] );
		$password	= strval( $args[3] );
		$title		= $args[4];
		$alias		= $args[5];
		$intro		= $args[6];
		$full		= $args[7];
		$publish	= (int)$args[8];
		$access		= (int)$args[9];
		$front		= (int)$args[10];
		$published_up	= $args[11];
		$now		= $args[12];

		// Publish tiem
		if ($published_up !='0'){
			$now = strtotime($now);	

			$serverNow = strtotime(date("Y-m-d H:i:s"));
			$difference =0;	
			$difference = abs(intval(($now - $serverNow)/3600));
			if ( ((abs($now-$serverNow))%3600)>((60-5)*60)   ){
				$difference++;
			}

			if ($serverNow>$now){
				$difference = -$difference;
			}


			$published_up = strtotime($published_up);
			$ora = date ("H",$published_up)-$difference;
			$minuti = date ("i",$published_up);
			$secondi = date ("s",$published_up);
			$anno = date ("Y",$published_up);
			$mese = date ("m",$published_up);
			$giorno = date ("d",$published_up);

			$published_up = date( "Y-m-d H:i:s",mktime($ora,$minuti,$secondi,$mese,$giorno,$anno ));
		}
		else{
			$published_up ='00-00-00 00:00:00';
			$difference = 0;
		}


		return $this->mw_newPost($blogid, $username, $password, $title, $alias, $intro, $full, $publish, $access,$front, $published_up, $difference);
	}

public function blogger_editPost()
{
	$args		= func_get_args();

	if(func_num_args() < 6){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$postid		= (int)$args[1];
	$catId		= (int)$args[2];
	$username	= strval( $args[3] );
	$password	= strval( $args[4] );
	$title		= $args[5];
	$alias 		= $args[6];
	$intro		= $args[7];
	$full		= $args[8];
	$state		= (int)$args[9];
	$access		= (int)$args[10];
	$front		= (int)$args[11];
	$published_up	= $args[12];
	$now		= $args[13];


	// Publish tiem
	if ($published_up !='0'){
		$now = strtotime($now);	

		$serverNow = strtotime(date("Y-m-d H:i:s"));
		$difference =0;	
		$difference = abs(intval(($now - $serverNow)/3600));
		if ( ((abs($now-$serverNow))%3600)>((60-5)*60)   ){
			$difference++;
		}

		if ($serverNow>$now){
			$difference = -$difference;
		}


		$published_up = strtotime($published_up);
		$ora = date ("H",$published_up)-$difference;
		$minuti = date ("i",$published_up);
		$secondi = date ("s",$published_up);
		$anno = date ("Y",$published_up);
		$mese = date ("m",$published_up);
		$giorno = date ("d",$published_up);

		$published_up = date( "Y-m-d H:i:s",mktime($ora,$minuti,$secondi,$mese,$giorno,$anno ));
	}
	else{
		$published_up ='00-00-00 00:00:00';
		$difference = 0;
	}
	return $this->mw_editPost($postid, $username, $password, $title, $alias, $intro, $full, $state, $access, $front, $published_up, $catId, $difference);
}

public function blogger_deletePost()
{
	global $xmlrpcBoolean;

	$args		= func_get_args();

	if(func_num_args() < 5){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$postid		= (int)$args[1];
	$username	= $args[2];
	$password	= $args[3];
	$publish	= (int)$args[4];

	$user = $this->authenticateUser($username, $password);

	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}

	$userid = intval($user->get('id'));

	$model = $this->getModel('Article');
	$row = $model->getTable();
	$result = $row->load($postid);
	if(!$result){
		return $this->response(JText::_('PLG_JOOOID_ITEM_WAS_NOT_FOUND'));
	}

	if(!$model->canEditState($row)){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $userid)
	{
		return $this->response(JText::sprintf('PLG_JOOOID_EDITING_OTHER_USER', $row->title));
	}

	$row->checkout((int)$userid);

	$row->ordering = 0;
	$row->state = -2;//to trash

	if (!$row->check()){
		return $this->response($row->getError());
	}

	if (!$row->store()){
		return $this->response($row->getError());
	}

	$row->checkin();

	//clear cache
	$cache = & JFactory::getCache('com_content');
	$cache->clean();

	return new xmlrpcresp(new xmlrpcval('true', $xmlrpcBoolean));
}

public function blogger_updatePost()
{
	global $xmlrpcBoolean;
	echo "sss";
	print_r($row);die;
	$args		= func_get_args();

	if(func_num_args() < 5){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$postid		= (int)$args[1];
	$username	= $args[2];
	$password	= $args[3];
	$publish	= (int)$args[4];

	$user = $this->authenticateUser($username, $password);

	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}

	$userid = intval($user->get('id'));

	$model = $this->getModel('Article');
	$row = $model->getTable();
	$result = $row->load($postid);
	if(!$result){
		return $this->response(JText::_('PLG_JOOOID_ITEM_WAS_NOT_FOUND'));
	}

	if(!$model->canEditState($row)){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $userid)
	{
		return $this->response(JText::sprintf('PLG_JOOOID_EDITING_OTHER_USER', $row->title));
	}

	$row->checkout((int)$userid);

	$row->state = $publish;//to trash
	if ($publish == -2) $row->ordering = 0;

	if(isset($args[5])){
		$intro	= $args[5];
		$row->introtext = $args[5];
		if(isset($args[6])){
			$intro	= $args[6];
			$row->fulltext = $args[6];
		}
	}


	if (!$row->check()){
		return $this->response($row->getError());
	}

	if (!$row->store()){
		return $this->response($row->getError());
	}

	$row->checkin();

	//clear cache
	$cache = & JFactory::getCache('com_content');
	$cache->clean();

	return new xmlrpcresp(new xmlrpcval('true', $xmlrpcBoolean));
}

public function blogger_getRecentPosts()
{
	$args		= func_get_args();

	if(func_num_args() < 5){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}


	$blogid		= (int)$args[1];
	$username	= strval( $args[2] );
	$password	= strval( $args[3] );
	$numposts	= (int)$args[4];

	return $this->mw_getRecentPosts($blogid, $username, $password, $numposts);
}

public function setFeatured($id, $featured){


	$db =& JFactory::getDBO();

	$db->setQuery(
			'DELETE FROM #__content_frontpage WHERE' .
			' content_id='.$id
		     );

	if (!$db->query()) {
		$this->setError($db->getErrorMsg());
	}


	if ($featured == 1){


		$db->setQuery('UPDATE #__content_frontpage SET ordering = ordering + 1 ORDER BY ordering ASC');

		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
		}

		$db->setQuery(
				'DELETE FROM #__content_frontpage WHERE' .
				' content_id=0'
			     );	

		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
		}

		$db->setQuery(
				'INSERT INTO #__content_frontpage (`content_id`, `ordering`)' .
				' VALUES ('.$id.',1)'
			     );

		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
		}

	}

	/*
	   $table = $this->getTable('Featured', 'ContentTable');
	   $table->reorder();
	 */

	/*
	   $tmp = $old_featured;
	   $arr = array_keys ($tmp);	
	   $outp ="";
	   for ($i =0;$i<count($arr);$i++){
	   $outp .= ",".$arr[$i]."=>".$tmp[$arr[$i]]."\n";
	   }

	   $fid=fopen("logx_featured","w");
	   fwrite($fid,$outp);
	   fclose($fid);  
	 */
}

public function mw_newPost()
{
	$args		= func_get_args();
	//joooid_log($args);

	if(func_num_args() < 4){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$blogid		= (int)$args[0];
	$username	= $args[1];
	$password	= $args[2];
	$title		= $args[3];
	$alias		= $args[4];
	$intro		= $args[5];
	$full		= $args[6];
	if(isset($args[7]))$publish	= $args[7];
	$access		= $args[8];
	$front		= (int)$args[9];
	$published_up 	= $args[10];


	$user = $this->authenticateUser($username, $password);
	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}

	if(!$user->authorise('core.create', 'com_content.category.'.$blogid)){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	$data  = $this->buildArticle($title, $alias, $intro, $full, $publish, $access, $front,$published_up);
	$data['featured'] = $front;
	$data['catid'] = (int)$blogid;

	//print_r($data);
	//die;
	$model = $this->getModel('Article');

	if($model->allowAdd($data) !== true){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}


	if(!$model->save($data)){
		return $this->response($model->getError());
	}
	$this->setFeatured($model->getState('article.id'), $front);

	return (new xmlrpcresp(new xmlrpcval($model->getState('article.id'), 'string')));
}

public function mw_editPost()
{
	$args		= func_get_args();

	if(func_num_args() < 4){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$postid		= (int)$args[0];
	$username	= $args[1];
	$password	= $args[2];
	$title		= $args[3];
	$alias		= $args[4];
	$intro		= $args[5];
	$full		= $args[6];
	$state		= $args[7];
	$access		= $args[8];

	$front		= (int)$args[9];
	$published_up 	= $args[10];
	$catid	 	= $args[11];

	$user = $this->authenticateUser($username, $password);
	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}

	$data  = $this->buildArticle($title, $alias, $intro, $full, $state, $access, $front, $published_up);
	$data['id'] = $postid;
	$data['featured'] = $front;
	$data['catid'] = (int)$catid;


	$model = $this->getModel('Article');
	$row = $model->getItem($postid);
	if(!$user->authorise('core.edit.state','com_content.category.'.$data['catid']) && $data['state']!=$row->state){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}
	if(!$user->authorise('core.edit','com_content.category.'.$data['catid']) && $data['state']=$row->state){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}
	/*if(!$user->authorise('core.edit.state', 'com_content.category.'. $data['catid'])&&!$user->authorise('core.edit', 'com_content.category.'. $row->id)){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}*/

	if($model->allowEdit($data) !== true){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	if(!$model->save($data)){
		return $this->response($model->getError());
	}

	$this->setFeatured($postid, $front);

	return (new xmlrpcresp(new xmlrpcval('true', 'string')));
}

public function mw_getPost()
{
	$args		= func_get_args();

	if(func_num_args() < 3){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$postid		= (int)$args[3];
	$username	= strval( $args[1] );
	$password	= strval( $args[2] );

	$user = $this->authenticateUser($username, $password);

	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}
	$model = $this->getModel('Article');

	$data = array();
	$data['id'] = $postid;

	if($model->allowEdit($data) !== true){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	$row = $model->getItem($postid);
	if(empty($row)){
		return $this->response(JText::_('PLG_JOOOID_ITEM_WAS_NOT_FOUND'));
	}

	$ret = $this->buildStruct($row);

	if(!$ret[0]){
		return $this->response($ret[1]);
	}

	return new xmlrpcresp($ret[1]);
}

public function mw_getRecentPosts()
{
	global $xmlrpcArray;

	$args		= func_get_args();

	if(func_num_args() < 3){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$blogid		= (int)$args[0];
	$username	= $args[1];
	$password	= $args[2];

	$limit		= 0;

	if(isset($args[3])){
		$limit = (int)$args[3];
	}

	$mt	= false;

	if(isset($args[5])){
		$mt = (boolean)$args[5];
	}

	$user = $this->authenticateUser($username, $password);
	//joooid_log($user);
	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}

	$blogid = (int)$blogid;
	if($blogid > 0){
		JRequest::setVar('filter_category_id', $blogid);
	}

	JRequest::setVar('limit', $limit);
	$model = $this->getModel('Articles');
	//		$model->setState('list.limit', $limit);

	$userid = (int)$user->get('id');

	$temp = $model->getItems();
	//joooid_log($temp);
	$articles = array();
	if(count($temp)){
		foreach ($temp as $row)
		{
			$canEdit	= $user->authorise('core.edit', 'com_content.article.'.$row->id);
			$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $row->checked_out == $userid || $row->checked_out == 0;
			$canEditOwn	= $user->authorise('core.edit.own', 'com_content.article.'.$row->id) && $row->created_by == $userid;

			if(($canEdit || $canEditOwn) && $canCheckin){
				$res = $this->buildStruct($row, $mt);

				if ($res[0]){
				$articles[] = $res[1];
				}
			}
		}
	}
	return new xmlrpcresp(new xmlrpcval($articles, $xmlrpcArray));
}

public function mt_getPostCategories()
{
	$args		= func_get_args();

	if(func_num_args() < 3){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$postid		= (int)$args[0];
	$username	= strval( $args[1] );
	$password	= strval( $args[2] );

	$user = $this->authenticateUser($username, $password);

	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}

	$postid = (int)$postid;

	$model = $this->getModel('Article');
	$row = $model->getItem($postid);
	if(!$row){
		return $this->response(JText::_('PLG_JOOOID_ITEM_WAS_NOT_FOUND'));
	}

	$data = array();
	$data['id'] = $row->id;
	$data['created_by'] = $row->created_by;
	if($model->allowEdit($data) !== true){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	if(empty($row->catid)){
		return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
	} else {
		$cmodel = $this->getModel('Category');
		$category = $cmodel->getItem((int)$row->catid);
		if(empty($category)){
			return $this->response(JText::_('PLG_JOOOID_CATEGORY_WAS_NOT_FOUND'));
		}

		if(!$cmodel->canEditState($category) && $category->published < 1){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
	}

	$structarray = array();

	$structarray[] = new xmlrpcval(
			array('categoryName' => new xmlrpcval($category->title, 'string'),
				'categoryId' => new xmlrpcval($category->id, 'string'),
				'isPrimary' => new xmlrpcval(1, 'boolean')),
			'struct');

	return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
}

public function mt_setPostCategories()
{
	$args		= func_get_args();

	if(func_num_args() < 4){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$blogid		= (int)$args[0];
	$username	= strval( $args[1] );
	$password	= strval( $args[2] );
	$categories	= $args[3];

	$user = $this->authenticateUser($username, $password);

	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}

	$blogid = (int)$blogid;

	$model = $this->getModel('Article');
	$row = $model->getTable();
	$result = $row->load($blogid);
	if(!$result){
		return $this->response(JText::_('PLG_JOOOID_ITEM_WAS_NOT_FOUND'));
	}

	if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
		return $this->response(JText::sprintf('PLG_JOOOID_EDITING_OTHER_USER', $row->title));
	}

	$data = array();
	$data['id'] = $row->id;
	$data['created_by'] = $row->created_by;
	if($model->allowEdit($data) !== true){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	$row->checkout((int)$user->id);

	$cmodel = $this->getModel('Category');

	if($blogid && is_array($categories) && count($categories)){
		$catid = 0;
		$primary_catid = 0;
		for($i = 0; $i < count($categories); $i++){
			if(!isset($categories[$i]['categoryId'])){
				continue;
			}
			if(isset($categories[$i]['categoryId']) && !(int)$categories[$i]['categoryId']){
				continue;
			}

			$tempcatid = (int)$categories[$i]['categoryId'];

			if($catid == 0){
				$catid = $tempcatid;
			}

			if(isset($categories[$i]['isPrimary']) && $categories[$i]['isPrimary']){
				$primary_catid = $tempcatid;
			}
		}

		if($catid && $primary_catid && $primary_catid !== $catid){
			$catid = $primary_catid;
		}

		if(!$catid){
			return $this->response(JText::_('PLG_JOOOID_CORRECT_CATEGORY'));
		}

		$row->catid = $catid;

		if (!$row->check()){
			return $this->response($row->getError());
		}

		//Double
		//			$row->version++;

		$dispatcher =& JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');

		$result = $dispatcher->trigger('onBeforeContentSave', array( & $row, false));
		if(in_array(false, $result, true)) {
			return $this->response($row->getError());
		}

		if (!$row->store()){
			return $this->response($row->getError());
		}

		$row->reorder("catid = " . (int) $row->catid);

		$dispatcher->trigger('onAfterContentSave', array( & $row, false));

		//clear cache
		$cache = & JFactory::getCache('com_content');
		$cache->clean();

	}

	$row->checkin();

	return (new xmlrpcresp(new xmlrpcval('1', 'boolean')));
}

public function mt_getRecentPostTitles()
{
	$args		= func_get_args();

	if(func_num_args() < 4){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$blogid		= (int)$args[0];
	$username	= strval( $args[1] );
	$password	= strval( $args[2] );

	$limit = 0;

	if(isset($args[4])){
		$limit		= (int)$args[4];
	}

	return $this->mw_getRecentPosts($blogid, $username, $password, $limit, true);
}

public function mt_getCategoryList()
{
	$args		= func_get_args();

	if(func_num_args() < 3){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$blogid		= (int)$args[0];
	$username	= strval( $args[1] );
	$password	= strval( $args[2] );

	return $this->blogger_getUserBlogs($blogid, $username, $password, true);
}

public function mt_supportedTextFilters()
{
	return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
}

public function mt_publishPost()
{
	$args		= func_get_args();

	if(func_num_args() < 3){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$postid		= (int)$args[0];
	$username	= strval( $args[1] );
	$password	= strval( $args[2] );

	$user = $this->authenticateUser($username, $password);

	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}

	$model = $this->getModel('Article');
	$row = $model->getTable();
	$result = $row->load($postid);
	if(!$result){
		return $this->response(JText::_('PLG_JOOOID_ITEM_WAS_NOT_FOUND'));
	}

	if(!$user->authorise('core.edit.state', 'com_content.article.'.$item->id)){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
		return $this->response(JText::sprintf('PLG_JOOOID_EDITING_OTHER_USER', $row->title));
	}

	$data = array();
	$data['id'] = $row->id;
	$data['created_by'] = $row->created_by;
	if($model->allowEdit($data) !== true){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	$row->checkout((int)$user->id);

	$row->state = 1;
	if (!$row->check()){
		return $this->response($row->getError());
	}

	$article->version++;

	if (!$row->store()){
		return $this->response($row->getError());
	}

	$row->checkin();

	//clear cache
	$cache = & JFactory::getCache('com_content');
	$cache->clean();

	return (new xmlrpcresp(new xmlrpcval('1', 'boolean')));
}

public function mt_getTrackbackPings()
{
	$args		= func_get_args();

	if(func_num_args() < 1){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$blogid		= (int)$args[0];

	//pingIP, pingURL, pingTitle
	return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
}

public function mt_supportedMethods()
{
	return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
}

public function mw_newMediaObject()
{
	$args		= func_get_args();

	if(func_num_args() < 4){
		return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
	}

	$blogid		= (int)$args[0];
	$username	= strval( $args[1] );
	$password	= strval( $args[2] );
	$dir		= strval( $args[3] );
	$file_name 	= strval( $args[4] );
	$file 		= base64_decode( $args[5] );

	$user = $this->authenticateUser($username, $password);

	if (!$user)
	{
		return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
	}

	$model = $this->getModel('Article');
	$row = $model->getTable();
	// 		$result = $row->load($blogid);
	// 		if(!$result){
	// 			return $this->response(JText::_('PLG_JOOOID_ITEM_WAS_NOT_FOUND'));
	// 		}

	if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
		return $this->response(JText::sprintf('PLG_JOOOID_EDITING_OTHER_USER', $row->title));
	}

	$data = array();
	$data['id'] = $row->id;
	$data['created_by'] = $row->created_by;
	if($model->allowEdit($data) !== true){
		return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
	}

	$params = JComponentHelper::getParams('com_media');
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');

	if(empty($file)){
		return $this->response(JText::_('PLG_JOOOID_FILE_EMPTY'));
	}
	//File size check
	$maxSize = (int) ($params->get('upload_maxsize', 0) * 1024 * 1024);
	if($maxSize && strlen($file) > $maxSize){
		return $this->response(JText::_('PLG_JOOOID_NOT_ALLOWED_FILE_SIZE'));
	}

	if(empty($file_name)){
		return $this->response(JText::_('PLG_JOOOID_FILE_EMPTY'));
	}

	//filename check
	$temp = pathinfo($file_name);
	$file_name = trim($temp['basename']);
	if(empty($file_name)){
		return $this->response(JText::_('PLG_JOOOID_FILENAME_EMPTY'));
	}

	//$file_name = strtolower(JFile::makeSafe($file_name));
	$ext = JFile::getExt($file_name);

	$allowable = explode(',', $params->get('upload_extensions'));
	$ignored = explode(',', $params->get('ignore_extensions'));
	$images = explode(',', $params->get('image_extensions'));

	if (!in_array($ext, $allowable) && !in_array($ext,$ignored)){
		return $this->response(JText::_('PLG_JOOOID_NOT_ALLOWED_FILETYPE'));
	}

	require_once JPATH_ADMINISTRATOR.'/components/com_media/helpers/media.php';

	$images_path = str_replace('/', DS, JPATH_ROOT. DS. $params->get('image_path', 'images'));
	$file_path = str_replace('/', DS, JPATH_ROOT. DS. $params->get('file_path', 'images'));

	if(in_array($ext, $images)){
		$destination = $images_path;
	} else {
		$destination = $file_path;
	}

	$destination .= DS;

	// 		if($this->params->get('userfolder')){
	// 		$dir = JFile::makeSafe($dir);
	if(!empty($dir)){
		$destination .= $dir;
		if(!JFolder::exists($destination)){
			if(!JFolder::create($destination)){
				return $this->response(JText::_('PLG_JOOOID_NOT_ABLE_TO_CREATE_FOLDER'));
			}
		}

		if(!JFile::exists($destination. DS. 'index.html')){
			$html = '<html><body></body></html>';
			JFile::write($destination. DS. 'index.html', $html);
		}

		$destination .= DS;
	}
	// 		}

	// 		if(file_exists($destination . $file_name) && (/*!isset($file_struct['overwrite']) || !$file_struct['overwrite'] ||*/ !$this->params->get('overwrite'))){
	// 			$nameonly = str_replace(strrchr($file_name, '.'), '', $file_name);//for 1.5.10 or under
	// 			$nameonly .= '_'. JUtility::getHash(microtime()*1000000);
	// 			$file_name = JFile::makeSafe($nameonly. '.'. $ext);
	// 		}

	if(!JFile::write($destination. $file_name, $file)){
		return $this->response(JText::_('PLG_JOOOID_NOT_ABLE_TO_UPLOAD_FILE'));
	}

	if(!file_exists($destination . $file_name)){
		return $this->response(JText::sprintf('PLG_JOOOID_NOT_ABLE_TO_UPLOAD_FILE'));
	}

	$url = JURI::root(true). str_replace(array(JPATH_ROOT, DS), array('', '/'), $destination. $file_name);

	return (new xmlrpcresp(new xmlrpcval($url, 'string')));
}

protected function authenticateUser($username, $password)
{
	jimport( 'joomla.user.authentication');
	$auth = & JAuthentication::getInstance();
	$credentials['username'] = $username;
	$credentials['password'] = $password;
	$authuser = $auth->authenticate($credentials, null);

	if($authuser->status == JAUTHENTICATE_STATUS_FAILURE || empty($authuser->username) || empty($authuser->password) || empty($authuser->email)){
		return false;
	}

	$user =& JUser::getInstance($authuser->username);
	//Check Status
	if(empty($user->id) || $user->block || !empty($user->activation)){
		return false;
	}

	JFactory::getSession()->set('user', $user);

	return $user;
}

protected function getCatTitle($id)
{
	$db =& JFactory::getDBO();
	if(!$id){
		return;
	}
	$query = 'SELECT title'
		. ' FROM #__categories'
		. ' WHERE id = '. (int)$id
		;
	$db->setQuery( $query );
	return $db->loadResult();

}

protected function GoogleDocsToContent(&$content)
{

	if(is_array($content) || (is_string($content) && strpos($content, 'google_header') === false)){
		return;
	}

	//Header title
	$headerregex = '/<div.+?google_header[^>]+>(.+?)<\/div>/is';
	//Old page break;
	$oldpbregex = '/<p.+?page-break-after[^>]+>.*?<\/p>/is';
	//Horizontal line
	$hrizonregex = '/<hr\s+?size="2"[^>]*?>/is';
	//New page break;
	$newpbregex = '/<hr\s+?class="pb"[^>]*?>/is';

	$match = array();
	if(preg_match($headerregex, $content, $match)){
		$title = trim($match[1]);
		$introandfull = preg_replace($headerregex, '', $content);
	} else {
		$title = JString::substr( $content, 0, 30 );
		$introandfull = str_replace($title, '', $content);
	}

	$text = preg_split($oldpbregex, $introandfull, 2, PREG_SPLIT_NO_EMPTY);
	$introtext = '';
	$fulltext = '';
	if(count($text) > 1){
		$introtext = trim($text[0]);
		$fulltext = trim($text[1]);
	} else {

		//new
		if(!$this->params->get('readmore')){
			//Horizontal line
			$regex = $hrizonregex;
		} else {
			//Page break
			$regex = $newpbregex;
		}

		//first horizontal line or pagebreak
		$text = preg_split($regex, $introandfull, 2, PREG_SPLIT_NO_EMPTY);
		if(count($text) > 1){
			$introtext = trim($text[0]);
			$fulltext = trim($text[1]);
		} else {
			$introtext = trim($introandfull);
		}
	}

	if($this->params->get('pagebreak')){
		$count = 2;
		//for pagebreak
		$text = preg_split($newpbregex, $introtext, -1, PREG_SPLIT_NO_EMPTY);
		if(count($text) > 1){
			$introtext = '';
			for($i = 0; $total = count($text), $i < $total;$i++){
				$alt = JText::sprintf('PAGEBREAK', $count);
				$count++;
				$introtext .= $text[$i];
				if($i < ($total -1)){
					$introtext .= '<hr title="'. $alt. '" alt="'. $alt. '" class="system-pagebreak" />';
				}
			}
		}

		if(!empty($fulltext)){
			$text = preg_split($newpbregex, $fulltext, -1, PREG_SPLIT_NO_EMPTY);
			if(count($text) > 1){
				$fulltext = '';
				for($i = 0; $total = count($text), $i < $total;$i++){
					$alt = JText::sprintf('PAGEBREAK', $count);
					$count++;
					$fulltext .= $text[$i];
					if($i < ($total -1)){
						$fulltext .= '<hr title="'. $alt. '" alt="'. $alt. '" class="system-pagebreak" />';
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
	$fulltext = str_replace($replace_from, $replace_to, $fulltext);

	$content = array();
	$content['title']			= $title;
	$content['description']		= $introtext;
	$content['mt_text_more']	= $fulltext;
	return;
}

protected function buildStruct($row, $mt=false)
{
	$user = JFactory::getUser();

	if($mt){
		$xmlArray = array(
				'userid'			=> new xmlrpcval( $row->created_by, 'string' ),
				'dateCreated'		=> new xmlrpcval( $date , 'dateTime.iso8601' ),
				'postid'		=> new xmlrpcval( $row->id, 'string' ),
				'title'			=> new xmlrpcval( $row->title, 'string' ),
				);
	} else {

		$link	= JRoute::_(ContentHelperRoute::getArticleRoute($row->id, $row->catid), false, 2);
		$xmlArray = array(
				'userid'		=> new xmlrpcval( $row->created_by, 'string' ),
				'date'			=> new xmlrpcval( $row->publish_up , 'string' ),
				'date_created'		=> new xmlrpcval( $row->created , 'string' ),
				'postid'		=> new xmlrpcval( $row->id, 'string' ),
				'description'		=> new xmlrpcval( $row->introtext, 'string' ),
				'title'			=> new xmlrpcval( $row->title, 'string' ),
				'alias' 		=> new xmlrpcval( $row->alias, 'string'),
				'catid' 		=> new xmlrpcval( $row->catid, 'string'),
				'categories'		=> new xmlrpcval( array( new xmlrpcval($row->category_title, 'string') ) , 'array' ),
				'link'			=> new xmlrpcval( $link, 'string' ),
				'mt_excerpt'		=> new xmlrpcval( $row->metadesc, 'string' ),
				'text_more'		=> new xmlrpcval( $row->fulltext, 'string' ),
				'mt_keywords'		=> new xmlrpcval( $row->metakey, 'string' ),
				'state'			=> new xmlrpcval( $row->state, 'string' ),
				'access'		=> new xmlrpcval( $row->access, 'string' ),
				'frontpage'		=> new xmlrpcval( $row->featured, 'string' )
				);
	}

	$xmlObject = new xmlrpcval($xmlArray, 'struct');
	return array(true, $xmlObject);
}

protected function buildData($content, $publish, $blogger=false)
{
	$date = JFactory::getDate();
	$created = $date->toMySQL();

	$user = JFactory::getUser();
	$userid = intval( $user->get('id') );

	if($blogger){
		$this->GoogleDocsToContent($content);
	}

	if(!isset($content['description'])){
		$content['description'] = '';
	}

	$content['articletext'] = $content['description'];
	unset($content['description']);

	//alias
	if(isset($content['mt_basename'])  && !empty($content['mt_basename'])){
		$content['alias'] = $content['mt_basename'];
		unset($content['mt_basename']);
	} else if(isset($content['wp_slug'])  && !empty($content['wp_slug'])){
		$content['alias'] = $content['wp_slug'];
		unset($content['wp_slug']);
	}

	if(!isset($content['mt_text_more'])){
		$content['mt_text_more'] = '';
	}

	$content['mt_text_more'] = trim($content['mt_text_more']);

	if(JString::strlen($content['mt_text_more']) < 1){
		$temp = explode('<!--more-->', $content['articletext']);//for MetaWeblog
		if(count($temp) > 1){
			$content['articletext'] = $temp[0]. '<hr id="system-readmore" />';
			$content['articletext'] .= $temp[1];
		}
	} else {
		$content['articletext'] .= '<hr id="system-readmore" />';
		$content['articletext'] .= $content['mt_text_more'];
	}

	unset($content['mt_text_more']);

	if(!isset($content['mt_keywords'])){
		$content['mt_keywords'] = '';
	}

	$content['metakey'] = $content['mt_keywords'];

	if(!isset($content['mt_excerpt'])){
		$content['mt_excerpt'] = '';
	}

	$content['metadesc'] = $content['mt_excerpt'];

	$content['state'] = 0;

	if ($publish){
		$content['state'] = 1;
	}

	$content['language'] = $this->params->get('language', '*');

	//date
	if(isset($content['dateCreated_gmt'])){
		$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
		$content['created']  = $content['publish_up'] = $date->toMySQL();
	} else if(isset($content['dateCreated'])){
		$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
		$content['created']  = $content['publish_up'] = $date->toMySQL();
	}

	if(empty($content['id']) && empty($content['created'])){
		$content['created'] = JFactory::getDate()->toMySQL();
	}

	return $content;
}

protected function buildArticle($title, $alias, $intro, $full, $state, $access, $front,$published_up)
{
	$date = JFactory::getDate();
	$created = $date->toMySQL();

	$user = JFactory::getUser();
	$userid = intval( $user->get('id') );

	if(!isset($content['description'])){
		$content['description'] = '';
	}
	$content['title'] = $title;
	$content['wp_slug'] = $alias;

	$content['alias'] = $alias;

	$content['articletext'] = $intro.''.$full;;
	$content['description'] = $intro;
	$content['mt_text_more'] = $full;
	$content['mt_basename'] = $intro;

	if(!isset($content['mt_keywords'])){
		$content['mt_keywords'] = '';
	}

	$content['metakey'] = $content['mt_keywords'];

	if(!isset($content['mt_excerpt'])){
		$content['mt_excerpt'] = '';
	}

	$content['metadesc'] = $content['mt_excerpt'];

	$content['state'] = $state;
	$content['access'] = $access;


	// 		if ($publish){
	// 			$content['state'] = 1;
	// 		}

	$content['language'] = $this->params->get('language', '*');

	//date
	if(isset($content['dateCreated_gmt'])){
		$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
		$content['created']  = $content['publish_up'] = $date->toMySQL();
	} else if(isset($content['dateCreated'])){
		$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
		$content['created']  = $content['publish_up'] = $date->toMySQL();
	}

	if(empty($content['id']) && empty($content['created'])){
		$content['created'] = JFactory::getDate()->toMySQL();
	}


	if (isset($published_up) && $published_up!='0') {
		$content['publish_up'] = $published_up;
	}

	//joooid_log( $difference."    ".$content['created'] ."##".$content['publish_up']);
	//echo(".....   ".$content['created'] ."##".$content['publish_up']."");
	//die;


	return $content;
}

protected function getModel($type, $prefix='JOOOIDModel', $config=array())
{
	if(version_compare(JVERSION, '3.0.0', '>=')){
			return JModelLegacy::getInstance($type, $prefix, $config);
		} else {
			return JModel::getInstance($type, $prefix, $config);
		}
}

protected function response($msg)
{
	global $xmlrpcerruser;
	$trace = debug_backtrace();
	$caller = $trace;
	joooid_log("-------------------\n");
	joooid_log("[ERRORE]:".$msg."\n");
	if (isset($caller[1]['function']))
		joooid_log("Class:".$caller[1]['class']."\n");
	joooid_log("Function:".$caller[1]['function']."\n");
	joooid_log("File:".$caller[0]['file']."\n");
	joooid_log("Line:".$caller[0]['line']."\n");
	joooid_log("-------------------\n");

	return new xmlrpcresp(0, $xmlrpcerruser + 1, $msg);
}
}
