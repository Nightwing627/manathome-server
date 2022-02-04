<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - Spelling "As You Type" </title>
</head>
<body>
<form id="form1" name="form1" action="">
<textarea name="spellfield"  id="spellfield" cols="30" rows="5">As You type spellchecking with red and green wiggly underlines also only takes 4 lines of PHP code.   This solution works across all major browsers including IE.  Nothhing is instalsled at client - and only a few files need to be uploaded to your server.</textarea>
<?php
	require "../include.php";
	$mySpell = new SpellAsYouType();
	$mySpell->InstallationPath = "../";
	$mySpell->Fields = "ALL";
	echo $mySpell->Activate();
?>
 </form>
</body>
</html>
