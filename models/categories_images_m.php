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
class Categories_images_m extends MY_Model {


	public $_table = 'nct_categories_images';

	public function __construct()
	{
		parent::__construct();
	}


	public function create_file_folder()
	{

		$to_insert = 
		[
			'parent_id' => 0,
			'slug' => 'shop_category_images', //generate_slug()
			'name' => 'Category Images',
			'location' => 'local',
			'remote_container' => '',
			'date_added' => now(),
			'sort' => now(), //will implement the ordering in later version
			'hidden' => 0,
		];


		return $this->db->insert('file_folders',$to_insert); //returns id
	}


	public function get_available_file_folders()
	{
		return  $this->db->where('parent_id',0)->where('slug','shop_category_images')->where('name','Category Images')->where('hidden',1)->get('file_folders')->row();
	}


}