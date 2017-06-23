<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CnfolLog Class for Linux
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/errors.html
 */
class CI_CnfolLog {

	var $_path;
	var $_date_fmt	= 'Y-m-d H:i:s';
	var $_enabled = TRUE;
	var $_code = '';
	var $_rotate_limit = 600;
	var $_levels = array('DEBUG'=>1,'INFO'=>2,'ERROR'=>3,'FATAL'=>4,'WARN'=>5);


	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function CI_CnfolLog()
	{
		$config =& get_config();

		$this->_path = ($config['log_path'] != '') ? $config['log_path'] : BASEPATH.'logs/';

		if ( ! is_dir($this->_path) OR ! is_really_writable($this->_path))
		{
			$this->_enabled = FALSE;
		}

		if ($config['log_date_format'] != '')
		{
			$this->_date_fmt = $config['log_date_format'];
		}

		# Log name prefix
		if(isset($config['log_prefix']))
		{
			$this->name_prefix = $config['log_prefix'];
		}
		else
		{
			$this->name_prefix = '';
		}
		# Log project info
		if(isset($config['log_code']))
		{
			$this->_code = $config['log_code'];
		}
		else
		{
			$this->_code = '';
		}
		if(isset($config['log_rotate_size']) && $config['log_rotate_size']!='')
		{
			$this->_rotate_limit = $config['log_rotate_size'];
		}
		else
		{
			if(isset($config['log_rotate_time']))
			{
				$this->_rotate_limit = $config['log_rotate_time'];
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Write Log File
	 *
	 * @access	public
	 * @param	string	the error level
	 * @param	string	the error message
	 * @return
	 */
	function write($level, $msg)
	{
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}

		$level = strtoupper($level);

		if ( ! isset($this->_levels[$level]))
		{
			return FALSE;
		}

		$log = '%'.date('Y-m-d H:i:s').' '.$level.' '.$this->_code.' - '.$msg;

		$bin = '/usr/sbin/rotatelogs';

		$format = '%Y-%m-%d-%H-%M';

		$file = $this->_path.'/'.$this->name_prefix.'_'.$format.'.log';

		$rotate = $this->_rotate_limit;

		return `echo "$log" | $bin $file $rotate`;
	}

}
// END CnfolLog Class

/* End of file CnfolLog.php */
/* Location: ./system/libraries/CnfolLog.php */