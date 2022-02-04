<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - Hello World</title>
</head>
<body>
<form id="form1" name="form1" action="">
<textarea name="spellfield"  id="spellfield" cols="50" rows="20">
&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot; ?&gt;
	&lt;feature&gt;
	  PHPSpellCheck can automatically work with markup languages (even if badly formed):
	  &lt;lang type=&#x27;tag&#x27;&gt;HTML&lt;/lang&gt;	  
	  &lt;lang type=&#x27;tag&#x27;&gt;XHTML&lt;/lang&gt;
	  &lt;lang type=&#x27;tag&#x27;&gt;XML&lt;/lang&gt;
	&lt;/feature&gt;
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
