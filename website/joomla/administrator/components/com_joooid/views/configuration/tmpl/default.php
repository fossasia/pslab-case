<?php defined('_JEXEC') or die;?>
<style type="text/css">
fieldset dl dt, fieldset div{padding-left:2em;}
</style>
<fieldset>
	<legend><?php echo JText::_('COM_JOOOID_INSTALLED_JOOOID_PLUGINS');?></legend>
	<?php $plugins = array();
		$plugins['xmlrpc'] = false;	
		$plugins['joooidcontent'] = false;	
	
	?>
	<?php foreach($this->joooid_plugins as $row):?>
<?php		if ($row->name =="joooid"){ 
			$plugins['xmlrpc'] = true;
		} 
?>



<?php		

		if ($row->name =="joooidcontent"){ 
			$plugins['joooidcontent'] = true;
		} 
?>

	<?php endforeach;?>

	<dl>
		<dt style="color:<?php echo ($plugins['xmlrpc'])?"green": "red"?>">Joooid XMLRPC: <strong>
			<?php echo ($plugins['xmlrpc'])?"Enabled": JText::_('COM_JOOOID_NOT_ENABLED_JOOOID_PLUGIN')?></strong>
		</dt>
		<dt style="color:<?php echo ($plugins['joooidcontent'])?"green": "red"?>">Joooid Content Plugin: <strong><?php echo ($plugins['joooidcontent'])?"Enabled": JText::_('COM_JOOOID_NOT_ENABLED_JOOOID_PLUGIN')?></strong></dt>
	</dl>

</fieldset>

