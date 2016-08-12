function validateForm(){
	// Make quick references to our fields
	var first_name = document.getElementById('fullname');
	var last_name = document.getElementById('last_name');
	var email = document.getElementById('email');

	// Check each input in the order that it appears in the form!
	if(isAlphabet(fullname, "Please enter only letters for your name")){
		if(isAlphabet(last_name, "Please enter only letters for your name")){
						if(emailValidator(email, "Please enter a valid email address")){
							return true;
						}
		}
	}

	return false;

}

function isAlphabet(elem, helperMsg){
	var alphaExp = /^[a-zA-Z]+$/;
	if(elem.value.match(alphaExp)){
		return true;
	}else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

function emailValidator(elem, helperMsg){
	var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
	if(elem.value.match(emailExp)){
		return true;
	}else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}