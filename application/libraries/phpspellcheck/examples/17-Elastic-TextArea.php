<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - Elastic Text Area</title>
</head>
<body>
<form id="form1" name="form1" action="">
	<h1>Auto-Resizing Spell-As-You-Type Field </h1>

	
<textarea name="spellfield"  id="spellfield" cols="50" rows="20" style="height:auto" >This simple example shows in PHPSpellCheck providing an eleastic textarea.  It automatically reszes as the uzer types to make more room....
	
	All you need to do is add style="height:auto" to your textarea!
	
</textarea>

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
