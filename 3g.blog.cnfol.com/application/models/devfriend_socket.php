<?php
/*
	--|用户信息
	--|modify  2011-8-23  lifeng
*/

class DevFriend_socket extends MY_Model
{	
    function __construct()
    {
        parent::MY_Model();
    }
    
    //要新接口 创建标签   
    function addFriendTag($data)  
    {
    //	$type = "U112";
	//	var_dump($data);
		//$rs = $this->socket['passport']->senddata($type, $data);
    	return true;
    	
    }

	/*
	 --|获取个人博客好友分组
	 --|缓存key: K1078,K1079
	 */
  //  function searchFriendTag($data) 
  	function friendGroup($data, $flag=true)
    {
    
    	if(empty($data['MemberID']))
			return false;
		$type = 'B150';   //type xx
		$data['Status']     =  isset($data['Status'])? $data['Status']:'0';
		$data['StartNo']    =  isset($data['StartNo'])? $data['StartNo']:-1;
		$data['IsPublic']    =  isset($data['IsPublic'])? $data['IsPublic']:0;		
		
		if(ISCACHE && $flag)
		{
			if($data['StartNo'] == -1)
			{
				$ckey = config_item('K1078');  //xx
		//		echo 'ckey'.$ckey.'<Br>';
				$ckey = str_replace('{MemberID}',$data['MemberID'],$ckey); 
//				echo $ckey;
				$rs = $this->cache->get($ckey);
				$res = "";
				if(!$res)
				{
					$rs = $this->socket['newblog']->senddata($type, $data); 
					$rs = xmltoarray($rs);
					if($this->_checkrs($rs, $type))
					{
						$rs['FlagCode'] =  md5($rs['TtlRecords'].time());
						$this->cache->set($ckey, $rs, EXPIRETIME_4);
						return $rs;
					}
					else
					{
						return false;
					}
				}
				return $rs;
			}
			else
			{
				$ckey = config_item('K1079'); 
				$ckey  = str_replace('{MemberID}',$data['MemberID'],$ckey); 
				$ckey   = str_replace('{PageNo}',($data['StartNo']/FRIEND_GROUP_PAGESIZE+1), $ckey);				
				$rs = $this->cache->get($ckey);
				$res = "";
				//if(!$rs || ($data['FlagCode'] != $rs['FlagCode']))  //test
				if(!$res  || ($data['FlagCode'] != $rs['FlagCode']))
				{
					$rs = $this->socket['newblog']->senddata($type, $data); 
					$rs = xmltoarray($rs);
					if($this->_checkrs($rs, $type))
					{
						$getData = array();
						$getData['RetRecords']	= $rs['RetRecords'];
						$getData['Record']		= isset($rs['Record'])? $rs['Record']:array();
						$getData['FlagCode'] 	= $data['FlagCode'];
						if($getData['RetRecords'] == 1)
						{
							$getData['Record'] = array($getData['Record']);
						}
						$this->cache->set($ckey, $getData, EXPIRETIME_4);
						unset($rs);
					//	var_dump($getData['Record']);
						return $getData['Record'];
					}
					else
					{
						return false;
					}
				}
				return $rs;
			}
		}
		else
		{
			$rs = $this->socket['newblog']->senddata($type, $data); 
			$rs = xmltoarray($rs);

			if($this->_checkrs($rs, $type))
			{
				if($data['StartNo'] == -1)
				{
					return $rs['TtlRecords'];	
				}
				$getData = array();
				$getData['RetRecords']	= $rs['RetRecords'];
				$getData['Record']		= isset($rs['Record'])? $rs['Record']:array();
			}
			else
			{
				return false;
			}
			unset($rs);
			return $getData['Record'];
		}
    }    
    
    
    function delGroup()
    {
    	
    	
    	$ckey = config_item('K1078');  //xx    	
    	$ckey = str_replace('{MemberID}',$data['MemberID'],$ckey);    	   	
    	$this->cache->delete($ckey);

    }
    
    /*
     * 修改分组
     */
    function modifyGroup($data)
    {
    	
    	
    }
    /*
     * 实名认证
     */
    function realNameAuth($data)
    {
  /*  	if(ISCACHE)
    	{
	    //	$ckey	= config_item('K1003');    	
	    //	$ckey  = str_replace('{UserID}', $data, $ckey);
	    //	$rs     = $this->cache->get($ckey);    
	    	// 	$type = "XXX";
	    	if(!$rs)
	    	{

		    //	$rs = $this->socket['newblog']->senddata($type, $data);
		    	$rs = xmltoarray($rs);
		    	if($rs->_checkrs($rs))
		    	{
		    		$rs = $this->cache->set($ckey, $rs, EXPIRETIME_3);   
		    		return $rs; 		
		    	} 
		    	return false;
	    	}
    	}
    	else 
    	{
    		$rs = $this->socket['newblog']->senddata($type, $data);
    		$rs = xmltoarray($rs);
    		if($rs['Code'] == "00")
    		{
    			$rs = $rs['Record'];
    		} 
    		else 
    		{
    			return false;
    		}   		
    	}	*/
    	$rs = array('flag'=>'1', 'describle'=>'无敌分析师');
    //	$rs = "";
    	return $rs;
    	
    }
    
}    
?>