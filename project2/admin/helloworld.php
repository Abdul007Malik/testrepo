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

//import the assets css, js
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'administrator/components/com_helloworld/assets/css/adminstyle.css');
$document->addStyleDeclaration('.icon-helloworld {background-image: 	url(../media/com_helloworld/images/Tux-16x16.png);}');

$document->addStyleSheet('http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css');
$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
$document->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js');

$document->addScriptDeclaration('var $hd = jQuery.noConflict();');




// Require helper file
JLoader::register('HelloWorldHelper', JPATH_COMPONENT . '/helpers/helloworld.php');
// Create the controller

// Require specific controller if requested

require_once( JPATH_ADMINISTRATOR.'/components/com_helloworld/controller.php' );
$controller = JRequest::getWord('view', '');
if($controller) {
    $path = JPATH_ADMINISTRATOR.'/components/com_helloworld/controllers/'.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

$classname    = 'HelloWorldController'.$controller;
$controller   = new $classname( );
// Access check: is this user allowed to access the backend of this component?
if (!JFactory::getUser()->authorise('core.manage', 'com_helloworld')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();