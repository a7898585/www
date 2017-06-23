<?php

class Attachment_socket extends MY_Model {

    function Attachment_socket() {
        parent::MY_Model();
    }

    /**
     * 功能：    根据用户ID或者用户群组获取附件配置列表
     * @Type     0:用户ID  1:组户群组
     * @QryData  内容依Type而异 空白或0表示全部
     * @return   array
     */
    function getConfigList($data) {
        $type = 'U400';
        $qrytype = $data['Type'];

        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs, $type)) {
            return $rs['Record'];
        } else {
            return false;
        }
    }

    /**
     * 功能：    根据用户ID获取已使用空间大小
     * @userID   用户ID
     * @return   array
     */
    function getEmploySpace($data) {
        $type = 'U401';

        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            return $rs['Record']['Size'];
        } else {
            return false;
        }
    }

    /**
     * 功能：                新增附件
     * @data
      UserID              用户ID
      SaveType            储存类别(选填)  1:共享 2:博客 3:贴吧
      AttachmentSortID    档案类别        1:我的视频 2:我的文档 3:我的图片
      Name                附件名
      Size                附件大小
      URL                 附件地址
      Remark              文件描述(选填)
      MemberID            博客ID(选填)
      AlbumID             相册ID(选填)
     * @return               bool
     */
    function addAttachment($data) {
        $type = 'U410';
        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            return true;
        } else {
            return false;
        }
    }

}

//end class
?>