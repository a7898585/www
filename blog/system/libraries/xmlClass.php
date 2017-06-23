<?php
class CI_xmlClass{
	
	private $xml = '';

	function __construct($string='')
	{

		if (!empty($string)) 
		{
			$this->xml = new SimpleXMLElement($string);
		}
	}

	function loadStr($string)
	{

		if (!isset($string) || empty($string))
		{
			return false;
		}
		$rs = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);

		if ($rs) 
		{
			$this->_objectToArray($rs);
			return $rs;
		}
		else
		{
			return false;
		}
	}

	function loadFile($file)
	{

		if (!isset($file) || empty($file))
		{
			return false;
		}
		elseif (!file_exists($file))
		{
			return false;
		}
		$rs = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOCDATA);
		
		if ($rs) 
		{
			$this->_objectToArray($rs);
			return $rs;
		}
		else
		{
			return false;
		}
	}

	function getChild($node)
	{
		if (!isset($node) || empty($node))
		{
			return false;
		}
		$rs = $this->xml->xpath($node);

		if ($rs) 
		{
			$this->_objectToArray($rs);
			return $rs;
		}
		else
		{
			return false;
		}
	}

	private function _objectToArray(&$object) 
	{
		if(!empty($object))
		{
			$object = (array)$object;
			foreach ($object as $key => $value)
			{
				if (is_object($value)||is_array($value))
				{   
					$this->_objectToArray($value);
					$object[$key] = $value;
				} 
			}
		}
		else
		{
			$object = '';
		}

	}
}
?>