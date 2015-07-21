//Main Javascript functions  - sitewide
function submitform(formname) {
	document.getElementById(formname).submit()
}

function CheckAlphaNumeric(e){
	var unicode=e.charCode? e.charCode : e.keyCode
	if (unicode!=8&&unicode!=13&&unicode!=9) { //if the key isn't the backspace key (which we should allow)
		if ((unicode>47&&unicode<58)||(unicode>64&&unicode<91)||(unicode>96&&unicode<123)) {
		} else {
			return false;
		}
	} else {
		if (unicode==13) {
			//document.form.submit()
		}
		return true;
	}
}
function CheckDecimal(e){
	var unicode=e.charCode? e.charCode : e.keyCode
	if (unicode!=8&&unicode!=13&&unicode!=9) { //if the key isn't the backspace key (which we should allow)
		if ((unicode>47&&unicode<58)||(unicode==46)) {
		} else {
			return false;
		}
	} else {
		if (unicode==13) {
			//document.form.submit()
		}
		return true;
	}
}



