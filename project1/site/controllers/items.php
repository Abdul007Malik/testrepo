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
use Joomla\Utilities\ArrayHelper;
/**
 * HelloWorlds Controller
 *
 * @since  0.0.1
 */
class HelloWorldControllerItems extends JControllerLegacy
{
	protected $default_view="items";

	function __construct()
	{
		parent::__construct();
		JRequest::setVar( 'view', 'items' );
		// Register Extra tasks
		$this->registerTask('add','edit');
		$this->registerTask('apply', 'save');
		//$this->registerTask('save2new', 'save');
		//$this->registerTask('save2copy', 'save');

		if (empty($this->option)){
			$this->option = 'com_' . strtolower($this->getName());
		}
		if (empty($this->text_prefix))
		{
			$this->text_prefix = strtoupper($this->option);
		}
				// Guess the list view as the suffix, eg: OptionControllerSuffix.
		if (empty($this->view_list))
		{
			$r = null;
			if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r))
			{
				throw new \Exception(\JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
			}
			$this->view_list = strtolower($r[2]);
		}

	}

	/**
 * display the edit form
 * @return void
 */
	function edit()
	{
				JRequest::setVar( 'view', 'items' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		$this->display();
	}
	public function getModel($name = 'Items', $prefix = 'HelloworldModel', $config = array())
  {
		parent::getModel($name,$prefix,$config);
	}
}
