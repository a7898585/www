<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><div class="clear1"></div>
<!--抢票王-->
<div style="margin: auto; width:1000px;">
                                                          
<div class="footer">  
  <div class="footernew">
    <div class="footerbox_1">
      <div class="footerbox_1a">网站导航</div>
      <div class="footerbox_1b"> <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=225354b99c73694b520da67136ce2a6b&action=category&catid=0&num=5&order=listorder+ASC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'category')) {$data = $content_tag->category(array('catid'=>'0','order'=>'listorder ASC','limit'=>'5',));}?>
        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?> <a target="_blank" href="<?php echo $r['url'];?>"><?php echo $r['catname'];?></a><br>
        <?php $n++;}unset($n); ?>
        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?> </div>
    </div>
    <div class="footerbox_1">
      <div class="footerbox_1a">关于我们</div>
      <div class="footerbox_1b"> <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=abc02536addf0cdc69284fed6900d53f&action=category&catid=58&num=5&order=listorder+ASC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'category')) {$data = $content_tag->category(array('catid'=>'58','order'=>'listorder ASC','limit'=>'5',));}?>
        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?> <a target="_blank" href="<?php echo $r['url'];?>"><?php echo $r['catname'];?></a><br>
        <?php $n++;}unset($n); ?>
        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?> </div>
    </div>
    <div class="footerbox_3">
      <div class="footerbox_3a">漫画城</div>
      <div class="footerbox_3b">
        <div class="image">
          <div class="imagebox"><img src="<?php echo IMG_PATH;?>img/u269_normal.png"></div>
          <div class="textbox"><a target="_blank" href="http://weibo.com/manhuacheng">新浪微博</a></div>
        </div>
        <div class="clear"></div>
        <div class="imagetext">扫描二维码，关注漫画城</div>
      </div>
    </div>
    <div class="footerbox_1">
      <div class="footerbox_1a">动漫交流</div>
      <div class="footerbox_1b"> <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=8860d7c51d9f955aef4da83ed5b29097&action=category&catid=70&num=5&order=listorder+ASC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'category')) {$data = $content_tag->category(array('catid'=>'70','order'=>'listorder ASC','limit'=>'5',));}?>
        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?> <a target="_blank" href="<?php echo $r['url'];?>"><?php echo $r['catname'];?></a><br>
        <?php $n++;}unset($n); ?>
        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?> </div>
    </div>
    <div class="footerbox_1" >
      <div class="footerbox_1a">更多</div>
      <div class="footerbox_1b" style="border-right:none;"> <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=09af636036051c6ca3c2abcfc5625862&action=category&catid=63&num=5&order=listorder+ASC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'category')) {$data = $content_tag->category(array('catid'=>'63','order'=>'listorder ASC','limit'=>'5',));}?>
        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?> <a target="_blank" href="<?php echo $r['url'];?>"><?php echo $r['catname'];?></a><br>
        <?php $n++;}unset($n); ?>
        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?> <a target="_blank" href="/index.php?m=feedback">留言建议</a><br>
      </div>
    </div>
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
 
  <div class="footerheight">
    <p><?php echo $footer_txt;?></p>
    <?php  if($_SERVER['REQUEST_URI'] != "/"){ ?>
<?php echo GetAd(1);?>
<?php echo GetAd(2);?>

<?php } ?>
<?php echo GetAd(5);?>
    <p>如有侵权作品或是违反国家法律作品，请联系448176171@qq.com审核无误后我们会立即删掉!</p>
    <p><?php echo COPYRIGHT;?></p>
    <p><a href="<?php echo siteurl();?>"><?php echo $SEO['site_name'];?></a>&nbsp;|&nbsp;<a href="#" style="padding-right:20px;">推荐给朋友</a>&nbsp;&nbsp;广告合作 QQ:448176171  &nbsp;&nbsp;<?php echo COMPANY;?><a href="http://www.miibeian.gov.cn/" target="_blank" style="padding-left:20px;"><?php echo MIIBEIAN;?></a></p>
  </div>
  <div style="display:none"> 
    <script language="javascript" type="text/javascript" src="http://js.users.51.la/15855535.js"></script>
    <noscript>
    <a href="http://www.51.la/?15855535" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/15855535.asp" style="border:none" /></a>
    </noscript>
  </div>
</div>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?5b770694dd85461e90a74c644ecd2647";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</body></html>