<?php

// consentmanager.php 
// 20230321 0934
// KMB

// WINDSOR
// an add in for BALMORAL 

// Long Term Tracker


// *****************************************************************************************************************************
// 0011HOUSEKEEPING

ini_set('displayErrors', 0);        // toggle 0 for off, 1 for on (1 only to aid devwork, then revert to 0)
//error_reporting(E_ALL);

require ('foundations/configwindsor.php');
require ('foundations/commonfns.php');

$cScript                    = get_script_name($_SERVER['SCRIPT_FILENAME']);
$cWebapp                    = get_webapp_name($_SERVER['SCRIPT_FILENAME']); 
$cIPAddress                 = get_ip_address($_SERVER['HTTP_CLIENT_IP'],$_SERVER['HTTP_X_FORWARDED_FOR'],$_SERVER['REMOTE_ADDR']);
$cHTTPReferrer              = $_SERVER['HTTP_REFERER'];

// *****************************************************************************************************************************
// 1111VARIABLES SET OR RESET 

$dWebapp                    = ucwords(strtolower($cActiveApp));   // capitalise the first letter of the webapp name
$title                      = "$dWebapp cookie consent manager";
$echoString                 = "";
$cookieWriteCookieConsent   = 0;

// *****************************************************************************************************************************
// 2222VALIDATE

$cookieResponse = $_GET['cookieResponse'];

        if ($cookieResponse == 1)
            {
                $echoID     = 21423 ;
                //$echoString = $echoString."<P>$echoID cookieResponse is $cookieResponse ";
        
                // (in the querystring) cookies accepted
                
                $cookieWriteCookieConsent = 1;
            }
        else
            {
                $echoID     = 21424 ;
                //$echoString = $echoString."<P>$echoID cookieResponse is $cookieResponse ";
        
                // (in the querystring) cookies rejected 
                // or cookie modal closed with the X
                
                $target = "impasse.php?referrer=$cHTTPReferrer";
                header("Location:$target");
                exit;
            }
        
// *****************************************************************************************************************************
// 3333DATABASE  

// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// ***************************************************************************************************
// cookieWriteCookieConsent
// 20211230 0728

if($cookieWriteCookieConsent == 1)
    {
        // ***********************************************************
        // stage 000
        
        // preamble
        
        // cookieResponse from user is 1, allow cookies (else the user has already been taken to impasse)
        
        // ***********************************************************
        // stage 001
        
        // writeCookie
        
        $cookieName   = "cookieConsent";              // the name of the actual cookie
        $cookieValue  = $cookieResponse;              // the param inside the cookie
        $cookieExpiry = $cookieLifeConsent;           // see config
        
        setcookie($cookieName, $cookieValue, $cookieExpiry, "/");
        
        // ***********************************************************
        // stage 002
        
        // take the user back to wherever they were supposed to be
        
        $target = $cHTTPReferrer;
        header("Location:$target");
        exit;
    }

// *****************************************************************************************************************************
// 5555ECHO HTML

// 20221128 2135 this 5 5 5 5 section is redundant, it was used during devwork, before 
// all the header("Location:$target") commands were ready

require ('header.php');
         
echo"

<P>
202211282041flag
<P>
cHTTPReferrer is $cHTTPReferrer

<center>

<P>
$title

<P>
$messageString

</center>

";    

require ('footer.php');

?> 