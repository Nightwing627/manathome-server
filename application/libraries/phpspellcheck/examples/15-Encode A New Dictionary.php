<?php
// Encodes a text file into a PHPSpellCheckDictionary
// View in your browser - the Save As.... /phpspellcheck/dictionaries/my_dictionary_name.dic
// If you thing other would bennefit - please share at www.phpspellcheck.com

require "../include.php"   ;
$spellcheckObject = new PHPSpellCheck();
$strWholeDict = file_get_contents("assets/wordlist.txt");
$ArrWholeDict = explode("\n",$strWholeDict);
$spellcheckObject = new PHPSpellCheck();
echo $spellcheckObject  -> BuildDictionary( $ArrWholeDict );

// Save the result as an ISO-8859 encoded .dic textfile.  Use CR & LF for new lines.
?>