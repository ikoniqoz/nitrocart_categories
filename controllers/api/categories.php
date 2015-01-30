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
include_once('apicore.php');
class Categories extends Apicore
{

	protected $section = 'api';

	public function __construct()
	{
		parent::__construct();            
	}


	/**
	 * Gain access to all products
	 * 
	 * @param  [type]  $key    [description]
	 * @param  [type]  $action [description]
	 * @param  integer $limit  [description]
	 * @param  integer $offset [description]
	 * @return [type]          [description]
	 */
	public function all($key,$limit=50,$offset=0)
	{
		$endpoint = 'api/'.__METHOD__.'/';

		//if not valid,  the reqponse will die, no need to check after this line
		parent::req($endpoint,$key);

		//ok now go
		$result = $this->db->where('hidden',0)->limit($limit)->offset($offset)->get('nct_categories')->result();


		//send back the data
		parent::send($endpoint,JSONStatus::Success,'',$result);
	}

	public function category($id, $key)
	{
		$endpoint = "api/".__METHOD__."/ [{$id}]";

		//if not valid,  the reqponse will die, no need to check after this line
		parent::req($endpoint,$key);

		//ok now go
		$result = $this->db->where('hidden',0)->where('id',$id)->get('nct_categories')->row();


		//send back the data
		parent::send($endpoint,JSONStatus::Success,'',$result);
	}

}
