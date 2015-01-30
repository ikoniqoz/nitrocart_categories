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
class Module_Nitrocart_categories extends Module
{

	/**
	 *
	 * @var string
	 */
	public $version = '2.2.5';


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
			      				array(
			      						'name'	=> 'Category API',
			      						'uri'	=>'/api/categories(/:any)?',
			      						'dest'	=>'nitrocart_categories/api/categories$1'
			      					 ),	
			      				array(
			      						'name'	=> 'Category AdminAPI',
			      						'uri'	=>'/admin/api/categories(/:any)?',
			      						'dest'	=>'nitrocart_categories/admin/api/categories$1'
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
				'hidden'		=> array('type' => 'INT', 'constraint' => '1'	, 'unsigned' => TRUE, 'null' => TRUE, 'default' => 0),
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
        $this->load->library('nitrocart/nitrocore_library'); 
		$this->ci = get_instance();
	}


	/**
	 * info()
	 * @description: Creates 2 arrays to diplay for the module naviagtion
	 *			   One array is returned based on the user selection in the settings
	 *
	 */
	public function info()
	{

		$info =  array(
			'name' => array(
				'en' => 'NitroCart Categories',
			),
			'description' => array(
				'en' => 'Ecommerce extension for NitroCMS',
			),
			'skip_xss' => FALSE,
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => FALSE,
			'author' => 'Salvatore Bordonaro',
            'roles' => 
            [
            	'admin_manage',
	            'admin_categories',
            ],
			'sections' => []
		);

        $this->load->library('nitrocart/Toolbox/Nc_cp');
        $info['sections'] = $this->nc_cp->get_common_sections_menu();

		$info['sections']['categories'] = array(
			'name' => 'nitrocart:admin:categories',
			'uri' => 'admin/nitrocart_categories/categories',
            'shortcuts' => 
            				[ 
            					['name' => 'nitrocart_categories:create', 'uri' => 'admin/nitrocart_categories/categories/create/','class' => 'add' ]
            				],
		);

		return $info;
	}

	public function disable() { }
	
	public function enable() { }
	/*
	 * The menu is handled by the main SHOP module
	 * Not needed here
	 */
    public function admin_menu(&$menu)
    {
    	 //$menu['lang:nitrocart:admin:shop_admin']['Categories'] 		= 'admin/nitrocart_categories/categories';
    }



	public function install()
	{

        if ( CMS_VERSION < '0.0.0' ) {
            return FALSE;
        }

		if(!$this->isRequiredInstalled())
		{
			return FALSE;
		}

		// Install tables
		$tables_installed = $this->install_tables( $this->module_tables );

		// if the tables installed, now time to register this sub-module with
		if( $tables_installed  )
		{

				//menu
		        $data = [];
		        $data[] = 
		        [
		            'label'         => 'Categories',
		            'uri'           => 'admin/nitrocart_categories/categories',
		            'menu'          => 'lang:nitrocart:admin:shop_admin',
		            'module'        => 'categories',
		            'order'         => 38,
		        ];
		        $this->db->insert_batch('nct_admin_menu', $data);



			if($this->install_settings())
			{
				Events::trigger("SHOPEVT_RegisterModule", $this->mod_details);
			}

			return TRUE;
		}

		return FALSE;
	}


	/*
	 */
	public function uninstall()
	{
		$this->load->library('nitrocart_categories/categories_installer');
		return $this->categories_installer->uninstall();
	}



	/*
	 */
	public function upgrade($old_version)
	{
		return TRUE;
	}


	public function help()
	{
		return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
	}



	private function init_templates()
	{
		 return TRUE;
	}

	private function install_settings()
	{

		$settings = array(

			'shop_cat_layout' => array( 
				'title' => 'Categories Layout File',
				'description' => 'Select which prefered layout file to use on Categories module. If the layout does not exist, it will default to the pyro default.html',
				'type' => 'select',
				'default' => 'nitrocart.html',
				'value' =>  'nitrocart.html',
				'options' => 'default.html=default.html|nitrocart.html=nitrocart.html|nitrocart_categories.html=nitrocart_categories.html',
				'is_required' => TRUE,
				'is_gui' => TRUE,
				'module' => 'nitrocart',
				'order' => 100
			),
		);

		foreach ($settings as $slug => $setting)
		{
			//set the settings name
			$setting['slug'] = $slug;

			if (!$this->db->insert('settings', $setting))
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	public function isRequiredInstalled()
	{

		$this->ci->load->model('module/module_m');
		$module_core = $this->ci->module_m->get_by('slug', 'nitrocart' );

    	if( $module_core && $module_core->installed == TRUE)
    	{
    		$module = $this->ci->module_m->get_by('slug', 'nitrocart' );
    		if( $module && $module->installed == TRUE)
    		{
				//we can now install this shop module
				return TRUE;
			}
    	}

    	return FALSE;
	}

}
/* End of file details.php */