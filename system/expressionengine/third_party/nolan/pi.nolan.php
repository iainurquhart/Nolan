<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( !function_exists('ee') )
{
	function ee()
	{
		static $EE;
		if ( ! $EE) $EE = get_instance();
		return $EE;
	}
}

$plugin_info = array(
    'pi_name'         => 'Nolan',
    'pi_version'      => '1.0',
    'pi_author'       => 'Iain Urquhart',
    'pi_author_url'   => 'http://iain.co.nz/',
    'pi_description'  => 'Helpers for working with the Nolan Fieldtype',
    'pi_usage'        => Nolan::usage()
);


class Nolan {

	// just return the format method for now.
	public function __construct()
	{
		$this->return_data = $this->format();
	}

	// text format can be 'xhtml', 'markdown', 'br', 'none', or 'lite'
	// defaults to xhtml
	public function format()
	{

		ee()->load->library('typography');
		ee()->typography->initialize();

		$text_format = ee()->TMPL->fetch_param('text_format', 'xhtml');

		return ee()->typography->parse_type( ee()->TMPL->tagdata, array('text_format' => $text_format) );
	}

    // --------------------------------------------------------------------
	
	/**
	* Usage
	*
	* Plugin Usage
	*
	* @access	public
	* @return	string
	*/
	function usage()
	{
		ob_start(); 
		?>
		{exp:nolan:xhtml}Text to process as xhtml{/exp:nolan:xhtml}
		{exp:nolan:auto_br}Text to process as auto br{/exp:nolan:auto_br}

		<?php
		$buffer = ob_get_contents();
	
		ob_end_clean(); 

		return $buffer;
	}

	// --------------------------------------------------------------------

}

/* End of file pi.nolan.php */
/* Location: ./system/expressionengine/third_party/nolan/pi.nolan.php */