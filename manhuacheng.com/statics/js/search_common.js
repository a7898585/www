	function setmodel(value, id, q) {
		$("#typeid").val(value);
		$("#search a").removeClass();
		id.addClass('on');
		if(q!=null && q!='') {
			window.location='?m=search&c=index&a=init&typeid='+value+'&q='+q;
		}
	}