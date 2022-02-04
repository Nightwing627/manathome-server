<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - Skinning the Spellchecker and Context Menu</title>
</head>
<body>
<form id="form1" name="form1" action="">
<textarea name="spellfield"  id="spellfield" cols="50" rows="20">The CSS Theme property allows you to skin the spellchecker.  You can copy and create new themes in the /phpspellcheck/themes directory.
	
	The example theme "Bright" looks clean - fresh and googly.  If you create something great - please post it on our WIKI to share it with other users
	
	If you want to change layout and CSS just isn't enough - you have access to the UI sourcecode. But before you go hacking away... give CSS a whirl.  teh CSS Zen appraoch used separates design and application in a very clean mannner.
	
</textarea>

<?php
	require "../include.php";
	$mySpell = new SpellCheckButton();
	$mySpell->InstallationPath = "../";
	$mySpell->Fields = "ALL";
	$mySpell->CSSTheme = "bright";
	echo $mySpell->SpellImageButton();
	
	$mySpell = new SpellAsYouType();
	$mySpell->InstallationPath = "../";
	$mySpell->Fields = "ALL";
	$mySpell->CSSTheme = "bright";	
	echo $mySpell->Activate();
?>
 </form>
</body>
</html>
