<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - Hello World</title>
</head>
<body>
<form id="form1" name="form1" action="">
	<h1> PHPSpellCheck in Debug Mode</h1>
<textarea name="spellfield"  id="spellfield" cols="50" rows="20" >This simple example shows PHPSpellcheck's main features.  
	
Spelling As-You-Type AND Spelling in a window dialog.

The SpellAsYouType class adds red-wiggly-underlines and a context menu.

The SpellCheckButton class adds a more traditional spellcheck button and popup window.

You can use these features separately - or combined. Either one can be implemented in 4 lines of php code.

</textarea>

<script type="text/javascript">
    var LIVESPELL_DEBUG_MODE = true;
</script>

<?php
	require "../include.php";	//   "phpspellcheck/include.php" // Full file path to the include.php file in the phpspellcheck Folder
	$mySpell = new SpellCheckButton();
	$mySpell->InstallationPath = "../";	  // "/phpspellcheck/" //  Relative URL of phpspellcheck within your site
	$mySpell->Fields = "ALL";
	echo $mySpell->SpellImageButton();
	
	$mySpell = new SpellAsYouType();
	$mySpell->InstallationPath = "../"; // "/phpspellcheck/" // Relative URL of phpspellcheck within your site
	$mySpell->Fields = "ALL";
	echo $mySpell->Activate();
?>
 </form>
</body>
</html>
