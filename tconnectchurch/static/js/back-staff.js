$(document).ready(function(){
	//global vars
	var form 				= $("#stylizedForm");
	var name 				= $("#name");
	var sectionid		 	= $("#sectionid");
	var categoriesid		= $("#categoriesid");

	//On blur
	name.blur(validateTitle);

	sectionid.blur(validateSectionid);
	categoriesid.blur(validateCategoriesid);

	//On key press
	name.keyup(validateTitle);

	sectionid.keyup(validateSectionid);
	categoriesid.keyup(validateCategoriesid);

	//On Submitting
	form.submit(function(){
		if(validateTitle() & validateSectionid()  & validateCategoriesid())
			return true
		else
			return false;
	});

	function validateTitle(){
		//if it's NOT valid
		if(name.val().length < 3){
			name.addClass("error");
			return false;
		}
		//if it's valid
		else{
			name.removeClass("error");
			return true;
		}
	}

	function validateSectionid(){
		//it's NOT valid
		if(sectionid.val() == ""){
			sectionid.addClass("error");
			return false;
		}
		//it's valid
		else{
			sectionid.removeClass("error");
			return true;
		}
	}

	function validateCategoriesid(){
		//it's NOT valid
		if(categoriesid.val() == ""){
			categoriesid.addClass("error");
			return false;
		}
		//it's valid
		else{
			categoriesid.removeClass("error");
			return true;
		}
	}
});