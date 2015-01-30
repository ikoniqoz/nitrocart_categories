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
class Events_Nitrocart_categories
{

	public $mod_details = array(
			      'name'=> 'Categories', 
			      'namespace'=>'nitrocart_categories',
			      'product-tab'=> TRUE, 
			      'prod_tab_order'=> 3, 
			      'cart'=> FALSE,
			      'has_admin'=> TRUE,
			      'routes'=>
			      		array(
			      				array(
			      						'name'	=> 'Categories List',
			      						'uri'	=> '/categories(/:num)?',
			      						'dest'	=> 'nitrocart_categories/categories/index$1'
			      					 ),
			      				array(
			      						'name'	=> 'Category [Detail]',
			      						'uri'	=>'/categories/category(/:any)?',
			      						'dest'	=>'nitrocart_categories/categories/category$1'
			      					 ),
			      				array(
			      						'name'	=> 'Products by Category',
			      						'uri'	=>'/categories/products(/:any)?',
			      						'dest'	=>'nitrocart_categories/categories/products$1'
			      					 ),
			      			),

				);


	public function __get($var)
	{
		if (isset(get_instance()->$var))
		{
			return get_instance()->$var;
		}
	}

	// Put code here for everywhere
	public function __construct()
	{
		//New events to replace all of the above -
		Events::register('SHOPEVT_AdminProductGet', array($this, 'shopevt_admin_product_get'));
		Events::register('SHOPEVT_AdminProductDelete', array($this, 'shopevt_admin_product_delete'));
		Events::register('SHOPEVT_AdminProductDuplicate', array($this, 'shopevt_admin_product_duplicate'));
		Events::register('SHOPEVT_AdminProductListGetFilters', array($this, 'shopevt_adminproductlist_get_filters'));
	}


	public function shopevt_adminproductlist_get_filters($o)
	{
		$this->load->model('nitrocart_categories/categories_admin_m');
		$o->modules = $this->categories_admin_m->get_products_filter( $o->modules );	
	}


	public function shopevt_admin_product_delete($deleted_product_id)
	{
		$this->load->model('nitrocart_categories/categories_products_m');

		$this->categories_products_m->delete_by_product( $deleted_product_id );
	}

	public function shopevt_admin_product_duplicate($duplicateData = array())
	{
		$or_id  = $duplicateData['OriginalProduct'];
		$new_id = $duplicateData['NewProduct'];

		$this->load->model('nitrocart_categories/categories_products_m');
		$this->categories_products_m->product_duplicated( $or_id ,$new_id );

	}


	/**
	 * This will be called when the admin product data has been requested.
	 * It will inform all other modules to fetch any data that may be associated
	 * The ID of the product is passed (always by ID and Never by SLUG)
	 */
	public function shopevt_admin_product_get($product)
	{
		// Send data back
		$this->load->model('nitrocart_categories/categories_admin_m');
		$this->load->model('nitrocart_categories/categories_products_m');

		//get a dropdown lost of available categries
		$product->modules['nitrocart_categories']['list'] = 	$this->categories_admin_m->get_tree2( 0 );

		$results = $this->categories_products_m->get_by_product($product->id);

		$product->modules['nitrocart_categories']['assigned'] = $this->categories_products_m->prepare_results_for_admin_tab($results);

		$product->module_tabs[] = (object) $this->mod_details;

	}


}
/* End of file events.php */