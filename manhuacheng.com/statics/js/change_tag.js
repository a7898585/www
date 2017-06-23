function secBoard(elementID,listName,n,hoverclass,noclass) {
	var elem = document.getElementById(elementID);
	var elemlist = elem.getElementsByTagName("li");
	for (var i=0; i<elemlist.length; i++) {
		elemlist[i].className = noclass;
		var m = i+1;
		document.getElementById(listName+"_"+m).style.display = "none";
	}
	elemlist[n-1].className = hoverclass;
	document.getElementById(listName+"_"+n).style.display = "block";
}
