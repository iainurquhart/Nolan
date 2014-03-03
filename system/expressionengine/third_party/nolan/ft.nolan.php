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
		'version' => '2.3.1'
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
		parent::__construct();
		
		$this->site_id 		= $this->EE->config->item('site_id');
		$this->asset_path	= defined('URL_THIRD_THEMES') ? URL_THIRD_THEMES . '/nolan_assets' : $this->EE->config->item('theme_folder_url') . '/third_party/nolan_assets';
		$this->drag_handle  = '&nbsp;';
		$this->nolan_nav 	= '<a class="remove_row" href="#">-</a> <a class="add_row" href="#">+</a>';
		$this->nolan_cache 	   =& $this->EE->session->cache['nolan_ft_data'];
	}
	
	
	// --------------------------------------------------------------------
	
	
	public function install()
	{
		return array(
			'nolan_license'  => ''
		);
	}
	
	// --------------------------------------------------------------------

	public function accepts_content_type($name)
	{
		return ($name == 'channel' || $name == 'grid');
	}

	// --------------------------------------------------------------------

	public function display_global_settings()
	{
		ee()->lang->loadfile('nolan');
		$nolan_license = (isset($this->settings['nolan_license'])) ? $this->settings['nolan_license'] : '';
		return form_label(lang('nolan_license'), 'nolan_license').NBS.form_input('nolan_license', $nolan_license).NBS.NBS.NBS.' ';
	}

	// --------------------------------------------------------------------

	public function save_global_settings()
	{
		return array_merge($this->settings, $_POST);
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
		return $this->display_cell($data, 'field');
	}

	public function grid_display_field($data)
	{
	   return $this->display_cell($data);
	}

	// --------------------------------------------------------------------
	
	/**
	 * grid_display_settings
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	array
	 */
	public function grid_display_settings($data)
	{
		$this->EE->lang->loadfile('nolan');
		$nolan_col_labels = (isset($data['nolan_col_labels'])) ? $data['nolan_col_labels'] : '';
		$nolan_col_names  = (isset($data['nolan_col_names']))  ? $data['nolan_col_names']  : '';
		$nolan_col_types = (isset($data['nolan_col_types'])) ? $data['nolan_col_types'] : '';
		$nolan_max_rows  = (isset($data['nolan_max_rows']))  ? $data['nolan_max_rows']  : '';

		

		return array(
			EE_Fieldtype::grid_settings_row(lang('nolan_col_labels'), form_input('nolan_col_labels', $nolan_col_labels), FALSE),
			EE_Fieldtype::grid_settings_row(lang('nolan_col_names'), form_input('nolan_col_names', $nolan_col_names), FALSE),
			EE_Fieldtype::grid_settings_row(lang('nolan_col_types'), form_input('nolan_col_types', $nolan_col_types), FALSE),
			EE_Fieldtype::grid_settings_row(lang('nolan_max_rows'), form_input('nolan_max_rows', $nolan_max_rows), FALSE)
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * display_cell
	 * 
	 * @access	public
	 * @param	mixed $data
	 * @return	void
	 */
	public function display_cell($data, $type = 'cell')
	{
		
		$this->_add_nolan_assets();
		$this->EE->load->library('table');
		$vars = array();
		
		$vars['col_labels'] = $this->get_col_attributes('nolan_col_labels');
		$vars['col_names']  = $this->get_col_attributes('nolan_col_names');
		$vars['col_types']  = $this->get_col_attributes('nolan_col_types');
		$vars['max_rows']	= (isset($this->settings['nolan_max_rows'])) ? $this->settings['nolan_max_rows'] : '';
		$vars['col_width'] = 100 / count($vars['col_labels']).'%';

		if($data != '' && !is_array($data))
		{
			// matrix is converting json quotes to entities...
			$data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
			
			$data = json_decode($data, TRUE);
		}
		elseif(is_array($data)) // comes back as array if publish page validation fails
		{
			foreach($data as $col_name => $values)
			{
				foreach($values as $key => $value)
				{
					$new_data[$key][$col_name] = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
				}
			}
			
			$data = $new_data;
			
		}
		
		$vars['row_data']    = array();
		$vars['cell_name']   = (isset($this->cell_name)) ? $this->cell_name : $this->field_name;
		$vars['drag_handle'] = $this->drag_handle;
		$vars['nav'] 		 = $this->nolan_nav;
		$vars['type'] = $type;

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
		return $this->save_cell($data);
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

		$data = (!is_array($data)) ? json_decode($data, TRUE) : $data;

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
			$data = json_encode($data);
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
	
		return $this->save_cell($data);
	
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
		return ($data != '') ? json_decode($data, TRUE) : array();
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
			
			$limit  	=  ( isset($params['limit']) ) ? (int) $params['limit'] : '';
			$offset 	=  ( isset($params['offset']) ) ? (int) $params['offset'] : '';
			$backspace 	=  ( isset($params['backspace']) ) ? (int) $params['backspace'] : '';
			$cols 		=    $this->get_col_attributes();

			$count_vars['total_nolan_cols'] = count($cols);
			$count_vars['total_nolan_rows'] = count($data);
			
			$tagdata = $this->EE->functions->var_swap($tagdata, $count_vars);
			$tagdata = $this->EE->functions->prep_conditionals($tagdata, $count_vars);

			if( $offset ) $data = array_slice($data, $offset);
			if( $limit )  $data = array_slice($data, 0, $limit);

			if( ! $data ) return ''; // offset and limit might have nulled our data array

			$i = 1;
			
			foreach($data as $key => &$item)
			{
				$item['nolan_row_count'] = $i++;

				// make sure vars are defined for each column in
				// the nolan column_labels
				foreach($cols as $col)
				{
					if( ! isset($item[$col]) )
					{
						$item[$col] = '';
					}
				}
			}

			$r = $this->EE->TMPL->parse_variables($tagdata, $data);

			if( $backspace )  $r = substr($r, 0, - $backspace);

			return $r;

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

		$this->EE->lang->loadfile('nolan');

		if (! isset($data['nolan_col_labels'])) $data['nolan_col_labels'] = '';
		if (! isset($data['nolan_col_names'])) $data['nolan_col_names'] = '';
		if (! isset($data['nolan_col_types'])) $data['nolan_col_types'] = '';
		if (! isset($data['nolan_max_rows'])) $data['nolan_max_rows'] = '';


		$this->EE->table->add_row(
			lang('nolan_col_labels'),
			 form_input('nolan_col_labels', $data['nolan_col_labels'])		
		);

		$this->EE->table->add_row(
			lang('nolan_col_names'),
			 form_input('nolan_col_names', $data['nolan_col_names'])		
		);
		$this->EE->table->add_row(
			lang('nolan_col_types'),
			 form_input('nolan_col_types', $data['nolan_col_types'])		
		);

		$this->EE->table->add_row(
			lang('nolan_max_rows'),
			 form_input('nolan_max_rows', $data['nolan_max_rows'])		
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
		if (! isset($data['nolan_col_types'])) $data['nolan_col_types'] = '';
		if (! isset($data['nolan_max_rows'])) $data['nolan_max_rows'] = '';
		
		return array(
			array(lang('nolan_col_labels'), form_input('nolan_col_labels', $data['nolan_col_labels'], 'class="matrix-textarea"')),
			array(lang('nolan_col_names'), form_input('nolan_col_names', $data['nolan_col_names'], 'class="matrix-textarea"')),
			array(lang('nolan_col_types'), form_input('nolan_col_types', $data['nolan_col_types'], 'class="matrix-textarea"')),
			array(lang('nolan_max_rows'), form_input('nolan_max_rows', $data['nolan_max_rows'], 'class="matrix-textarea"'))
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
		return array(
			'nolan_col_labels' => $data['nolan_col_labels'],
			'nolan_col_names' => $data['nolan_col_names'],
			'nolan_col_types' => $data['nolan_col_types'],
			'nolan_max_rows' => $data['nolan_max_rows']
		);
	}


	// --------------------------------------------------------------------


	public function update($from)
	{
	    if ($from == $this->info['version']) return FALSE;

	    if (version_compare($from, '2', '<'))
	    {

	    	// get all our nolan columns
	        $nolan_cols = $this->EE->db->get_where(
	        	'matrix_cols', 
	        	 array('col_type' => 'nolan')
	        )->result_array();

	        // each column in matrix_data needs to be updated
	        foreach($nolan_cols as $col)
	        {

	        	$nolan_rows = $this->EE->db->get_where(
	        		'matrix_data', 
	        		 array(
	        		 	'col_id_'.$col['col_id'].' IS NOT NULL' => NULL
	        		 )
	        	)->result_array();


	        	foreach($nolan_rows as $row)
	        	{

	        		$old_data = @unserialize($row['col_id_'.$col['col_id']]);

	        		if(is_array($old_data))
	        		{

						$this->EE->db->update(
							'matrix_data', 
							array(
								'col_id_'.$col['col_id'] => (string) json_encode( $old_data )
							), 
							array('row_id' => $row['row_id'])
						);

	        		}

	        	}
	        	
	        }

	        $this->EE->db->update(
				'fieldtypes', 
				array('has_global_settings' => 'y'), 
				array('name' => 'nolan')
			);

	    }

	    return TRUE;
	}




	
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Load nolan CSS
	 */
	private function _add_nolan_assets()
	{
		if (! isset($this->nolan_cache['assets_added']) )
		{
			$this->nolan_cache['assets_added'] = 1;
			
			$this->EE->cp->add_to_foot('
				<link type="text/css" href="'.$this->asset_path.'/css/nolan.css" rel="stylesheet" />
				<script src="'.$this->asset_path.'/js/jquery.roland.js?v2.2"></script>
				<script src="'.$this->asset_path.'/js/nolan.js?v2.2"></script>
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

/* End of file ft.nolan.php */
/* Location: ./system/expressionengine/third_party/nolan/ft.nolan.php */