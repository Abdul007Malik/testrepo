<?php
/*------------------------------------------------------------------------
# com_products - SALES
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

$linkI = JRoute::_('index.php?option=com_helloworld&view=categories');
$linkC = JRoute::_('index.php?option=com_helloworld&view=items');	
?>


<div id="homepanel">

<div class="adminform">
	<div class="cpanel-left">
		<div class="cpanel">
        	<div class="icon-wrapper">
                
                <div class="icon">
                    <a href="index.php?option=com_helloworld&view=categories"><img src="../media/com_helloworld/images/item-64x64.png" alt="<?php echo JText::_('Categories'); ?>" /><span><?php echo JText::_('Categories'); ?></span></a>
                </div>
				<div class="icon">
                    <a href="index.php?option=com_helloworld&view=items"><img src="../media/com_helloworld/images/category-64x64.png" alt="<?php echo JText::_('Items'); ?>" /><span><?php echo JText::_('Items'); ?></span></a>
                </div>
				
            </div>
		</div>
	</div>
	<div class="cpanel-right">
    
	<?php
        
        $title = JText::_( 'DES_HEAD_HELLOWORLD' );
		
		echo JHtml::_('tabs.start', 'panel-tabs');
		
		echo JHtml::_('tabs.panel', $title, 'cpanel-panel-hexdata');
		        
        echo JText::_('COM_HELLOWORLD_DESCRIPTION');
        
        echo JHtml::_('tabs.end');
		
    ?>
        
	</div>
	
<div class="clr"></div></div>