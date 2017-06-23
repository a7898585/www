<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template("content","header"); ?>
<!--main-->
<div class="mainborder" style="width: 550px;margin: auto;">
	<div class="m705">
		<form action="/index.php" method="post" name="feedback">
			<input type="hidden" value="feedback" name="m" />
			<input type="hidden" value="index" name="c" />
			<input type="hidden" value="init" name="a" />
			<input type="hidden" value="1" name="submit" />
			<dl class="bzfkDl">
				<dt class="dt1 bzBg"></dt>
				<dd>
					<p>请选择留言类型</p>
					<p class="p2">
					<input type="radio" name="fb_class" id="fb_class0" value="会员问题" />
					<label for="fb_class0">会员问题</label>
					<input type="radio" name="fb_class" id="fb_class1" value="故障投诉">
					<label for="fb_class1">故障投诉</label>
					<input type="radio" name="fb_class" id="fb_class2" value="改善建议">
					<label for="fb_class2">改善建议</label>
					<input type="radio" name="fb_class" id="fb_class3" value="漫画内容需求">
					<label for="fb_class3">漫画内容需求</label>
					<input type="radio" name="fb_class" id="fb_class4" value="新手咨询">
					<label for="fb_class4">新手咨询</label>
					<input type="radio" name="fb_class" id="fb_class5" value="其他">
					<label for="fb_class5">其他</label>
					</p>
				</dd>
			</dl>

			<dl class="bzfkDl">
				<dt class="dt2 bzBg"></dt>
				<dd>
					<p>请详细描述您的建议、意见、问题等</p>
					<p>反馈内容字数0~2000之间</p>
					<textarea class="bzfkTextarea" name="erroContent"></textarea>
				</dd>
			</dl>

			<dl class="bzfkDl">
				<dt class="dt2 bzBg"></dt>
				<dd>
					<p>请输入正确的邮件地址</p>
					<input type="text" size="25" class="bzfkInputText" id="email" name="email">
				</dd>
			</dl>
			<dl class="bzfkDl">
				<dd>
					<input  type="submit"  value="提交" >
				</dd>	
			</dl>
		</form>
	</div>
</div>
<script>
function checkradio(){
	for (var x=0; x<document.feedback.fb_class.length; x++)
	{if(document.feedback.fb_class[x].checked) return true;}
}
function myfun(){
	if(!checkradio()){
		document.getElementById('erroClass').style.display="block";
		return false;
	}
	document.feedback.submit();
}
</script>

<?php include template("content","footer"); ?>
