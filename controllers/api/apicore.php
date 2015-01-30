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
class Apicore extends Public_Controller
{

	protected $section = 'api';

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopPublicController');
        $this->load->library('nitrocart/Toolbox/Nc_status');
        $this->load->library('nitrocart/Toolbox/Nc_string');  
        
        /**
         * Track user request usage
         * @var boolean
         */
        $this->track_request =TRUE;

        /**
         * Note that track-request needs to be enabled for this to work
         * @var boolean
         */
        $this->log_responses = TRUE;     

	}

	/**
	 * Public handler
	 * @return [type] [description]
	 */
	public function index()
	{
		if($input = $this->input->post())
		{
			var_dump($input);
		}
		die;
	}



	/**
	 * All incoming request should be routed/extend this.
	 * 
	 * @param  [type] $key    [description]
	 * @return [type]         [description]
	 */
	protected function req($endpoint,$key)
	{
		if($row = $this->_validate_key($endpoint,$key))
		{
			if($this->track_request) 
				$this->_logrequest($row);

			return TRUE;
		}
		else
		{
			// Just die now, no need to bubble
			$array = ['status'=>FALSE,'message'=>'Sorry, you do not have access.'];
			echo json_encode($array);die;
		}
	}


	/**
	 * Send the data
	 * @param  [type] $endpoint [description]
	 * @param  [type] $status   [description]
	 * @param  [type] $message  [description]
	 * @param  [type] $result   [description]
	 * @return [type]           [description]
	 */
	protected function send($endpoint,$status,$message,$result)
	{
		$return = [];
		$return['status'] = $status;
		$return['message'] = $message;
		$return['result'] = $result;

		$response = json_encode($return);

		if(($this->log_responses)AND($this->track_request)) 
			$this->log($endpoint,$response);

		//die($response);		
		die('<code>'.$response.'</code>');		
	}


	/**
	 * Validates a key and acccess rights
	 * 
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	private function _validate_key($endpoint,$key)
	{
		if($row = $this->db->where('key',strtoupper($key))->get('nct_api_keys')->row())
		{
			if($row->max_allowed > $row->tot_curr_requests)
			{
				$this->key_id = $row->id;
				if($row->enabled)
				{
					return $row;
				}
			}
		}
		return FALSE;
	}


	private function _logrequest($keyrow)
	{
		$data =  ['tot_requests'=>($keyrow->tot_requests + 1),'tot_curr_requests'=>($keyrow->tot_curr_requests + 1)];

		$this->db
			->where('key',strtoupper($keyrow->key))
			->update('nct_api_keys', $data );
	}

	/**
	 * Log information in database
	 * 
	 * @param  [type] $endpoint [description]
	 * @param  [type] $response [description]
	 * @return [type]           [description]
	 */
	private function log($endpoint,$response)
	{
		$data =
		[
			'key_id'	=> $this->key_id,
			'endpoint'	=> $endpoint,
			'date'		=> date('Y-m-d H:m:s'),
			'result'	=> $response
		];
		$this->db->insert('nct_api_requests',$data);
	}

}
