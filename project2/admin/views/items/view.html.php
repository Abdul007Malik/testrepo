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
if(!class_exists('JToolbar')) {
   require_once JPATH_SITE . '/libraries/cms/toolbar/toolbar.php';
}
if(!class_exists('JToolbarHelper')) {
   require_once JPATH_SITE . '/libraries/cms/toolbar/helper.php';
}
/**
 * ITEM View
 *
 * @since  0.0.1
 */
class HelloWorldViewItems extends JViewLegacy
{
   protected $hwCategories;
   protected $items;
   protected $item;
    /**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Get application
    $app = JFactory::getApplication();
    $context = "com_helloworld.item.list";
    $search = $app->getUserStateFromRequest( $context.'search', 'search', '',	'string' );
    $lang = $app->getUserStateFromRequest( $context.'lang', 'lang', '',	'string' );
		$search = JString::strtolower( $search );
	  $publish_item= $app->getUserStateFromRequest( $context.'publish_item', 'publish_item',	'',	'string' );
		$filter_order     = $app->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
    $filter_order_Dir = $app->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );

		$this->config = $this->get('Config');
    $layout = JRequest::getWord('layout', 'default');
        if($layout=='form'){

            $this->item			= $this->get('Item','item');

             // What Access Permissions does this user have? What can (s)he do?
            $this->canDo = JHelperContent::getActions('com_helloworld');

            // Check for errors.
            if (count($errors = $this->get('Errors')))
            {
                throw new Exception(implode("\n", $errors), 500);
            }
            $this->hwCategories = $this->get('hwCategories','item');

        }else{

            // Get data from the model
            $this->items			= $this->get('Items');
            $this->pagination		= $this->get('Pagination');
            $this->state			= $this->get('State');
            //$this->filter_order 	= $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'greeting', 'cmd');
            //$this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
            $this->lists['search']= $search;
            $this->lists['lang']= $lang;
     				$this->lists['publish_item']= $publish_item;
            $this->lists['order_Dir'] = $filter_order_Dir;
    				$this->lists['order']     = $filter_order;
          // What Access Permissions does this user have? What can (s)he do?
            $this->canDo = JHelperContent::getActions('com_helloworld');

            // Check for errors.
            if (count($errors = $this->get('Errors')))
            {
                throw new Exception(implode("\n", $errors), 500);
            }

		}

        // Set the submenu
		HelloWorldHelper::addSubmenu('items');
		// Set the toolbar and number of found items
		$this->addToolBar($layout);

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument($layout);
	}
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar($layout = "default")
    {
        if($layout=='form'){

            $input = JFactory::getApplication()->input;

            // Hide Joomla Administrator Main menu
            $input->set('hidemainmenu', true);

            $isNew = ($this->item->id == 0);
			      $this->hwCategories = $this->get('HWCategories','item');
            if ($isNew)
            {
                // For new records, check the create permission.
                if ($this->canDo->get('core.create'))
                {
                    JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
                    JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
                    JToolBarHelper::custom('save2new', 'save-new.png', 'save-new_f2.png',
                                           'JTOOLBAR_SAVE_AND_NEW', false);
                }
                JToolBarHelper::cancel('cancel', 'JTOOLBAR_CANCEL');

			}
			else
			{
				if ($this->canDo->get('core.edit'))
				{
					// We can save the new record
					JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('save', 'JTOOLBAR_SAVE');

					// We can save this record, but check the create permission to see
					// if we can return to make a new one.
					if ($this->canDo->get('core.create'))
					{
						JToolBarHelper::custom('save2new', 'save-new.png', 'save-new_f2.png','JTOOLBAR_SAVE_AND_NEW', false);
					}
				}
				if ($this->canDo->get('core.create'))
				{
					JToolBarHelper::custom('save2copy', 'save-copy.png', 'save-copy_f2.png',
										   'JTOOLBAR_SAVE_AS_COPY', false);
				}
				JToolBarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
			}
        }else{
            $title = JText::_('COM_HELLOWORLD_MANAGER_ITEMS');

            if ($this->pagination->total)
            {
                $title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
            }

            JToolBarHelper::title($title, 'Item');
            if ($this->canDo->get('core.create'))
            {
                JToolBarHelper::addNew('add', 'JTOOLBAR_NEW');
            }
            if ($this->canDo->get('core.edit'))
            {
                JToolBarHelper::editList('edit', 'JTOOLBAR_EDIT');
                JToolBarHelper::unpublish('unpublish', 'JTOOLBAR_UNPUBLISH');
                JToolBarHelper::archiveList('archive', 'JTOOLBAR_ARCHIVE');
                JToolBarHelper::trash('trash', 'JTOOLBAR_TRASH');
            }
            if ($this->canDo->get('core.delete'))
            {
                JToolBarHelper::deleteList('', 'delete', 'JTOOLBAR_DELETE');
            }
            if ($this->canDo->get('core.admin'))
            {
                JToolBarHelper::divider();
                JToolBarHelper::preferences('com_helloworld');
            }
        }
    }
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument($layout='default')
	{
        if($layout=='form'){
			$document = JFactory::getDocument();
			$isNew = ($this->item->id < 1);

            $document->setTitle($isNew ? JText::_('COM_HELLOWORLD_ITEM_CREATING') :
                                JText::_('COM_HELLOWORLD_ITEM_EDITING'));
			$this->script=$this->get('Script','item');
            $document->addScript(JURI::root() . $this->script);
            $document->addScript(JURI::root() . "/administrator/components/com_helloworld"
                                 . "/views/items/submitbutton.js");
            JText::script('COM_HELLOWORLD_ITEM_ERROR_UNACCEPTABLE');

        }else{
            $document = JFactory::getDocument();
            $document->setTitle(JText::_('COM_HELLOWORLD_ADMINISTRATION'));
        }
	}
}
