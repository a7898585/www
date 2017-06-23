<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><style>
body,html{background:none; padding:0; margin:0}
.log{line-height:24px;*line-height:27px; height:24px;float:right; font-size:12px}
.log span{color:#ced9e7}
.log a{color:#049;text-decoration: none;}
.log a:hover{text-decoration: underline;}
</style>
<body style="background-color:transparent">
<div class="log"><?php if($_username) { ?><?php echo L('hellow');?> <?php echo get_nickname();?>, <a href="<?php echo APP_PATH;?>index.php?m=member" target="_blank"><?php echo L('member_center');?></a> <a href="<?php echo APP_PATH;?>index.php?m=member&c=index&a=logout&forward=<?php echo urlencode($_GET['forward']);?>" target="_top"><?php echo L('logout');?></a><?php } else { ?><a href="<?php echo APP_PATH;?>index.php?m=member&c=index&a=register" target="_blank"><?php echo L('register');?></a> <span>|</span> <a href="<?php echo APP_PATH;?>index.php?m=member&c=index&a=login&forward=<?php echo urlencode($_GET['forward']);?>" target="_top"><?php echo L('login');?></a><?php } ?></div>
</body>