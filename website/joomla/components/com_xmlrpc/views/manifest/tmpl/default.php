<?php defined('_JEXEC') or die;?>
<?php
if(is_null($this->xml)){
	return;
}
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo $this->xml->saveHTML();
