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
class Categories_admin_m extends MY_Model
{

	private $_description_tags = '<b><div><strong><em><i><u><ul><ol><li><p><span><a><br><br />';
    public $_table = 'nct_categories';

	public	$_create_validation_rules = array(
			array(
				'field' => 'name',
				'label' => 'Name',
				'rules' => 'trim|required|max_length[100]'
			),
			array(
				'field' => 'description',
				'label' => 'lang:description',
				'rules' => 'trim|xss_clean|prep_for_form'
			),
		);

	public	$_edit_validation_rules = array(
			array(
				'field' => 'name',
				'label' => 'Name',
				'rules' => 'trim|required|max_length[100]'
			),
			array(
				'field' => 'slug',
				'label' => 'lang:slug',
				'rules' => 'trim|max_length[100]|required'
			),
			array(
				'field' => 'description',
				'label' => 'lang:description',
				'rules' => 'trim|xss_clean|prep_for_form'
			),
			array(
				'field' => 'file_id',
				'label' => 'Image',
				'rules' => 'trim'
			),
			array(
				'field' => 'order',
				'label' => 'Order',
				'rules' => 'trim|numeric'
			),
	);


	public function __construct()
	{
		parent::__construct();
	}

	public function get_all_parents()
	{

		$categories = $this->order_by('order', 'asc')->where('parent_id',0)->get_all();

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
		return $this->count_by('parent_id', $parent_id);
	}



	public function create($input)
	{

		$this->load->helper('nitrocart_categories/admin');

		$_name = strip_tags($input['name']);

		$to_insert = array(
			'name' 			=> $_name,
			'description' 	=> strip_tags($input['description'], $this->_description_tags),
			'slug' 			=> $this->getNewSlug($_name),
			'file_id' 		=> '',
			'parent_id' 	=> (isset($input['parent_id']))?$input['parent_id']:0,
			'order' 		=> 0,
			'hidden'		=> 1,
            'created_by'    => $this->current_user->id,
            'created'       => date("Y-m-d H:i:s"),
            'updated'       => date("Y-m-d H:i:s"),
		);

		$id = $this->insert($to_insert);

		return $id;
	}


	/**
	 *
	 * @return INT id of the updated row for success
	 * @access public
	 */
	public function edit($id, $input)
	{
		// Prepare
		$to_update = array(
			'name' => strip_tags($input['name']),
			'description' => strip_tags($input['description'], $this->_description_tags),
			'slug' => $this->getNewSlug( $input['slug'], $id ),
			'updated' => date("Y-m-d H:i:s"),
		);

		return $this->update($id, $to_update);
	}

	public function create_child( $parent_id, $child_name )
	{
		$this->load->helper('nitrocart_categories/admin');

		$to_insert = array();
		$to_insert['name'] 			= strip_tags($child_name);
		$to_insert['description'] 	= '';
		$to_insert['slug'] 			= $this->getNewSlug(strip_tags($child_name));
		$to_insert['file_id'] 		= '';
		$to_insert['parent_id'] 	= $parent_id;
		$to_insert['order'] 		= 0;

		$to_insert['created_by'] 	= $this->current_user->id;
		$to_insert['created'] 		= date("Y-m-d H:i:s");
		$to_insert['updated'] 		= date("Y-m-d H:i:s");

		return $this->insert($to_insert);
	}

	private function getNewSlug($inSlug='', $igNor = -1)
	{
		$this->load->helper('nitrocart_categories/admin');
		$slug = shop_slugify( $inSlug );
		$category = $this->where('id !=',$igNor)->get_by('slug', $slug );

		$suffix = (isset($category)) ? '-'. $this->db->where('id !=',$igNor)->like('slug', $category->slug)->get('nitrocart_categories')->num_rows() : '' ;

		return $slug.$suffix;
	}

    public function delete($id)
    {
    	//
    	// Process the children first
    	//
		$sub_categories = $this->get_children($id);
		foreach($sub_categories as $cat)
		{
			$this->delete($cat->id);
		}

        //
        // Must also delete the associations
        //
        $this->db->where('category_id', $id )->delete('nct_categories_products');

        //
        // Now delete the category
        //
        $status = parent::delete($id);
    }


	public function hide($id)
	{
		return $this->toggle_visibility($id, 0);
	}

	public function show($id)
	{
		return $this->toggle_visibility($id, 1);
	}

	public function toggle_visibility($id, $set_visiblity = 1 )
	{
		// Step 1: Delete all childs
		$sub_categories = $this->get_children($id);
		foreach($sub_categories as $cat)
		{
			$this->toggle_visibility($cat->id, $set_visiblity );
		}

		//now hide
		$to_update = array(
			'hidden' => $set_visiblity,
		);

		return $this->update($id, $to_update);
	}


	public function reorder($id,$order)
	{
		$to_update = array(
			'order' => $order,
		);
		return $this->update($id, $to_update);
	}

	public function assign_image($category_id,$file_id)
	{
		// Prepare
		$to_update = array(
			'file_id' => $file_id,
			'updated' => date("Y-m-d H:i:s"),
		);
		return $this->update($category_id, $to_update);
	}

	public function clear_image($category_id)
	{
		// Prepare
		$to_update = array(
			'file_id' =>  '',
			'updated' => date("Y-m-d H:i:s"),
		);
		return $this->update($category_id, $to_update);
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
		$children = $this->where('parent_id',$parent_id)->get_all();

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
		$children = $this->where('parent_id',$parent_id)->get_all();

		foreach($children as $child)
		{
			$return_array[$child->id] = $prefix . $child->name;
			$return_array = $this->get_tree2($child->id, $return_array, $prefix . $child->name  . ' &rarr; ');//$prefix . $child->name 
		}

		return $return_array;
	}


	public function get_products_filter($i_array)
	{
		$categories = $this->get_all();

		foreach ($categories as $key => $value) 
		{
			$i_array["By Categories"]["nitrocart_categories,{$value->id}|{$value->id}"] = $value->name;
		}

		return $i_array;
	}

	public function get_ancestors( $category_id = 0 , &$parentcategory =null )
	{

		$category = $this->get('id',$category_id);


		if($category)
		{
			if($parentcategory)$category->parent =$parentcategory;

			if($category->parent_id > 0)
			{
				return $this->get_ancestors($category->parent_id, $category );
			}

			return $category;
		}

		return $parentcategory;
	}

}