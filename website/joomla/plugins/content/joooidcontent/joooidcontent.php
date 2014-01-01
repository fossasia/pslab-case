<?php
/**
 * @version		1.0
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

require_once(dirname(__FILE__) . '/JSON.php');
/**
 * Article Date plugin.
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.articledate
 */
class plgContentJoooidContent extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @since	1.6
	 */
	public function onContentPrepare($context, &$row, &$params, $page=0)
	{
		

		error_log(E_ALL);
		//echo "<pre>";	
		$row ->text = $this->executeTokens($row->text);
		//print_r($row);
		//die();


	}

	private function getParams($elem){

		$aa = preg_split("/\" *, */",$elem);
		for ($i=1;$i<count($aa);$i++){
			$aa[$i] = preg_replace("/^\" */i","",$aa[$i]);	
		}

		// 1st parameter  = embed type
		$aa[0] = str_replace("{\"joooidPlugin_","",$aa[0]);
		$aa[0] = str_replace("{\"joooidPlugin_","",$aa[0]);
		// If there aren't parameters
		if (count($aa)==1){
			$aa[count($aa)-1] = preg_replace("/};$/","",$aa[count($aa)-1]);

		}else{
			// If there are parameters
			$aa[count($aa)-1] = preg_replace("/\"};$/","",$aa[count($aa)-1]);
		}
		//print_r($aa);

		return $aa;
	}



	private function createMap($arr){
		$obj = new joooid_map();
		$obj->id = (isset($arr->id))?$arr->id:'';
		$obj->width = $arr->width;
		$obj->height = $arr->height;
		$obj->align=$arr->align;
		$obj->lat_long=$arr->latitude." , ".$arr->longitude;
		$obj->mapType=(isset($arr->map_type))?$arr->map_type:'';
		$obj->zoomLevel=$arr->zoom;
		$obj->infoWindow=$arr->text;
		return $obj;
	}

	private function createYoutube($arr){
		$obj = new joooid_youtube();
		$obj->url = $arr->id;
		$obj->width = $arr->width;
		$obj->height = $arr->height;
		$obj->align = $arr->align;
		$obj->allowFullscreen=$arr->allowFullscreen;
		$obj->theme=$arr->theme;
		$obj->hd=$arr->hd;
		$obj->autoplay=$arr->autoplay;
		return $obj;
	}

	private function createParamsArr($type,$arr){
		if ($type=="youtube"){
			return $this->createYoutube($arr);
		}
		else if($type=="map"){
			return $this->createMap($arr);
		}
		return null;
	}

	// map, youtube, gallery
	private function executeTokens($text){
		$regex = "/{JoooidContent[\w\ \?\=,\/\.:\"\&\-]*}/i";			
		$val = 0;
		$val = preg_match($regex, $text,$matches);
		if ($val > 0 ) {
			$document =& JFactory::getDocument();
			$document->addScript("https://maps.google.com/maps/api/js?sensor=true");
		}
		while ($val!=0){
			//echo($matches[0]);
			$stringa =str_replace("JoooidContent","", $matches[0]);
			$jsonObj = new JSON;
			$json = $jsonObj->unserialize($stringa);
			if (!isset($json->type)) {
				$text = preg_replace($regex,str_replace("JoooidContent","TYPEERRORJoooidContent",$matches[0]),$text);
				$val = preg_match($regex, $text,$matches);
				continue;
			}
			$obj = $this->createParamsArr($json->type,$json);
			if ($obj == null){
				$text = preg_replace($regex,str_replace("JoooidContent","ERRORJoooidContent",$matches[0]),$text);
				$val = preg_match($regex, $text,$matches);
				continue;
				}
			$html = $obj->render();

			$text = str_replace($matches[0],$html,$text);
			$val = preg_match($regex, $text,$matches);

		}

		$subst = preg_replace ("/{ERRORJoooidContent/", "{JoooidContent",$text);
		$text = $subst;
		

		// matches[0]: original token
		// matches[1]: type
		// matches[2]: parameters 

		//print_r($matches);
		return $text;
	}
}


class joooid_youtube{
	public $url='';
	public $width='';
	public $height='';
	public $align='';
	public $allowFullscreen='';
	public $theme='';
	public $hd='';
	public $autoplay='';

	public function __construct(){}
	public function render(){

		// Convert align from center|right|left to the right style
		if ($this->align=="right"){
			$this->align ="margin: 0 0 0 auto; width:".$this->width."px; height:".$this->height."px;";
		}
		else if ($this->align=="center"){
			$this->align ="margin: 0 auto; width:".$this->width."px; height:".$this->height."px;";
		}
		else{
			$this->align ="margin: 0 auto 0 0; width:".$this->width."px; height:".$this->height."px;";
		}

		// Default values
		if ($this->theme=='') $this->theme ="dark";
		if ($this->hd=='') $this->hd ="1";
		if ($this->autoplay=='') $this->autoplay ="0";


		$stringa ='';
		$stringa .='<div style="margin:10px 0 10px; width:100%; height:'.$this->height.'px;">';
		$stringa .='<div style="'.$this->align.'">';
		$stringa .='<object width="'.$this->width.'px" height="'.$this->height.'px">
			<param name="movie" value="https://www.youtube.com/v/'.$this->url.'?fs=1&theme='.$this->theme.'&hd='.$this->hd.'&autoplay='.$this->autoplay.'"></param>
			<param name="allowFullScreen" value="'.$this->allowFullscreen.'"></param>
			<param name="allowScriptAccess" value="always"></param>
			<embed src="https://www.youtube.com/v/'.$this->url.'?fs=1&theme='.$this->theme.'&hd='.$this->hd.'&autoplay='.$this->autoplay.'"
			type="application/x-shockwave-flash"
			allowfullscreen="'.$this->allowFullscreen.'"
			allowscriptaccess="always"
			width="'.$this->width.'px" height="'.$this->height.'px">
			</embed>
			</object>
			';
		$stringa .="</div>";
		$stringa .='</div>';
		return $stringa;
	}

}

class joooid_map{
	public $id='';
	public $width='';
	public $height='';
	public $align='';
	public $lat_long='';
	public $mapType='';
	public $zoomLevel='';
	public $infoWindow='';

	public function __construct(){}
	public function render(){
		// Convert align from center|right|left to the right style
		
		if ($this->align=="right"){
			$this->align ='width:'.$this->width.'px; height:'.$this->height.'px; margin : 0px 0px 0px auto;';
		}
		else if ($this->align=="center"){
			$this->align ='width:'.$this->width.'px; height:'.$this->height.'px; margin : 0px auto;';
		}
		else if ($this->align=="left"){
			$this->align ='width:'.$this->width.'px; height:'.$this->height.'px; margin : 0px auto 0px 0px;';
		}

		if ($this->zoomLevel=="") $this->zoomLevel="7";


		// $stringa ='<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>';

		$stringa .='<div style="margin:10px 0 10px; width: 100%; height: '.$this->height.'px;"><div id="map_canvas_'.$this->id.'" style="'.$this->align.'"> </div></div>
					<script type="text/javascript">
						var latlng = new google.maps.LatLng('.$this->lat_long.');
						var myOptions = {zoom: '.$this->zoomLevel.',center: latlng,mapTypeId: google.maps.'.$this->mapType.'};
						var map = new google.maps.Map(document.getElementById("map_canvas_'.$this->id.'"),myOptions);
						var marker = new google.maps.Marker({ position: latlng, map: map, title:"Hello Joooid!"});
						var infowindow = new google.maps.InfoWindow({ content: "'.$this->infoWindow.'"});
						infowindow.open(map,marker);
					</script>';


		return $stringa;
	}
}
