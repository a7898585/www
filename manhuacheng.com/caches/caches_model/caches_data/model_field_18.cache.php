<?php
return array (
  'PlayTime' => 
  array (
    'fieldid' => '286',
    'modelid' => '18',
    'field' => 'PlayTime',
    'name' => '播放时长',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'text',
    'setting' => 'array (
  \'size\' => \'50\',
  \'defaultvalue\' => \'\',
  \'ispassword\' => \'0\',
  \'more\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '1',
    'isfulltext' => '1',
    'isposition' => '0',
    'listorder' => '0',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'size' => '50',
    'defaultvalue' => '',
    'ispassword' => '0',
    'more' => '',
  ),
  'CartoonID' => 
  array (
    'fieldid' => '285',
    'modelid' => '18',
    'field' => 'CartoonID',
    'name' => '动漫ID',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'number',
    'setting' => 'array (
  \'source\' => \'PlayTime\',
  \'minnumber\' => \'1\',
  \'maxnumber\' => \'\',
  \'decimaldigits\' => \'0\',
  \'size\' => \'\',
  \'defaultvalue\' => \'\',
  \'more\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '0',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'source' => 'PlayTime',
    'minnumber' => '1',
    'maxnumber' => '',
    'decimaldigits' => '0',
    'size' => '',
    'defaultvalue' => '',
    'more' => '',
  ),
  'web_url' => 
  array (
    'fieldid' => '289',
    'modelid' => '18',
    'field' => 'web_url',
    'name' => '采集网址',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'text',
    'setting' => 'array (
  \'size\' => \'50\',
  \'defaultvalue\' => \'\',
  \'ispassword\' => \'0\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '1',
    'isfulltext' => '1',
    'isposition' => '0',
    'listorder' => '0',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'size' => '50',
    'defaultvalue' => '',
    'ispassword' => '0',
  ),
  'from' => 
  array (
    'fieldid' => '294',
    'modelid' => '18',
    'field' => 'from',
    'name' => '来源网站',
    'tips' => '只需填入如:土豆,优酷,乐视,爱奇艺等',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'text',
    'setting' => 'array (
  \'size\' => \'50\',
  \'defaultvalue\' => \'土豆\',
  \'ispassword\' => \'0\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '1',
    'isfulltext' => '1',
    'isposition' => '0',
    'listorder' => '0',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'size' => '50',
    'defaultvalue' => '土豆',
    'ispassword' => '0',
  ),
  'catid' => 
  array (
    'fieldid' => '242',
    'modelid' => '18',
    'field' => 'catid',
    'name' => '栏目',
    'tips' => '',
    'css' => '',
    'minlength' => '1',
    'maxlength' => '6',
    'pattern' => '/^[0-9]{1,6}$/',
    'errortips' => '请选择栏目',
    'formtype' => 'catid',
    'setting' => 'array (
  \'defaultvalue\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '-99',
    'unsetroleids' => '-99',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '1',
    'isselect' => '1',
    'iswhere' => '1',
    'isorder' => '0',
    'islist' => '1',
    'isshow' => '1',
    'isadd' => '1',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '1',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'defaultvalue' => '',
  ),
  'typeid' => 
  array (
    'fieldid' => '243',
    'modelid' => '18',
    'field' => 'typeid',
    'name' => '类别',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'typeid',
    'setting' => 'array (
  \'minnumber\' => \'\',
  \'defaultvalue\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '1',
    'isselect' => '1',
    'iswhere' => '1',
    'isorder' => '0',
    'islist' => '1',
    'isshow' => '1',
    'isadd' => '1',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '2',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'minnumber' => '',
    'defaultvalue' => '',
  ),
  'title' => 
  array (
    'fieldid' => '244',
    'modelid' => '18',
    'field' => 'title',
    'name' => '标题',
    'tips' => '',
    'css' => 'inputtitle',
    'minlength' => '1',
    'maxlength' => '80',
    'pattern' => '',
    'errortips' => '请输入标题',
    'formtype' => 'title',
    'setting' => '',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '1',
    'isselect' => '1',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '1',
    'isshow' => '1',
    'isadd' => '1',
    'isfulltext' => '1',
    'isposition' => '1',
    'listorder' => '4',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
  ),
  'keywords' => 
  array (
    'fieldid' => '245',
    'modelid' => '18',
    'field' => 'keywords',
    'name' => '关键词',
    'tips' => '多关键词之间用空格或者“,”隔开',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '40',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'keyword',
    'setting' => 'array (
  \'size\' => \'100\',
  \'defaultvalue\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '-99',
    'unsetroleids' => '-99',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '1',
    'isselect' => '1',
    'iswhere' => '1',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '1',
    'isadd' => '1',
    'isfulltext' => '1',
    'isposition' => '0',
    'listorder' => '7',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'size' => '100',
    'defaultvalue' => '',
  ),
  'description' => 
  array (
    'fieldid' => '246',
    'modelid' => '18',
    'field' => 'description',
    'name' => '摘要',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '255',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'textarea',
    'setting' => 'array (
  \'width\' => \'98\',
  \'height\' => \'46\',
  \'defaultvalue\' => \'\',
  \'enablehtml\' => \'0\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '1',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '1',
    'isshow' => '1',
    'isadd' => '1',
    'isfulltext' => '1',
    'isposition' => '1',
    'listorder' => '10',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'width' => '98',
    'height' => '46',
    'defaultvalue' => '',
    'enablehtml' => '0',
  ),
  'updatetime' => 
  array (
    'fieldid' => '247',
    'modelid' => '18',
    'field' => 'updatetime',
    'name' => '更新时间',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'datetime',
    'setting' => 'array (
  \'dateformat\' => \'int\',
  \'format\' => \'Y-m-d H:i:s\',
  \'defaulttype\' => \'1\',
  \'defaultvalue\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '1',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '1',
    'iswhere' => '1',
    'isorder' => '1',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '12',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'dateformat' => 'int',
    'format' => 'Y-m-d H:i:s',
    'defaulttype' => '1',
    'defaultvalue' => '',
  ),
  'content' => 
  array (
    'fieldid' => '248',
    'modelid' => '18',
    'field' => 'content',
    'name' => '内容',
    'tips' => '<div class="content_attr"><label><input name="add_introduce" type="checkbox"  value="1" checked>是否截取内容</label><input type="text" name="introcude_length" value="200" size="3">字符至内容摘要
<label><input type=\'checkbox\' name=\'auto_thumb\' value="1" checked>是否获取内容第</label><input type="text" name="auto_thumb_no" value="1" size="2" class="">张图片作为标题图片
</div>',
    'css' => '',
    'minlength' => '1',
    'maxlength' => '999999',
    'pattern' => '',
    'errortips' => '内容不能为空',
    'formtype' => 'editor',
    'setting' => 'array (
  \'toolbar\' => \'full\',
  \'defaultvalue\' => \'\',
  \'enablekeylink\' => \'1\',
  \'replacenum\' => \'2\',
  \'link_mode\' => \'0\',
  \'enablesaveimage\' => \'1\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '0',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '1',
    'isfulltext' => '1',
    'isposition' => '0',
    'listorder' => '13',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'toolbar' => 'full',
    'defaultvalue' => '',
    'enablekeylink' => '1',
    'replacenum' => '2',
    'link_mode' => '0',
    'enablesaveimage' => '1',
  ),
  'thumb' => 
  array (
    'fieldid' => '249',
    'modelid' => '18',
    'field' => 'thumb',
    'name' => '缩略图',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '100',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'image',
    'setting' => 'array (
  \'size\' => \'50\',
  \'defaultvalue\' => \'\',
  \'show_type\' => \'1\',
  \'upload_maxsize\' => \'1024\',
  \'upload_allowext\' => \'jpg|jpeg|gif|png|bmp\',
  \'watermark\' => \'0\',
  \'isselectimage\' => \'1\',
  \'images_width\' => \'\',
  \'images_height\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '0',
    'issearch' => '0',
    'isselect' => '1',
    'iswhere' => '1',
    'isorder' => '0',
    'islist' => '1',
    'isshow' => '1',
    'isadd' => '1',
    'isfulltext' => '0',
    'isposition' => '1',
    'listorder' => '14',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'size' => '50',
    'defaultvalue' => '',
    'show_type' => '1',
    'upload_maxsize' => '1024',
    'upload_allowext' => 'jpg|jpeg|gif|png|bmp',
    'watermark' => '0',
    'isselectimage' => '1',
    'images_width' => '',
    'images_height' => '',
  ),
  '' => NULL,
  'pages' => 
  array (
    'fieldid' => '251',
    'modelid' => '18',
    'field' => 'pages',
    'name' => '分页方式',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'pages',
    'setting' => '',
    'formattribute' => '',
    'unsetgroupids' => '-99',
    'unsetroleids' => '-99',
    'iscore' => '0',
    'issystem' => '0',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '16',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
  ),
  'inputtime' => 
  array (
    'fieldid' => '252',
    'modelid' => '18',
    'field' => 'inputtime',
    'name' => '发布时间',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'datetime',
    'setting' => 'array (
  \'fieldtype\' => \'int\',
  \'format\' => \'Y-m-d H:i:s\',
  \'defaulttype\' => \'0\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '0',
    'issearch' => '0',
    'isselect' => '1',
    'iswhere' => '1',
    'isorder' => '1',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '1',
    'listorder' => '17',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'fieldtype' => 'int',
    'format' => 'Y-m-d H:i:s',
    'defaulttype' => '0',
  ),
  'posids' => 
  array (
    'fieldid' => '253',
    'modelid' => '18',
    'field' => 'posids',
    'name' => '推荐位',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'posid',
    'setting' => 'array (
  \'cols\' => \'4\',
  \'width\' => \'125\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '1',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '18',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'cols' => '4',
    'width' => '125',
  ),
  'groupids_view' => 
  array (
    'fieldid' => '254',
    'modelid' => '18',
    'field' => 'groupids_view',
    'name' => '阅读权限',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '100',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'groupid',
    'setting' => 'array (
  \'groupids\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '0',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '19',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'groupids' => '',
  ),
  'islink' => 
  array (
    'fieldid' => '262',
    'modelid' => '18',
    'field' => 'islink',
    'name' => '转向链接',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'islink',
    'setting' => '',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '1',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '20',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
  ),
  'url' => 
  array (
    'fieldid' => '255',
    'modelid' => '18',
    'field' => 'url',
    'name' => 'URL',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '300',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'text',
    'setting' => 'array (
  \'size\' => \'\',
  \'defaultvalue\' => \'\',
  \'ispassword\' => \'0\',
  \'more\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '1',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '1',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '1',
    'isshow' => '1',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '50',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'size' => '',
    'defaultvalue' => '',
    'ispassword' => '0',
    'more' => '',
  ),
  'listorder' => 
  array (
    'fieldid' => '256',
    'modelid' => '18',
    'field' => 'listorder',
    'name' => '排序',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '6',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'number',
    'setting' => '',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '1',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '1',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '51',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
  ),
  'views' => 
  array (
    'fieldid' => '263',
    'modelid' => '18',
    'field' => 'views',
    'name' => '点击次数',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '6',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'number',
    'setting' => '',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '1',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '1',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '51',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
  ),
  'template' => 
  array (
    'fieldid' => '257',
    'modelid' => '18',
    'field' => 'template',
    'name' => '内容页模板',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '30',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'template',
    'setting' => 'array (
  \'size\' => \'\',
  \'defaultvalue\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '-99',
    'unsetroleids' => '-99',
    'iscore' => '0',
    'issystem' => '0',
    'isunique' => '0',
    'isbase' => '0',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '53',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'size' => '',
    'defaultvalue' => '',
  ),
  'allow_comment' => 
  array (
    'fieldid' => '258',
    'modelid' => '18',
    'field' => 'allow_comment',
    'name' => '允许评论',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '0',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'box',
    'setting' => 'array (
  \'options\' => \'允许评论|1
不允许评论|0\',
  \'boxtype\' => \'radio\',
  \'fieldtype\' => \'tinyint\',
  \'minnumber\' => \'1\',
  \'cols\' => \'5\',
  \'width\' => \'88\',
  \'size\' => \'1\',
  \'default_select_value\' => \'1\',
)',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '0',
    'issystem' => '0',
    'isunique' => '0',
    'isbase' => '0',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '54',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'options' => '允许评论|1
不允许评论|0',
    'boxtype' => 'radio',
    'fieldtype' => 'tinyint',
    'minnumber' => '1',
    'cols' => '5',
    'width' => '88',
    'size' => '1',
    'default_select_value' => '1',
  ),
  'status' => 
  array (
    'fieldid' => '259',
    'modelid' => '18',
    'field' => 'status',
    'name' => '状态',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '2',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'box',
    'setting' => '',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '1',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '55',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
  ),
  'readpoint' => 
  array (
    'fieldid' => '260',
    'modelid' => '18',
    'field' => 'readpoint',
    'name' => '阅读收费',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '5',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'readpoint',
    'setting' => 'array (
  \'minnumber\' => \'1\',
  \'maxnumber\' => \'99999\',
  \'decimaldigits\' => \'0\',
  \'defaultvalue\' => \'\',
)',
    'formattribute' => '',
    'unsetgroupids' => '-99',
    'unsetroleids' => '-99',
    'iscore' => '0',
    'issystem' => '0',
    'isunique' => '0',
    'isbase' => '0',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '1',
    'isshow' => '1',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '55',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
    'minnumber' => '1',
    'maxnumber' => '99999',
    'decimaldigits' => '0',
    'defaultvalue' => '',
  ),
  'username' => 
  array (
    'fieldid' => '261',
    'modelid' => '18',
    'field' => 'username',
    'name' => '用户名',
    'tips' => '',
    'css' => '',
    'minlength' => '0',
    'maxlength' => '20',
    'pattern' => '',
    'errortips' => '',
    'formtype' => 'text',
    'setting' => '',
    'formattribute' => '',
    'unsetgroupids' => '',
    'unsetroleids' => '',
    'iscore' => '1',
    'issystem' => '1',
    'isunique' => '0',
    'isbase' => '1',
    'issearch' => '0',
    'isselect' => '0',
    'iswhere' => '0',
    'isorder' => '0',
    'islist' => '0',
    'isshow' => '0',
    'isadd' => '0',
    'isfulltext' => '0',
    'isposition' => '0',
    'listorder' => '98',
    'disabled' => '0',
    'isomnipotent' => '0',
    'isseo' => '0',
  ),
);
?>