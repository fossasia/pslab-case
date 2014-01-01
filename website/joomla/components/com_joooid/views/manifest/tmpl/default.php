<?php defined('_JEXEC') or die;?>
<?php
JFactory::getDocument()->setMimeEncoding('text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo $this->xml->saveHTML();
