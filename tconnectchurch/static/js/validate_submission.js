$(document).ready(function(){
	//global vars
	var form 										= $("#stylizedForm");
	var first_name 								= $("#first_name");
	var last_name 								= $("#last_name");
	var address 									= $("#address");
	var city 											= $("#city");
	var province 									= $("#province");
	var country 									= $("#country");
	var postal 										= $("#postal");
		
	var phone 										= $("#phone");
	var email	 									= $("#email");
	var film_title									= $("#film_title");
	var running_time 							= $("#running_time");
	var year_of_production 					= $("#year_of_production");
	var model_brand_of_device_used	= $("#model_brand_of_device_used");
	var editing_software_used				= $("#editing_software_used");
	var film_synopsis 							= $("#film_synopsis");
	var bio_of_director 							= $("#bio_of_director");
	
	//On blur
    first_name.blur(validateFirst);
	last_name.blur(validateLast);
	
	address.blur(validateAddress);
	city.blur(validateCity);
	province.blur(validateProvince);
	country.blur(validateCountry);
	postal.blur(validatePostal);
	
	phone.blur(validatePhone);
	email.blur(validateEmail);
	film_title.blur(validateFtitle);
	running_time.blur(validateRtime);
	year_of_production.blur(validateYP);
	model_brand_of_device_used.blur(validateDevice);
	editing_software_used.blur(validateEdit);
	film_synopsis.blur(validateFilms);
	bio_of_director.blur(validateBio);

	//On key press
    first_name.keyup(validateFirst);
	last_name.keyup(validateLast);
     
	address.keyup(validateAddress);
	city.keyup(validateCity);
	province.keyup(validateProvince);
	country.keyup(validateCountry);
	postal.keyup(validatePostal);

	phone.keyup(validatePhone);
	email.keyup(validateEmail);
	film_title.keyup(validateFtitle);
	running_time.keyup(validateRtime);
	year_of_production.keyup(validateYP);
	model_brand_of_device_used.keyup(validateDevice);
	editing_software_used.keyup(validateEdit);
	film_synopsis.keyup(validateFilms);
	bio_of_director.keyup(validateBio);

	//On Submitting
	form.submit(function(){
		if(validateFirst() & validateLast() & validateAddress() & validateCity() & validateProvince() & validateCountry() & validatePostal() & validateEmail() & validatePhone() & validateFtitle()  & validateRtime() & validateYP()& validateDevice() & validateEdit() & validateFilms() & validateBio() )
			return true
		else
			return false;
	});

	function validateFirst(){
		//if it's NOT valid
		if(first_name.val().length < 2){
			first_name.addClass("error");
			return false;
		} else{
			first_name.removeClass("error");
			return true;
		}
	}

	function validateLast(){
		//if it's NOT valid
		if(last_name.val().length < 2){
			last_name.addClass("error");
			return false;
		} else{
			last_name.removeClass("error");
			return true;
		}
	}
	
	function validateAddress(){
		//if it's NOT valid
		if(address.val().length < 4){
			address.addClass("error");
			return false;
		} else{
			address.removeClass("error");
			return true;
		}
	}

	function validateCity(){
		//if it's NOT valid
		if(city.val().length < 1){
			city.addClass("error");
			return false;
		} else{
			city.removeClass("error");
			return true;
		}
	}

	function validateProvince(){
		//if it's NOT valid
		if(province.val().length < 2){
			province.addClass("error");
			return false;
		} else{
			province.removeClass("error");
			return true;
		}
	}
 
	function validateCountry(){
		//if it's NOT valid
		if(country.val().length < 1){
			country.addClass("error");
			return false;
		} else{
			country.removeClass("error");
			return true;
		}
	}
 
	function validatePostal(){
		//if it's NOT valid
		if(postal.val().length < 4){
			postal.addClass("error");
			return false;
		} else{
			postal.removeClass("error");
			return true;
		}
	}

	function validatePhone(){
		var phoneExp = /^[a-zA-Z0-9\.\-]+$/;
		if(phone.val().length < 3){
			phone.addClass("error");
			return false;
		} else{
			phone.removeClass("error");
			return true;
		}
	}

	function validateEmail(){
		//testing regular expression
		var a = $("#email").val();
		var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;

		if(filter.test(a)){
			email.removeClass("error");
			return true;
		} else{
			email.addClass("error");
			return false;
		}
	}		
	
	function validateFtitle(){
		if(film_title.val().length < 7){
			film_title.addClass("error");
			return false;
		} else{
			film_title.removeClass("error");
			return true;
		}
	}
	
	function validateRtime(){
		if(running_time.val().length < 2){
			running_time.addClass("error");
			return false;
		} else{
			running_time.removeClass("error");
			return true;
		}
	}

	function validateYP(){
		if(year_of_production.val().length < 2){
			year_of_production.addClass("error");
			return false;
		} else{
			year_of_production.removeClass("error");
			return true;
		}
	}	
	
	function validateDevice(){
		if(model_brand_of_device_used.val().length < 2){
			model_brand_of_device_used.addClass("error");
			return false;
		} else{
			model_brand_of_device_used.removeClass("error");
			return true;
		}
	}	
	
	function validateEdit(){
		if(editing_software_used.val().length < 2){
			editing_software_used.addClass("error");
			return false;
		} else{
			editing_software_used.removeClass("error");
			return true;
		}
	}	
	
	function validateFilms(){
		if(film_synopsis.val().length < 2){
			film_synopsis.addClass("error");
			return false;
		} else{
			film_synopsis.removeClass("error");
			return true;
		}
	}	
	
	function validateBio(){
		if(bio_of_director.val().length < 2){
			bio_of_director.addClass("error");
			return false;
		} else{
			bio_of_director.removeClass("error");
			return true;
		}
	}	
});