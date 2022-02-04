<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<title>PHPSpellCheck Example - Multi Lingual</title>
</head>
<body>
<form id="form1" name="form1" action="">
<textarea name="spellfield"  id="spellfield" cols="50" rows="10">PhpSpellCheck allows you to spellcheck with many international dictionaries.  Using commas in the language decaration, you can spellcheck in more than 1 language at teh same time.  Which is ideal for multi linguage documents.
	
You can also translate the User Interface in seconds using the UserInterfaceLanguage property.

* Note - To use the Spannish dictionary - you must download "Espanol.dic" from PHPSpellcheck.com and copy it into the /phpspellcheck/dictionaries directory in your web site. 
</textarea><br/>

<?php
	require "../include.php";
	$mySpell = new SpellCheckButton();
	$mySpell->InstallationPath = "../";
	$mySpell->Fields = "spellfield";
	$mySpell->Language="English (International), Espanol";
	$mySpell->Text="Spell Check in English and Spanish at the same time";
	$mySpell->UserInterfaceLanguage="es";
	echo $mySpell->SpellButton();
	

?>
<br/><br/><br/>

<textarea name="spellfield2"  id="spellfield2" cols="50" rows="10">
PHPSpellCheck has user Interface support for Unicode languages such as Chinese, Japansese and Arabic.
	
* Be usre to set your document meta:
&lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html;charset=UTF-8&quot; /&gt;
	
* And your encodingalso in your PHP header
&lt;?php  	header(&#x27;Content-Type: text/html; charset=utf-8&#x27;); ?&gt;
	

</textarea>
<p>As-You-Type spellchecking with the User Interface in Chinese!</p>
<?php
$mySpell = new SpellAsYouType();
$mySpell->InstallationPath = "../";
$mySpell->Fields = "spellfield2";
$mySpell-> Language="English (International)";
$mySpell-> UserInterfaceLanguage="cn";
echo $mySpell->Activate();
?>
 

</form>
</body>
</html>
