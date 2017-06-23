<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
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
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/errors.html
 */
class MY_Log extends CI_Log {

    var $_my_levels = array('ERROR' => '1', 'DEBUG' => '2', 'INFO' => '3', 'WARN' => '4', 'FATAL' => '5');

    /**
     * Constructor
     *
     * @access	public
     */
    function MY_Log() {
        $config = & get_config();
        parent::CI_Log();

        # Log name prefix
        if (isset($config['log_prefix']))
            $this->name_prefix = $config['log_prefix'];
        else
            $this->name_prefix = '';
    }

    /**
     * Write Log File
     *
     * Generally this function will be called using the global log_message() function
     *
     * @access	public
     * @param	string	the error level
     * @param	string	the error message
     * @param	bool	whether the error is a native PHP error
     * @return	bool
     */
    function write_log($level = 'error', $msg, $php_error = FALSE) {
        if ($this->_enabled === FALSE) {
            return FALSE;
        }

        $level = strtoupper($level);

        if (!isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold)) {
            return FALSE;
        }

        //$filepath = $this->log_path.'log-'.date('Y-m-d').EXT;
        //Change by avenger
        #$filepath = $this->log_path.$_SERVER['HTTP_HOST'].'log-'.date('Y-m-d').EXT;
        #Change by arno
        $filepath = $this->log_path . $this->name_prefix . date('Y-m-d-H-i') . '.log';

        $message = '';

        #if ( ! file_exists($filepath))
        #{
        #	$message .= "<"."?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
        #}

        if (!$fp = @fopen($filepath, "a")) {
            return FALSE;
        }

        #$message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";

        $message = '%' . date($this->_date_fmt) . ' ' . $level . ' ' . $msg . "\n";

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, 0666);
        return TRUE;
    }

    /**
     * Write Log File
     *
     * Generally this function will be called using the global log_message() function
     *
     * @access	public
     * @param	string	the error level
     * @param	string	the error message
     * @param	bool	whether the error is a native PHP error
     * @return	bool
     */
    function write($level = 'error', $msg) {

        if ($this->_enabled === FALSE) {
            return FALSE;
        }

        $level = strtoupper($level);

        if (!isset($this->_my_levels[$level])) {
            return FALSE;
        }

        $filepath = $this->log_path . $this->name_prefix . date('Y-m-d-H-i') . '.log';

        $message = '';

        if (!$fp = @fopen($filepath, "a")) {
            return FALSE;
        }

        $message = '%' . date($this->_date_fmt) . ' ' . $level . ' ' . $msg . "\n";

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, 0666);
        return TRUE;
    }

}

// END Log Class
?>
