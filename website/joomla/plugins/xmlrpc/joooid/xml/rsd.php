<?php
/**
 * 			JOOOID RSD Joomla
 * @version			1.0.0
 * @package			JOOOID Joomla
 * @copyright			Copyright (C) 2007 - 2011 Joomler!.net. All rights reserved.
 * @license			Http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author			Yoshiki Kozaki : joomlers@gmail.com
 * @link			Http://www.joomler.net/
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class JOOOIDRSDJoomla
{
	public static function buildXML($params)
	{
		$apiLink = JRoute::_(JOOOIDHelperRoute::getServiceRoute(), false, 2);

		$xml = new DOMDocument('1.0', 'UTF-8');

		$rsdElem = $xml->createElement('rsd');
		$rsdElem->setAttribute('version', '1.0');
		$rsdElem->setAttribute('xmlns',  'http://archipelago.phrasewise.com/rsd');

		$rsd = $xml->appendChild($rsdElem);
		$service = $rsd->appendChild($xml->createElement('service'));
		$service->appendChild($xml->createElement('engineName', 'Joomla!'));
		$service->appendChild($xml->createElement('engineLink', 'http://www.joomla.org/'));
		$service->appendChild($xml->createElement('homePageLink', JURI::root()));
		$apis = $service->appendChild($xml->createElement('apis'));

//		$WordPress = $xml->createElement('api');
//		$WordPress->setAttribute('name', 'WordPress');
//		$WordPress->setAttribute('blogID', '1');
//		$WordPress->setAttribute('preferred', ($preferred == 'wordpress' ? 'true':'false'));
//		$WordPress->setAttribute('apiLink', $apiLink);
//		$apis->appendChild($WordPress);

		$MovableType = $xml->createElement('api');
		$MovableType->setAttribute('name', 'MovableType');
		$MovableType->setAttribute('blogID', '0');
		$MovableType->setAttribute('preferred', 'true');
		$MovableType->setAttribute('apiLink', $apiLink);
		$apis->appendChild($MovableType);

		$MetaWeblog = $xml->createElement('api');
		$MetaWeblog->setAttribute('name', 'MetaWeblog');
		$MetaWeblog->setAttribute('blogID', '0');
		$MetaWeblog->setAttribute('preferred', 'false');
		$MetaWeblog->setAttribute('apiLink', $apiLink);
		$apis->appendChild($MetaWeblog);

		$Blogger = $xml->createElement('api');
		$Blogger->setAttribute('name', 'Blogger');
		$Blogger->setAttribute('blogID', '0');
		$Blogger->setAttribute('preferred', 'false');
		$MetaWeblog->setAttribute('apiLink', $apiLink);
		$apis->appendChild($Blogger);

		return $xml;
	}
}