<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - Rich/HTML Editors</title>

</head>
<body>


	
<form id="form1" name="form1" action="">
	<h1>Rich HTML Editors (MCE Example)</h1>

	
<textarea name="spellfield" id="spellfield" cols="50" rows="20"  >
PHP spelll check can work alonsgide almot al majur web HTML editors. 
just set: 	$mySpell->Fields = "editors"; and render a SpellImageButton.

Note that spellcheck-as-you-type is not availablem, but spellchecking ina window is
</textarea>


<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea"
 });


</script>



<?php
	require "../include.php";	//   "phpspellcheck/include.php" // Full file path to the include.php file in the phpspellcheck Folder
	$mySpell = new SpellCheckButton();
	$mySpell->	HiddenButtons="";
	$mySpell->InstallationPath = "../";	  // "/phpspellcheck/" //  Relative URL of phpspellcheck within your site
	$mySpell->Fields = "editors";
	echo $mySpell->SpellImageButton();
?>

 </form>
</body>
</html>
