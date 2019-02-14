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

JFormHelper::loadFieldClass('list');

/**
 * HelloWorld Form Field class for the HelloWorld component
 *
 * @since  0.0.1
 */
class JFormFieldItem extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var         string
	 */
	protected $type = 'item';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array  An array of JHtml options.
	 */
	protected function getOptions()
	{
		$db    = JFactory::getDBO();
		//get the current query object by getQuery(new=false)
		$query = $db->getQuery(true);
		$query->select('#__hwitems.id as id,name,#__hwcategories.title as title,catid');
		$query->from('#__hwitems');
		$query->leftJoin('#__hwcategories on catid=#__hwcategories.id');
		// Retrieve only published items
		$query->where('#__hwitems.published = 1');
		$db->setQuery((string) $query);
		/*Method to get an array of the resultset rows from the database query where each row is an object. The array of objects can optionally be keyed by a field name, but defaults to a sequential numeric array.

		loadObjectList(string $key = '', string $class = 'stdClass') : mixed*/

		$messages = $db->loadObjectList();
		$options  = array();

		if ($messages)
		{
			foreach ($messages as $message)
			{
				$options[] = JHtml::_('select.option', $message->id, $message->name .($message->catid ? ' (' . $message->title . ')' : ''));
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
