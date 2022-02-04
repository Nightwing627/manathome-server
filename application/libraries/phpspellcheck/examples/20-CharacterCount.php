<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - Hello World</title>
   

</head>
<body>
	

	<h1>Hello World</h1>

	
<textarea name="spellfield" id="spellfield" cols="50" rows="20" maxlength='250'  >Character Count</textarea>
<div id="counter1"></div>

<script type='text/javascript'>

countChars = function(field,output){
	var oField = document.getElementById(field);
	var oOut = document.getElementById(output);
	if(!oField || !oOut){return false};
	var maxChars =  oField.maxLengh;
	if(!maxChars){maxChars =  oField.getAttribute('maxlength') ; };
		 
	if(!maxChars){return false};
		
		
	var remainingChars =   maxChars - oField.value.length
	oOut.innerHTML = remainingChars+" Characters Remaining"
}

setInterval(function(){countChars('spellfield','counter1')},50);

</script>

<?php
	require "../include.php";	//   "phpspellcheck/include.php" // Full file path to the include.php file in the phpspellcheck Folder

	$mySpell = new SpellAsYouType();
	$mySpell->InstallationPath = "../"; // "/phpspellcheck/" // Relative URL of phpspellcheck within your site
	$mySpell->Fields = "ALL";
	echo $mySpell->Activate();
?>



</body>
</html>
