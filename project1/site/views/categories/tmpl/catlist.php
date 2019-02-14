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
<form action="index.php?option=com_helloworld&view=categories&layout=catlist" method="post" id="adminForm" name="adminForm">

  <div class="row-fluid">
    <div class="span6">
      <?php
				echo JLayoutHelper::render(
					'joomla.searchtools.default',
					array('view' => $this)
				);
			?>
    </div>
  </div>

  <div class="catlist">
    <?php foreach ($this->items as $i=>$item) {
      $link = JRoute::_('index.php?option=com_helloworld&view=items&layout=itemlist&catid=' . $item->id);?>
    <div class="elementbox">
      <legend><a href="<?php echo $link; ?>">
      <?php echo $item->title; ?></a></legend>
        <?php /*//echo $this->pagination->getRowOffset($i); ?>
        <div class="imagebox"><?php $paths=json_decode($item->images,true);
          if($paths){
            foreach($paths as $i=>$path){?>
                  <img src="<?php echo JURI::root();?>components/com_helloworld/assets/upload/item/images/<?php echo $path; ?>"/>
            <?php  }
          }
          //print_r($paths);
          //echo $this->item->images;
          */?>
        <!--/div-->
        <div class="detailsbox">
          <label>Author : <?php echo $item->author ?></label>
        </div>
      </div>
    <?php } ?>
</div>
  <?php echo $this->pagination->getListFooter(); ?>

  <input type="hidden" name="boxchecked" value="0"/>
  <?php echo JHtml::_('form.token'); ?>
</form>
