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
if (!function_exists('HelperGet_category_name'))
{

	//make sure the slug is valid
	function HelperGet_category_name($id)
	{

		$ci =& get_instance();

		$ci->load->model('nitrocart_categories/categories_admin_m','categories_admin_m');


		$cat = $ci->categories_admin_m->get($id);

		return $cat->name;

	}
}

if (!function_exists('category_get'))
{

	//make sure the slug is valid
	function category_get($id,$parent = FALSE)
	{
		$ci =& get_instance();
		$ci->load->model('nitrocart_categories/categories_admin_m','categories_admin_m');
		$cat = $ci->categories_admin_m->get($id);
		return $cat;
	}
}

if (!function_exists('HelperGet_category_has_parent'))
{

	//make sure the slug is valid
	function HelperGet_category_has_parent($id)
	{
		$ci =& get_instance();
		$ci->load->model('nitrocart_categories/categories_admin_m','categories_admin_m');
		$cat = $ci->categories_admin_m->get($id);
		return ($cat->parent_id >0)?TRUE:FALSE;

	}
}

if (!function_exists('CategoryHelper_get_top_most'))
{

	//make sure the slug is valid
	function CategoryHelper_get_top_most($id)
	{

		$ci =& get_instance();

		$ci->load->model('nitrocart_categories/categories_admin_m','categories_admin_m');

		$cat = $ci->categories_admin_m->get_top_most($id);


	}
}

if (!function_exists('CategoryHelper_category_image'))
{

	//make sure the slug is valid
	function CategoryHelper_category_image($category)
	{
		if($category->file_id != NULL)
		{
			return "<img src='files/thumb/{$category->file_id}/50/50' alt='{$category->name}' />";
		}

		return '<div class="img_noimg"></div>';

	}
}