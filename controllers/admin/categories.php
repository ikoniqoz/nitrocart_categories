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
class Categories extends Admin_Controller
{

	protected $section = 'categories';
	private $data;


	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->lang->load('nitrocart_categories');


		$this->data = new StdClass;

		// Load all the required classes
		$this->load->model('nitrocart_categories/categories_admin_m');
		$this->load->helper('nitrocart_categories/admin');
		$this->load->library('form_validation');

        $this->template
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/tables.css')
                    ->append_css('nitrocart::admin/deprecated.css')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css');
	}

	/**
	 * List all items
	 */
	public function index()
	{
		//check if has access
		role_or_die('nitrocart_categories', 'admin_categories');

		// Build the view with shop/views/admin/clearances.php
		$this->data->categories = $this->categories_admin_m->get_all_parents(); 

		$this->template
				->title($this->module_details['name'])
				->append_js('module::sortable.shop.js')
				->append_js('module::sortable.jquery.js')
				->build('admin/categories/list', $this->data);
	}


	/**
	 * Create a new Brand
	 */
	public function create($redirect='standard')
	{

		//check if has access
		role_or_die('nitrocart_categories', 'admin_categories');

		$this->data = (object) array();

		// Set validation rules
		$this->form_validation->set_rules($this->categories_admin_m->_create_validation_rules);

		// if postback-validate
		if ($this->form_validation->run())
		{

			$input = $this->input->post();

			//Create a new category and retrieve the ID
			$id = $this->categories_admin_m->create($input);

			//Inform the system that we have a new category
			Events::trigger('SHOPEVT_CategoryCreated', $id );

			//Session message
			$this->session->set_flashdata('success', lang('nitrocart_categories:create_success'));

			if($input['btnAction']=='save_exit')
			{
				if($redirect=='standard')
					redirect('admin/nitrocart_categories/categories/');
				else
					redirect("admin/shop/{$id}");

			}

			//Redirect
			redirect('admin/nitrocart_categories/categories/edit/'.$id);

		}
		else
		{
			foreach ($this->categories_admin_m->_create_validation_rules as $key => $value)
			{
				$this->data->{$value['field']} = '';
			}
		}


		// Build page
		$this->template
			->title($this->module_details['name'])
			->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
			->build('admin/categories/create', $this->data);
	}


	/**
	 *	We need to alter edit to stop allow changing product.
	 *	Product and category can not change
	 */
	public function edit( $id = null)
	{

		//check if has access
		role_or_die('nitrocart_categories', 'admin_categories');



		//check if we have an id and if is numeric
		if( ! $id || ! is_numeric($id) )
		{
			$this->session->set_flashdata('error', lang('nitrocart_categories:invalid_id') );
			redirect('admin/nitrocart_categories/categories');
		}

		// Get row
		$row = $this->categories_admin_m->get($id);

		Events::trigger('SHOPEVT_AdminGetCategory',$id);


		// Check if exist
		if (!$row)
		{
			$this->session->set_flashdata(JSONStatus::Error, 'Category not found!');
			redirect('admin/nitrocart_categories/categories');
		}


		$this->data = (object) $row;
		$this->form_validation->set_rules($this->categories_admin_m->_edit_validation_rules);

		// if postback-validate
		if ($this->form_validation->run())
		{
			$input = $this->input->post();
			$this->categories_admin_m->edit($id,$input);


			Events::trigger('SHOPEVT_CategoryChanged', $id );

			$this->session->set_flashdata('success', lang('nitrocart_categories:update_success'));

			if($input['btnAction']=='save_exit')
			{
				redirect('admin/nitrocart_categories/categories/');
			}

			redirect("admin/nitrocart_categories/categories/edit/{$id}");
		}

		$this->data->children = $this->categories_admin_m->get_children($this->data->id);

		// Build page
		$this->template
			->enable_parser(TRUE)
			->title($this->module_details['name'])
			->append_js('module::sortable.shop.js')
			->append_js('module::sortable.jquery.js')
			->append_js('module::dropzone.js')
			->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
			->build('admin/categories/edit', $this->data);
	}

	public function add_quick($product_id)
	{
		//check if has access
		role_or_die('nitrocart_categories', 'admin_categories');

		if( $cat_name = $this->input->post('cat_name') )
		{

			$input_to_add = array();
			$input_to_add['name'] = trim($cat_name);
			$input_to_add['description'] = '';
			$input_to_add['slug'] =  trim($cat_name);
			$input_to_add['image_id'] = 0;
			$input_to_add['parent_id'] = 0;
			$input_to_add['order'] = 0;
			$input_to_add['user_data'] = '';

			$new_id = $this->categories_admin_m->create($input_to_add); //create simple just adds name/value not other optins

			if($new_id)
			{
				Events::trigger('SHOPEVT_CategoryCreated', $new_id );

				echo json_encode(array('status'=>'success','id'=>$new_id ,'product_id'=>$product_id));exit;
			}
			else
			{
				echo json_encode(array('status'=>'success','id'=>$new_id ,'product_id'=>$product_id, 'message'=>'Failed to add.'));exit;
			}

		}
		else
		{

			echo json_encode(array('status'=>'error', 'message'=>'Not a valid POST'));exit;
		}
	}



	private function upload_image($category_id)
	{
		//upload all images
		//loop and get the first valid image
		//then call assign_image(catid,image_id)
		//of the success upload

		$folder_id = 2; //do not have one yet

		//upload each image
		foreach($_FILES as $key => $_file)
		{
			// we should also check for type of file uploading

			// uploads images to files module
			$upload = Files::upload($folder_id, $_file['name'], 'new_category_image' );

			// Get the Image ID
	    	$image_id = $upload['data']['id'];

	    	return $this->assign_image($category_id,$new_file_id);

	    	//do not do anymore than 1 image request

		}
	}

	/**
	 * Assign an image to a category
	 *
	 * @param  [type] $category_id [description]
	 * @param  [type] $image_id    [description]
	 * @return [type]              [description]
	 */
	private function assign_image($category_id,$image_id)
	{
		if($this->categories_admin_m->assign_image($category_id,$image_id))
			return JSONStatus::Success;

		$status = JSONStatus::Error;
	}


	/**
	 * Simple delete, will need to work on validation and return messages
	 * @param unknown_type $id
	 */
	public function delete($id = null, $ret_cat = 0)
	{
		if($input = $this->input->post())
		{
			if(isset($input['btnAction']))
			{
				$this->_deleteMany();
			}
		}

		//check if has access
		role_or_die('nitrocart_categories', 'admin_categories');

		//check if we have an id and if is numeric
		if( ! $id || ! is_numeric($id) )
		{
			$this->session->set_flashdata('error', lang('nitrocart_categories:invalid_id') );
			redirect('admin/nitrocart_categories/categories');
		}

		if($this->categories_admin_m->delete($id))
		{
			Events::trigger('SHOPEVT_CategoryDeleted', $id );

			if($this->input->is_ajax_request())
			{
				echo (json_encode(
					array(
						'status'=>JSONStatus::Success,
						)
					)
				);
				exit;
			}

		}

		if($ret_cat>0)
			redirect('admin/nitrocart_categories/categories/edit/'.$ret_cat);
		else
			redirect('admin/nitrocart_categories/categories');
	}

	private function _deleteMany()
	{

		$input = $this->input->post();


		if(isset($input['action_to']))
		{
			foreach( $input['action_to'] as $key => $value )
			{
				$this->categories_admin_m->delete( $value );
			}
		}

		redirect('admin/nitrocart_categories/categories');
	}


	public function reorder()
	{
		$arr = array();
		$arr['status'] = JSONStatus::Error;
		$arr['message'] = 'Not initiated';

		if($this->input->post('cat_list'))
		{
			$catlist = $this->input->post('cat_list');
			$catlist = explode(',',$catlist);

			$order = 0;
			foreach($catlist as $cat)
			{
				$this->categories_admin_m->reorder($cat,$order);
				$order++;
			}

			$arr['status'] = JSONStatus::Success;
			$arr['message'] = 'Categories reordered:'.$this->input->post('cat_list');
		}

		echo json_encode($arr);die;
	}


	public function addchild($id,$meta='')
	{
		//check if has access
		role_or_die('nitrocart_categories', 'admin_categories');

		$this->data->id = $id;
		$view = 'admin/categories/addchild';

		return $this->load->view($view,$this->data); die;
	}

	/**
     *
	 */
	public function savechild()
	{
		//check if has access
		role_or_die('nitrocart_categories', 'admin_categories');

		if( $input = $this->input->post() )
		{
			if(!(isset($input['value1'])) AND !(isset($input['id'])))
			{
				die;
			}

			$id = $input['id'];
			$new_cat_name = $input['value1'];

			$new_cat_name = trim($input['value1']);

			$new_id = $this->categories_admin_m->create_child($id, $new_cat_name); //create simple just adds name/value not other optins

			Events::trigger('evt_category_created', $new_id );

			if(isset($input['btnAction']))
			{
				if($input['btnAction'] == 'save_and_edit')
				{
					redirect('admin/nitrocart_categories/categories/edit/'.$new_id);
				}
			}


			//go back to the original cat
			redirect('admin/nitrocart_categories/categories/edit/'.$id );
		}

		redirect('admin/nitrocart_categories/categories/');
	}

	public function link($product_id,$category_id)
	{

		//take params and create, if fail, thats ok shouldmean its already done
		$this->load->model('nitrocart_categories/categories_products_m');

		$link_id = $this->categories_products_m->insert(array('product_id'=>$product_id,'category_id'=>$category_id));

		$return_array = array();
		$return_array['status'] = JSONStatus::Success;
		$return_array['is_linked'] = TRUE;
		$return_array['link_id'] = $link_id;
		$return_array['product_id'] = $product_id;
		$return_array['category_id'] = $category_id;

		echo json_encode($return_array);exit;
	}


	public function unlink($product_id,$category_id, $link_id)
	{
		//allow access

		$this->load->model('nitrocart_categories/categories_products_m');

		$this->categories_products_m->delete($link_id);

		$return_array = array();
		$return_array['status'] = JSONStatus::Success;
		$return_array['is_linked'] = FALSE;
		$return_array['link_id'] = '';
		$return_array['product_id'] = $product_id;
		$return_array['category_id'] = $category_id;

		echo json_encode($return_array);exit;
	}


	public function visibility($category_id, $show='on')
	{
		$show = ($show=='on')? 1 : 0;

		if($show)
			$this->categories_admin_m->show($category_id);
		else
			$this->categories_admin_m->hide($category_id);


		redirect('admin/nitrocart_categories/categories/edit/'.$category_id);
	}

}