<?php

// tickets.php 
// 20221227 0755
// KMB

// BALMORAL 

// a user management suite

// *****************************************************************************************************************************
// DOCUMENTATION

// Balmoral User Management Portal Documentation 00N.docx

// *****************************************************************************************************************************
// 0011HOUSEKEEPING

session_start();                    // no code, only comments, allowed above the session_start line

ini_set('displayErrors', 0);        // toggle 0 for off, 1 for on (1 only to aid devwork, then revert to 0)
//error_reporting(E_ALL);

require ('../foundations/configbalmoral.php');
require ('../foundations/commonfns.php');

$cSessionID         = session_id();
$cUserData          = get_user_data();
$cScript            = get_script_name($_SERVER['SCRIPT_FILENAME']);
$cActiveApp         = get_webapp_name($_SERVER['SCRIPT_FILENAME']);  
$cIPAddress         = get_ip_address($_SERVER['HTTP_CLIENT_IP'],$_SERVER['HTTP_X_FORWARDED_FOR'],$_SERVER['REMOTE_ADDR']);
$cHTTPReferrer      = $_SERVER['HTTP_REFERER'];

// set cookieCalledPage
// scripts in the access controlled dir (the vault) set cookieCalledPage to help navigation for not-yet-logged-in users using bookmarks 

$cookieName         = "cookieCalledPage";                           // the name of the actual cookie
$cookieValue        = $cScript;                                     // the param inside the cookie
$cookieExpiry       = time() + $cookieLifeCalledPage;               // time now + seconds
      
setcookie($cookieName, $cookieValue, $cookieExpiry, "/");  

// *****************************************************************************************************************************
// 1111VARIABLES SET OR RESET 

// primary var

$dScript                 = ucwords(strtolower($cScript));      // capitalise the first letter
$dWebapp                 = ucwords(strtolower($cActiveApp));   // capitalise the first letter
$title                   = "$dWebapp $dScript";
$echoString              = "";

// unpack cUserData

$errMsg                  = $errMsg.$cUserData["commonFnsErrMsg"];
$echoString              = $echoString.$cUserData["commonFnsEchoString"];
$cSystemno               = $cUserData["cSystemno"];
$cNameSurname            = $cUserData["cNameSurname"];
$cNameFirstName          = $cUserData["cNameFirstName"];
$cNameMiddleName         = $cUserData["cNameMiddleName"];
$cNamePronunciation      = $cUserData["cNamePronunciation"];
$cNameKnownAs            = $cUserData["cNameKnownAs"];

if ($cNameKnownAs == FALSE){$cDisplayName="Logged in as ".$cNameFirstName."&nbsp;".$cNameSurname;}else{$cDisplayName="Logged in as ".$cNameKnownAs."$nbsp;".$cNameSurname;}

// local var

$showTableDummy                   = 0;

$getDataDummy                     = 0;

$dashboardTableDummy              = "";

// *****************************************************************************************************************************
// 2222VALIDATE

if ($_POST['fOriginator'] == "someVar")
    {
        // 20221227 0745 xxx resume
        
        $echoID      = 00000 ;
        $echoString  = $echoString."<P>$echoID fOriginator is $fOriginator ";
        	    
    }
else 
    {
        // first call to this script either by login process or by navbar button        
        $lErrCode            = 0 ; 
        $lNarrative          ="call to $cScript";
                
                        $echoID             = 00000 ;
                        //$echoString = $echoString."<P>$echoID tSystemno      $tSystemno";
           	            
                        // ****************************************************************
                        // ****************************************************************
                        // UPDATE_LOG
                        // 20220922 1251
                        // package the data
                        $logData = array (
                                              "mSystemno"      => "$cSystemno ", 
                                              "mActivityCode"  => "$cActivityCode",  
                                              "mLogDateTime"   => "$sqlNow",    
                                              "mOperand"       => "$cOperand",   
                                              "mScript"        => "$cScript", 
                                              "mWebapp"        => "$cActiveApp", 
                                              "mErrCode"       => "$lErrCode",
                                              "mNarrative"     => "$lNarrative",    
                                              "mIPAddress"     => "$cIPAddress", 
                                              "mHTTPReferrer"  => "$cHTTPReferrer" 
                                         );
                        // log this data and collect error info (if any)
                        $logFeedback = update_log($logData); 
                        $errMsg      = $errMsg.$logFeedback["commonFnsErrMsg"];
                        $echoString  = $echoString.$logFeedback["commonFnsEchoString"];
                        // ****************************************************************
                        // ****************************************************************
                        
        $echoID      = 00000 ;
        //$echoString  = $echoString."<P>$echoID cActiveApp is $cActiveApp ";
        
        $showTableDummy           = 1;
        $getDataDummy             = 1;
                        
    }
 
// *****************************************************************************************************************************
// 3333DATABASE WORK

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm


// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm


// *****************************************************************************************************************************
// 5555ECHO HTML

require ('../header.php');

echo "
          <table border=0 cellpadding=4 cellspacing=4 width=100%>
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $dashboardTableDummy
            202212270756placeholder
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $dashboardTableDummy
            202212270756placeholder
            </td>
           </tr>
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $dashboardTableDummy
            202212270756placeholder
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $dashboardTableDummy
            202212270756placeholder
            </td>
           </tr>
          </table>
            

";

require ('../footer.php');

?>