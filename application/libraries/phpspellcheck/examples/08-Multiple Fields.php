<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - Multiple Fields </title>
</head>
<body>
<form id="form1" name="form1" action="">
<textarea name="spellfield1"  id="spellfield1" cols="20" rows="5">Spelling multiple fields is very easy</textarea>
<textarea name="spellfield2"  id="spellfield2" cols="20" rows="5">Just add all of their IDs into the Fields property. Use a</textarea>
<input type="text" name="spellfield3"  id="spellfield3" Value=" comma to separate them" />
<textarea name="spellfield4"  id="spellfield3" cols="20" rows="5">To Spellcheck ALL fields at once - set the Fields property to "ALL". easy huh?</textarea>
<?php
	require "../include.php";
	$mySpell = new SpellCheckButton();
	$mySpell->InstallationPath = "../";
	$mySpell->Fields = "spellfield1,spellfield2,spellfield3";  //"ALL" would also work.
	echo $mySpell->SpellImageButton();
?>

 </form>
</body>
</html>
