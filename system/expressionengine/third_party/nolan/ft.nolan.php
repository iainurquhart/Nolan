<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * Nolan Fieldtype
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Iain Urquhart
 * @link		http://iain.co.nz
 * @copyright 	Copyright (c) 2011 Iain Urquhart
 * @license   	All rights reserved
 */

// ------------------------------------------------------------------------

class Nolan_ft extends EE_Fieldtype
{
	public $info = array(
		'name' => 'Nolan',
		'version' => '1.0.0'
	);
	
	var $has_array_data = TRUE;
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * constructor
	 * 
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		parent::EE_Fieldtype();
		
		$this->site_id 		= $this->EE->config->item('site_id');
		$this->asset_path 	= $this->EE->config->item('theme_folder_url').'third_party/nolan_assets/';
		$this->drag_handle  = '&nbsp;';
		$this->nolan_nav 	= '<a class="remove_row" href="#">-</a> <a class="add_row" href="#">+</a>';
		$this->cache 	   =& $this->EE->session->cache['nolan_ft_data'];
	}
	
	
	// --------------------------------------------------------------------
	
	
	public function install()
	{
		return array();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * display_field
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	void
	 */
	public function display_field($data)
	{
		$vars = array();
		return $this->EE->load->view('field', $vars, TRUE);
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * display_cell
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	void
	 */
	public function display_cell($data)
	{
		
		$this->_add_nolan_assets();
		$this->EE->load->library('table');
		$vars = array();
		
		$vars['col_labels'] = $this->get_col_attributes('nolan_col_labels');
		$vars['col_names']  = $this->get_col_attributes('nolan_col_names');
		
		if($data != '' && !is_array($data))
		{
			$data = unserialize( html_entity_decode($data, ENT_COMPAT, 'UTF-8') );
		}
		elseif(is_array($data)) // comes back as array if publish page validation fails
		{
			foreach($data as $col_name => $values)
			{
				foreach($values as $key => $value)
				{
					$new_data[$key][$col_name] = html_entity_decode($value, ENT_COMPAT, 'UTF-8');
				}
			}
			
			$data = $new_data;
			
		}
		
		$vars['row_data']    = array();
		$vars['cell_name']   = $this->cell_name;
		$vars['drag_handle'] = $this->drag_handle;
		$vars['nav'] 		 = $this->nolan_nav;

		if(is_array($data))  $vars['row_data'] = $this->process_array($vars['col_names'], $data);

		return $this->EE->load->view('cell', $vars, TRUE);
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * save (not used by our cell)
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	mixed $data
	 */
	public function save($data)
	{
		return $data;
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * save_cell
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	mixed $data
	 */
	public function save_cell($data)
	{

		$nolan_col_names = $this->get_col_attributes('nolan_col_names');
		
		$new_data = array();

		if(count($data))
		{
			foreach($data as $col_name => $values)
			{
				foreach($values as $key => $value)
				{
					$new_data[$key][$col_name] = $value;
				}
			}
		}
		
		$data = $this->process_array($nolan_col_names, $new_data);
		
		if(is_array($data))
		{
			$data = serialize($data);
		}
				
		return $data;
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * post_save
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	void
	 */
	function post_save($data)
	{
	
		$data = $this->cache['data'][$this->settings['field_id']];
		return '';
	
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * pre_process
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	array
	 */
	public function pre_process($data)
	{
		return ($data != '') ? unserialize($data) : array();
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * replace_tag
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @param	mixed $params = array()
	 * @param	mixed $tagdata = FALSE
	 * @return	void
	 */
	public function replace_tag($data, $params = array(), $tagdata = FALSE)
	{

		if($tagdata)
		{
			
			$count_vars['total_nolan_cols'] = count($this->get_col_attributes());
			$count_vars['total_nolan_rows'] = count($data);
			
			$tagdata = $this->EE->functions->var_swap($tagdata, $count_vars);
			$tagdata = $this->EE->functions->prep_conditionals($tagdata, $count_vars);
			
			$i = 1;
			
			foreach($data as &$item)
			{
				$item['nolan_row_count'] = $i++;
			}
			
			return $this->EE->TMPL->parse_variables($tagdata, $data);

		}
		else
		{
			return 1; // for simple {if fieldname} conditionals
		}
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * display_settings
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	void
	 */
	public function display_settings($data)
	{
		$vars = array();

		$this->EE->table->add_row(
			array('data' => $this->EE->load->view('field_settings', $vars, TRUE), 'colspan' => 2)				
		);
			
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * display_settings
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	void
	 */
	public function display_cell_settings($data)
	{
	
		$this->EE->lang->loadfile('nolan');
	
		if (! isset($data['nolan_col_labels'])) $data['nolan_col_labels'] = '';
		if (! isset($data['nolan_col_names'])) $data['nolan_col_names'] = '';
		
		return array(
			array(lang('col_labels'), form_input('nolan_col_labels', $data['nolan_col_labels'], 'class="matrix-textarea"')),
			array(lang('col_names'), form_input('nolan_col_names', $data['nolan_col_names'], 'class="matrix-textarea"'))
		);
	}
		
		
	// --------------------------------------------------------------------
 		
 		
 	/**
	 * display_settings
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	void
	 */
	public function save_settings($data)
	{
		
		$options = $this->EE->input->post('options');
		
		if(is_array($options))
		{
			$options = serialize($options);
		}
				
		return array(
			'nolan_options' => $options,
		);
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Load nolan CSS
	 */
	private function _add_nolan_assets()
	{
		if (! isset($this->cache['assets_added']) )
		{
			$this->cache['assets_added'] = 1;
			
			$this->EE->cp->add_to_head('
				<link type="text/css" href="'.$this->asset_path.'/css/nolan.css" rel="stylesheet" />
				<script src="'.$this->asset_path.'/js/jquery.roland.js"></script>
				<script src="'.$this->asset_path.'/js/nolan.js"></script>
			');
			
			$this->EE->cp->add_js_script('ui', 'sortable');
		}
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * trim_array_items
	 * removes white space from an explode of col values
	 * 
	 * @access	private
	 * @param	array $array
	 * @return	array
	 */
	private function trim_array_items( $array = array() )
	{
		foreach($array as &$item)
		{
			$item = trim($item);
		}
		return $array;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * process_array
	 * 
	 * @access	private
	 * @param	mixed $data
	 * @return	void
	 */
	private function process_array($keys, $arrays)
	{
	    $final = array();
	
	    foreach($arrays as $a)
	    {
	        $next = array();
	        foreach($keys as $k)
	        {
	            $next[$k] = isset($a[$k]) ? $a[$k] : '';
	        }
	        $final[] = $next;
	    }
	
	    return $final;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * get_col_attributes
	 *
	 * Col names/labels are defined via simple pipe delimited values
	 * this method takes a string like: foo | bar | barley
	 * and returns an array:
	 * Array
	 *	(
	 *	    [0] => foo
	 *	    [1] => bar
	 *	    [2] => barley
	 *	)
	 * 
	 * 
	 * nolan_col_names and nolan_col_labels are stored the same
	 * @access	private
	 * @param	string $col_key
	 * @return	array
	 */
	private function get_col_attributes($col_key = 'nolan_col_names')
	{
		$col_attributes = (isset($this->settings[$col_key])) ? explode('|', $this->settings[$col_key]) : array();
		
		if(count($col_attributes))
		{
			$col_attributes  = $this->trim_array_items($col_attributes);
		}
		
		return $col_attributes;
	}
	
	

}

/* End of file ft.taxonomy.php */
/* Location: ./system/expressionengine/third_party/taxonomy/ft.taxonomy.php */