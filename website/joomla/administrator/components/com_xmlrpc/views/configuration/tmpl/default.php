<?php defined('_JEXEC') or die;?>
<style type="text/css">
fieldset dl dt, fieldset div{padding-left:2em;}
</style>
<fieldset>
	<legend><?php echo JText::_('COM_XMLRPC_INSTALLED_XMLRPC_PLUGINS');?></legend>
<?php if(count($this->xmlrpc_plugins)):?>
	<?php foreach($this->xmlrpc_plugins as $row):?>
	<dl>
		<dt style="color:green"><strong><?php echo $this->escape($row->name);?></strong></dt>
	</dl>
	<?php endforeach;?>
<?php else:?>
<div><strong style="color:red"><?php echo JText::_('COM_XMLRPC_NOT_ENABLED_XMLRPC_PLUGIN')?></strong></div>
<?php endif;?>
</fieldset>
<fieldset>
	<legend><?php echo JText::_('COM_XMLRPC_INSTALLED_RSD_PLUGINS');?></legend>
<?php if($this->rsd_plugins):?>
	<div><strong style="color:green"><?php echo JText::_('COM_XMLRPC_ENABLED');?></strong></div>
<?php else:?>
	<div><strong style="color:red"><?php echo JText::_('COM_XMLRPC_NOT_ENABLED_RSD_PLUGIN')?></strong></div>
<?php endif;?>
</fieldset>