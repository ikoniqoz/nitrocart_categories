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
class Plugin_Nitrocart_categories extends Plugin
{

	public $version = '1.0.0';
	public $name = array(
		'en' => 'NitroCart Categories',
	);
	public $description = array(
		'en' => 'Access user and cart information for almost any part of SHOP.',
	);

	/**
	 * Get the CI instance into this object
	 *
	 * @param unknown_type $var
	 */
	public function __get($var)
	{
		if (isset(get_instance()->$var))
		{
			return get_instance()->$var;
		}
	}
	
	/**
	 * Returns a PluginDoc array that PyroCMS uses
	 * to build the reference in the admin panel
	 *
	 * All options are listed here but refer
	 * to the Asset plugin for a larger example
	 *
	 * @return array
	 */
	public function _self_doc()
	{
		$info = array(


			'categories' => array(
				'description' => array(
					'en' => 'Display a list of Categories.'
				),
				'single' => false,
				'double' => true,
				'variables' => 'link|categories',
				'attributes' => array(),
			),
			'category' => array(
				'description' => array(
					'en' => 'Get all fields of a particular category by Category-ID, OR just get the value of a particular field.'
				),
				'single' => false,
				'double' => true,
				'variables' => 'id|slug|name|user_data',
				'attributes' => array(
					'id' => array(
						'type' => 'Integer',
						'required' => true,
					),
					'field' => array(
						'type' => 'String',
						'default' => '',
						'required' => false,
					),
				),
			),

		);

		return $info;
	}


	/**
	 * x=VIEW_LIST|VIEW_LAYER
	 * product=num - This will help with the current selected categories
	 *
	 * @return [type] [description]
	 */
	function tree()
	{

		if(!$this->db->table_exists('nct_categories')) { return ''; }

		$this->load->model('nitrocart_categories/categories_m');
		$this->load->library('nitrocart_categories/categories_library');

		$parent =(int) $this->attribute( 'parent', "0" );
		$xPARAM = $this->attribute( 'x', "" );
		$product = $this->attribute( 'product', "" );

		$xPARAM = explode ( ',' , $xPARAM);

		$categories = $this->categories_m->get_tree( $parent );

		return $this->categories_library->process($categories);

	}




	/**
	 * ASC is default, you can specifiy, but it is ignored
	 *
	 * {{ nitrocart:all order="ORDER|ID|NAME|SLUG" x="ASC|DESC|" }}
	 *
	 *    {{ name }}
	 *    {{ id }}	 		- INT
	 *    {{ slug }} 		- Unique slug for category
	 *    {{ uri }} 		- The full URI for the category, the slug will only return the text part not the full site url.
	 *	  {{ description }}
	 *
	 * {{ /nitrocart:all }}
	 *
	 */
	function all()
	{

		if(!$this->db->table_exists('nct_categories')) { return array(); }

		$this->load->model('nitrocart_categories/categories_m');

		$xPARAM = $this->attribute( 'x', "" );
		$order = $this->attribute( 'order', "id" );
		$offset = $this->attribute( 'offset', 0 ); //
		$limit = $this->attribute( 'limit', 100 ); //

		$xPARAM = explode( ',' , $xPARAM);
		$orderby  = (in_array( "DESC" , $xPARAM )) ? "desc": "asc";

		$categories = $this->categories_m->order_by($order,$orderby)->limit( $limit , $offset )->get_all();

		return $categories;
	}

	/**
	 * ASC is default, you can specifiy, but it is ignored
	 *
	 * {{ nitrocart:categories order="ID|NAME|SLUG" parent="5" limit="10" offset="10" order="id|slug|name|order" x="ASC|DESC|" }}
	 *
	 *    {{ name }}
	 *    {{ id }}	 		- INT
	 *    {{ slug }} 		- Unique slug for category
	 *    {{ uri }} 		- The full URI for the category, the slug will only return the text part not the full site url.
	 *	  {{ description }}
	 *
	 * {{ /nitrocart:categories }}
	 *
	 */
	function categories()
	{

		if(!$this->db->table_exists('nct_categories')) { return array(); }

		$this->load->model('nitrocart_categories/categories_m');

		$xPARAM = $this->attribute( 'x', "" );
		$order = $this->attribute( 'order', "id" );
		$parent = (int) $this->attribute('parent', '-1');
		$limit = $this->attribute( 'limit', 100 ); //
		$offset = $this->attribute( 'offset', 0 ); //

		$xPARAM = explode ( ',' , $xPARAM);
		$orderby  = (in_array( "DESC" , $xPARAM )) ? "desc": "asc";

		if($parent != -1)
			$this->categories_m->where('parent_id',$parent);

		$categories = $this->categories_m->where('hidden',0 )->order_by( $order, $orderby )->limit( $limit , $offset )->get_all();

		return $categories;
	}

 		

	/**
	 *
	 * back, if the category get the array of categry otherwise blank result
	 */
	function category()
	{

		if(!$this->db->table_exists('nct_categories')) { return ''; }

		$id = $this->attribute('id', '');
		$getby = (is_numeric($id))? 'id' : 'slug' ;

		$this->load->model('nitrocart_categories/categories_m');
		$category = $this->categories_m->get_by( $getby, $id );

		return ( $category ) ? (array) $category : '' ;
	}


	/*list all categories by product*/
	function product()
	{
		if(!$this->db->table_exists('nct_categories')) { return ''; }

		$this->load->model('nitrocart_categories/categories_products_m');
		$this->load->model('nitrocart_categories/categories_m');
		//$this->load->helper('nitrocart/admin');

		//product
		$product_id = $this->attribute( 'id', 0 ); //

		$categories = (array) $this->categories_products_m->get_by_product($product_id);

		foreach($categories as $key => $cat)
		{
			$the_cat = $this->categories_m->get($cat->category_id);
			if($the_cat)
			{
				$categories[$key]->category_name =  $the_cat->name;
				$categories[$key]->category_slug =  $the_cat->slug;
			}
		}

		return $categories;

	}

	/*list products by a category*/
	function products()
	{
		if(!$this->db->table_exists('nct_categories')) { return ''; }

		$cat_id = $this->attribute( 'category', '' );
		$limit = (int) $this->attribute( 'limit', "5" );
		$offset = (int) $this->attribute( 'offset', "0" );

		$this->load->model('nitrocart_categories/categories_m');
		$this->load->model('nitrocart_categories/categories_products_m');
		$this->load->model('nitrocart/products_front_m');


		$getby = (is_numeric($cat_id))? 'id' : 'slug' ;
		$category = $this->categories_m->get_by($getby,$cat_id);


		$product_list = $this->categories_products_m->get_products_by_category( $category->id , $limit , $offset );


		return $product_list;

	}


}
/* End of file plugin.php */