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
class Image_library
{

	protected $jpeg_quality = 90;
	protected $png_quality = 9;


	public function __construct()
	{
		log_message('debug', "Class Initialized");
	}

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

	public function uninstall_module()
	{
		if($this->db->table_exists('nct_categories_images'))
		{
			$this->load->model('nitrocart_categories/categories_images_m');
			$this->load->library('files/files');

			$images = $this->categories_images_m->get_all();
			foreach($images as $image)
			{
				$ar = Files::delete_file($image->file_id,'nitrocart_categories');
			}
		}
	}


	/**
	 * This will delete the physical image for the category
	 * @param  [type] $category_id [description]
	 * @return [type]              [description]
	 */
	public function sanitize_category($category_id)
	{
		//load model
		$this->load->model('nitrocart_categories/categories_m');
		$this->load->library('files/files');
		$this->load->model('files/file_folders_m');

		//Get categry DB data
		$cat = $this->categories_m->get($category_id);
		if($cat)
		{
			if($cat->file_id != '')
			{
				Files::delete_file($cat->file_id,'nitrocart_categories');
			}
		}

		return TRUE;
	}


}