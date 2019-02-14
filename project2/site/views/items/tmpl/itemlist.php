<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * This layout file is for displaying the front end form for capturing a new helloworld message
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidator');
?>
<form action="index.php?option=com_helloworld&view=items&layout=itemlist" method="post" id="adminForm" name="adminForm">
  <div class="row-fluid">
    <div class="span6">
      <?php
				echo JLayoutHelper::render(
					'joomla.searchtools.default',
					array('view' => $this)
				);
			//echo $this->lists['catid']?>
    </div>
  </div>
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
  <label>Create New Item <input name="newIB" type="button" value="New Item"
    onclick="location.href='index.php?option=com_helloworld&view=items&layout=form';"/></label>
  <Select name="catid" class="inputbox" onchange="this.form.submit()">
    <option value="0">Select Categories</option>
    <?php
      //var_dump($this->categories);jexit();
      if($this->categories){
        foreach($this->categories as $category){
          if($category){
            if($category->id==$this->lists['catid']){

              echo "<option value='$category->id' selected='selected'>$category->title</option>";
            }else{
            //var_dump($category);
            echo "<option value='$category->id'>$category->title</option>";
          }
            }
        }
      }?>
  </Select>
  <div class="itemlist">

    <?php foreach ($this->items as $i=>$item) {?>
      <div class="elementbox">
        <legend><?php echo $item->name; ?></legend>
        <?php //echo $this->pagination->getRowOffset($i); ?>
        <div class="imagebox"><?php $paths=json_decode($item->images,true);
          if($paths){
            foreach($paths as $i=>$path){?>
                  <img src="<?php echo JURI::root();?>components/com_helloworld/assets/upload/item/images/<?php echo $path; ?>"/>
            <?php  }
          }
          //print_r($paths);
          //echo $this->item->images;
          ?>
        </div>
        <div class="detailsbox">
          <label>Description : <?php echo $item->description?></label>
          <label>Details : <?php echo trim($item->detail)?></label>
          <label>Author : <?php echo $item->author ?></label>
          <div class="imagebox">
          <?php if($paths){
            $paths=json_decode($item->images,true);
            foreach($paths as $path)?>
            <img src="<?php echo JURI::root();?>components/com_helloworld/assets/upload/category/images/<?php echo $path; ?>"/>
          <?php } ?>
        </div>
        </div>
      </div>
    <?php }?>

  </div>

  <?php echo $this->pagination->getListFooter(); ?>

  <input type="hidden" name="boxchecked" value="0"/>
  <?php echo JHtml::_('form.token'); ?>
</form>
