<?php
#  Class PHPSpellCheck - A SpellChecking Class for PHP allowing you to build custom spellchecking applications.

require "../include.php"   ;

#	Create a PHPSpellCheck Core Engine Instance ;
$spellcheckObject = new PHPSpellCheck();

#	Copy and Paste your license key here.  Buy one onlline at www.phpspellcheck.com 
$spellcheckObject -> LicenceKey="TRIAL";


#	BASIC SETTINGS
$spellcheckObject -> IgnoreAllCaps = false;
$spellcheckObject -> IgnoreNumeric = false;
$spellcheckObject -> CaseSensitive = true;


# Set up the file path to the (.dic) dictionaries folder
$spellcheckObject -> DictionaryPath = ("../dictionaries/");

# Sets the tollerance of the spellchecker to 'unlikely' suggestions. 0=intollerant ... 10=very tollerant  
$spellcheckObject -> SuggestionTollerance = 1;

# Loads a dictionary - you can load more than one at the same time */
 $spellcheckObject -> LoadDictionary("English (International)") ;
#Add vocabulary to the spellchecer from a text file loaded from the DictionaryPath*/
$spellcheckObject -> LoadCustomDictionary("custom.txt");


/* Alternative methods to load vocabulary
$spellcheckObject -> LoadCustomDictionaryFromURL( $URL );
$spellcheckObject ->AddCustomDictionaryFromArray(array("popup","nonsensee"));
/*

/* Ban a list of words which will never be alloed as correct spellings.  Ths is great for filtering profanicy.*/
$spellcheckObject -> LoadCustomBannedWords("language-rules/banned-words.txt"); 
/*
You can also add banned words from an array which you could easily populate from an SQL query
//$spellcheckObject -> AddBannedWords(array("primary"));
*/

/* Load a lost of Enforced Corrections from a file.  This allows you to enforce a spelling suggestion for a specific word or acronym.*/
$spellcheckObject -> LoadEnforcedCorrections("language-rules/enforced-corrections.txt");

/*Load a list of common typing mistakes to fine tune the suggestion performance.*/
$spellcheckObject -> LoadCommonTypos("language-rules/common-mistakes.txt");




# Lets spellcheck and array of words - just for the fun of it
//$words = array("phonetik","orthographicallyidiotic","Wibble","noun","arm","leeg","friut","basketcase","spellchecker","TeeTH","tHe","ALL","it","elephant","bananna");

$words=explode(",","didn't,numbear,eficient,Frrame,neverbe,unintrusive,spellcheck,popup");

foreach ($words as $word){
echo  "Word:".$word."<br/>" ;
$spelledItRight = $spellcheckObject->SpellCheckWord($word);
echo  "SpellCheck:" . ($spelledItRight?"True":"False")."<br/>";
if(!$spelledItRight){
echo  "Suggestions:" . implode(" , ",$spellcheckObject->Suggestions($word))."<br/>";
echo  "Reason:" . $spellcheckObject->ErrorTypeWord($word)."<br/>";
}
echo "<hr/>";
}




?>