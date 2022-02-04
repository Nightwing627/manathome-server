<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - JavaScript Events</title>
</head>
<body>
<form id="form1" name="form1" action="">
	<h1>PHPSpellCheck &amp;  JavaScript Events</h1>

	
<textarea name="spellfield"  id="spellfield" cols="100" rows="12" >PHP SPell chek haz a fuull set of javascrupt evemts for boht as-youtype and "in dialog" spellchecking.

This example traps the onDialogOpen event and also the onChangeWord event from the As-You-Type spellcheck. 

Others Events are:

onDialogOpen
onDialogComplete  (Perhaps you may wish to submit a form after this event) 
onDialogCancel 
onDialogClose
onChangeLanguage 
onIgnore 
onIgnoreAll 
onChangeWord
onChangeAll 
onLearnWord
onLearnAutoCorrect 
onUpdateFields




</textarea>

<?php
	require "../include.php";	//   "phpspellcheck/include.php" // Full file path to the include.php file in the phpspellcheck Folder
	$mySpell = new SpellCheckButton();
	$mySpell->InstallationPath = "../";	  // "/phpspellcheck/" //  Relative URL of phpspellcheck within your site
	$mySpell->Fields = "spellfield";
	$mySpell->WindowMode = "popup";
	
	echo $mySpell->SpellImageButton();
	?>
	
	
	<script type='text/javascript'>
	var mySpellObject = <?php echo ($mySpell->ID)?>;
	mySpellObject.onDialogOpen = function(){
		alert("onDialogOpen event fired")		
	}
	</script>

	<script type='text/javascript'>
	var mySpellObject = <?php echo ($mySpell->ID)?>;
	mySpellObject.onDialogClose = function(){
		alert("onDialogClose event fired")		
	}
	</script>
	
	
 

	
<?
	$mySpell = new SpellAsYouType();
	$mySpell->InstallationPath = "../"; // "/phpspellcheck/" // Relative URL of phpspellcheck within your site
	$mySpell->Fields = "spellfield";
	echo $mySpell->Activate();
?>

<script type='text/javascript'>
var mySpellObject = <?php echo ($mySpell->ID)?>;
	mySpellObject.onChangeWord = function(){
			alert("onChangeWord event fired from content spellcheck")		
		}
</script>


 </form>
</body>
</html>
