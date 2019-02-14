<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if(!class_exists('JToolBarHelper')) {
   require_once JPATH_ADMINISTRATOR . '/includes/toolbar.php';
}
/**
 * Items View
 *
 * @since  0.0.1
 */
class HelloWorldViewItems extends JViewLegacy
{

   protected $items;
   protected $categories;

   /**
  * Display the Hello World view
  *
  * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
  *
  * @return  void
  */
  function display($tpl = null)
  {   // Get application
      $app = JFactory::getApplication();
      //$layout = JRequest::getWord('layout', 'itemlist');

      $context = "com_helloworld.site.items";
      $this->config = $this->get('Config');
      $layout = JRequest::getWord('layout', 'itemlist');
      if($layout=='form'){

        $this->item			= $this->get('Item');

               // What Access Permissions does this user have? What can (s)he do?
        $this->canDo = JHelperContent::getActions('com_helloworld');

              // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
          throw new Exception(implode("\n", $errors), 500);
        }
        $this->hwCategories = $this->get('hwCategories','item');

      }else{
        $all = $app->input->get('all', null, 'int');
        if(isset($all) && $all=='1'){
          $app->setUserState( $context.'catid', null);
		    }

        $catid = $app->getUserStateFromRequest( $context.'catid', 'catid', '',	'int' );
        // Get data from the model
        $this->items			= $this->get('Items');
        $this->pagination		= $this->get('Pagination');
        $this->state			= $this->get('State');
        $this->categories = $this->get('Categories');
        //$this->filter_order 	= $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'greeting', 'cmd');
        //$this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
        $this->filterForm    	= $this->get('FilterForm');
        $this->activeFilters 	= $this->get('ActiveFilters');
        // What Access Permissions does this user have? What can (s)he do?
        $this->canDo = JHelperContent::getActions('com_helloworld');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
           throw new Exception(implode("\n", $errors), 500);
        }
        $this->lists['catid']=$catid;
      }

      // Display the template
  		parent::display($tpl);

  		// Set the document
  		//$this->setDocument($layout);
  }


}
