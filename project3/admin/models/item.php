<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 use Joomla\Registry\Registry;
 use Joomla\Utilities\ArrayHelper;
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class HelloWorldModelItem extends JModelLegacy
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Item', $prefix = 'HelloWorldTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 *
	public function getScript()
	{
		return 'administrator/components/com_helloworld/models/forms/helloworld.js';
	}*/



	/**
	 * Method to check if it's OK to delete a message. Overrides JModelAdmin::canDelete
	 */
	protected function canDelete($record)
	{
		if( !empty( $record->id ) )
		{
			return JFactory::getUser()->authorise( "core.delete", "com_helloworld.item." . $record->id );
		}
	}


	//To save the data in the table
	function save($data)
	{
		$table = $this->getTable();
		$app = JFactory::getApplication();
		$db		=  JFactory::getDBO();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : 0;

		if(empty($data['id']))
		{
			if(!JFactory::getUser()->authorise('core.create','com_helloworld'))
			{
			$this->setError(JText::_( 'NOT_AUTHORISED_TO_ADD' ));
		    return false;
			}
		}
		if(!empty($data['id']))
		{
			if(!JFactory::getUser()->authorise('core.edit','com_helloworld') && !JFactory::getUser()->authorise('core.edit.own','com_helloworld'))
			{
			$this->setError(JText::_( 'NOT_AUTHORISED_TO_EDIT' ));
			return false;
			}
		}
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);

			}
			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}

			if (!$table->check()) {
			$this->setError($this->_db->getErrorMsg());
			return 0;
			}
			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}
			// Clean the cache.
			$this->cleanCache();

		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
		if (isset($table->$key))
        {
             $this->setState($this->getName() . '.id', $table->$key);
        }

	 return true;
	}
	public function validate($data){
		$return=$data;
		if(!empty($data['name'])){
			$regex="/^[^0-9][a-zA-Z0-9_ ]+$/";
            $test = preg_match($regex,$data['name']);
			if(!$test || $test=='0'){

				$this->setError("Item name must not contains only numerals");
				return false;
			}
		}else{
			$this->setError("Item name must not be empty");
			return false;
		}
    if(empty($data['lang']) || !$data['lang']){
      $this->setError("Please Select language");
			return false;
    }
    if(empty($data['spublishdate']) || empty($data['epublishdate'])){
      $this->setError("start publish date and/or end publish date must not be empty");
			return false;
    }else if(!empty($data['spublishdate']) && !empty($data['epublishdate']) && $data['epublishdate']<=$data['spublishdate']){
      $this->setError("start publish date must be less than end publish date");
			return false;
    }
		if(empty($data['catid'])){
			$return['catid']=0;
		}
		if(!empty($data['params'])){
			if(isset($data['params']['show_category']) && ($data['params']['show_category']>1 || $data['params']['show_category']<0)){
				$return['params']['show_category']=0;
			}
		}

		return $return;
	}
	/*
	* method getcategories */
	public function getHWCategories(){
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id,title')
			  ->from('#__hwcategories');

		$db->setQuery($query);
		$result=$db->loadObjectList();
		return $result;
	}
    /**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  \JObject|boolean  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = JFactory::getApplication()->input->getInt($key);
		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);
			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}
		// Convert to the \JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = ArrayHelper::toObject($properties, '\JObject');
		if (property_exists($item, 'params'))
		{
			$registry = new Registry($item->params);
			$item->params = $registry->toArray();
		}
		return $item;
	}

/**
	 * Stock method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		// Load the parameters.
		$value = JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		return JFactory::getUser()->authorise('core.edit.state', $this->option);
	}

	public function delete(&$pks){
			$table = $this->getTable();
			// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				if ($this->canDelete($table))
				{
					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}
				}else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error)
					{
						JLog::add($error, JLog::WARNING, 'jerror');
						return false;
					}
					else
					{
						JLog::add(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
						return false;
					}
				}
			}

			else
			{
				$this->setError($table->getError());
				return false;
			}

		}
		// Clear the component's cache
		$this->cleanCache();
		return true;
	}


  /*
  *  Method to delete the Images*/
  public function deleteImg($img,$id){

    if(!isset($img)){
      $this->setError('Unable to remove the image');
      return false;
    }

    if(!isset($id)){
      $this->setError('Unable to remove the image');
      return false;
    }
		$table = $this->getTable();

			// Iterate the items to delete each one.
    if ($table->load($id))
		{
				if ($this->canDelete($table))
				{
					if (!$table->deleteImg($img))
					{
						$this->setError($table->getError());
						return false;
					}
				}else
				{
					$error = $this->getError();
					if ($error)
					{
						JLog::add($error, JLog::WARNING, 'jerror');
						return false;
					}
					else
					{
						JLog::add(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
						return false;
					}
				}
			}else
			{
				$this->setError($table->getError());
				return false;
			}


		// Clear the component's cache
		$this->cleanCache();
		return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function publish(&$pks, $value = 1)
	{

		$user = JFactory::getUser();
		$table = $this->getTable();
		$pks = (array) $pks;
		// Include the plugins for the change of state event.
		JPluginHelper::importPlugin($this->events_map['change_state']);
		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();
			if ($table->load($pk))
			{
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
					return false;
				}
				// If the table is checked out by another user, drop it and report to the user trying to change its state.
				if (property_exists($table, 'checked_out') && $table->checked_out && ($table->checked_out != $user->id))
				{
					JLog::add(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'), JLog::WARNING, 'jerror');
					// Prune items that you can't change.
					unset($pks[$i]);
					return false;
				}
			}
		}
		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id')))
		{
			$this->setError($table->getError());
			return false;
		}

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}
		// Clear the component's cache
		$this->cleanCache();
		return true;
	}
}
