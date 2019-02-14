<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');


JHtml::_('formbehavior.chosen', 'select');

$listOrder     = $this->lists['order'];//$this->escape($this->state->get('list.ordering'));
$listDirn      = $this->lists['order_Dir'];//$this->escape($this->state->get('list.direction'));
?>
<form action="index.php?option=com_helloworld&view=categories" method="post" id="adminForm" name="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo JHtmlSidebar::render(); ?>
	</div>

	<div id="j-main-container" class="span10">
	<div class="row-fluid">
		<div class="span6">
			<div class="filter-select fltrt" style="float:right;">
				<?php echo JText::_('COM_HELLOWORLD_CATEGORIES_FILTER'); ?>

				<select name="publish_item" id="publish_item" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_HELLOWORLD_ALL_STATE');?></option>
					<option value="p" <?php  if( 'p'== $this->lists['publish_item']) echo 'selected="selected"'; ?>><?php echo JText::_('COM_HELLOWORLD_PUBLISHED');?></option>
			  	<option value="u" <?php  if('u'== $this->lists['publish_item']) echo 'selected="selected"'; ?>><?php echo JText::_('COM_HELLOWORLD_UNPUBLISHED');?></option>
						<option value="t" <?php  if( 't'== $this->lists['publish_item']) echo 'selected="selected"'; ?>><?php echo JText::_('COM_HELLOWORLD_TRASHED');?></option>
				  	<option value="a" <?php  if('a'== $this->lists['publish_item']) echo 'selected="selected"'; ?>><?php echo JText::_('COM_HELLOWORLD_ARCHIVED');?></option>
				</select>
				<select name="lang" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->lists['lang']);?></select>


				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?><br>
		</div>
		<div class="search_buttons">
      <div class="btn-wrapper input-append">
				<input placeholder="Search" type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i><span class="search_text"><?php echo JText::_('COM_HELLOWORLD_SEARCH'); ?></span></button>
				<button  class="btn" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('COM_HELLOWORLD_RESET'); ?></button>
			</div>
		</div>
	</div>
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th width="1%"><?php echo JText::_('COM_HELLOWORLD_NUM'); ?></th>
			<th width="2%">

			<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				<?php //echo JHtml::_('grid.checkall'); ?>
			</th>
			    <th width="20%">
						<?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_CATEGORY_TITLE', 'title', $listDirn, $listOrder); ?>
                    <?php //echo JHtml::_('grid.sort', 'COM_HELLOWORLD_CATEGORY_TITLE', 'title', $listDirn, $listOrder); ?>
                </th>
								<th width="10%">
                    <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_N_ITEMS', 'nitems', $listDirn, $listOrder); ?>
                </th>
                <th width="10%">
                    <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_AUTHOR', 'author', $listDirn, $listOrder); ?>
                </th>
								<th width="11%">
                    <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_LANGUAGE', 'lang', $listDirn, $listOrder); ?>
                </th>
                <th width="17%">
                    <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_CREATED_DATE', 'created', $listDirn, $listOrder); ?>
                </th>
               <th width="17%">
                    <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_MODIFIED_DATE', 'modified', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_PUBLISHED', 'published', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_ID', 'id', $listDirn, $listOrder); ?>
				</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php foreach ($this->items as $i => $row) :
					$link = JRoute::_('index.php?option=com_helloworld&view=categories&task=edit&id=' . $row->id); ?>
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
						<td>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_HELLOWORLD_EDIT_CATEGORY'); ?>">
							<?php echo $row->title; ?></a>
						</td>
						<td align="center">
							<?php echo $row->nitems; ?>
						</td>
						<td align="center">
              <?php echo $row->author; ?>
            </td>
						<td align="center">
              <?php echo $row->lang; ?>
            </td>
            <td align="center">
              <?php echo substr($row->created, 0, 10); ?>
            </td>
            <td align="center">
              <?php echo substr($row->modified, 0, 10); ?>
            </td>
						<td align="center">
							<?php echo JHtml::_('jgrid.published', $row->published, $i, '', true, 'cb'); ?>
						</td>
						<td align="center">
							<?php echo $row->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
