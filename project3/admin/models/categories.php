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
				'created',
				'nitems',
				'lang'
			);
		}
		parent::__construct($config);
		$context	= 'com_helloworld.category.list.';
        // Get pagination request variables
		$mainframe = JFactory::getApplication();
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		$filter_language		= $mainframe->getUserStateFromRequest( $context.'filter_language',	'filter_language',	'' );
		//$akey			= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		//$akey			= JString::strtolower( $this->_akey );
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);


        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);

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

		$query->select('c.id as id, c.title as title, c.published as published, c.lang, c.created_time as created, c.modified_time as modified')
			  ->from($db->quoteName('#__hwcategories', 'c'));

		 // Join with users table to get the username of the author
		$query->select($db->quoteName('u.username', 'author'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = c.created_user_id');
		$query->select('count(i.id) as nitems')
			->join('LEFT', $db->quoteName('#__hwitems', 'i') . ' ON i.catid = c.id');


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
			$query->where('c.title LIKE ' . $like);
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('c.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(c.published IN (0, 1))');
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'title');
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

				$context	= 'com_helloworld.category.list.';

				$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
				$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );

		 		$groupby = ' group by c.id';
				$orderby = $groupby . ' order by '.$filter_order.' '.$filter_order_Dir . ' ';

				return $orderby;
			}

	function _buildContentFilter()
			{

				$mainframe =JFactory::getApplication();
				$db =JFactory::getDBO();
				$context	= 'com_helloworld.category.list.';
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
					$where[] = 'c.published= 1';
					else if($publish_item =='u')
					$where[] = 'c.published = 0';
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
					$where[] = 'c.lang ='.$db->quote($lang);
				}

				if (!empty($search))
				{
					$like = $db->quote('%' . $search . '%');
					$where[] = 'c.title LIKE ' . $like;
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
			$query .= ' and c.access_id IN (' . $levels . ')';
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
