<?php

/**
  +------------------------------------------------------------------------------
 * 处理swfupload上传的文件
  +------------------------------------------------------------------------------
 * @category  
 * @package  Class
 * @author    jianglw
 */
class Flashupload {

    private $upload_name = '';
    public $extension = '';
    public $maxbytes = '';
    public $allowextension = '';

    const OVER_MAXIMUM = 500;
    const NOT_ALLOWED_TYPE = 403;

    function __construct($arr) {

        $this->upload_name = $arr['name'];
        $this->maxbytes = $arr['maxbytes'];
        $path_info = pathinfo($_FILES[$this->upload_name]['name']);
        $this->allowextension = ',' . $arr['allowextension'] . ',';
        $this->extension = $path_info["extension"];
    }

    /**
     * public 保存方法
     *
     * @param string $destination
     * @return mixed
     */
    function saveto($destination, $user) {

        if (!$this->checkbytes()) {

            return self::OVER_MAXIMUM;
        }
        if (!$this->checkextension()) {

            return self::NOT_ALLOWED_TYPE;
        }
        $uploaded_url = $this->copyFile($destination, $user);
        return $uploaded_url;
    }

    /**
     * 检查大小
     *
     * @return boolean
     */
    private function checkbytes() {

        $file_size = @filesize($_FILES[$this->upload_name]["tmp_name"]);
        if (!$file_size || $file_size > $this->maxbytes) {

            return false;
        } else {

            return true;
        }
    }

    /**
     * 检查后缀
     *
     * @return boolean
     */
    private function checkextension() {

        if (strpos($this->allowextension, ',' . $this->extension . ',') !== false) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * 复制文件到指定路径
     * 注意 mkdirs 在项目 common.php中
     *
     * @param string $destination
     * @return mixed
     */
    private function copyFile($destination, $user) {
        $tmpname = date('ymdhis') . $user . rand(1000, 9999) . '.' . $this->extension;
//        mkdir($destination, 0777, true);
        $file = $destination . $tmpname;

        if (!move_uploaded_file($_FILES[$this->upload_name]['tmp_name'], $file)) {
            return false;
        } else {
	    ftpAttachment('ident/'.$tmpname, $file); //ftpͬ²½ͼƬ
            return $tmpname;
        }
    }

}

if (!function_exists('mkdirs')) {

    /**
     * 创建目录们~
     *
     * @param unknown_type $path
     * @param unknown_type $mode
     */
    function mkdirs($dir, $mode = 0777) {
        $dirArray = explode(DIRECTORY_SEPARATOR, $dir);
        $dirArray = array_filter($dirArray);
        $created = "";
        foreach ($dirArray as $key => $value) {
            if (!empty($created)) {
                $created .= DIRECTORY_SEPARATOR . $value;
                if (!is_dir($created)) {
                    mkdir($created, $mode);
                }
            } else {
                if (!is_dir($value)) {
                    mkdir($value, $mode);
                }
                $created .= $value;
            }
        }
    }

}
