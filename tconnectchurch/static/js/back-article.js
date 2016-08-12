$(document).ready(function(){
	//global vars
	var form 				= $("#stylizedForm");
	var title 					= $("#title");
	var sectionid		 	= $("#sectionid");
	var categoriesid		= $("#categoriesid");

	//On blur
	title.blur(validateTitle);

	sectionid.blur(validateSectionid);
	categoriesid.blur(validateCategoriesid);

	//On key press
	title.keyup(validateTitle);

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
		if(title.val().length < 3){
			title.addClass("error");
			return false;
		}
		//if it's valid
		else{
			title.removeClass("error");
			return true;
		}
	}

	function validateFulltxt(){
		//it's NOT valid
		if(fulltxt.val().length <4){
			fulltxt.addClass("error");
			return false;
		}
		//it's valid
		else{
			fulltxt.removeClass("error");
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