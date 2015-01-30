<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');
/**	
 * NitroCMS Open eCommerce Platform
 *
 * @author Sal Bordonaro (2013-2015)
 * @author Salvatore McDonald (2014-2015)
 *
 * @package NitroCMS\Core\
 *
 *
 * @copyright  Copyright (c) 2013-2015
 * @copyright  Inspired Technology Australia
 * @copyright  Nitrocart.net
 * @copyright  Salvatore McDonald
 * @copyright  2015 NitroCart Dev Team
 *
 * @contribs PyroCMS Dev Team, PyroCMS Community, NitroCMS Community
 *
 */
class Categories_m extends MY_Model
{

    public $_table = 'nct_categories';


	public function __construct()
	{
		parent::__construct();

	}

	public function get_all_parents()
	{

		$categories = $this->order_by('order', 'asc')->where('hidden',0)->where('parent_id',0)->get_all();

		foreach($categories as $category)
		{
			$category->children_count = $this->count_children($category->id);
		}

		return $categories;
	}


	public function get_children($id)
	{
		return $this->order_by('order', 'asc')->where('parent_id', $id)->get_all();
	}

	public function count_children($parent_id)
	{
		return $this->where('hidden',0)->count_by('parent_id', $parent_id);
	}



	/**
	 * Traditional tree approach
	 * @param  integer $parent_id    [description]
	 * @param  array   $return_array [description]
	 * @return [type]                [description]
	 */
	public function get_tree( $parent_id = 0 , $currentCategories=array()  )
	{

		// selecting the parents
		$children = $this->where('hidden',0)->where('parent_id',$parent_id)->get_all();

		$caretgory_list_array = array();

		foreach($children as $child)
		{
			$caretgory_list_array[$child->id] = array();
			$caretgory_list_array[$child->id]['category'] = $child;
			$caretgory_list_array[$child->id]['current'] = FALSE;
			$caretgory_list_array[$child->id]['children'] = $this->get_tree($child->id,$currentCategories);
		}

		return $caretgory_list_array;
	}



	//return the tree from the parents id
	//this works by flattening the categories tree.
	//It does not simply just get all categories, it gets all categories by  aparent, then flatens
	public function get_tree2( $parent_id = 0 , $return_array = array() , $prefix = '' )
	{

		// selecting the parents
		$children = $this->where('hidden',0)->where('parent_id',$parent_id)->get_all();

		foreach($children as $child)
		{
			$return_array[$child->id] = $prefix . $child->name;
			$return_array = $this->get_tree2($child->id, $return_array, $prefix . $child->name . ' &rarr; ');
		}

		return $return_array;
	}

	public function get_ancestors( $category_id = 0 , &$parentcategory =null )
	{

		$category = $this->get('id',$category_id);


		if($category)
		{
			if($parentcategory)$category->parent = $parentcategory;

			if($category->parent_id > 0)
			{
				return $this->get_ancestors($category->parent_id, $category );
			}

			return $category;
		}

		return $parentcategory;
	}


}