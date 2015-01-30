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
class Categories extends Public_Controller
{

	protected $view_mode;

	public function __construct()
	{
		parent::__construct();
		Events::trigger('SHOPEVT_ShopPublicController');

		Settings::get('shop_open_status') OR redirect( NC_ROUTE . '/closed');

		// Retrieve some core settings
		$this->load->model('nitrocart_categories/categories_m');
		$this->data = (object) array();

		$this->limit = Settings::get('shop_qty_perpage_limit_front');
		$this->view_mode = ($this->session->userdata('products_view_mode')) ? $this->session->userdata('products_view_mode') : 'list';
		$this->order_by =  ($this->session->userdata('products_ordering_by')) ? $this->session->userdata('products_ordering_by') : 'id';
		$this->order_by_dir =  ($this->session->userdata('products_ordering_by_order')) ? $this->session->userdata('products_ordering_by_order') : 'asc';

		$this->setLayoutForShop();

        $this->template	
        	->title(Settings::get('shop_name'))
			->set_breadcrumb('Home', '/')
			->set_breadcrumb( Settings::get('shop_name'), '/'.NC_ROUTE);
	}


	/**
	 * This displays the list of ALL products.
	 * @uri yourdomain.com/shop/products
	 */
	public function index( $offset = 0 )
	{
		//$this->limit = ;

 		$filter = array();

 		$total_items = $this->categories_m->count_by( array('parent_id' =>0, 'hidden'=>0 ));

		//  Build pagination for these items
		$pagination = create_pagination( NC_ROUTE.'/categories/' , $total_items, $this->limit, 3);

		//  Count total items by the given filter
		$categories = $this->categories_m->where('hidden',0 )->where('parent_id',0)->limit($pagination['limit'])->offset($pagination['offset'])->get_all();


		// finally
		$this->template
        	->title(Settings::get('shop_name'),'Categories')		
			->set_breadcrumb('Categories')
			->set('total_items', $total_items)
			->set('categories', $categories)
			->set('pagination', $pagination )
			->build('nitrocart_categories/categories_list');
	}

	/**
	 * View the category details
	 *
	 */
	public function category( $idslug = 0 )
	{
		$method = (is_numeric($idslug))? 'id' : 'slug' ;

		$category = $this->categories_m->get_by( $method,  $idslug );

		if( ! $category )
		{
			$this->session->set_flashdata(JSONStatus::Error,'Unable to find category.');
			redirect(NC_ROUTE.'/categories');
		}

		$this->load->model('nitrocart_categories/categories_products_m');
		$category->product_count = $this->categories_products_m->get_products_by_category_count( $category->id );

		$this->template
        	->title(Settings::get('shop_name'),'Categories', $category->name)			
			->set_breadcrumb('Categories', '/'.NC_ROUTE.'/categories')
			->set_breadcrumb($category->name)
			->set('category',$category)
			->build('nitrocart_categories/category_detail');		
	}


	/**
	 * View list of products by category
	 * $idslug = category_id
	 */
	public function products($idslug = 0, $offset = 0)
	{
		$data = (object) [];

		$this->load->model('nitrocart/products_front_m');
		$this->load->model('nitrocart_categories/categories_products_m');

		$method = (is_numeric($idslug)) ? 'id' : 'slug' ;
		$category = $this->categories_m->get_by( $method,  $idslug );

		//if no category, redirect away from here
		$category OR redirect(NC_ROUTE.'/categories');
		$products = [];

		$pag_uri = NC_ROUTE."/categories/products/{$idslug}";

		//count the total products in the category
		$total_items =  $this->categories_products_m->get_products_by_category_count( $category->id );
		$pagination = create_pagination( $pag_uri , $total_items, $this->limit, 5);	

		$data->products =  $this->categories_products_m->get_products_by_category($category->id, $pagination['limit'], $pagination['offset'] );
		$data->product_count = $total_items;
		$data->pagination = $pagination;
		$data->category = $category;
		$data->viewmode = $this->view_mode;

		//view file
		$view_file = $this->getProductListViewFile();

		$sort_by = $this->order_by . '/' . $this->order_by_dir;

		// finally
		$this->template
        	->title(Settings::get('shop_name'),'Categories', 'Products')				
			->set_breadcrumb('Categories', NC_ROUTE.'/categories')
			->set_breadcrumb($category->name, NC_ROUTE.'/categories/category/'.$category->slug)			
			->set_breadcrumb( 'Products' )
			->set('offset',$offset)
			->set('limit',$this->limit)
			->set('per_page',$this->limit) //for compatibility we duplicate the value
			->set('sort_by',$sort_by)			
			->set('message','')
			->set('view_title',$category->name)		
			->build('nitrocart_categories/'.$view_file, $data );
	}

	/**
	 * Overrides the layout so we use shop.html instead of nitrocart_categories.html
	 */
	private function setLayoutForShop()
	{
		if($preferred = $this->settings_m->get_by(['slug' => 'shop_cat_layout']))
		{
			if($this->template->layout_exists($preferred->value))
			{
				$this->template->set_layout($preferred->value);
			}	
		}
	}

	private function getProductListViewFile()
	{
		if($this->view_mode=='grid')
		{
			return 'category_products_grid';
		}
		return 'category_products_list';
	}		

}