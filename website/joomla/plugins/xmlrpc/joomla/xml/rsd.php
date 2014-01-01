<?php
/**
 * 			XMLRPC RSD Joomla
 * @version			1.2.0
 * @package		XMLRPC for Joomla!
 * @copyright		Copyright (C) 2007 - 2013 Yoshiki Kozaki All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author		Yoshiki Kozaki  info@joomler.net
 * @link 			http://www.joomler.net/
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class XMLRPCRSDJoomla
{
	public static function buildXML($params)
	{
		$apiLink = JRoute::_(XMLRPCHelperRoute::getServiceRoute(), false, 2);

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

		$WordPress = $xml->createElement('api');
		$WordPress->setAttribute('name', 'WordPress');
		$WordPress->setAttribute('blogID', '1');
		$WordPress->setAttribute('preferred', 'true');
		$WordPress->setAttribute('apiLink', $apiLink);
		$apis->appendChild($WordPress);

		$MovableType = $xml->createElement('api');
		$MovableType->setAttribute('name', 'MovableType');
		$MovableType->setAttribute('blogID', '0');
		$MovableType->setAttribute('preferred', 'false');
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