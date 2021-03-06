﻿<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
  +----------------------------------------------------------------------+
  | SofeeFramework for PHP 4                                             |
  +----------------------------------------------------------------------+
  | Copyright (c) 2004-2005 Sofee Development Team.(http://www.sofee.cn) |
  +----------------------------------------------------------------------+
  | This source file is subject to version 1.00 of the Sofee license,    |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.sofee.cn/license/1_00.txt.                                |
  | If you did not receive a copy of the Sofee license and are unable to |
  | obtain it through the world-wide-web, please send a note to          |
  | license@sofee.cn so we can mail you a copy immediately.              |
  +----------------------------------------------------------------------+
  | Author: Justin Wu <ezdevelop@gmail.com>                              |
  +----------------------------------------------------------------------+
*/

/* $Id: SofeeXmlParser.php,v 1.3 2005/05/30 06:30:14 wenlong Exp $ */

/**
* Sofee XML Parser class - This is an XML parser based on PHP's "xml" extension.
*
* The SofeeXmlParser class provides a very simple and easily usable toolset to convert XML
* to an array that can be processed with array iterators.
*
* @package        SofeeFramework
* @access        public
* @version        $Revision: 1.1 $
* @author        Justin Wu <wenlong@php.net>
* @homepage        http://www.sofee.cn
* @copyright    Copyright (c) 2004-2005 Sofee Development Team.(http://www.sofee.cn)
* @since        2005-05-30
* @see            PEAR:XML_Parser | SimpleXML extension
*/
class CI_SofeeXmlParser {

    /**
    * XML parser handle
    *
    * @var        resource
    * @see        xml_parser_create()
    */
    var $parser;

    /**
    * source encoding
    *
    * @var        string
    */
    var $srcenc;

    /**
    * target encoding
    *
    * @var        string
    */
    var $dstenc;

    /**
    * the original struct
    *
    * @access    private
    * @var        array
    */
    var $_struct = array();

    /**
    * Constructor
    *
    * @access        public
    * @param        mixed        [$srcenc] source encoding
    * @param        mixed        [$dstenc] target encoding
    * @return        void
    * @since
    */
    function CI_SofeeXmlParser($srcenc = null, $dstenc = null) {
        $this->srcenc = $srcenc;
        $this->dstenc = $dstenc;

        // initialize the variable.
        $this->parser = null;
        $this->_struct = array();
    }

    /**
    * Free the resources
    *
    * @access        public
    * @return        void
    **/
    function free() {
        if (isset($this->parser) && is_resource($this->parser)) {
            xml_parser_free($this->parser);
            unset($this->parser);
        }
    }

    /**
    * Parses the XML file
    *
    * @access        public
    * @param        string        [$file] the XML file name
    * @return        void
    * @since
    */
    function parseFile($file) {
        $data = @file_get_contents($file) or die("Can't open file $file for reading!");
        $this->parseString($data);
    }

    /**
    * Parses a string.
    *
    * @access        public
    * @param        string        [$data] XML data
    * @return        void
    */
    function parseString($data) {
        if ($this->srcenc === null) {
            $this->parser = @xml_parser_create() or die('Unable to create XML parser resource.');
        } else {
            $this->parser = @xml_parser_create($this->srcenc) or die('Unable to create XML parser resource with '. $this->srcenc .' encoding.');
        }

        if ($this->dstenc !== null) {
            @xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, $this->dstenc) or die('Invalid target encoding');
        }
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);    // lowercase tags
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);        // skip empty tags
        if (!xml_parse_into_struct($this->parser, $data, &$this->_struct)) {
            printf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($this->parser)),
                    xml_get_current_line_number($this->parser)
            );
            $this->free();
            exit();
        }

        $this->_count = count($this->_struct);
        $this->free();
    }

    /**
    * return the data struction
    *
    * @access        public
    * @return        array
    */
    function getTree() {
        $i = 0;
        $tree = array();

        $tree = $this->addNode(
            $tree,
            $this->_struct[$i]['tag'],
            (isset($this->_struct[$i]['value'])) ? $this->_struct[$i]['value'] : '',
            (isset($this->_struct[$i]['attributes'])) ? $this->_struct[$i]['attributes'] : '',
            $this->getChild($i)
        );

        unset($this->_struct);
        return ($tree);
    }

    /**
    * recursion the children node data
    *
    * @access        public
    * @param        integer        [$i] the last struct index
    * @return        array
    */
    function getChild(&$i) {
        // contain node data
        $children = array();

        // loop
        while (++$i < $this->_count) {
            // node tag name
            $tagname = $this->_struct[$i]['tag'];
            $value = isset($this->_struct[$i]['value']) ? $this->_struct[$i]['value'] : '';
            $attributes = isset($this->_struct[$i]['attributes']) ? $this->_struct[$i]['attributes'] : '';

            switch ($this->_struct[$i]['type']) {
                case 'open':
                    // node has more children
                    $child = $this->getChild($i);
                    // append the children data to the current node
                    $children = $this->addNode($children, $tagname, $value, $attributes, $child);
                    break;
                case 'complete':
                    // at end of current branch
                    $children = $this->addNode($children, $tagname, $value, $attributes);
                    break;
                case 'cdata':
                    // node has CDATA after one of it's children
                    $children['value'] .= $value;
                    break;
                case 'close':
                    // end of node, return collected data
                    return $children;
                    break;
            }

        }
        //return $children;
    }

    /**
    * Appends some values to an array
    *
    * @access        public
    * @param        array        [$target]
    * @param        string        [$key]
    * @param        string        [$value]
    * @param        array        [$attributes]
    * @param        array        [$inner] the children
    * @return        void
    * @since
    */
    function addNode($target, $key, $value = '', $attributes = '', $child = '') {
        if (!isset($target[$key]['value']) && !isset($target[$key][0])) {
            if ($child != '') {
                $target[$key] = $child;
            }
            if ($attributes != '') {
                foreach ($attributes as $k => $v) {
                    $target[$key][$k] = $v;
                }
            }

            $target[$key]['value'] = $value;
        } else {
            if (!isset($target[$key][0])) {
                // is string or other
                $oldvalue = $target[$key];
                $target[$key] = array();
                $target[$key][0] = $oldvalue;
                $index = 1;
            } else {
                // is array
                $index = count($target[$key]);
            }

            if ($child != '') {
                $target[$key][$index] = $child;
            }

            if ($attributes != '') {
                foreach ($attributes as $k => $v) {
                    $target[$key][$index][$k] = $v;
                }
            }
            $target[$key][$index]['value'] = $value;
        }
        return $target;
    }

}

// END Memcache Class

/* End of file Memcache.php */
/* Location: ./system/libraries/Memcache.php */
