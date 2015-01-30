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
class Products_admin_filter_m extends MY_Model
{

    /**
     * The default table for this model
     * @var string
     */
    public $_table = 'nct_products';

    public function __construct()
    {
        parent::__construct();
    }



    protected function _prepare_filter($filter = array())
    {

        $my_filter = array();

        if (array_key_exists('f_filter', $filter))
        {
            //well we need this anyway
            $my_filter['f_filter']['action'] = 'join';
            $my_filter['f_filter']['key'] = '';            
            $my_filter['f_filter']['value'] = (int) $filter['f_filter'];        
        }


        //need to add this on others
        // FEATURED
        if (array_key_exists('f_featured', $filter))
        {
            switch($filter['f_featured'])
            {
                case 'yes':
                    $my_filter['f_featured']['action'] = 'where';
                    $my_filter['f_featured']['key'] = 'featured';
                    $my_filter['f_featured']['value'] = 1;
                    break;
                case 'no':
                    $my_filter['f_featured']['action'] = 'where';
                    $my_filter['f_featured']['key'] = 'featured';
                    $my_filter['f_featured']['value'] = 0;
                    break;   
                case 'all':                    
                default:
                    //do nothing
                    break;                                                             
            }

        }
        
        if (array_key_exists('search', $filter))
        {
            //it cannot be blank
            if(trim($filter['search']) != "")
            {
                //create the filter
                 $my_filter['search'] = array(
                    'action'=>'like',
                    'key'=>'name',
                    'value'=>trim($filter['search']),
                 );
            }
        }
        if (array_key_exists('status', $filter))
        {
            switch($filter['status'])
            {
                case 'active':
                    $my_filter['status']['action'] = 'where';
                    $my_filter['status']['key'] = 'deleted';
                    $my_filter['status']['value'] = NULL;
                    break;
                case 'deleted':
                    $my_filter['status']['action'] = 'where';
                    $my_filter['status']['key'] = 'deleted !=';
                    $my_filter['status']['value'] = 'NULL';
                    break;   
                case 'all':                    
                default:
                    //do nothing
                    break;                                                             
            }

        }


        if (array_key_exists('visibility', $filter))
        {
            switch($filter['visibility'])
            {
                case 'on':
                    $my_filter['visibility']['action'] = 'where';
                    $my_filter['visibility']['key'] = 'public';
                    $my_filter['visibility']['value'] = 1;
                    break;
                case 'off':
                    $my_filter['visibility']['action'] = 'where';
                    $my_filter['visibility']['key'] = 'public';
                    $my_filter['visibility']['value'] = 0;
                    break;                    
                case 'all':                    
                default:
                    //no filter applied
                    break;                                                             
            }

        }


        if (array_key_exists('order_by', $filter))
        {
            $my_filter['order'] = array();

            //create the filter
            $my_filter['order'] = array(
                'action'=>'order_by',
                'key'=>trim($filter['order_by']),
                'value'=>'asc', //asc by def
            );
        
            //now which direction
            if (array_key_exists('order_by_order', $filter))
            {
                //create the filter
                $my_filter['order']['value'] = $filter['order_by_order'];
            }
   
        }


        return $my_filter;
    }


    /**
     * Admin Count Filter
     *
     * @param  array  $filter [description]
     * @return [type]         [description]
     */
    public function filter_count($filter = array())
    {
        $this->reset_query();

        $new_filters = $this->_prepare_filter($filter);

        //join
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $value = (int)$filter['value'];
            if($action=='join')
            {
                $result = $this->db->where('category_id', $value)->get('nct_categories_products')->result();
                $ids = array();
                foreach($result as $k=>$v)
                {
                    $ids[] = $v->product_id;
                }
                if(count($ids))
                {
                    $this->where_in('id',  $ids);                       
                }    
                else 
                {
                    return 0;
                }  
                //$ids = array(1, 2, 6);
                //$this->db->where_in('id', $ids);                
                break;
            }
        }


        //where+like
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if(($action=='where')|| ($action=='like'))
                $this->$action($key,$value);
        }

        //order bys
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if($action=='order_by')
                $this->$action($key,$value);
        }      


        $this->from($this->_table);


        return $this->count_all_results();
    }

    /**
     * Override MY_Model as we have hidden and deleted
     *
     * @return [type] [description]
     */
    public function count_all()
    {
        $filter = array();
        //$filter['deleted'] = NULL;
        $count = $this->count_by($filter);
        return $count;
    }


    public function filter($filter = array() , $limit, $offset = 0)
    {
        $this->reset_query();

        $new_filters = $this->_prepare_filter($filter);

        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $value = (int)$filter['value'];
            if($action=='join')
            {
                $result = $this->db->where('category_id', $value)->get('nct_categories_products')->result();
                $ids = array();
                foreach($result as $k=>$v)
                {
                    $ids[] = $v->product_id;
                }

                if(count($ids))
                {
                    $this->where_in('id',  $ids);                       
                }    
                else 
                {
                    return array();
                }                          
                break;
            }
        }


        //where+like
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if(($action=='where')|| ($action=='like'))
                $this->$action($key,$value);
        }

        //order bys
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if($action=='order_by')
                $this->$action($key,$value);
        }      

        $this->limit( $limit , $offset );

        return $this->get_all();
    }


}