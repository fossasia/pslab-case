<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

function joooid_explode_filtered_empty($var){
	if ($var == "")
		return(false);
	return(true); 
}

function stampaStacktrace(){
	var_dump(debug_backtrace(0));
}


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

	private $version = "2.3.0";
	private $listLimit = 50;
	private $context = "com_joooid";

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
			'blogger.updatePost' => array( 'function' => array($this, 'blogger_updatePost'), 'signature' => null ),
			'blogger.editPost' => array( 'function' => array($this, 'blogger_editPost'), 'signature' => null ),
			'metaWeblog.getPost' => array( 'function' => array($this, 'mw_getPost'), 'signature' => null ),
			'metaWeblog.newMediaObject' => array( 'function' => array($this, 'mw_newMediaObject'), 'signature' => null ),
			'metaWeblog.mediaGetFileList' => array( 'function' => array($this, 'mw_mediaGetFileList'), 'signature' => null ),
			'logger.on' => array( 'function' => array($this, 'loggerOn'), 'signature' => null ),
			'logger.off' => array( 'function' => array($this, 'loggerOff'), 'signature' => null ),
			'joooid.addUser' => array( 'function' => array($this, 'addUser'), 'signature' => null ),
			'joooid.addAccessLevel' => array( 'function' => array($this, 'addAccessLevel'), 'signature' => null ),
			'joooid.getUsersList' => array( 'function' => array($this, 'getUsersList'), 'signature' => null ),
			'joooid.getUsergroups' => array( 'function' => array($this, 'getUsergroups'), 'signature' => null ),
			'joooid.addUsergroup' => array( 'function' => array($this, 'addUsergroups'), 'signature' => null ),
			'joooid.getFrontpage' => array( 'function' => array($this, 'getFrontpage'), 'signature' => null ),
			'joooid.setFrontpage' => array( 'function' => array($this, 'setFrontpage'), 'signature' => null ),
			'joooid.getMenuList' => array( 'function' => array($this, 'getMenuList'), 'signature' => null ),
			'joooid.newMenu' => array( 'function' => array($this, 'newMenu'), 'signature' => null ),
			'joooid.templatePositions' => array( 'function' => array($this, 'templatePositions'), 'signature' => null ),
			'joooid.newMenuItem' => array( 'function' => array($this, 'newMenuItem'), 'signature' => null ),
			'joooid.getMenuItems' => array( 'function' => array($this, 'getMenuItems'), 'signature' => null ),
			'joooid.getConfig' => array( 'function' => array($this, 'getConfig'), 'signature' => null ),
			'joooid.widget' => array( 'function' => array($this, 'widget'), 'signature' => null ),

			'blogger.version' => array( 'function' => array($this, 'mw_version'), 'signature' => null )
				);
	}


	public function widget ($key,$username,$password,$param){
		global $xmlrpcArray;
		global $xmlrpcStruct;

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		// ****** Last 5 users

		$model = $this->getModel("Users");	
		JRequest::setVar('limit','5');
		JRequest::setVar('filter_order','registerDate');
		JRequest::setVar( 'filter_order_Dir', 'desc' );
		$users = $model->getItems();
		if(empty($users)){
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
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

		$usersXmlrpc = (new xmlrpcval($structarray, 'array'));


		// ****** Last 5 articles
		$mt	= false;
		$model = $this->getModel('Articles');
		JRequest::setVar('limit','5');
		JRequest::setVar('filter_order','created');
		JRequest::setVar( 'filter_order_Dir', 'desc' );
		$temp = $model->getItems();
		if(empty($temp)){
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
		}

		$articles = array();
		if(count($temp)){
			foreach ($temp as $row)
			{

				if (isset($rendered) && $rendered==1){
					$row->introtext = JHTML::_('content.prepare', $row->introtext);
					$row->fulltext = JHTML::_('content.prepare', $row->fulltext);
				}


				$row->permissions = $this->buildPermissions($user,'com_content.article',$row->id);

				$res = $this->buildStruct($row, $mt);

				if ($res[0]){
					$articles[] = $res[1];
				}
			}
		}
		$articlesXmlrpc = new xmlrpcval($articles, $xmlrpcArray);

		// ****** Number of connected users (guests,users,admins)
				
		// Initialise variables.
		$config	= JFactory::getConfig();
		$db		= JFactory::getDbo();
		$lang	= JFactory::getLanguage();

		$numGuests  = 0;
		$numBackend = 0;
		$numFrontend = 0;
		$query	= $db->getQuery(true);

		// Get the number of frontend sessions number
		$query->clear();
		$query->select('COUNT(session_id)');
		$query->from('#__session');
		$query->where('guest = 1 AND client_id = 0');

		$db->setQuery($query);
		$numGuests = (int) $db->loadResult();

		// Get the number of back-end logged in users.
		$query->clear();
		$query->select('COUNT(session_id)');
		$query->from('#__session');
		$query->where('guest = 0 AND client_id = 1');

		$db->setQuery($query);
		$numBackend = (int) $db->loadResult();

		// Get the number of frontend logged in users.
		$query->clear();
		$query->select('COUNT(session_id)');
		$query->from('#__session');
		$query->where('guest = 0 AND client_id = 0');

		$db->setQuery($query);
		$numFrontend = (int) $db->loadResult();


		$query->clear();
		$query->select('COUNT(*)');
		$query->from('#__messages');
		$query->where('state = 0 AND user_id_to = '.(int) $user->get('id'));

		$db->setQuery($query);
		$unread = (int) $db->loadResult();


		$statsXmlrpc = new xmlrpcval(
				array(	'numGuests' =>new xmlrpcval($numGuests,'string'),
					'numFrontend' =>new xmlrpcval($numFrontend,'string'),
					'numBackend' =>new xmlrpcval($numBackend,'string'),
					'unread' =>new xmlrpcval($unread,'string')
				),'struct' 
		);

		$returnValueXmlrpc = new xmlrpcval(
			array(
				'articles5'=> $articlesXmlrpc,
				'users5'=> $usersXmlrpc,
				'userStats' => $statsXmlrpc)
			,'struct');

		return new xmlrpcresp($returnValueXmlrpc);

	}


	public function getConfig ($key,$username,$password){
		global $xmlrpcArray;
		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		if($user->authorise('core.manage')==false){  

			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}


		$configuration = $this->getModel('Config');
		$data = $configuration->getData();
		$items = array();

		$aParam = array('offline','offline_message','display_offline_message','offline_image','sitename','captcha','list_limit','access','debug','debug_lang','dbtype','host','user','password','db','db_prefix','live_site','secret','gzip','error_reporting','ftp_host','ftp_port','ftp_user','ftp_pass','ftp_root','ftp_enable','mailer','mailfrom','fromname','sendmail','smtpauth','smtuser','smtpass','smtphost','smtpsecure','smtpport','caching','cache_handler','cachetime','MetaDesc','MetaKeys','MetaTitle','MetaAuthor','MetaVersion','robots','sef','sef_rewrite','sef_suffix','unicodeslugs','feed_limit','log_path','tmp_path','lifetime','session_handler','MetaRights','sitename_pagetitles','force_ssl','feed_email','cookie_domain','cookie_path','assed_id');


		$xmlArray = array();
		foreach ($aParam as $row){
			if(isset($data[$row])){
				$xmlArray[$row] = new xmlrpcval($data[$row],'string');	
			}
		}
		$xmlObject = new xmlrpcval($xmlArray, 'struct');
		return $xmlObject;
		
	}

	public function getMenuItems ($key,$username,$password,$menutype){
		global $xmlrpcArray;
		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		if($user->authorise('core.manage')==false){  
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		
		$model = $this->getModel('Menuitems');
		JRequest::setVar('limit','0');
		JRequest::setVar('menutype',$menutype);
		$tmp = $model->getItems();

		$items = array();
		if(count($tmp)){
			foreach ($tmp as $row){

				if(($row->menutype==$menutype)||true){
					$xmlArray = array(

							'id'			=> new xmlrpcval( $row->id, 'string' ),
							'menutype'		=> new xmlrpcval( $row->menutype, 'string' ),
							'title'			=> new xmlrpcval( $row->title, 'string' ),
							'alias'			=> new xmlrpcval( $row->alias, 'string' ),
							'note'			=> new xmlrpcval( $row->note, 'string' ),
							'path'			=> new xmlrpcval( $row->path, 'string' ),
							'link'			=> new xmlrpcval( htmlspecialchars_decode($row->link), 'string' ),
							'parent_id'		=> new xmlrpcval( $row->parent_id, 'string' ),
							'language'		=> new xmlrpcval( $row->language, 'string' ),
							'level'			=> new xmlrpcval( $row->level, 'string' ),
							'ordering'		=> new xmlrpcval( isset($row->ordering)?$row->ordering:0, 'string' ),//ordering not working on joomla 3.x
							'access'		=> new xmlrpcval( $row->access, 'string' ),
							'img'			=> new xmlrpcval( $row->img, 'string' ),
							'params'		=> new xmlrpcval( htmlspecialchars_decode($row->params), 'string' ),
							'home'			=> new xmlrpcval( $row->home, 'string' ),
							'language'		=> new xmlrpcval( $row->language, 'string' ),
							'published'		=> new xmlrpcval( $row->published, 'string' ),
							'access_level'		=> new xmlrpcval( $row->access_level, 'string' ),
							'name'			=> new xmlrpcval( $row->name, 'string' ),
							);
					$xmlObject = new xmlrpcval($xmlArray, 'struct');
					$items[] = $xmlObject;
					$xmlObject = null;
				}
			}

		}
		return new xmlrpcresp(new xmlrpcval($items, $xmlrpcArray));
	}


	public function addUsergroup ($key,$username,$password,$title,$parent_id,$id){

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		if($user->authorise('core.manage')==false){  

			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		$model = $this->getModel('Usergroup');


		$data = array();
		$data['parent_id'] = $parent_id;
		$data['title'] = $title;

		if(isset($id)){
			$data['id'] = (int)$id;
		}

		if( !$model->save($data)){
			return $this->response($model->getError());
		}
		return (new xmlrpcresp(new xmlrpcval($model->getState('usergroup.id'), 'string')));

	}

	public function newMenuItem ($key,$username,$password,$title,$alias,$note,$menutype,$component_id,$link,$published,$access,$parent_id,$img,$params,$id,$language){

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		if($user->authorise('core.manage')==false){  

			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		$model = $this->getModel('Menuitem');

		$row = $model->getItem((int)$id);

		$data = array();
		$data['menutype'] = $menutype;
		$data['title'] = $title;
		$data['alias'] = $alias;
		$data['type'] = 'component';
		$data['component_id'] = $component_id;
		$data['language'] = '*';
		if(isset($language))
			$data['language'] = $language;
		$data['note'] = $note;
		$data['link'] = $link;
		$data['published'] = $published;
		$data['access'] = $access;
		$data['apublished'] = 1;
		$data['parent_id'] = $parent_id;
		$data['img'] = $img;
		$data['params'] = $params;

		if(isset($id)){
			$data['id'] = (int)$id;

		}
		

		if( !$model->save($data)){
			return $this->response($model->getError());
		}
		return (new xmlrpcresp(new xmlrpcval($model->getState('item.id'), 'string')));

	}
	public function newMenu ($key,$username,$password,$menutype,$title,$description,$id,$language,$ordering,$access,$published,$showtitle,$position,$parameters){

		$user = $this->authenticateUser($username, $password);



		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		$model = $this->getModel('Menu');

		$data = array();
		if(isset($id))
			$data['id'] = (int)$id;
		$data['menutype'] = $menutype;
		$data['title'] = $title;
		$data['description'] = $description;

		if($model->allowAdd($data) !== true){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		if(isset($data['id']) && $model->allowEdit($data) !== true){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}

		if(!$model->save($data)){
			return $this->response($model->getError());
		}

		
		// Check if the module already exists
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__modules WHERE title = '$title'";
		$db->setQuery($query);
		$checkId = $db->loadResult();

		if (count($checkId) >0)
		{
			$db = JFactory::getDBO();
			$query = "DELETE FROM #__modules WHERE title = '$title'";
			$db->setQuery($query);
			$checkId = $db->loadResult(); 
	        }


		// Creating menu module

		$data = new stdClass();
		$data->title = $title;
		$data->ordering = $ordering;
		$data->position = $position;
		$data->checked_out = '0';
		$data->checked_out_time = '0000-00-00 00:00:00';
		$data->publish_up = '0000-00-00 00:00:00';
		$data->publish_down = '0000-00-00 00:00:00';
		$data->module = 'mod_menu';
		$data->access = $access;
		$data->published = $published;
		$data->showtitle = $showtitle;
		$data->params ='{"menutype":"'.str_replace('_','-',str_replace(' ','-',$menutype)).'","startLevel":"1","endLevel":"3","showAllChildren":"0","tag_id":"","class_sfx":"","window_open":"","layout":"","moduleclass_sfx":"_menu","cache":"1","cache_time":"900","cachemode":"itemid"}';
		$data->client_id = '0';
		$data->language = $language;


		$db = JFactory::getDBO();
		$db->insertObject('#__modules',$data,null);

		$moduleid = $db->insertid();
		$data = new stdClass();
		$data->moduleid=$moduleid;
		$data->menuid=$model->getState('menu.id').'';
		$db->insertObject('#__modules_menu',$data);

		return (new xmlrpcresp(new xmlrpcval($model->getState('menu.id'), 'string')));

	}



	public function getMenuList ($key,$username,$password){
		global $xmlrpcArray;

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}


		$model = $this->getModel('Menus');
		if($user->authorise('core.manage')==false){  

			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
	
		$items = $model->getIds();

		if(empty($items)){
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
		}
		$structarray = array();
		for($i=0;$i<count($items);$i++){

			$db = JFactory::getDBO();
			$query = "SELECT * FROM #__modules WHERE title = '".$items[$i]->title."'";
			$db->setQuery($query);
			$checkId = $db->loadObjectList();
			$language = '*';
			if(count($checkId)>0){
				$language = $checkId[0]->language;
			}

			if((!$this->authorize($user,'edit',$items[$i]->id,'com_menus.menu'))) continue;

			$array = array();
			$array['id']		= new xmlrpcval( $items[$i]->id, 'string' );
			$array['menutype']	= new xmlrpcval( $items[$i]->menutype, 'string' );
			$array['title']		= new xmlrpcval( $items[$i]->title, 'string' );
			$array['description']	= new xmlrpcval( $items[$i]->description, 'string' );
			$array['language']	= new xmlrpcval( $language, 'string' );

			$structarray[] = new xmlrpcval( $array, 'struct' );
		}

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));

	}



	public function setFrontpage ($key,$username,$password,$list){
		global $xmlrpcString;
		$user = $this->authenticateUser($username, $password);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		$userid = (int)$user->get('id');
		if($user->authorise('core.manage')==false){  

			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		$canEdit	= $user->authorise('core.edit', 'com_content.article');
		$canEditOwn	= $user->authorise('core.edit.own', 'com_content.article') && $row->created_by == $userid;
		if(!($canEdit || $canEditOwn)){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}



		JRequest::setVar('limit', 0);
		$model = $this->getModel('Featured');
		$temp = $model->getItems();
		
		$articles = array();
		if(count($temp)){
			foreach ($temp as $row)
			{
				$canEdit	= $user->authorise('core.edit', 'com_content.article.'.$row->id);
				$canCheckin	= $row->checked_out == $userid || $row->checked_out == 0;
				$canEditOwn	= $user->authorise('core.edit.own', 'com_content.article.'.$row->id) && $row->created_by == $userid;

				if(($canEdit || $canEditOwn) && $canCheckin){
					if (isset($rendered) && $rendered==1){
						$row->introtext = JHTML::_('content.prepare', $row->introtext);
						$row->fulltext = JHTML::_('content.prepare', $row->fulltext);
					}
					if (!in_array($row->id,$list)){
						$this->mw_editPost($row->id,$username,$password,$row->title,$row->alias,$row->introtext,$row->fulltext,$row->state,$row->access, 0,$row->publish_up,$row->catid);
					}

					$articles[] = $row->id;

				}
			}
		}

		for($i=0;$i<count($list);$i++){

			$postid = (int)$list[$i];

			$modelArticle = $this->getModel('Article');
			$row = $modelArticle->getItem($postid);
			if(!$row){
				return $this->response(JText::_('PLG_JOOOID_ITEM_WAS_NOT_FOUND'));
			}
			if($row->featured==0){
				$this->mw_editPost($row->id,$username,$password,$row->title,$row->alias,$row->introtext,$row->fulltext,$row->state,$row->access, 1,$row->publish_up,$row->catid);
			}

		}

		$ret = $model->saveFront($list);
		return new xmlrpcresp(new xmlrpcval($ret, $xmlrpcString));

	}

	public function getFrontpage ($key,$username,$password){
		global $xmlrpcArray;

		$user = $this->authenticateUser($username, $password);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}


		if($user->authorise('core.manage')==false){  

			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		JRequest::setVar('limit', 0);
		$model = $this->getModel('Featured');
		$temp = $model->getItems();
		$articles = array();
		if(count($temp)){
			foreach ($temp as $row)
			{
				$canEdit	= $user->authorise('core.edit', 'com_content.article.'.$row->id);
				$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $row->checked_out == $userid || $row->checked_out == 0;
				$canEditOwn	= $user->authorise('core.edit.own', 'com_content.article.'.$row->id) && $row->created_by == $userid;

				if(($canEdit || $canEditOwn) && $canCheckin){
					if (isset($rendered) && $rendered==1){
						$row->introtext = JHTML::_('content.prepare', $row->introtext);
						$row->fulltext = JHTML::_('content.prepare', $row->fulltext);
					}			
					$row->is_frontpage = true;
					$res = $this->buildStruct($row, $mt);

					if ($res[0]){
						$articles[] = $res[1];
					}
				}
			}
		}
		if(empty($articles)){
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
		}

		return new xmlrpcresp(new xmlrpcval($articles, $xmlrpcArray));


	}


	public function getUsergroups ($key,$username,$password){
		$user = $this->authenticateUser($username, $password);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		$model = $this->getModel('Usergroups');
		$groups = $model->getItems();


		if(empty($groups)){
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
		}

		foreach($groups as $row){

			$structarray[] = new xmlrpcval(
					array(
						'id' => new xmlrpcval($row->id, 'string'),
						'title' => new xmlrpcval($row->title, 'string'),
						'level' => new xmlrpcval($row->level, 'string'),
						'parent_id' => new xmlrpcval($row->parent_id, 'string'),
						'user_count' => new xmlrpcval($row->user_count, 'string')
					     ),'struct');
		}
		if(empty($structarray)){
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
		}

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
	}

	public function addAccessLevel ($key,$username,$password,$title,$rules,$id){

		$user = $this->authenticateUser($username, $password);


		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		$model = $this->getModel('Level');

		for($i=0;$i<count($rules);$i++){
			$rules[$i] = intVal($rules[$i]);
		}

		$data = array();
		$data['title']=$title;
		$data['rules']=json_encode($rules);
		if(isset($id)){
			$data['id']= $id;
		}

		if(!$model->save($data)){
			return $this->response($model->getError());
		}
		return (new xmlrpcresp(new xmlrpcval($model->getState('level.id'), 'string')));

	}



	public function addUser ($key,$myusername,$mypassword,$username,$password,$name,$email,$block, $groups,$id,$sviluppi_futuri){
		$user = $this->authenticateUser($myusername, $mypassword);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		if(!$user->authorise('core.edit', 'com_users.user')){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		if(!$user->authorise('core.create', 'com_users.user')){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}

		$groupsArray = explode(" ", $groups);
		$groupsArray =  array_filter($groupsArray, "joooid_explode_filtered_empty");
		$model = $this->getModel("User");	
		$data = array();
		if (isset($id) && $id!=''){
			$data['userId']=$id;
			$data['id']=$id;
			$row = $model->getItem($id);
			if ($row->checked_out > 0 && $row->checked_out != $user->get('id')){
				return $this->response(JText::sprintf('PLG_JOOOID_EDITING_OTHER_USER', $row->title));
			}

		}
		$data['name']=$name;
		$data['username']=$username;
		if (isset($password) ){
			$data['password']=$password;
			$data['password2']=$password;
		}
		$data['email']=$email;
		$data['block']=$block;
		$data['groups']=$groupsArray;

		if(!$model->save($data)){
			return $this->response($model->getError());
		}


		return new xmlrpcresp(new xmlrpcval($model->getState('user.id'), 'int'));
	}



	public function getUsersList ($key,$username,$password,$start=-1,$limit=-1,$filters=array(),$order=array()){

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		if(!$user->authorise('core.manage')){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		if(!$user->authorise('core.edit', 'com_users.user')){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		if(!$user->authorise('core.create', 'com_users.user')){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		$model = $this->getModel("Users");	
		if($limit==-1)
			$limit = $this->listLimit;
		if($start == -1){
			$limit = 0;
			$start = 0;
		}

		// Empty all default request variables
		JRequest::setVar('filter_search','');
		JRequest::setVar('filter_active','');
		JRequest::setVar('filter_state','');
		JRequest::setVar('filter_group_id','');


		//applying order
		JRequest::setVar('order_key','id');
		JRequest::setVar('order_filter','desc');
		foreach($order as $key => $ord){
			JRequest::setVar('order_key',$key);
			JRequest::setVar('order_filter',$ord);
		}

		//applying filters
		foreach($filters as $key => $filter){
			JRequest::setVar('filter_'.$key, $filter);
		}



		JRequest::setVar('limit',$limit);
		JRequest::setVar('limitstart',$start);





		$users = $model->getItems();


		if(empty($users)){
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
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

		$db = JFactory::getDBO();

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

	}

	public function loggerOff($username,$password){
		joooid_log("Logger Off");
		$user = $this->authenticateUser($username, $password);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}
		$db = JFactory::getDBO();

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


		return $this->response(JText::_('PLG_JOOOID_OK_UPDATE'));


	}
	protected function buildCategory($title,$alias,$description,$parent_id,$published,$access,$created_user_id,$language,$id)
	{
		$date = JFactory::getDate();
		$created = $date->toSQL();

		$user = JFactory::getUser();
		$userid = intval( $user->get('id') );

		$content['description'] = $description;
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
		if ($parent_id==0) $parent_id=1;
		$content['parent_id'] = $parent_id;
		$content['created_user_id'] = $userid;
		$content['language'] = "*";

		$content['language'] = $this->params->get('language', '*');


		if(isset($content['dateCreated_gmt'])){
			$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
			$content['created']  = $content['publish_up'] = $date->toSQL();
		} else if(isset($content['dateCreated'])){
			$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
			$content['created']  = $content['publish_up'] = $date->toSQL();
		}

		if(empty($content['id']) && empty($content['created'])){
			$content['created'] = JFactory::getDate()->toSQL();
		}

		return $content;
	}

	public function blogger_newCategory()
	{
		$args		= func_get_args();

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
		$created_user_id= $args[9];
		$language	= $args[10];
		$id		= (int)$args[11];


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

		$row->published = -2;//to trash

		if (!$row->store()){
			return $this->response($row->getError());
		}

		$row->checkin();


		//clear cache
		$cache =  JFactory::getCache('com_content');
		$cache->clean();

		return new xmlrpcresp(new xmlrpcval('true', $xmlrpcBoolean));
	}


	public function mw_newCategory()
	{
		$args		= func_get_args();

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
		jimport('joomla.filesystem.path');
		$path = JTable::addIncludePath(JPATH_BASE.'/administrator/components/com_categories/tables');
		if(isset($id)){
			$oldModel = $this->getModel('Category');
			$row = $oldModel->getItem($id);
			if ($row->checked_out > 0 && $row->checked_out != $user->get('id')){
				return $this->response(JText::sprintf('PLG_JOOOID_EDITING_OTHER_USER', $row->title));
			}
		}


		$data  = $this->buildCategory($title,$alias,$description,$parent_id,$published,$access,$created_user_id,$language,$id);
		$model = $this->getModel('Category');

		$ret = $model->save($data);	

		if(!$ret){
			return $this->response($model->getError());
		}



		return (new xmlrpcresp(new xmlrpcval($model->getState('category.id'), 'string')));
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
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
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

	public function wp_newCategory()
	{
		global $xmlrpcerruser;

		$args		= func_get_args();

		if(func_num_args() < 4){
			return new xmlrpcresp(0, $xmlrpcerruser + 1,  JText::_('The request is illegal.'));
		}

		$username	= strval( $args[1] );
		$password	= strval( $args[2] );
		$category	= $args[3];

		$user = $this->authenticateUser($username, $password);

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

		if (!$row->publish($row->id,-2)){
			return $this->response($row->getError());
		}
		$model = $this->getModel('Category');
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

		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$structarray = array();

		$model = $this->getModel('Categories');
		$categories = $model->getItems();

		if(empty($categories)){
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
		}

		foreach($categories as $row){
			$row->permissions = $this->buildPermissions($user,'com_content.category',$row->id);
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
						'permissions' => new xmlrpcval($row->permissions, 'string'),
						'language' => new xmlrpcval($row->language, 'string'),
						'created_user_id' => new xmlrpcval($row->created_user_id, 'string'),
						'published' => new xmlrpcval($row->published, 'string')
					     ),'struct');
		}
		if(empty($structarray)){
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
		}

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));

	}

	public function templatePositions()
	{
		global $xmlrpcArray;
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


		// **** Start templatePosition
		$app = JFactory::getApplication();
		$template = $app->getTemplate();
		$positionsModel = $this->getModel('Positions');
		$positions = $positionsModel->getItems();

		$positionsarray = array();
		foreach($positions as $key=>$position){
			$list = array();
			if(isset($position)&&is_array($position)&&count($position)>0){
				foreach($position as $templateName => $templateDescription){
					$array = array();
					$array['name']		= new xmlrpcval( $templateName, 'string' );
					$array['description']	= new xmlrpcval( JText::_($templateDescription), 'string' );
					$list[] = new xmlrpcval( $array, $xmlrpcStruct );
				}
				$listxmlrpc = new xmlrpcval($list,$xmlrpcArray);
				$positionElement = array();
				$positionElement['name'] = new xmlrpcval($key,'string');
				$positionElement['param'] = $listxmlrpc;
				$positionsarray[] = new xmlrpcval($positionElement,$xmlrpcStruct);
			}
		}

		$positionsxmlrpc = new xmlrpcval($positionsarray,$xmlrpcArray);
		return new xmlrpcresp($positionsxmlrpc);

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



		$params = JComponentHelper::getParams('com_media');
		$basepath = $params->get('image_path');


		

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


		// **** Loading access levels

		$aclModel = $this->getModel('Levels');
		JRequest::setVar('limit', 0);
		$aclList = $aclModel->getItems();

		$accessarray = array();
		for($i=0;$i<count($aclList);$i++){



			$array = array();
			$array['id']		= new xmlrpcval( $aclList[$i]->id, 'string' );
			$array['title']		= new xmlrpcval( $aclList[$i]->title, 'string' );

			$accessarray[] = new xmlrpcval( $array, 'struct' );
		}

		$accessxmlrpc = new xmlrpcval($accessarray,'struct');
		// **** EndLoading access levels



		// **** Loading languages
		$languageList = JLanguageHelper::getLanguages();
		$languagearray = array();
		
		for($i=0;$i<count($languageList);$i++){
			$array = array();
			$array['lang_id']		= new xmlrpcval( $languageList[$i]->lang_id, 'string' );
			$array['lang_code']		= new xmlrpcval( $languageList[$i]->lang_code, 'string' );
			$array['title']			= new xmlrpcval( $languageList[$i]->title, 'string' );
			
			$languagearray[] = new xmlrpcval( $array, 'struct' );
		}
		$languagexmlrpc = new xmlrpcval($languagearray,'struct');


		// **** EndLoading languages

		$struct = new xmlrpcval(
				array(
					'nickname'	=> new xmlrpcval($user->username),
					'userid'	=> new xmlrpcval($user->id),
					'url'		=> new xmlrpcval(JURI::root()),
					'email'		=> new xmlrpcval($user->email),
					'lastname'	=> new xmlrpcval($lastname),
					'firstname'	=> new xmlrpcval($firstname),
					'imagepath' 	=> new xmlrpcval($basepath),
					'version'	=> new xmlrpcval($this->version),
					'access'	=> $accessxmlrpc,
					'language'	=> $languagexmlrpc
				     ), $xmlrpcStruct);

		return new xmlrpcresp($struct);

	}

	public function blogger_newPost()
	{
		$args		= func_get_args();

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
		$language 	= $args[13];

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


		return $this->mw_newPost($blogid, $username, $password, $title, $alias, $intro, $full, $publish, $access,$front, $published_up, $difference, $language);
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
		$language	= $args[14];


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
		return $this->mw_editPost($postid, $username, $password, $title, $alias, $intro, $full, $state, $access, $front, $published_up, $catId, $difference,$language);
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

		$cache =  JFactory::getCache('com_content');
		$cache->clean();

		return new xmlrpcresp(new xmlrpcval('true', $xmlrpcBoolean));
	}

	public function blogger_updatePost()
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
		$cache =  JFactory::getCache('com_content');
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
		$rendered	= isset($args[5])?(int)$args[5]:0;
		$start		= isset($args[6])?(int)$args[6]:0;
		$filters	= isset($args[7])?$args[7]:array();
		$order		= isset($args[8])?$args[8]:array();



		return $this->mw_getRecentPosts($blogid, $username, $password, $numposts, $rendered,$start,$filters,$order);
	}

	public function setFeatured($id, $featured){


		$db = JFactory::getDBO();

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

	}

	public function mw_newPost()
	{
		$args		= func_get_args();

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

		$language 	= $args[12];


		$user = $this->authenticateUser($username, $password);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		if(!$user->authorise('core.create', 'com_content.category.'.$blogid)){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}


		$data  = $this->buildArticle($title, $alias, $intro, $full, $publish, $access, $front,$language,$published_up,null);
		$data['featured'] = $front;
		$data['catid'] = (int)$blogid;

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
		$language 	= $args[13];

		$user = $this->authenticateUser($username, $password);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}


		$oldModel = $this->getModel('Article');
		$row = $oldModel->getItem($postid);
		if ($row->checked_out > 0 && $row->checked_out != $user->get('id')){
			return $this->response(JText::sprintf('PLG_JOOOID_EDITING_OTHER_USER', $row->title));
		}

		$ret = $this->buildStruct($row);
		if($published_up=="00-00-00 00:00:00"){
			$published_up = $row->publish_up;
		}

		$data  = $this->buildArticle($title, $alias, $intro, $full, $state, $access, $front, $language, $published_up,$row->created);
		$data['id'] = $postid;
		$data['featured'] = $front;
		$data['catid'] = (int)$catid;

		$model = $this->getModel('Article');
		$row = $model->getItem($postid);
		if (!$this->authorize($user,'publish',$postid,'com_content.article')){
			$data['featured']= $row->featured;
			$data['state']=$row->state;
		}

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
		$limit		= $args[3];
		$rendered	= $args[4];
		$start		= $args[5];
		$filters	= $args[6];
		$order		= $args[7];


		$limit		= 0;

		if(isset($args[3])){
			$limit = (int)$args[3];
		}

		$mt	= false;

		if(isset($args[5])){
			$mt = (boolean)$args[5];
		}

		$user = $this->authenticateUser($username, $password);
		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		$model = $this->getModel('Articles');

		//applying filters
		foreach($filters as $key => $filter){
			JRequest::setVar('filter_'.$key, $filter);
		}


		//applying order
		JRequest::setVar('order_key','title');
		JRequest::setVar('order_filter','asc');
		foreach($order as $key => $ord){
			JRequest::setVar('order_key',$key);
			$model->setState('list.ordering',$key);
			JRequest::setVar('order_filter',$ord);
			$model->setState('list.direction',$ord);
		}

		//applying limit
		JRequest::setVar('limit', $limit);
		JRequest::setVar('limitstart',$start);



		// Compatibility with older version of Joooid for cateogry id
		/* -- begin -- */
		$blogid = (int)$blogid;
		if($blogid > 0){
			JRequest::setVar('filter_category_id', $blogid);
		}
		/* -- end -- */


		//$userid = (int)$user->get('id');
		$temp = $model->getItems();
		if(empty($temp)){
			return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
		}

		$articles = array();
		if(count($temp)){
			foreach ($temp as $row)
			{

				if (isset($rendered) && $rendered==1){
					$row->introtext = JHTML::_('content.prepare', $row->introtext);
					$row->fulltext = JHTML::_('content.prepare', $row->fulltext);
				}


				$row->permissions = $this->buildPermissions($user,'com_content.article',$row->id);

				$res = $this->buildStruct($row, $mt);

				if ($res[0]){
					$articles[] = $res[1];
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
				return $this->response(JText::_('PLG_JOOOID_ELEMENTS_WAS_NOT_FOUND'));
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


			$dispatcher = JDispatcher::getInstance();
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

			$cache =  JFactory::getCache('com_content');
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

		$cache =  JFactory::getCache('com_content');
		$cache->clean();

		return (new xmlrpcresp(new xmlrpcval('1', 'boolean'))); }

	public function mt_getTrackbackPings()
	{
		$args		= func_get_args();

		if(func_num_args() < 1){
			return $this->response(JText::_('PLG_JOOOID_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[0];

		return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
	}

	public function mt_supportedMethods()
	{
		return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
	}



	public function mw_mediaGetFileList($key,$username,$password,$path){
		global $xmlrpcStruct;
		$params = JComponentHelper::getParams('com_media');
		$basepath = $params->get('image_path');
		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}


		if(!$user->authorise('admin.login', 'com_media')){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}
		if(strpos($path,'/../..')!==false || strpos($path, "/..") === 0 || strpos($path,"/.") ===0){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}

		$list = array();
		$tmp = array();

		$dir = $basepath.$path.'/';
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if (is_dir($dir.$file)&& $file!='.' && $file!='..'){
						$tmp[] = $file;
					}

				}
				closedir($dh);
			}
		}
		sort($tmp);
		$list = array_merge($list,$tmp);

		$tmp = array();
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if (!is_dir($dir.$file)){
						$tmp[] = $file;
					}

				}
				closedir($dh);
			}
		}
		$list = array_merge($list,$tmp);


		$ret = array();


		if ($dir!=$basepath){
			$ret[] =new xmlrpcval(array(
						'name'=>new xmlrpcval('..','string'),
						'type'=>new xmlrpcval(filetype($dir),'string'),
						'size'=>new xmlrpcval('4095','string')
						),$xmlrpcStruct) ;
		}

		for($i=0;$i<count($list);$i++){
			$file = $list[$i];

			$stat = @stat($dir.$file);
			if ($stat==FALSE) {$stat = array();$stat['size']='N/A';}

			$ret[] =new xmlrpcval(array(
						'name'=>new xmlrpcval($file,'string'),
						'type'=>new xmlrpcval(@filetype($dir.$file),'string'),
						'size'=>new xmlrpcval($stat['size'],'string')
						),$xmlrpcStruct) ;

		}

		$returnRpc =new xmlrpcval($ret, 'array');

		return (new xmlrpcresp($returnRpc));

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

		if (isset($args[5])){
			$file 		= base64_decode( $args[5] );

		}


		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_JOOOID_LOGIN_WAS_NOT_ABLE'));
		}

		$model = $this->getModel('Article');
		$row = $model->getTable();

		if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
			return $this->response(JText::sprintf('PLG_JOOOID_EDITING_OTHER_USER', $row->title));
		}

		$data = array();
		$data['id'] = $row->id;
		$data['created_by'] = $row->created_by;
		if($model->allowEdit($data) !== true){
			return $this->response(JText::_('PLG_JOOOID_DO_NOT_HAVE_AUTH'));
		}

		if(count($_FILES)>0){
			$content = $_FILES['file']['tmp_name'];
			if (file_exists($content)){
				$fid = fopen($content,'rb');
				$file = fread($fid,filesize($content));
				fclose($fid);
			}
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

		$ext = JFile::getExt($file_name);

		$allowable = explode(',', $params->get('upload_extensions'));
		$ignored = explode(',', $params->get('ignore_extensions'));
		$images = explode(',', $params->get('image_extensions'));

		if (!in_array($ext, $allowable) && !in_array($ext,$ignored)){
			return $this->response(JText::_('PLG_JOOOID_NOT_ALLOWED_FILETYPE'));
		}

		require_once JPATH_ADMINISTRATOR.'/components/com_media/helpers/media.php';

		$images_path = str_replace('/', '/', JPATH_ROOT. '/'. $params->get('image_path', 'images'));
		$file_path = str_replace('/', '/', JPATH_ROOT. '/'. $params->get('file_path', 'images'));

		if(in_array($ext, $images)){
			$destination = $images_path;
		} else {
			$destination = $file_path;
		}

		$destination .= '/';

		if(!empty($dir)){
			$destination .= $dir;
			if(!JFolder::exists($destination)){
				if(!JFolder::create($destination)){
					return $this->response(JText::_('PLG_JOOOID_NOT_ABLE_TO_CREATE_FOLDER'));
				}
			}

			if(!JFile::exists($destination. '/'. 'index.html')){
				$html = '<html><body></body></html>';
				JFile::write($destination. '/'. 'index.html', $html);
			}

			$destination .= '/';
		}

		if(!JFile::write($destination. $file_name, $file)){
			return $this->response(JText::_('PLG_JOOOID_NOT_ABLE_TO_UPLOAD_FILE'));
		}

		if(!file_exists($destination . $file_name)){
			return $this->response(JText::sprintf('PLG_JOOOID_NOT_ABLE_TO_UPLOAD_FILE'));
		}

		$url = JURI::root(true). str_replace(array(JPATH_ROOT, '/'), array('', '/'), $destination. $file_name);

		return (new xmlrpcresp(new xmlrpcval($url, 'string')));
	}

	protected function authenticateUser($username, $password)
	{
		jimport( 'joomla.user.authentication');
		$auth =  JAuthentication::getInstance();
		$credentials['username'] = $username;
		$credentials['password'] = $password;
		$authuser = $auth->authenticate($credentials, null);

		if($authuser->status == JAuthentication::STATUS_FAILURE || empty($authuser->username) || empty($authuser->password) || empty($authuser->email)){
			return false;
		}

		$user = JUser::getInstance($authuser->username);
		//Check Status
		if(empty($user->id) || $user->block || !empty($user->activation)){
			return false;
		}

		JFactory::getSession()->set('user', $user);

		return $user;
	}

	protected function getCatTitle($id)
	{
		$db = JFactory::getDBO();
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
		if((isset($row->is_frontpage)) && $row->is_frontpage!=null && $row->frontpage_order==null)
			return array(false,null);

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
					'language'		=> new xmlrpcval( $row->language, 'string' ),
					'permissions'		=> new xmlrpcval( $row->permissions, 'string' ),
					'state'			=> new xmlrpcval( $row->state, 'string' ),
					'access'		=> new xmlrpcval( $row->access, 'string' ),
					'frontpage'		=> new xmlrpcval( $row->featured, 'string' )
					);
			if (isset($row->frontpage_order))
				$xmlArray['frontpage_order'] = new xmlrpcval( $row->frontpage_order, 'string' );
		}

		$xmlObject = new xmlrpcval($xmlArray, 'struct');
		return array(true, $xmlObject);
	}

	protected function buildData($content, $publish, $blogger=false)
	{
		$date = JFactory::getDate();
		$created = $date->toSQL();

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
			$content['created']  = $content['publish_up'] = $date->toSQL();
		} else if(isset($content['dateCreated'])){
			$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
			$content['created']  = $content['publish_up'] = $date->toSQL();
		}

		if(empty($content['id']) && empty($content['created'])){
			$content['created'] = JFactory::getDate()->toSQL();
		}

		return $content;
	}

	protected function buildArticle($title, $alias, $intro, $full, $state, $access, $front,$language,$published_up,$dateCreated)
	{
		$date = JFactory::getDate();
		$created = $date->toSQL();

		$user = JFactory::getUser();
		$userid = intval( $user->get('id') );

		if(!isset($content['description'])){
			$content['description'] = '';
		}
		$content['title'] = $title;
		$content['wp_slug'] = $alias;

		$content['alias'] = $alias;

		$content['articletext'] = $intro.'<hr id="system-readmore" />'.$full;;
		$content['description'] = $intro;
		$content['mt_text_more'] = $full;
		$content['mt_basename'] = $intro;

		if($dateCreated != null )
			$content['created'] = $dateCreated;

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
		$content['language'] = $language;


		if(isset($content['dateCreated_gmt'])){
			$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
			$content['created']  = $content['publish_up'] = $date->toSQL();
		} else if(isset($content['created'])){
			$date = JFactory::getDate(iso8601_decode($content['created'], 0));
		}

		if(empty($content['id']) && empty($content['created'])){
			$content['created'] = JFactory::getDate()->toSQL();
		}


		if (isset($published_up) && $published_up!='0') {
			$content['publish_up'] = $published_up;
		}


		return $content;
	}

	protected function getModel($type, $prefix='JOOOIDModel', $config=array())
	{
		return JModelLegacy::getInstance($type, $prefix, $config);
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

	protected function buildPermissions($user,$asset,$id){
		$ret = '';

		$ret .= ($user->authorise('core.create', $asset.'.'. $id))?'1':'0';
		$ret .= ($user->authorise('core.delete', $asset.'.'. $id))?'1':'0';
		$ret .= ($user->authorise('core.edit', $asset.'.'. $id))?'1':'0';
		$ret .= ($user->authorise('core.edit.state', $asset.'.'. $id))?'1':'0';
		$ret .= ($user->authorise('core.edit.own', $asset.'.'. $id))?'1':'0';

		return $ret;
	}

	protected function authorize($user,$type, $id, $asset,$params=array()){
		$published =0;
		$checked_out =0;
		$owner = 0;
		if (isset($params['published'])) $published = $params['published'];
		if (isset($params['checked_out'])) $checked_out = $params['checked_out'];
		if (isset($params['owner'])) $owner = $params['owner'];
		if ($type=="publish"){
			if(true || $published < 1){
				if(!$user->authorise('core.edit.state', $asset.'.'. $id)){
					return false;
				}

				if(!$user->authorise('core.admin', 'com_checkin') && $checked_out > 0 && $checked_out != $user->get('id')){
					return false;
				}
			}

		}

		else if ($type=="edit"){
			if(!($user->authorise('core.edit', $asset.'.'. $id)||($user->authorise('core.edit.own', $asset.'.'. $id) && ($owner==$user->id) ))){
				return false; 
			}
		}
		else if ($type=="create"){
			if(!($user->authorise('core.edit.state', $asset.'.'. $id)||$user->authorise('core.create', $asset.'.'. $id)||$user->authorise('core.edit', $asset.'.'. $id))){
				return false;
			}
		}

		return true;
	}

}

