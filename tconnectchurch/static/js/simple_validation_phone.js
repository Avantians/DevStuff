function formValidator(){
	// Make quick references to our fields
	var first_name = document.getElementById('first_name');
	var last_name = document.getElementById('last_name');
	var email = document.getElementById('email');
	var phone = document.getElementById('phone');
	var product_interest = document.getElementById('product_interest');

	// Check each input in the order that it appears in the form!
	if(isAlphabet(first_name, "Please enter only letters for your name")){
		if(isAlphabet(last_name, "Please enter only letters for your name")){
			if(emailValidator(email, "Please enter a valid email address")){
				if(phoneValidator(phone, "Please enter a valid phone number")){
					if(selectValidator(product_interest, "Please select one of the options")){
					return true;
					}
				}
			}
		}
	}

	return false;

}

function selectValidator(elem, helperMsg){
	if(elem.value == ""){
		alert(helperMsg);
		elem.focus();
		return false;
	}
	else{
		return true;
	}
}

function phoneValidator(elem, helperMsg){
	var phoneExp = /^[a-zA-Z0-9\.\-]+$/;
	if(elem.value.match(phoneExp)){
		return true;
	}
	else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

function isAlphabet(elem, helperMsg){
	var alphaExp = /^[a-zA-Z]+$/;
	if(elem.value.match(alphaExp)){
		return true;
	}
	else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

function emailValidator(elem, helperMsg){
	var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
	if(elem.value.match(emailExp)){
		return true;
	}
	else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}