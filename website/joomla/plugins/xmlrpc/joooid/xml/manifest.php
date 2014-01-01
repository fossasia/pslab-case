<?php
/**
 * 			JOOOID Manifest Joomla
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



class JOOOIDManifestJoomla
{
	public function buildXML($params)
	{
		$xml = new DOMDocument('1.0', 'UTF-8');

		$root = $xml->createElement('manifest');
		$root->setAttribute('xmlns', 'http://schemas.microsoft.com/wlw/manifest/weblog');
		$manifest = $xml->appendChild($root);
		$options = $manifest->appendChild($xml->createElement('options'));
		$options->appendChild($xml->createElement('clientType', 'Joomla!'));
		$options->appendChild($xml->createElement('supportsKeywords', 'Yes'));
		$options->appendChild($xml->createElement('supportsFileUpload', 'Yes'));
		$options->appendChild($xml->createElement('supportsEmbeds', 'Yes'));
		$options->appendChild($xml->createElement('supportsPostAsDraft', 'Yes'));
		$options->appendChild($xml->createElement('supportsCategories', 'Yes'));
		$options->appendChild($xml->createElement('supportsAutoUpdate', 'Yes'));
		$options->appendChild($xml->createElement('supportsNewCategories', 'Yes'));
		$options->appendChild($xml->createElement('supportsNewCategoriesInline', 'Yes'));
		$options->appendChild($xml->createElement('supportsCustomDate', 'Yes'));
		$options->appendChild($xml->createElement('supportsCategoriesInline', 'Yes'));
		$options->appendChild($xml->createElement('supportsHierarchicalCategories', 'Yes'));
		$options->appendChild($xml->createElement('supportsSlug', 'Yes'));
		$options->appendChild($xml->createElement('supportsExcerpt', 'Yes'));
//		$options->appendChild($xml->createElement('supportsPages', 'Yes'));
//		$options->appendChild($xml->createElement('supportsPageParent', 'Yes'));
//		$options->appendChild($xml->createElement('supportsPageOrder', 'Yes'));
		$options->appendChild($xml->createElement('requiresXHTML', 'Yes'));
		$options->appendChild($xml->createElement('supportsExtendedEntries', 'Yes'));

		$options->appendChild($xml->createElement('supportsCommentPolicy', 'No'));
		$options->appendChild($xml->createElement('supportsPingPolicy', 'No'));
		$options->appendChild($xml->createElement('supportsTrackbacks', 'No'));
		$options->appendChild($xml->createElement('supportsEmptyTitles', 'No'));
		$options->appendChild($xml->createElement('requiresHtmlTitles', 'No'));

//		$options->appendChild($xml->createElement('supportsGetTags', 'No'));

		$weblog = $manifest->appendChild($xml->createElement('weblog'));
		$weblog->appendChild($xml->createElement('serviceName', 'Joomla!'));
		$weblog->appendChild($xml->createElement('imageUrl', JURI::root(true).'/components/com_joooid/assets/joomla-icon.png'));
		$weblog->appendChild($xml->createElement('watermarkImageUrl', JURI::root(true).'/components/com_joooid/assets/joomla-watermark.png'));
		$weblog->appendChild($xml->createElement('homepageLinkText', JText::_('PLG_JOOOID_MANIFEST_MANAGE_SITE')));
		$weblog->appendChild($xml->createElement('adminUrl', JURI::root(). 'administrator/'));
		$weblog->appendChild($xml->createElement('postEditingUrl', JURI::root(true).'/index.php?option=com_content&amp;task=article.edit&amp;a_id={post-id}'));

		$buttons = $manifest->appendChild($xml->createElement('buttons'));
		$button = $buttons->appendChild($xml->createElement('button'));
		$button->appendChild($xml->createElement('id', 0));
		$button->appendChild($xml->createElement('text', JText::_('PLG_JOOOID_MANIFEST_VIEW_HELP')));
		$button->appendChild($xml->createElement('imageUrl', JURI::root(true). '/components/com_joooid/assets/joomla-help.png'));
		$button->appendChild($xml->createElement('clickUrl', 'http://www.joomler.net/documents/141-xmlrpc-for-joomla.html'));

		$views = $manifest->appendChild($xml->createElement('views'));
		$views->appendChild($xml->createElement('default', 'WebLayout'));
		$view1 = $views->appendChild($xml->createElement('view'));
		$view1->setAttribute('type', 'WebLayout');
		$view1->setAttribute('src', JURI::root(true).'/index.php?option=com_joooid&task=weblayout&tmpl=component');
		$view2 = $views->appendChild($xml->createElement('view'));
		$view2->setAttribute('type', 'WebPreview');
		$view2->setAttribute('src', JURI::root(true).'/index.php?option=com_joooid&task=webpreview');

		return $xml;
	}
}
?>