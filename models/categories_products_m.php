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
class Categories_products_m extends MY_Model
{


	public $_table = 'nct_categories_products';

	public function __construct()
	{
		parent::__construct();
	}

	public function get_by_product($product_id)
	{
		return $this->where('product_id',$product_id)->get_all();
	}

	public function prepare_results_for_admin_tab($results)
	{

		$return_val = array();

		foreach($results as $result)
		{
			$return_val[$result->category_id] = $result->id;
		}

		return $return_val;

	}

	//this function duplicates all the records for original product id
	//with a new product id and the same category assignments
	public function product_duplicated($original_product_id,$new_product_id)
	{
		//fetch all rows where prod id = $or_id
		$original_product_cats = $this->where('product_id',$original_product_id)->get_all();

		foreach($original_product_cats AS $linkage)
		{
			//create the input
			$to_insert = array(
					'product_id' => $new_product_id ,
					'category_id' => $linkage->category_id,
			);

			//Add record
			$this->insert($to_insert); //returns id

		}

		return TRUE;

	}

	public function delete_by_product( $deleted_product_id )
	{
		return $this->delete_by('product_id',$deleted_product_id);
	}


	private function rand_in()
	{
		/*
		$count = $get->limit(4);
		$items = $get->get($count);

		$random_number = rand(0,4);
		return $items[$random_number];*/
	}


	/**
	 * This should solve a whole bunch of problems
	 */
	public function get_products_by_category($category_id,$limit=NULL,$offset=0)
	{
		$this->prep_prod_count_query($category_id);

		if($limit !=NULL)
			$this->db->limit($limit);

		return $this->db->offset($offset)->get()->result();
	}

	public function get_products_by_category_count($category_id)
	{
		$this->prep_prod_count_query($category_id);
		return $this->db->count_all_results();
	}

	private function prep_prod_count_query($category_id)
	{
		$this->db
				->select('nct_products.*')
				->from('nct_products')
				->join('nct_categories_products', 'nct_categories_products.product_id = nct_products.id', 'INNER')
				->where('nct_categories_products.category_id',$category_id)
				->where('nct_products.public',1)
				->where('nct_products.deleted',NULL);
		
	}

}