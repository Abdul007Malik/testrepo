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
class HelloWorldModelItems extends JModelList
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
				'name',
				'description',
				'published',
				'author',
				'modified',
				'created',
				'lang'
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

		$query->select('i.id as id, i.name as name,i.description as description, i.lang, i.published as published, i.created_time as created, i.modified_time as modified')
			  ->from($db->quoteName('#__hwitems', 'i'));

		// Join over the categories.
		$query->select($db->quoteName('c.title', 'category_title'))
			->join('LEFT', $db->quoteName('#__hwcategories', 'c') . ' ON c.id = i.catid');

		 // Join with users table to get the username of the author
		$query->select($db->quoteName('u.username', 'author'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = i.created_user_id');
			/*
		// Filter: like / search
		$search = $this->getState('filter.search');
		$lang = $this->getState('filter.lang');

		if(!empty($lang)){
			$query->where('c.lang ='.$db->quote($lang));
		}

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('i.name LIKE ' . $like);
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('i.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(i.published IN (0, 1))');
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'name');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
*/

		return $query;
	}

	function getItems()
	{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_data ))
			{
				 $query = $this->_buildQuery();

				 $filter = $this->_buildContentFilter();
				 $orderby = $this->_buildItemOrderBy();

				 $query .= $filter;
				 $query .= $orderby;
				 //$this->_data = $this->_getList( $query );
				 $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			}

			return $this->_data;
	}

	function getTotal()
		{

			 if (empty($this->_total)) {
				$query = $this->_buildQuery();
				$query .= $this->_buildContentFilter();
				$query  .= $this->_buildItemOrderBy();
				$this->_total = $this->_getListCount($query);

				}
			   return $this->_total;
		}

	function _buildItemOrderBy()
			{
				$mainframe = JFactory::getApplication();

				$context	= 'com_helloworld.item.list.';

				$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
				$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );

		 		$orderby = ' order by '.$filter_order.' '.$filter_order_Dir . ' ';

				return $orderby;
			}

	function _buildContentFilter()
			{

				$mainframe =JFactory::getApplication();
				$db =JFactory::getDBO();
				$context	= 'com_helloworld.item.list.';
				$search		= $mainframe->getUserStateFromRequest( $context.'search', 'search',	'',	'string' );
				$lang = $mainframe->getUserStateFromRequest( $context.'lang', 'lang',	'',	'string' );
				$publish_item		= $mainframe->getUserStateFromRequest( $context.'publish_item', 'publish_item',	'',	'string' );
				$search		= JString::strtolower( $search );

				$where = array();

				//check user authorization
				$user = JFactory::getUser();
				$userId = $user->get( 'id' );
				$levels = JAccess::getAuthorisedViewLevels($user->id);
				$levels_list =  '(' . implode(',', $levels) . ')';
				//$groups = JAccess::getGroupsByUser($user->id);

				//echo "<br>";print_r($groups);
				//jexit();
				if(is_array($levels)){
					$where[] ="c.access_id IN $levels_list";
				}else {
					$where[] =  "c.access_id IN (1,5)";
				}
				
				if($publish_item)
				{

					if ( $publish_item == 'p' )
					$where[] = 'i.published= 1';

					else if($publish_item =='u')
					$where[] = 'i.published = 0';
					else if($publish_item =='a')
					$where[] = 'c.published = 2';
					else if($publish_item =='t')
					$where[] = 'c.published = -2';
					else{
						$where[] = '(c.published = 1 OR c.published = 0)';
					}
				}else{
					$where[] = '(c.published = 1 OR c.published = 0)';
				}

				if(!empty($lang)){
					$where[] = 'i.lang ='.$db->quote($lang);
				}

				if (!empty($search))
				{
					$like = $db->quote('%' . $search . '%');
					$where[] = 'i.name LIKE ' . $like;
				}

					//$where[] = 'c.id !=1';

					$filter = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';

				return $filter;
			}


	function _buildQuery()
 	{
		$user = JFactory::getUser();

 		 $query=$this->getListQuery();
		 if (!$user->authorise('core.admin','com_helloworld')){
			$levels = implode(',', $user->getAuthorisedViewLevels());
			$query .= ' and i.access_id IN (' . $levels . ')';
		}

		 return $query;
	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
}
