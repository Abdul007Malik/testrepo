<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
// IMPORT EDITOR CLASS
jimport( 'joomla.html.editor' );
JHtml::_('behavior.formvalidator');

// The following is to enable setting the permission's Calculated Setting
// when you change the permission's Setting.
// The core javascript code for initiating the Ajax request looks for a field
// with id="jform_title" and sets its value as the 'title' parameter to send in the Ajax request
JFactory::getDocument()->addScriptDeclaration('
	jQuery(document).ready(function() {
        title	 = jQuery("#jform_title").val();
		jQuery("#jform_title").val(title);
	});
');

//$editor    = $app->input->getCmd('editor', '');
// IMPORT EDITOR CLASS
jimport( 'joomla.html.editor' );

// GET EDITOR SELECTED IN GLOBAL SETTINGS
$config = JFactory::getConfig();
$global_editor = $config->get( 'editor' );

// GET USER'S DEFAULT EDITOR
$user_editor = JFactory::getUser()->getParam("editor");

if($user_editor && $user_editor !== 'JEditor') {
    $selected_editor = $user_editor;
} else {
    $selected_editor = $global_editor;
}

// INSTANTIATE THE EDITOR
$editor = JEditor::getInstance($selected_editor);

// SET EDITOR PARAMS
$params = array( 'smilies'=> '0' ,
    'style'  => '1' ,
    'layer'  => '0' ,
    'table'  => '0' ,
    'clear_entities'=>'0'
);
// DISPLAY THE EDITOR (name, html, width, height, columns, rows, bottom buttons, id, asset, author, params)

?>
<script>
jQuery(document).ready(function() {

	jQuery(".add_img").click(function(){
		var counter = jQuery('#app_img').length;
		if(counter<7){
		var file='<div class="controls up_clear"><input type="file" name="images[]" class="" id="" accept="image/*"/><a href="javascript:void(0);" class="remove" id="" style="margin-right:10px">Remove</a></div>';

		jQuery("#app_img").append(file);
		}else{
			alert('Add Image limit reached');
		}
		jQuery(".remove").bind("click",function(){
			 jQuery(this).parent().remove();
			});

	});
	jQuery(".rimg").bind("click",function(){
		//alert('hi');//alert(this.id);
		var this_object = jQuery(this);
		jQuery.ajax({
			url:'index.php?option=com_helloworld&view=items',
			type:'POST',
			data:{
				task:'deleteImg',
				id:'<?php echo $this->item->id; ?>',
				img:this.id
			},
			cache: false,
			success:function(data){
				var response = JSON.parse(data);
				if(response['result']==true){//alert('success');
				   this_object.parent().remove();
				}
				else{
					alert('error');
				}
		   }
		});

		/*jQuery(this).parent().remove();
		return false;*/
	});




	 jQuery(".dateshow").datepicker({
		showOn: "both",
		buttonImage: "components/com_helloworld/assets/images/calendar.png",
		dateFormat: "yy-mm-dd",
		showAnim: "slideDown",
		changeMonth: true,
		changeYear: true,
		maxDate:null,
		minDate:0,
		buttonImageOnly: true,
		buttonText: "Select date"
	});





});



Joomla.submitbutton = function(task) {
	if (task == '')
	{
		return false;
	}
	else
	{
		var isValid=true;
		var action = task;
		if (action!= 'cancel' && action != 'close')
		{
			var form = document.adminForm;
			var regex = /^[a-zA-Z]+$/;

			if(form.name.value == "")	{
				isValid = false;
			}if(form.spublishdate.value == "")	{
				isValid = false;
			}
		}

		if (isValid)
		{
			Joomla.submitform(task,form);
			return true;
		}
		else
		{
			alert(Joomla.JText._('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE',
			                     'Required fields must not be empty'));
			return false;
		}
	}
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=items&layout=form&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate"enctype="multipart/form-data">
	<input id="jform_title" type="hidden" name="item-message-title"/>
	<div class="form-horizontal">
  	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details',
			empty($this->item->id) ? JText::_('COM_HELLOWORLD_TAB_NEW_MESSAGE') : JText::_('COM_HELLOWORLD_TAB_EDIT_MESSAGE')); ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_HELLOWORLD_LEGEND_DETAILS') ?></legend>
				<div class="row-fluid">
					<div class="span6">
						<table class="adminform table table-striped">
							<tr>
								<td><label class="inputbox"><?php echo JText::_('COM_HELLOWORLD_ITEM_NAME_LABEL');?></label></td>
								<td><input name="name" type="text" size="40" class="inputbox validate-name" value="<?php echo $this->item->name?>" required></td>
							</tr>
							<tr>
								<td><label class="inputbox"><?php echo JText::_('COM_HELLOWORLD_ITEM_DESC_LABEL');?></label></td>
								<td><input name="description" type="text" size="40" class="inputbox validate-description" value="<?php echo $this->item->description?>" required></td>
							</tr>
							<tr>
								<td><label class="inputbox"><?php echo JText::_('COM_HELLOWORLD_ITEM_FIELD_CATID_LABEL') ?></label></td>
								<td><Select name="catid" class="inputbox">
									<option value="0">Select Categories</option>
									<?php
										//var_dump();
										if($this->hwCategories){
											foreach($this->hwCategories as $hwCategory){
												if($hwCategory){
													if($hwCategory->id==$this->item->catid){

														echo "<option value='$hwCategory->id' selected='selected'>$hwCategory->title</option>";
													}else{
													//var_dump($category);
													echo "<option value='$hwCategory->id'>$hwCategory->title</option>";}
												}
											}
										}?>
								</Select></td>
							</tr>
							<tr>
								<td><label class="required"><?php echo JText::_('Start Publish'); ?> * </label></td>
								<td><?php echo JHtml::calendar($this->item->spublishdate, 'spublishdate', 'spublishdate', '%Y-%m-%d %H:%M:%S') ?></td>
							</tr>
							<tr>
								<td><label class="required dateshow"><?php echo JText::_('Finish Publish'); ?></label></td>
								<td><?php echo JHtml::calendar($this->item->epublishdate, 'epublishdate', 'epublishdate', '%Y-%m-%d %H:%M:%S') ?></td>
							</tr>
							<tr>
								<td> <?php echo JText::_('Published'); ?>:</td>
								<td colspan="2" class="r_button">
								<input type="radio" name="published" value="1" <?php if($this->item->published == '1') echo 'checked="checked"'; ?> />  <?php echo JText::_('Yes'); ?>
								<input type="radio" name="published" value="0" <?php if($this->item->published != '1') echo 'checked="checked"'; ?> />  <?php echo JText::_('No'); ?>
								</td>
							</tr>
							<tr>
								<td><label class="required"><?php echo JText::_('Access'); ?></label></td>
								<td><?php echo JHtml::_('access.assetgrouplist','access_id', $this->item->access_id,'',false) ?></td>
							</tr>
							<tr>
								<td><label class="required"><?php echo JText::_('Language'); ?></label></td>

								<td><select name="lang" >
								<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
								<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->item->lang);?></select></td>
							</tr>
							<tr>
								<td> <?php echo JText::_('Featured'); ?>:</td>
								<td colspan="2" class="r_button">
								<input type="radio" name="featured" value="1" <?php if($this->item->featured == '1') echo 'checked="checked"'; ?> />  <?php echo JText::_('Yes'); ?>
								<input type="radio" name="featured" value="0" <?php if($this->item->featured != '1') echo 'checked="checked"'; ?> />  <?php echo JText::_('No'); ?>
								</td>
							</tr>
							<tr>
							    <td><label class="required"><?php echo JText::_('Images'); ?></label></td>
								<td><div class="controls"><a href="javascript:void(0);" id="img" class="add_img" onclick="">+Add Images</a></div>
								<div id="app_img"></div>

								<?php $paths=json_decode($this->item->images,true);
								if($paths){
									foreach($paths as $i=>$path){?>
										<div class="controls up_clear">
											<a class="prev" href="<?php echo JURI::root();?>components/com_helloworld/assets/upload/item/images/<?php echo $path; ?>" target="_blank">
												<img src="<?php echo JURI::root();?>components/com_helloworld/assets/upload/item/images/<?php echo $path; ?>"/>
											</a>
											<a href="javascript:void(0);" class="rimg" id="<?php echo $path;?>" style="margin-left:20px">Remove</a>
										</div>
									<?php  }
								}
								//print_r($paths);
								//echo $this->item->images;
								?>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</fieldset>

		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'editor', JText::_('COM_HELLOWORLD_TAB_CONTENT')); ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_ITEM_LEGEND_CONTENT') ?></legend>
				<div class="row-fluid">
					<div name='editor' class='span6'>
						<?php
						   echo $editor->display('detail', $this->item->detail, '100', '50', '30', '10', false);?>
					</div>
				</div>
			</fieldset>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>


	<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('save')">
					<span class="icon-ok"></span><?php echo JText::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('cancel')">
					<span class="icon-cancel"></span><?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
	</div>
	<input name="id" type="hidden" value="<?php echo $this->item->id;?>">
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
	</div>
</form>
