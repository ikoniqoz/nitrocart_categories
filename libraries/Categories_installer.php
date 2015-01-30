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
class Categories_installer
{
	public $mod_details = array(
			      'name'=> 'Categories', 
			      'namespace'=>'nitrocart_categories',
                  'path'=> 'admin', 
                  'driver'=> 'categories_installer',			      
			      'prod_tab_order'=> 4, 
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



	//List of tables used
	protected $module_tables = array(

			'nct_categories' => array(
				'id' 			=> array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => TRUE),
				'name' 			=> array('type' => 'VARCHAR', 'constraint' => '100'),
				'slug' 			=> array('type' => 'VARCHAR', 'constraint' => '100', 'unique' => TRUE, 'key' => true),
				'description' 	=> array('type' => 'TEXT'),
				'file_id' 		=> array('type' => 'CHAR', 'constraint' => '15', 'null' => TRUE, 'default' => NULL),
				'parent_id' 	=> array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'default' => 0), /*structure for heirachial but not by default*/
				'order' 		=> array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'default' => 0),
				'hidden'		=> array('type' => 'INT', 'constraint' => '1', 'unsigned' => TRUE, 'null' => TRUE, 'default' => 0),
                'created_by'    => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE, 'default' => 0),
                'created'       => array('type' => 'DATETIME', 'null' => TRUE, 'default' => NULL),
                'updated'       => array('type' => 'DATETIME', 'null' => TRUE, 'default' => NULL),
			),
			'nct_categories_products' 	=> array(
				'id' 					=> array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => TRUE),
				'product_id' 			=> array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'default' => 0),
				'category_id' 			=> array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'default' => 0),
				//no need for delete field, we never delete this row, even if prod deleted, we want all the data
			),
	);

	public function __construct()
	{
		$this->ci = get_instance();
	}

	public function install()
	{
		return TRUE;
	}

	public function uninstall()
	{

		foreach($this->module_tables as $table_name => $table_data)
		{
			$this->ci->dbforge->drop_table($table_name);
		}

		// Remove All settings for this module
		$this->ci->db->delete('settings', array('module' => 'nitrocart_categories'));

		$this->ci->db->delete('settings', array('slug' => 'shop_cat_layout'));


		if($this->ci->db->table_exists('nct_admin_menu'))
        	$this->ci->db->where('module','categories')->delete('nct_admin_menu');

		//Remove categories from the core module DB
		Events::trigger("SHOPEVT_DeRegisterModule", $this->mod_details);

		return TRUE;
	}

}
/* End of file details.php */