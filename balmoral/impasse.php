<?php

// impasse.php
// 20211230 0715
// KMB

// Vanguard 010
// a simplified content delivery system
  
// ***************************************************************************************
// dependencies
// 20211228 0506

require ('foundations/configbalmoral.php');
require ('foundations/commonfns.php');

// *****************************************************************************************************************************
// 1111VARIABLES SET OR RESET 

$cScript               = get_script_name($_SERVER['SCRIPT_FILENAME']);

$title                 = "Apologies!";

// establish default link for home page within correct dir for this web app
        
$thisHost              = $_SERVER['HTTP_HOST'];              // gives domain and not dir path
$thisScript            = $_SERVER['PHP_SELF'];               // gives dir path *and script* without domain name
$fullDirScriptPath     = "https://".$thisHost.$thisScript;
$webappPath            = get_webapp_path($fullDirScriptPath); 
$backLink              = $webappPath."index.php";
        
// *****************************************************************************************************************************
// 2222VALIDATE

if(!empty($_GET['referrer']))
    {
        // ***************************************************************************************************
        // preamble
        
        // try to set set a bespoke $backLink instead of the default
        
        // the user arrived at impasse.php via consentmanager.php 
        // what was the previous script to that?
        // it's in the query string, and
        // $backLink is that previous script
        
        // ***************************************************************************************************
        // validation parameters
        
        $validationScore       = 0;
        $validationTarget      = 0;
        
        // set lErrCode (here, early on) in case all validation items are OK, but we somehow 
        // have a mismatch between $validationScore and $validationTarget (due to bad code not bad data)
        
        $lErrCode              = 71209 ; 
        $lNarrative            = "login fail";

        // ***************************************************************************************************
        // get the data from the form, increment the validationTarget each time
        
        $validationTarget++;   $referrer                             = trim(htmlentities($_GET['referrer']));
        
	    $echoID                = 43476 ;
        
        //$echoString = $echoString."<P>$echoID referrer               $referrer";
   
        // ***************************************************************************************************
        // validate the data
        
        // referrer                        alpha - numeric       :/.?=-        20 to 150 char
        
        if (preg_match("/^[a-zA-Z0-9\:\/\.\?\=\-]{20,150}$/",          $referrer))         { $validationScore++; } else { $referrer     = ""; $lErrCode = 71210; $lNarrative ="invalid referrer URL";}

        // ***************************************************************************************************
        // did all the data pass all the validation?
        
        if($validationTarget != 0 && $validationScore == $validationTarget)
            {
                $backLink = $referrer;
            }
        
    }
        
// *****************************************************************************************************************************
// 3333DATABASE  

    // 20221129 0520 xxx resume *update_log($logData)* needed here
        
// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// *****************************************************************************************************************************
// 5555ECHO HTML

require ('header.php');

// 20220219 1135 the lone non breaking space is there to encourage the CSS divs to flow 
// consecutively without creating an empty blank line between one wrapper div and the next

echo "

&nbsp;
 
<div class = 'fontMid'>
 
 <center>
 
 <h2>
 $title
 </h2>
 
 <P>
 Without essential cookies this site cannot function.
 <P>
 This site does not use cookies for analytics, nor for tracking user behaviour.
 <P>
 <a href='$backLink'>Try again</a>
 
 </center>
 
</div>

";    

require ('footer.php');

?> 