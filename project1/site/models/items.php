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
				'title',
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
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('i.id as id, i.name as name, i.detail, i.images, i.description , i.spublishdate, i.created_time as created, i.modified_time as modified')
			  ->from($db->quoteName('#__hwitems', 'i'));

		// Join over the categories.
		$query->select($db->quoteName('c.title', 'category_title'))
			->join('LEFT', $db->quoteName('#__hwcategories', 'c') . ' ON c.id = i.catid');

		 // Join with users table to get the username of the author
		$query->select($db->quoteName('u.username', 'author'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = i.created_user_id');

		// Filter: like / search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('i.name LIKE ' . $like);
		}

		$app =JFactory::getApplication();

		$input = $app->input;
		$all = $input->get('all', null, 'int');
		if(isset($all) && $all=='1'){
			$app->setUserState( "$this->option.catid", null);
		}else{
			$catid = $input->get('catid', null, 'int');
		}
		if(!isset($catid)){
				//this state is null or 0 if it not come from categories view
				//so in
				$catid=$app->getUserState("$this->option.catid",0);
				if(isset($catid) && $catid!=0){
					$query->where("i.catid=$catid");
					$app->setUserState( "$this->option.catid", "$catid" );
				}
		}else{
				if($catid!=0){
				$query->where("i.catid=$catid");
				$app->setUserState( "$this->option.catid", "$catid" );
			}
		}

		//check user authorization
		$user = JFactory::getUser();
		$userId = $user->get( 'id' );
		$levels = JAccess::getAuthorisedViewLevels($user->id);
		$levels_list =  '(' . implode(',', $levels) . ')';

		if(is_array($levels)){
			$query->where("i.access_id IN $levels_list");
		}else {
			$query->where("i.access_id IN (1,5)");
		}

		$query->where('i.published = 1') ;

		//language filter
		$lang = JFactory::getLanguage();
		$langTag = ($lang->getTag())?$db->quote($lang->getTag()):$db->quote('en-GB');
		if(!empty($userId)){
			$query->where("(i.lang=".$langTag."  OR c.lang=".$db->quote('*').')' );
		}

		$query->where('i.published = 1') ;

		$today = $db->quote(date("Y-m-d H:i:s"));
		$query->where("i.spublishdate<=$today AND i.epublishdate>$today");
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'title');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));


		return $query;
	}
	public function getCategories(){

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('c.id as id, c.title as title')
				->from($db->quoteName('#__hwcategories', 'c'));

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
					$query->where("c.lang=".$langTag." OR c.lang=".$db->quote('*') );
				}


				$query->where('c.published = 1') ;

				$today = $db->quote(date("Y-m-d H:i:s"));
				$query->where("c.spublishdate<=$today AND c.epublishdate>$today");

		$db->setQuery($query);
		$results = $db->loadObjectList();
		return $results;
	}
}
