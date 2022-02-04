<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - Submitting and Validating Forms</title>
<style>

label{
font-family: sans-serif;		
font-size:12px;
}

</style>
</head>
<body>
<?php
if($_POST["message"])
{
	echo("<p>Form Submitted After Spellchecking!</p>");
	die();
}

?>	
<form id="form1" name="form1" action="" method="post">
	
<label>Subject<br/><input type="text" name="subject"  id="subject" cols="50" rows="20">
	</textarea></label>
	<br/>
<label>Message<br/><textarea name="message"  id="message" cols="50" rows="20">As you can see, PHP Spell Check can competently  spell multiple fields at once - and submit forms when spellchecking is complete.
</textarea></label>
<br/>
<?php
	require "../include.php";
	$mySpell = new SpellCheckButton();
	$mySpell->InstallationPath = "../";
	$mySpell->Fields = "subject,message";
	$mySpell->FormToSubmit = "form1";	
	$mySpell->Text = "SpellCheck and Then Submit";
	$mySpell->ShowSummaryScreen = false;
	echo $mySpell->SpellButton();
	

?>
 </form>
</body>
</html>
