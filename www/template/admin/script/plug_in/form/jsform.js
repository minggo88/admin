/*

*** 체크타입 ***
select,
eq_check,
blank,
digit,
hangul,
charall,
capital,
alpha,
alnum,
userid,
passwd,
email,
filename

var chk_option = [
	{
		'target':'name',
		'name':'홍길동',
		'type':'num',
		'msg':'정말 멋지다.'
	},
	{
		'target':'passwd',
		'target2':'passwd',
		'name':'홍길동',
		'type':'eq_check',
		'msg':'나름 멋지다.'
	}
];
*/

function jsForm (frm,chk_option) {
	chk_option = chk_option || [];
	for(var i = 0; i< chk_option.length; i++) {
		var obj = chk_option[i];
		if(obj['type'] === 'select' ) {
			var selected_idx = eval("frm."+obj['target']+".selectedIndex");
			if(selected_idx === 0) {
                if(typeof(toastr) == "undefined") {
                    alert(obj['msg']);
				} else {
                    fn_show_toast("warning", obj['msg']);
				}
				eval("frm."+obj['target']+".focus()");
				return false;
			}
		}
		else if(obj['type'] === 'eq_check' ) {
			var val = eval("frm."+obj['target']+".value");
			var val2 = eval("frm."+obj['target2']+".value");
			if(val !== val2 ) {
                if(typeof(toastr) == "undefined") {
                    alert(obj['msg']);
                } else {
                    fn_show_toast("warning", obj['msg']);
                }
				eval("frm."+obj['target2']+".focus()");
				return false;
			}
		}
		else if(obj['type'] === 'blank' ) {
			var val = eval("frm."+obj['target']+".value");
			if(val.length == 0) {
                if(typeof(toastr) == "undefined") {
                    alert(obj['msg']);
                } else {
                    fn_show_toast("warning", obj['msg']);
                }
				eval("frm."+obj['target']+".focus()");
				return false;
			}
		}
		else {
			var val = eval("frm."+obj['target']+".value");
			if(val.length == 0) {
                if(typeof(toastr) == "undefined") {
                    alert(obj['msg']);
                } else {
                    fn_show_toast("warning", obj['msg']);
                }
				eval("frm."+obj['target']+".focus()");
				return false;
			}
			if(!checkValid(val,obj['type'])) {
				var err_msg = {
					'digit':__('jsform1'),
					'hangul':__('jsform2'),
					'charall':__('jsform3'),
					'capital':__('jsform4'),
					'alpha':__('jsform5'),
					'alnum':__('jsform6'),
					'userid':__('jsform7'),
					'passwd':__('jsform8'),
					'email':__('jsform9'),
					'filename':__('jsform10'),
				};
                if(typeof(toastr) == "undefined") {
                    alert('['+obj['name']+'] '+ err_msg[obj['type']]);
                } else {
                    fn_show_toast("warning", '['+obj['name']+'] '+ err_msg[obj['type']]);
                }
				eval("frm."+obj['target']+".focus()");
				return false;
			}
		}
	}
	return true;
}

function checkValid (data,rule) {
	var filter;
	if( rule === 'digit') {
		filter = /^\d+$/;
	}
	else if (rule === 'hangul') {
		filter = /^[가-힣]+$/;
	}
	else if (rule === 'charall') {
		filter = /^[0-9a-zA-Z가-힣]+$/;
	}
	else if (rule === 'capital') {
		filter = /^[A-Z][0-9a-zA-Z]+$/;
	}
	else if (rule === 'alpha') {
		filter = /^[a-zA-Z]+$/;
	}
	else if (rule === 'alnum') {
		filter = /^[0-9a-zA-Z]+$/;
	}
	else if (rule === 'passwd') {
		//filter = /^[\s\S]*$/;
		filter = /^[a-zA-Z][0-9a-zA-Z\w\W]{3,}$/;
	}
	else if (rule === 'userid') {
		filter = /^[a-zA-Z][0-9a-zA-Z]{3,}$/;
	}
	else if (rule === 'email') {
		filter = /^([\w.])+\@(([\w])+\.)[a-zA-Z0-9]{2,}/;
	}
	else if (rule === 'filename') {
		filter = /^([\w])+.[a-zA-Z0-9]{2,}/;
	}
	else {
		filter = /^[\s\S]*$/;
	}
	if(filter.test(data)) {
		return true;
	}
	else {
		return false;
	}
}