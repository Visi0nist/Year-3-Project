<?php

// header.php
// 20230321 0934
// KMB

// WINDSOR
// an add in for BALMORAL 

// Long Term Tracker


// *****************************************************************************************************************************
// 0011HOUSEKEEPING

ini_set('displayErrors', 0);        // toggle 0 for off, 1 for on (1 only to aid devwork, then revert to 0)
//error_reporting(E_ALL);

//require ('foundations/configwindsor.php');
//require ('foundations/commonfns.php');

// *****************************************************************************************************************************
// 1111VARIABLES SET OR RESET 

// the next 3 lines are normally active in the main scrips, they are not active in header.php to avoid conflicts

//$cScript                 = get_script_name($_SERVER['SCRIPT_FILENAME']);
//$cHTTPReferrer           = $_SERVER['HTTP_REFERER'];
//$cWebapp                 = get_webapp_name($_SERVER['SCRIPT_FILENAME']); 

$thisHost                = $_SERVER['HTTP_HOST'];                             // gives domain and not dir path
$thisScript              = $_SERVER['PHP_SELF'];                              // gives dir path *and script* without domain name
$fullDirScriptPath       = "https://".$thisHost.$thisScript;
$webappPath              = get_webapp_path($fullDirScriptPath); 
$jsConsentManagerURL     = $webappPath."consentmanager.php?cookieResponse=";  // cookie management if needed is done in the core webapp path

$docTypeString           = '<!DOCTYPE html>';
$htmlString              = '<html lang="en-GB">';  // single quotes because double quotes must surround en-GB

$timeOutString           = "";
$headerString            = "";
$auxiliaryLinkRelPath    = "";
$navbarLHS               = "";
$navbarMID               = "";
$navbarRHS               = "";
$navbarCustom001         = "";
$navbarCustom002         = "";
$infoBar                 = "";

$stylesheet001           = "windsor001.css";
$bodyTag                 = "<body>";                // default

$cookieConsentGiven      = 0;                       // default
$cookieModalActive       = 0;                       // default
$cTokenLogin             = 0;                       // default
$userMustLoginNow        = 0;
$prepareCustomButtonBar  = 0;

// devwork

$allCookies            = $_SERVER["HTTP_COOKIE"];   // 20221128 2121 dev work - rem out later

$echoID                = 21422 ;
//$echoString            = $echoString."<P><B>$echoID cScript is $cScript</b>";
//$echoString            = $echoString."<P><B>$echoID fullDirScriptPath is $fullDirScriptPath</b>";
//$arrayString           = print_r($allCookies, TRUE); $echoString = $echoString."<P>$echoID allCookies is $arrayString ";                

// ******************************************************
// cookieLoginManagement

// when the user does not yet have a current valid login on the current device 
// write a temp (5 second) cookie to remember a call to an access controlled page (from a bookmark or similar) 

// https://www.markbrannigan.com/w/doku.php?id=php_cookies#cookie_flowchart_002

// read established cookies, if any 

// cookieCalledPage serialize

$cookieConsentGiven = $_COOKIE['cookieConsent'];
$cTokenLogin        = $_COOKIE['cookieLogin'];
$userCalledPage     = $_COOKIE['cookieCalledPage'];

// *****************************************************************************************************************************
// 2222VALIDATE

if ($cScript == "impasse")
    {
        // no special requirements
        // no cookieConsent management
        // no cookieLogin management
        // no cookieCalledPage required
        
        // let the user see the impasse page
    }
elseif ($cookieConsentGiven == False)
    {
        // cookieModal required, probably 
        
        $echoID             = 21404 ;
        //$echoString         = $echoString."<P>$echoID cookieConsentGiven is $cookieConsentGiven ";
        
        // cookieConsent has not yet been sought
        // subject to a few exceptions
        // set body tag so that cookieModal is invoked
        
        if (    $cScript == "consentmanager"
             || $cScript == "invoke" 
             || $cScript == "loggedout" 
             || $cScript == "timedout" 
           )
            {
                // permitted exception pages
                
                $echoID             = 21405 ;
                //$echoString         = $echoString."<P>$echoID cookieModalActive is $cookieModalActive ";
            }
        else
            {
                $cookieModalActive = 1;
                
                $bodyTag = "<body onLoad='cookieModal()'>";
                
                $echoID             = 21406 ;
                //$echoString         = $echoString."<P>$echoID scriptFilename is $cScript ";
            }
            
            
            
    }
    
if(strpos($fullDirScriptPath, $accessControlledDir) == True || $cScript == "login")
    {
        // ***************************************************************
        // stage 001
        
        // preamble
        
        // user wants an access controlled page
        // or
        // user wants the login page
        
        $echoID             = 21407 ;
        //$echoString         = $echoString."<P>$echoID string $accessControlledDir exists within the string $fullDirScriptPath ";
        
        // ***************************************************************
        // stage 002
                
        // evaluate cTokenLogin
        
        if ($cTokenLogin != FALSE) 
            {
                // authenticate 
                
                // cookieLogin looks OK on the local hard disk, but does it match a token on the server?
                
                // get mTokenDatano from table token
                // if no match, then there is no valid token
                // else we have valid token, and we can get mSystemno from table system_token_bond
                
                // *******************************************************************************
                // stage 003
        
        	    // Open connection
	    
                $echoID    = 21408 ;
                $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
                if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}          
                
                // *******************************************************************************
                // stage 004
        
                // Declare query
                $query = " 
                             SELECT 
                                     mTokenDatano
                               FROM 
                                     token
                              WHERE 
                                     mTokenLogin      = ?
                                AND
                                     mSession         = ?
                         ";  
                
                // *******************************************************************************
                // stage 005
        
                // Prepare statement
                if($stmt = $mysqli->prepare($query)) 
                    {
                         // Prepare parameters
                         $mTokenLogin    = $cTokenLogin;
                         $mSession       = session_id();
             
                         // Bind parameters
                         $stmt->bind_param
                             (
                                 "ss",
                           
                                 $mTokenLogin,
                                 $mSession
                             );               
                     
                         // Execute it
                         $stmt->execute();
           
                         if (mysqli_error($mysqli) != FALSE)
                             {
                                 $echoID     = 21409 ;
                                 $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                             }               
                  
                        // Bind results 
                        $stmt->bind_result
                            (
                                $oTokenDatano 
                            );
       
                        // Fetch the value
                        while($stmt->fetch())
                            {
                                $cTokenDatano   = htmlspecialchars($oTokenDatano);
                            }
   
                        // Clear memory of results
                        $stmt->free_result();
        
                        // Close statement
                        $stmt->close();
                    }
                
                // *******************************************************************************
                // stage 006
                
                // evaluate
        
                if($cTokenDatano == FALSE)
                    {
                        // current cTokenLogin from cookieLogin is not matched by a cTokenDatano from the DB
                
                        $userMustLoginNow = 1;                        
                    }
                else
                    {
                        // current cTokenLogin from cookieLogin matches cTokenDatano from the DB
                        // get the user mSystemno so that we can look up more credentials later
                        
                        // *******************************************************************************
                        // stage 007
        
                        // Declare query
                        $query = " 
                                             SELECT 
                                                     mSystemno
                                               FROM 
                                                     system_token_bond
                                              WHERE 
                                                     mTokenDatano     = ?
                                         ";  
                
                        // *******************************************************************************
                        // stage 008
        
                        // Prepare statement
                        if($stmt = $mysqli->prepare($query)) 
                                    {
                                         // Prepare parameters
                                         $mTokenDatano   = $cTokenDatano;
             
                                         // Bind parameters
                                         $stmt->bind_param
                                             (
                                                 "i",
                                           
                                                 $mTokenDatano
                                             );               
                                     
                                         // Execute it
                                         $stmt->execute();
           
                                         if (mysqli_error($mysqli) != FALSE)
                                             {
                                                 $echoID     = 21410 ;
                                                 $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                                             }               
                  
                                        // Bind results 
                                        $stmt->bind_result
                                            (
                                                $oSystemno 
                                            );
       
                                        // Fetch the value
                                        while($stmt->fetch())
                                            {
                                                $cSystemno   = htmlspecialchars($oSystemno);
                                            }
   
                                        // Clear memory of results
                                        $stmt->free_result();
        
                                        // Close statement
                                        $stmt->close();
                                    }
                                    
                        $echoID             = 21411 ;
                        //$echoString         = $echoString."<P>$echoID cSystemno is $cSystemno ";  
                        
                        // *******************************************************************************
                        // stage 009
                        
                        $prepareCustomButtonBar = 1;
        
                    }
                        
                // *******************************************************************************
                // stage 010
        
                // Close connection
                $mysqli->close();
                
                // *******************************************************************************
                // stage 011
                
                // already logged in?
                
                // did a logged in user call the login page (probably, from a bookmark)
                // take the user to dashboard instead
                
                if (strpos($fullDirScriptPath, "login.php") == TRUE && $cSystemno == TRUE)
                    {
                        // goto dashboard
                        
                        $target = $webappPath.$accessControlledDir."dashboard.php";
                        
                        $echoID             = 21412 ;
                        //$echoString         = $echoString."<P>$echoID cSystemno is $cSystemno ";
                        //$echoString         = $echoString."<P>$echoID webappPath is $webappPath ";
                        //$echoString         = $echoString."<P>$echoID accessControlledDir is $accessControlledDir ";
                        //$echoString         = $echoString."<P>$echoID target is $target ";
                        
                        header("Location:$target");
                        exit;                         
                    }
                elseif($cScript != "login")
                    {
                        // the called page is in the access controlled dir
                        // mod the html head tags to get the correct link rel filepaths - up one dir
        
                        $auxiliaryLinkRelPath = "../";
                                
                        $echoID             = 21413 ;
                        //$echoString         = $echoString."<P>$echoID cSystemno is $cSystemno ";
                        //$echoString         = $echoString."<P>$echoID calledDirScriptPath is $calledDirScriptPath ";
                        //$echoString         = $echoString."<P>$echoID webappPath is $webappPath ";
                        //$echoString         = $echoString."<P>$echoID accessControlledDir is $accessControlledDir ";
                        //$echoString         = $echoString."<P>$echoID target is $target ";
                    } 
            }
        else
            {
                // there is no cookieLogin
                
                $userMustLoginNow = 1;
            }
    }
    
if($userMustLoginNow == 1 && $cScript != "login")
    {
        // user must first goto login, and a message is added to the screen
        
        $target = $webappPath."login.php?signal=please"; 
        
        $echoID             = 21414 ;
        //$echoString         = $echoString."<P>$echoID target is $target ";
        
        header("Location:$target");
        exit;
    }
            
// ***************************************************************************************
// timeOutString
// 20220212 1402

// do not use timeouts on the log in, the log out and various other pages as specified in this "if test"
        
// *****************************************************************************************************************************
// 3333DATABASE  

// ***************************************************************************************
// subroutine
// YYYYMMDD

// ***************************************************************************************
// buildButtonBar
// 20221218 1428

if ($prepareCustomButtonBar == 1)
    {
        // ***************************************************************
        // stage 000
        
        // preamble
        
        // get the buttons for this user
        // 20221218 1428 this is an all or nothing operation at the moment
        // in future, there may be grades that do not get certain buttons
        // right now we have only superusers and clients and nothing in the middle
        // clients do not have access to the UMP
        
        // the logged in user is cSystemno, established from the cookie and the cross ceck against the DB
        
        // ***************************************************************
        // stage 001
        
        // variables
        
        $cButtonDatano     = array();
        $cButtonText       = array();
        $cButtonScript     = array();
        
        // *******************************************************************************
        // stage 003
        
        // Open connection
	    
        $echoID    = 21415 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}          
        
        // *******************************************************************************
        // stage 004
        
        // Declare query
        $query = " 
                           SELECT 
                                   mButtonDatano
                             FROM 
                                   consumer_button_bond
                            WHERE 
                                   mSystemno      = ?
                              AND
                                   mButtonEndDateTime
                          IS NULL
                 ";  
        
        // *******************************************************************************
        // stage 005
        
        // Prepare statement
        if($stmt = $mysqli->prepare($query)) 
            {
                // Prepare parameters
                $mSystemno = $cSystemno ; 
                
                $stmt->bind_param
                    (
                        "i",
                        $mSystemno
                    );  
            
                //Execute it
                $stmt->execute();
       
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 21416 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                     }
 
                // Bind results 
                $stmt->bind_result
                    (
                        $oButtonDatano
                    );
       
                // Fetch the value
                while($stmt->fetch())
                    {
                        $cButtonDatano[]      = htmlspecialchars($oButtonDatano);
                    }
   
                // Clear memory of results
                $stmt->free_result();                    
                    
                // Close statement
                $stmt->close();
            }
            
        //********************************************************
        // stage 006
        
        // sort 
        // we need them in standard order no matter when or how they were invoked (or revoked)
        
        sort($cButtonDatano);
            
        $echoID       = 21417 ;
        
        //$echoString = $echoString."<P>$echoID fLoggedInUser  is $fLoggedInUser  ";
        //$arrayString = print_r($cButtonDatano, TRUE); $echoString = $echoString."<P>$echoID cButtonDatano is $arrayString "; 
        
        //********************************************************
        // stage 007
        
        // get button names
        
        //$cButtonDatano     = array();
        //$cButtonText       = array();
        //$cButtonScript     = array();
        
        $cButtonDatanoElements = count($cButtonDatano);
     
        for ($cButtonDatanoCycle=0; $cButtonDatanoCycle<$cButtonDatanoElements ; $cButtonDatanoCycle++)
            {
                $echoID     = 21418 ;

                //$echoString = $echoString."<P>$echoID cButtonDatano is $cButtonDatano[$cButtonDatanoCycle]";
                
                if($stmt = $mysqli->prepare ("   SELECT 
                                                        mButtonText 
                                                   FROM 
                                                        button_text 
                                                  WHERE 
                                                        mButtonDatano = ?
                                            ")
                  ) 
                    {
                        // Prepare parameters
                        $mButtonDatano = $cButtonDatano[$cButtonDatanoCycle]; 
                
                        $stmt->bind_param
                            (
                                "i",
                                $mButtonDatano
                            );  
            
                        //Execute it
                        $stmt->execute();
               
                        if (mysqli_error($mysqli) != FALSE)
                             {
                                 $echoID     = 21419 ;
                                 $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                             } 
 
                        // Bind results 
                        $stmt->bind_result
                            (
                                $oButtonText
                            );
       
                        // Fetch the value
                        while($stmt->fetch())
                            {
                                $tButtonText             = htmlspecialchars($oButtonText);
                                
                                // button names are normal
                                // script names are lower case without spaces
                                
                                $cButtonText[]        = $tButtonText; 
                                $tButtonText          = str_replace(" ", "", $tButtonText);
                                $cButtonScripts[]     = strtolower($tButtonText).".php"; 
                            }
   
                        // Close statement
                        $stmt->close();
                    }                
            }
            
        $echoID       = 21420 ;
  
        //$arrayString = print_r($cButtonText, TRUE); $echoString = $echoString."<P>$echoID cButtonText is $arrayString ";
        //$arrayString = print_r($cButtonScripts, TRUE); $echoString = $echoString."<P>$echoID cButtonScripts is $arrayString ";
        
        
        
        
        
        // *******************************************************************************
        // stage 00N
        
        // Close connection
        $mysqli->close();
        
        
    }





// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// ***************************************************************************************
// subroutine
// YYYYMMDD

// ***************************************************************************************
// paths and files

$faviconPathFile  = $auxiliaryLinkRelPath."images/favicon.ico";
$mainCssPathFile  = $auxiliaryLinkRelPath."styles/".$stylesheet001;
$modalCssPathFile = $auxiliaryLinkRelPath."styles/cookieconsentmodal002.css";
$logoPathFile     = $auxiliaryLinkRelPath."images/logo.png";

// ***************************************************************************************
// cookieModalFunctionString

$cookieModalFunctionString = "

<script>
function cookieModal(){modal.style.display = 'block';}
</script>

";

// ***************************************************************************************
// cookieModalJavaScriptString

// JS string variables must be declared in single quotes, double quotes will bork the PHP
// var target = 'consentmanager.php?cookieResponse='+selected;

$cookieModalJavaScriptString = "

<script>
function cookieModalResponse(selected) 
    {
        var jsConsentManagerURL = '$jsConsentManagerURL';
        var target              = jsConsentManagerURL+selected;
        window.location         = target;        
    } 
</script>

";

// ***************************************************************************************
// cookieModalHtmlString

$cookieModalHtmlString = "

<div id='cookieModalText' class='modal'>

  <div class='modal-box'>
   <div class='modal-top'>
    <span class='modal-left'>
    Cookies &amp; Privacy
    </span>
    <span class='modal-right' aria-label='close modal' onclick='cookieModalResponse(99);'>
    &times;
    </span>
    </div>
    <div class='modal-core'>
     This website uses essential cookies only.
     <br><br>
     Please accept.
     <br><br>
     <center>
      <input class='standardWidthFormButton' type='button' value='Accept' onclick='cookieModalResponse(1);'>
      &nbsp; &nbsp; &nbsp; &nbsp; 
      <input class='standardWidthFormButton' type='button' value='Reject' onclick='cookieModalResponse(99);'>
     </center>
   </div>
  </div>

</div>

<script>
var modal = document.getElementById('cookieModalText');
</script>

";  




// ***************************************************************************************
// buildButtonBar
// 20221218 1428

if ($prepareCustomButtonBar == 1)
    {
        // ***************************************************************
        // stage 000
        
        // preamble
        
        // logged in users
        // LHS logo which links to dashboard.php 
        // MID all buttons + log out
        // RHS search form
                
        // ***************************************************************
        // stage 001
        
        // assembly
        
        $cButtonDatanoElements = count($cButtonDatano);
        
        if($cButtonDatanoElements != FALSE)
            {
                // user buttons are present in table system_button_bond
                
                for ($cButtonDatanoCycle=0; $cButtonDatanoCycle<$cButtonDatanoElements ; $cButtonDatanoCycle++)
                    {
                        $navbarCustom001 = $navbarCustom001."<a href='".$cButtonScripts[$cButtonDatanoCycle]."' class='navbarButton'>".$cButtonText[$cButtonDatanoCycle]."</a> ";
                        
                        $echoID     = 21421 ;
                        //$echoString = $echoString."<P>$echoID cButtonDatano is $cButtonDatano[$cButtonDatanoCycle]";
                    }
                    
            }
            
        $navbarLHS = "<a href='dashboard.php'><img src='$logoPathFile' border=0 alt='logo'></a>";
        $navbarMID = $navbarCustom001."<a href='../logout.php' class='navbarButton'>Log&nbsp;Out</a>";
        $navbarRHS = "
        
                <form  style = 'display:inline;' action = 'search.php'    method = 'POST'>
                <input type  = 'hidden'                                   name   = 'fOriginator'      value = 'headerSearch34108'>
                <input class = 'paleBackground'  type   = 'text'          name   = 'fSearchCriteria'  value = ''           size='35'>
                <input class = 'navbarButton'    type   = 'submit'                                    value = ' Search 34108 '>
                </form>
        
                ";
        
        if ($dScript == "Search")
            {
                $dScript = "Search Results";
            }        
                
        $infoBar = "
    
        <div class='wrapperSimple100'>
        
         <table width=100% cellpadding=0 cellspacing=0 border=0>
           <tr>
           <tr>
             <td>
             &nbsp;
             </td>
             <td colspan=3>
             $dScript
             </td>
             <td style='text-align: right; vertical-align: top; min-width:1px; padding:4px;'>
                
             $cDisplayName
        
             </td>
             <td>
             &nbsp;
             </td>
           </tr>        
         </table>
         
        </div> 
       
        ";
            
    }
else
    {
        // ***************************************************************
        // stage 000
        
        // preamble
        
        // logged out users 
        // LHS logo which links to index.php 
        // MID empty
        // RHS log in button
        
        // ***************************************************************
        // stage 001
        
        // assembly
        
        $navbarLHS = "<a href='index.php'><img src='$logoPathFile' border=0 alt='logo'></a>";
        $navbarMID = "&nbsp;";
        $navbarRHS = "<a href='login.php' class='navbarButton'>Log&nbsp;In</a>";   
        
    }
        
// ***************************************************************************************
// navbar finalise
// 20230102 0809

$navbarCustom002 = $navbarCustom002."

       <div class='wrapperGradient100'>
        
         <table width=100% cellpadding=0 cellspacing=0 border=0>
           <tr>
             <td width=20 style='text-align: left; vertical-align: middle; min-width:1px' colspan=1>
             &nbsp;
             </td>
             <td width=10 style='text-align: left; vertical-align: middle; min-width:1px; padding:0.3em 0 0.15em 0;' colspan=1>        
             
             $navbarLHS 
             
             </td>
             <td width=20 style='text-align: left; vertical-align: middle; min-width:1px' colspan=1>
             &nbsp;
             </td>
             <td style='text-align: left; vertical-align: middle; min-width:1px' colspan=1>
             
             $navbarMID 
             
             </td>
             <td width=350em style='text-align: right; vertical-align: middle; min-width:1px' colspan=1>
             
             $navbarRHS        
             
             </td>
             <td width=20 style='text-align: left; vertical-align: middle; min-width:1px' colspan=1>
             &nbsp;
             </td>
           </tr>
         </table>
         
       </div> 
             
       ";
       
$navbarCustom002 = $navbarCustom002.$infoBar;

// *****************************************************************************************************************************
// 5555ECHO HTML

echo $docTypeString."\n".$htmlString;    // 20210508 1831 there is no shortcut to doing this - must concatenate exactly as shown

echo "

    <head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <meta name='format-detection'   content='telephone=no'>
    <link rel='shortcut icon'       type='image/x-icon'      href='$faviconPathFile'>
    <link rel='stylesheet'          type='text/css'          href='$mainCssPathFile'>
    <link rel='stylesheet'          type='text/css'          href='$modalCssPathFile'>
    <title>$title</title>
    $timeOutString
    </head>
    
    $bodyTag
    
";
  
if ($cookieModalActive == 1)
    {
        echo "
        
            $cookieModalFunctionString
            $cookieModalJavaScriptString
            
            $cookieModalHtmlString    
        ";
    }
    
echo "

$navbarCustom002
    
";    

if ($errMsg != FALSE)
    {
        echo "<center><P><font color='red'>$errMsg</font></P></center>";
    }

if ($echoString != FALSE)
    {
        echo "<P>$echoString";
    }
    
?>