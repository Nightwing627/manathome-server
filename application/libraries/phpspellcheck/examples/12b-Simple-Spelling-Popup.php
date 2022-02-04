<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - Spelling Window </title>
</head>
<body>
<form id="form1" name="form1" action="">
<textarea name="spellfield"  id="spellfield" cols="20" rows="5">Spelling in a SpellingWindow dialg can actually be implemented in 3 lines of code.  Can you do it?  </textarea>
<?php
	require "../include.php";
	$mySpell = new SpellCheckButton();
	$mySpell->InstallationPath = "../"; 
	$mySpell->Fields = "spellfield";
	echo $mySpell->SpellImageButton();
?>
 </form>
</body>
</html>
