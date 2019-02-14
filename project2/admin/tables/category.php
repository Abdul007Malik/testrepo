<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
require(JPATH_ROOT.'/libraries/joomla/filesystem/file.php');
/**
 * Hello Table class
 *
 * @since  0.0.1
 */
class HelloWorldTableCategory extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__hwcategories', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @param       array           named array
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	2.5
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_helloworld.category.'.(int) $this->$k;
	}
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	2.5
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}
	/**
	 * Method to get the asset-parent-id of the item
	 *
	 * @return	int
	 */
	protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance('Asset');
		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();
		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId=$assetParent->id;
		}
		return $assetParentId;
	}

	function store($updateNulls = false)
	{
		//store multiple images

		$input=JFactory::getApplication()->input;
		$files=$input->files->get('images');
		//echo "<pre>".$files;var_dump($files);echo $this->id;jexit();
		if($files){
			$images=array();
			$nm='';//
			$tw= 100;
			$twlr= 200;
			$tp= 'thumbs/';
			$tplr= 'lowRes/';

			foreach($files as $i=>$file){
				$tmpFilePath=$file['tmp_name'];

				if($nm==$file['name']){continue;}//
				$nm=$file['name'];//

				$name=time().'_'.JFile::makeSafe($file['name']);
				if($tmpFilePath!=""){
					$newFilePath=JPATH_COMPONENT_SITE."/assets/upload/category/images/".$name;
					$flag=JFile::upload($tmpFilePath, $newFilePath);
					if(!$this->createThumbnail($name, $tw, $tp)){
						$this->setError('Unable to create the thumbnails');
						return false;
					}
					if(!$this->createThumbnail($name, $twlr, $tplr)){

						$this->setError('Unable to create the thumbnails');
						return false;
					}
				}
				$images[$i]=$name;
			}
			$pre=$this->images;
			$pre=json_decode($pre,true);
			if(count($pre)>=7 || (count($pre)+count($images)>7)){
				$this->setError('Sorry Unable to add images more than 7');
				return false;
			}else{
				foreach($pre as $p){
					if(!in_array($p,$images)){
						array_push($images,$p);
					}
				}

				$images=json_encode($images);
				$this->images=$images;
			}
		}



		if(!parent::store($updateNulls))	{
			return false;
		}

		return true;

	}

	public function createThumbnail($filename, $iw, $tp) {

		$final_width_of_image = $iw;
		$path_to_image_directory = JPATH_COMPONENT_SITE."/assets/upload/category/images/";
		$path_to_thumbs_directory = JPATH_COMPONENT_SITE."/assets/upload/category/images/".$tp;

		if(preg_match('/[.](jpg)$/', $filename)) {
			$im = imagecreatefromjpeg($path_to_image_directory . $filename);
		} else if (preg_match('/[.](gif)$/', $filename)) {
			$im = imagecreatefromgif($path_to_image_directory . $filename);
		} else if (preg_match('/[.](png)$/', $filename)) {
			$im = imagecreatefrompng($path_to_image_directory . $filename);
		}

		$ox = imagesx($im);
		$oy = imagesy($im);

		$nx = $final_width_of_image;
		$ny = floor($oy * ($final_width_of_image / $ox));
		$nm = imagecreatetruecolor($nx, $ny);
		imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
		if(!file_exists($path_to_thumbs_directory)) {
		  if(!mkdir($path_to_thumbs_directory)) {
			   return false;
		  }
		}
		//$filename = time() . '_' . $filename;
		imagejpeg($nm, $path_to_thumbs_directory . $filename);
		return true;
	}


	/*
	* Method  to delete the Image in the row*/

	public function deleteImg($img){

			$pre=$this->images;
			$pre=json_decode($pre,true);
		  $key=array_search($img,$pre);
			if($pre[$key]==$img){
				$image=JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR."/assets/upload/category/images/";
				JFile::delete($image.$pre[$key]);
				//$thumbs=JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR."/assets/upload/category/images/thumbs/";
				JFile::delete($image.'thumbs/'.$pre[$key]);
				//$lowRes=JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR."/assets/upload/category/images/lowRes/";
				JFile::delete($image.'lowRes/'.$pre[$key]);
				unset($pre[$key]);
			}
			if(count($pre)){
				$this->images=json_encode($pre);
			}else{
				$this->images='';
			}
			if(!parent::store())	{
				return false;
			}

			return true;
 }
}
