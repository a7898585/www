<?php
/*
	--|author leicc
	—-|个人博客的相关操作
	--|包括获取个人博客列表、添加个人博客
	--|合法性检查 以及获取个人博客信息
*/
class Blogonline_socket extends MY_Model
{	
    function Blogonline_socket()
    {
        parent::MY_Model();
    }
	
	/*
	 --|获取个人博客直播主题列表
	 */
	function getOnlineSubjectList($data)
	{
		$type = 'B480';
		$data['StartNo']    =  isset($data['StartNo'])? $data['StartNo']:0;
		$data['QryCount']   =  isset($data['QryCount'])? $data['QryCount']:10;
		$rs = $this->socket['newblog']->senddata($type, $data);

		$rs = xmltoarray($rs);

		if($this->_checkrs($rs, $type))
		{
			if($data['StartNo'] == -1)
			{
				return isset($rs['TtlRecords'])? $rs['TtlRecords']:0;
			}

			if(isset($rs['RetRecords']) && $rs['RetRecords'] > 0)
			{
				$rs['Record'] = ($rs['RetRecords'] == 1)? array(0=>$rs['Record']):$rs['Record'];
                return $rs;
			}
		}
		return false;
	}
    
    //删除博客直播主题
    function delOnlineSubject($data)
    {
        $type = 'B482';
		$rs = $this->socket['newblog']->senddata($type, $data);

		$rs = xmltoarray($rs);

		if($this->_checkrs($rs, $type))
		{
			return true;
		}
		return false;
    }

    //增修博客直播主题
    function addOnlineSubject($data)
    {
        $type = 'B481';
		$rs = $this->socket['newblog']->senddata($type, $data);

		$rs = xmltoarray($rs);

		if($this->_checkrs($rs, $type))
		{
			return true;
		}
		return false;
    }

    //获取主题内容
    function getOnlineSubjectBlock($data)
    {
        $type = 'B483';
		$rs = $this->socket['newblog']->senddata($type, $data);

		$rs = xmltoarray($rs);

		if($this->_checkrs($rs, $type))
		{
			if($data['StartNo'] == -1)
			{
				return isset($rs['TtlRecords'])? $rs['TtlRecords']:0;
			}

			if(isset($rs['RetRecords']) && $rs['RetRecords'] > 0)
			{
				$rs['Record'] = ($rs['RetRecords'] == 1)? array(0=>$rs['Record']):$rs['Record'];
                return $rs['Record'];
			}
		}
		return false;
    }

    //新增主题内容
    function addOnlineSubjectBlock($data)
    {
        $type = 'B484';
		$rs = $this->socket['newblog']->senddata($type, $data);

		$rs = xmltoarray($rs);

		if($this->_checkrs($rs, $type))
		{
			return true;
		}
		return false;
    }

    //删除直播主题内容板块
    function delOnlineSubjectBlock($data)
    {
        $type = 'B485';
		$rs = $this->socket['newblog']->senddata($type, $data);

		$rs = xmltoarray($rs);

		if($this->_checkrs($rs, $type))
		{
			return true;
		}
		return false;
    }
}
//end class
?>