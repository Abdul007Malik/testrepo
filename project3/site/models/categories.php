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

/**
 * HelloWorldList Model
 *
 * @since  0.0.1
 */
class HelloWorldModelCategories extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'title',
				'published',
				'author',
				'modified',
				'created'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('c.id as id, c.title as title')
			  ->from($db->quoteName('#__hwcategories', 'c'));

		 // Join with users table to get the username of the author
		$query->select($db->quoteName('u.username', 'author'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = c.created_user_id');

		// Filter: like / search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('c.title LIKE ' . $like);
		}

		//check user authorization
		$user = JFactory::getUser();
		$userId = $user->get( 'id' );
		$levels = JAccess::getAuthorisedViewLevels($user->id);
		$levels_list =  '(' . implode(',', $levels) . ')';
		//$groups = JAccess::getGroupsByUser($user->id);

		//echo "<br>";print_r($groups);
		//jexit();
		if(is_array($levels)){
			$query->where("c.access_id IN $levels_list");
		}else {
			$query->where("c.access_id IN (1,5)");
		}
		//language filter
		$lang = JFactory::getLanguage();
		$langTag = ($lang->getTag())?$db->quote($lang->getTag()):$db->quote('en-GB');
		if(!empty($userId)){
			$query->where("(c.lang=".$langTag." OR c.lang=".$db->quote('*').')' );
		}


		$query->where('c.published = 1') ;
		$today = $db->quote(date("Y-m-d H:i:s"));
		$query->where("c.spublishdate<=$today AND c.epublishdate>$today");


		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'title');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		return $query;
	}
}
