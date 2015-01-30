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
class Categories_library
{

	public function __construct()
	{

	}


	public function process( $categories = array(), $outer_el ='ul', $inner_el='li')
	{
		return $this->build($categories,$outer_el,$inner_el);
	}

	//need to add classes
	//first
	//current
	//has_children
	//parent == everything
	//last
	public function build($categories, $outer_el, $inner_el='li')
	{
		$first = TRUE;
		$last = FALSE;

		$str ='';
		if($categories)
		{
			$str = "<{$outer_el}>";


			$counter = 0;
			foreach($categories as $key => $category)
			{
				$counter++;
				$class = '';

				//var_dump(count($categories));
				//echo $key;die;

				if(count($categories) == ($counter))
				{
					$last = TRUE;
				}

				//var_dump($category['children']);die;

				if($category['children'])
				{
					$class = 'has_children';
				}

				$class .= ($category['current'])? ' current' : '' ;
				$class .= ($first)? ' first' : '' ;
				$class .= ($last)? ' last' : '' ;


				$str .= "<{$inner_el} class='{$class}'>";
				$str .= "   <a href='{{nitrocart:uri}}/categories/products/{$category['category']->slug}'>{$category['category']->name}</a>";

				$str .= $this->build($category['children'],$outer_el,$inner_el);

				$str .= '</{$inner_el}>';

				$first = FALSE;
			}

			$str .= "</{$outer_el}>";
		}

		return $str;
	}

}
/* End of file details.php */