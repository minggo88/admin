
function SmsMsgCheck(){
	var tmpStr;

	tmpStr = document.forms[0].tran_msg.value;
	cal_byte(tmpStr);
}

function cal_byte(aquery){

	var tmpStr;
	var temp=0;
	var onechar;
	var tcount;
	tcount = 0;

	tmpStr = new String(aquery);
	temp = tmpStr.length;

	for (k=0;k<temp;k++)
	{
		onechar = tmpStr.charAt(k);
		if (escape(onechar) =='%0D') { } else if (escape(onechar).length > 4) { tcount += 2; } else { tcount++; }
	}

	document.getElementById("smsByte").innerText = tcount + " Bytes";	
}

