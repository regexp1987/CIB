<?php

Class Magellan_Settings {

	public $active, $static, $admin_head, $admin_body, $hidden, $default;
    const OPTION_PREFIX = 'magellan_';
	
    protected static $_instance = null;
        
    /* Return instance of Class */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
	function __construct() 
	{
		$this->parse_default_settings();
        $this->create_default_setting_hash_table();
		
		add_action('init', array($this, 'late_update_settings_data'));
	}
	
	function late_update_settings_data() 
	{
		$this->admin_head = apply_filters('magellan_setup_admin_head', $this->admin_head);
		$this->admin_body = apply_filters('magellan_setup_admin_body', $this->admin_body);
		$this->static = apply_filters('magellan_setup_admin_static', $this->static);
		$this->hidden = apply_filters('magellan_setup_admin_hidden', $this->hidden);
	}
	
	function parse_default_settings()
	{
		global $_settings_static, $_settings_admin_head, $_settings_admin_body, $_settings_hidden;
		$this->admin_head = $_settings_admin_head;
		$this->admin_body = $_settings_admin_body;
		$this->static = $_settings_static;
		$this->hidden = $_settings_hidden;
	}
	

	public static function store_settings($data)
	{        
        $data = self :: filter_settings($data);
        
        //save settings to wp options
        foreach($data as $key => $val)
        {
            update_option(self :: OPTION_PREFIX . esc_attr($key), $val);
        }
       
	}
    
	
	public function update_single($name, $value)
	{
        update_option(self :: OPTION_PREFIX . esc_attr($name), $value);
        
        //legacy remove later
        $this->active[$name] = $value;
        self :: store_settings($this->active);
	}
    
    public function get_single($name, $allow_cache)
    {
        $default = (!empty($this->default[$name]) ? $this->default[$name] : false );
        
        if($allow_cache)
        {
            $value = get_option(self :: OPTION_PREFIX . esc_attr($name), $default);
        }
        else
        {
            $value = $default;
        }
        
        return $value;
    }
	
    /*
     * Merge static, hidden and admin body settings to get a default value list
     */
    public function create_default_setting_hash_table()
    {
        
        foreach($this->static as $key => $val)
		{
		    $this->default[$key] = $val;
		}
        
        
        foreach($this->admin_body as $section)  //insert the options into settings object
        {		
            foreach($section as $cf) 
            {
                foreach($cf as $c) 
                {
                    $this->default[$c['slug']] = $c['default'];
                }   
            }
		}
        
        foreach($this->hidden as $key => $val)
		{
		    $this->default[$key] = $val;
		}
        
    }
    
    public function export_settings()
    {
        $data = array();
        
        foreach($this->admin_body as $section)  //gets the options
        {		
            foreach($section as $cf) 
            {
                foreach($cf as $c) 
                {
                    $data[$c['slug']] = get_option(self :: OPTION_PREFIX . esc_attr($c['slug']));
                }
            }
		}
        
        return json_encode($data);
    }
    
    public static function import_settings($data)
    {
        $settings = json_decode($data, true);
        self :: store_settings($settings);
    }
	
    public function reset_settings()
    {
        foreach($this->admin_body as $section)  //deletes the options
        {		
            foreach($section as $cf) 
            {
                foreach($cf as $c) 
                {
                    delete_option(self :: OPTION_PREFIX . esc_attr($c['slug']));                    
                }   
            }
		}
    }
    
    public function get_visual_editor_settings()
    {

        $return = array('body' => array(), 'head' => array());
        
        if(!empty($this->admin_body['visual_editor']))
        {
            $return['body'] = $this->admin_body['visual_editor'];
        }
        if(!empty($this->admin_head['visual_editor']))
        {
            $return['head'] = $this->admin_head['visual_editor']['children'];
        }
        return $return;
    }
    
	public static function filter_settings($array, $direction = false)
	{
		if(is_array($array) || is_object($array))
		{
			foreach($array as $key => $val) 
			{
				$filtered = self :: filter_settings($val, $direction);
				if(is_object($array))
				{
					$array->$key = $filtered;
				}
				else
				{
					$array[$key] = $filtered;
				}
			}
		}
		else
		{
			if(!$direction)
			{
                $array = addslashes(trim($array));
			}
			else
			{
				$array = stripslashes($array);
			}
		}
		return $array;
	}
}

function MAGELLAN_SETTINGS_INSTANCE() {
	return Magellan_Settings::instance();
}


?>