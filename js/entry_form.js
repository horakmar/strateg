/**
 * File entry_form.js.
 *
 * Functions for Entry form application
 *
 */

function ToggleSecond(alone) {
	if(alone == true){
		document.getElementById('second_biker').style.display = 'none';
        document.getElementById('fname1').required = false;
        document.getElementById('sname1').required = false;
//		document.getElementById('need_team').style.display = 'none'
	}else{
		document.getElementById('second_biker').style.display = 'block';
        document.getElementById('fname1').required = true;
        document.getElementById('sname1').required = true;
//		document.getElementById('need_team').style.display = 'inline'
	}
}

// document.body.onload = function() { ToggleSecond(document.getElementById('alone').checked) };
