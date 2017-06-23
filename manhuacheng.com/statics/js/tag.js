function secBoard(elementID,listName,n) {
	var isclear = document.getElementById("isclear");
	if(listName == "infolista" && n == 1) {
		if(typeof(isclear) != null)
			document.getElementById("isclear").innerHTML = "<a href='javascript:delhistory()'>清空</a>";
	}else if(listName == "infolista" && n == 2) {
		if(typeof(isclear) != null)
			document.getElementById("isclear").innerHTML = "<a target=\"_blank\" href=\"/help/helpmark.html\">帮助</a>";
	}
	var elem = document.getElementById(elementID);
	var elemlist = elem.getElementsByTagName("h3");
	for (var i=0; i<elemlist.length; i++) {
		elemlist[i].className = "normal";
		var m = i+1;
		document.getElementById(listName+"_"+m).className = "normal";
	}
	elemlist[n-1].className = "current";
	document.getElementById(listName+"_"+n).className = "current";
}

