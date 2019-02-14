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
class HelloWorldControllerCategories extends JControllerLegacy
{
	protected $default_view="categories";
	protected $view_list;
	protected $text_prefix;

	function __construct()
	{
		parent::__construct();
		JRequest::setVar( 'view', 'categories' );
		// Register Extra tasks
    $this->registerTask( 'unpublish', 'publish' );
		$this->registerTask('archive', 'publish');
		$this->registerTask('trash', 'publish');
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');

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
    JRequest::setVar( 'view', 'categories' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		$this->display();
	}

	public function deleteImg()
	{
		$return = new stdClass;
		$return->result = true;
		$app   = JFactory::getApplication();
		$model = $this->getModel('category');
		$table = $model->getTable();
		$data = JRequest::get('post'); //print_r($data);jexit('asfe');
		//var_dump($data);
		 $img=$data['img'];
		 $recordId=$data['id'];
		if(!$model->deleteImg($img,$recordId)){

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_REMOVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(
			    JRoute::_(
						'index.php?option=' . $this->option . '&view=categories&layout=form &id=' . $recordId, false
					)
			);
			$return->result=false;
			echo json_encode($return);jexit();
		}
		echo json_encode($return);
		jexit();
}


    /*Display method*/
    public function display($cachable = false, $urlparams = array())
	{
		$document = \JFactory::getDocument();
		$viewType = $document->getType();
		$viewName = $this->input->get('view', $this->default_view);
		$viewLayout = $this->input->get('layout', 'default', 'string');
		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
		// Get/Create the model
		if ($model = $this->getModel($viewName))
		{
            // Push the model into the view (as default)
			$view->setModel($model, true);
            if ($model = $this->getModel('category'))
            {
                // Push another model into the view
                $view->setModel($model);

            }
		}

		$view->document = $document;
		// Display the view
		if ($cachable && $viewType !== 'feed' && \JFactory::getConfig()->get('caching') >= 1)
		{
			$option = $this->input->get('option');
			if (is_array($urlparams))
			{
				$app = \JFactory::getApplication();
				if (!empty($app->registeredurlparams))
				{
					$registeredurlparams = $app->registeredurlparams;
				}
				else
				{
					$registeredurlparams = new \stdClass;
				}
				foreach ($urlparams as $key => $value)
				{
					// Add your safe URL parameters with variable type as value {@see \JFilterInput::clean()}.
					$registeredurlparams->$key = $value;
				}
				$app->registeredurlparams = $registeredurlparams;
			}
			try
			{
				/** @var \JCacheControllerView $cache */
				$cache = \JFactory::getCache($option, 'view');
				$cache->get($view, 'display');
			}
			catch (\JCacheException $exception)
			{
				$view->display();
			}
		}
		else
		{
			$view->display();
		}
		return $this;
	}



	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save($key = null,$urlVars = null)
	{	// Check for request forgeries.

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app   = JFactory::getApplication();
		$model = $this->getModel('category');
		$table = $model->getTable();
		$data  = JRequest::get('POST');

		//$checkin = property_exists($table, $table->getColumnAlias('checked_out'));
		$context = "$this->option.".$this->view_list;
		$task = $this->getTask();
		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}
		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}
		$recordId = $this->input->getInt($urlVar);
		$isNew=($recordId<1)?true:false;
		// Populate the row id from the session.
		$data[$key] = $recordId;
		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy')
		{
			// Reset the ID, the multilingual associations and then treat the request as for Apply.
			$data[$key] = 0;
			$isNew=true;
			//$data['associations'] = array();
			$task = 'apply';
			// add the 'created by' and 'created' date fields

		}
		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(
			    JRoute::_(
						'index.php?option=' . $this->option . '&view=categories&layout=form &id=' . $recordId, false
					)
			);
			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($data);
		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}
			// Save the data in the session.
			$app->setUserState($context . '.data', $data);
			// Redirect back to the edit screen.
			$this->setRedirect(
			    JRoute::_(
						'index.php?option=' . $this->option . '&view=categories&layout=form &id=' . $recordId, false
					)
			);
			return false;
		}

		if($isNew){
			// add the 'created by' and 'created' date fields
			$validData['created_user_id'] = JFactory::getUser()->get('id', 0);
			$validData['created_time'] = date('Y-m-d h:i:s');
		}else{
            $validData['modified_user_id'] = JFactory::getUser()->get('id', 0);
            $validData['modified_time'] = date('Y-m-d h:i:s');

        }
		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);
			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(
			    JRoute::_(
						'index.php?option=' . $this->option . '&view=categories&layout=form &id=' . $recordId, false
					)
			);
			return false;
		}


		$this->setMessage(JText::_($this->text_prefix.($recordId == 0 && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS'));
		// Redirect the user and adjust session state based on the chosen task.

		switch ($task)
		{
			case 'apply':
				// Set the record data in the session.
				$recordId = $model->getState('category.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
				//$model->checkout($recordId);
				// Redirect back to the edit screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=categories&layout=form &id=' . $recordId, false
					)
				);
				break;
			case 'save2new':
				//echo JRequest::getVar('layout');jexit();
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
				// Redirect back to the edit screen.
				$this->setRedirect(
				    JRoute::_(
						'index.php?option=' . $this->option . '&view=categories&layout=form', false
					)
				);
				break;
			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
				$url = 'index.php?option=' . $this->option . '&view=categories';
				// Check if there is a return value
				$return = $this->input->get('return', null, 'base64');
				if (!is_null($return) && JUri::isInternal(base64_decode($return)))
				{
					$url = base64_decode($return);
				}
				// Redirect to the list screen.
				$this->setRedirect(JRoute::_($url, false));
				break;
		}

		return true;

	}

	/**
	* Implement to allowAdd or not
	*
	* Not used at this time (but you can look at how other components use it....)
	* Overwrites: JControllerForm::allowAdd
	*
	* @param array $data
	* @return bool
	*/
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		return $user->authorise('core.create', $this->option) || count($user->getAuthorisedCategories($this->option, 'core.create'));
	}
	/**
	* Implement to allow edit or not
	* Overwrites: JControllerForm::allowEdit
	*
	* @param array $data
	* @param string $key
	* @return bool
	*/
	protected function allowEdit($data = array(), $key = 'id')
	{
		$id = isset( $data[ $key ] ) ? $data[ $key ] : 0;
		if( !empty( $id ) )
		{
			return JFactory::getUser()->authorise( "core.edit", $this->option . ".category." . $id );
		}
	}

	protected function allowSave($data, $key = 'id')
	{
		$recordId = isset($data[$key]) ? $data[$key] : '0';
		if ($recordId)
		{
			return $this->allowEdit($data, $key);
		}
		else
		{
			return $this->allowAdd($data);
		}
	}


  public function publish()
	{

		// Check for request forgeries
		\JSession::checkToken() or die(\JText::_('JINVALID_TOKEN'));
		// Get items to publish from the request.
		$cid = $this->input->get('cid', array(), 'array');
		$data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');
		if (empty($cid))
		{
			\JLog::add(\JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), \JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('category');
			// Make sure the item ids are integers
			$cid = ArrayHelper::toInteger($cid);
			// Publish the items.
			try
			{
				$model->publish($cid, $value);
				$errors = $model->getErrors();
				$ntext = null;
				if ($value === 1)
				{
					if ($errors)
					{
						\JFactory::getApplication()->enqueueMessage(\JText::plural($this->text_prefix . '_N_ITEMS_FAILED_PUBLISHING', count($cid)), 'error');
					}
					else
					{
						$ntext = $this->text_prefix .'_N_ITEMS_PUBLISHED';
					}
				}
				elseif ($value === 0)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value === 2)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
				}
				if ($ntext !== null)
				{
					$this->setMessage(\JText::plural($ntext, count($cid)));
				}
			}
			catch (\Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}
		$extension = $this->input->get('extension');
		$extensionURL = $extension ? '&extension=' . $extension : '';
		$this->setRedirect(\JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}


	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function delete()
	{
		// Check for request forgeries
		\JSession::checkToken() or die(\JText::_('JINVALID_TOKEN'));
		// Get items to remove from the request.
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid', array(), 'array');
		if (!is_array($cid) || count($cid) < 1)
		{
			\JLog::add(\JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), \JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('category');
			// Make sure the item ids are integers
			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(\JText::plural($this->text_prefix .'_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError(), 'error');
			}
			// Invoke the postDelete method to allow for the child class to access the model.
			//$this->postDeleteHook($model, $cid);
		}
		$this->setRedirect(\JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}

	function cancel()
	{
		$msg = JText::_( 'Operation Cancelled' );
		$this->setRedirect( 'index.php?option=com_helloworld&view=categories', $msg );

	}


}
