<?php
/*------------------------------------------------------------------------
# com_sales - SALES
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die(); 

class HelloWorldViewHome extends JViewLegacy
{
    
    function display($tpl = null)
    {
		//$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title( JText::_( 'COM_HELLOWORLD_HOME' ), 'Home' );
		JToolBarHelper::help('help', true);
		HelloWorldHelper::addSubmenu('home');
				
		parent::display($tpl);
        
    }

}
