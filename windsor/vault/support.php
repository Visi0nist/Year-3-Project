<?php

// support.php 
// 20230322 0738
// KMB

// WINDSOR
// an add in for BALMORAL 

// Long Term Tracker

// *****************************************************************************************************************************
// 0011HOUSEKEEPING

session_start();                    // no code, only comments, allowed above the session_start line

ini_set('displayErrors', 0);        // toggle 0 for off, 1 for on (1 only to aid devwork, then revert to 0)
//error_reporting(E_ALL);

require ('../foundations/configwindsor.php');
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

$dScript                 = ucwords(strtolower($cScript));   // capitalise the first letter
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

//$getDataActivity                  = 0;
//$getDataUser                      = 0;
//$getDataTicket                    = 0;
//$getDataFrozen                    = 0;

//$dashboardTableActivity           = "";
//$dashboardTableUser               = "";
//$dashboardTableTicket             = "";
//$dashboardTableFrozen             = "";

// *****************************************************************************************************************************
// 2222VALIDATE

if ($_POST['fOriginator'] == "someVar")
    {
        // dummy code
        // unlike most scripts, dashboard does not call itself again (to perform ations)
        // it only calls other scripts
        // however, to maintain a consistent look and feel to all scripts
        // this if-else test is inluded
        // and variables above are set to NIL
        // then reset as required (a little further down)

        $echoID      = 21404 ;
        $echoString  = $echoString."<P>$echoID fOriginator is $fOriginator ";
        	    
    }
else 
    {
        // first call to this script either by login process or by navbar button        
        $lErrCode            = 0 ; 
        $lNarrative          ="call to $cScript";
                
                        $echoID             = 21405 ;
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
                        
        $echoID      = 21425 ;
        //$echoString  = $echoString."<P>$echoID cActiveApp is $cActiveApp ";
        
        //$getDataActivity   = 1;
        //$getDataUser       = 1;
        //$getDataTicket     = 1;
        //$getDataFrozen     = 1;
                        
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

// 20221225 2014 uncharacteristically, the PHP prep is not done here in the case of dashboard.php
// because dashbord has many panels, all of which share the same common array names like cLogno and cLogDateTime 
// if the PHP assembly is left until this stage it would mean that the 3 3 3 3 stage overwrites earlier versions of
// those critical arrays, and in prototyping that showed up as all the panels have the same charaterists as the "Activity"
// panel. Hence, the PHP prep is done within he 3 3 3 3 section at the end of the relevant sub routine

// *****************************************************************************************************************************
// 5555ECHO HTML

require ('../header.php');

echo "
          <table border=0 cellpadding=4 cellspacing=4 width=100%>
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $dashboardTableTicket 20230322030728placeholder
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $dashboardTableFrozen 20230322030728placeholder
            </td>
           </tr>
          </table>

";

require ('../footer.php');

?>