<?php

// configbalmoral.php
// partially cloned from config.php 20200815 1509
// 20220830 2036
// KMB

// BALMORAL 

// a user management suite

// *****************************************************************************************************************************
// ACCESSCONTROL

$accessControlledDir = "vault/";         // forward slash included at end, eg "vault/" 
                                         // to aid pattern matching in header.php
                                         // sub dir below this are auto treated as access controlled
                                         // other dir are treated as available without a login
                                         // therefore all access controlled pages must reside in or below this

// *****************************************************************************************************************************
// APPEARANCE

$cellPadding               = 4;
$cellSpacing               = 4;
$dataCellPadding           = "0px";                                          // padding for mNoteText etc data cells

// html symbols - https://www.w3schools.com/charsets/ref_utf_geometric.asp

$htmlTriangleUp                = "&#9651;";                                      // hyperlink this tab - up arrow
$htmlTriangleRight             = "&#9655;";                                      // hyperlink new tab on right - right arrow

$htmlTickPlain                 = "&#10004;";                                  
$htmlTickGreen                 = "<font color='#00A800'>$htmlTickPlain</font>";                             
$htmlCrossPlain                = "&#10008;";                                 
$htmlCrossRed                  = "<font color='#C00000'>$htmlCrossPlain</font>";  
$htmlWarningAmber              = "<font color='#FF6101'>&#x25B2;</font>";        // filled orange triangle

// *****************************************************************************************************************************
// COOKIES

// cookieConsent                            user consent management

$cookieLifeConsent        = 0;              // until the browser is closed - param 3 is set to 0
//$cookieLifeConsent        = 4;              // measured in seconds, 4 seconds
//$cookieLifeConsent        = 10;             // measured in seconds, 10 seconds
//$cookieLifeConsent        = 600;            // measured in seconds, 10 mins
//$cookieLifeConsent        = 1800;           // measured in seconds, 30 mins
//$cookieLifeConsent        = 10800;          // measured in seconds, 3 hours
//$cookieLifeConsent        = 86400;          // measured in seconds, 24 hours
//$cookieLifeConsent        = 1209600;        // measured in seconds, 14 days

// cookieLogin                              user login management

//$cookieLifeLogin          = 0;              // until the browser is closed - param 3 is set to 0
//$cookieLifeLogin          = 4;              // measured in seconds, 4 seconds
//$cookieLifeLogin          = 600;            // measured in seconds, 10 mins
//$cookieLifeLogin          = 1800;           // measured in seconds, 30 mins
//$cookieLifeLogin          = 10800;          // measured in seconds, 3 hours
$cookieLifeLogin          = 86400;          // measured in seconds, 24 hours
//$cookieLifeLogin          = 1209600;        // measured in seconds, 14 days

// cookieCalledPage                         courtesy tool for users logging in via book marked "other page" 

$cookieLifeCalledPage     = 5;              // measured in seconds, 5 seconds

// *****************************************************************************************************************************
// DATABASE
 
$hostName                      = "localhost";                                         

$dbName                        = "REDACTED";  
$dbUser                        = "REDACTED";                                      
$dbPass                        = "REDACTED";        // production version will need an updated password

$loginEnabledSite              = 1;                   // ordinarily, is this a user login oriented website? cookies evaluate 0 or 1 accordingly

// *****************************************************************************************************************************
// DATETIME

$bellaToday                    = date("Ymd");
$bellaNow                      = date("Ymd Hi");
$bellaIso8601                  = date("Ymd Hi s");

$sqlNow                        = date("YmdHis");      // mySQL format - used in activity log and token

$mailServerDay                 = date("D"); 
$mailServerDate                = date("d M Y H:i:s"); 
$mailServerDate                = "Date: ".$mailServerDay.", ".$mailServerDate." +0000";

$unixNow                       = time();
$timeZoneOffset                = date("Z");           // offset from GMT in seconds

$year                          = date("Y");           // used in footer
$oneDay                        = (24*60*60);          // one day in seconds for SQL Unix conversions etc

// *****************************************************************************************************************************
// DIRECTORY

$pairedWebappA                 = "balmoral";          // lower case name of the main directory holding the UMP
$pairedWebappB                 = "windsor";           // lower case name of the main directory holding the Consumer Portal

// *****************************************************************************************************************************
// DISPLAYLIMITS

$limitShort                    = 10;
$limitMid                      = 50;
$limitLong                     = 100;
    
$dashboardLimitTickets         = $limitShort;
$dashboardLimitFrozen          = $limitShort;
$dashboardLimitActivity        = $limitMid;
$dashboardLimitUsers           = $limitMid;

$usersLimitSummary             = $limitShort;

// *****************************************************************************************************************************
// DOMAIN

$maxUploadBytes                = 30000;
$maxUploadKB                   = $maxUploadBytes/1000;
$maxUploadKB                   = $maxUploadKB."K";

// *****************************************************************************************************************************
// FOOTER

$footerDialogue001             = "someStrapLine";
$copyrightHolder               = "someOrg";
$copyrightStartYear            = 2022;

// *****************************************************************************************************************************
// LOGINS

$loginAttemptsLimit            = 5;

$loginLockOutTimeMins          = 30;
$loginLockOutTimeSecs          = $loginLockOutTimeMins*60;

$userTargetPage                = "dashboard.php";                  // default - may be overuled by cookieCalledPage 

// *****************************************************************************************************************************
// MAIL

$devWorkMailAddress            = "dummy"; 

// *****************************************************************************************************************************
// PBKDF2 HASHING

$salt                          = "REDACTED";                                            
$counter                       = "REDACTED";                                          
$keyLength                     = "REDACTED";                                                 

// *****************************************************************************************************************************
// USER

// new starters get these values by default
// later, newer buttons (20230101 0831 undecided) may be restricted to higher grade staff (eg HR tools, annual reviews and the like)

$starterButtons                = array("100101","100102","100103","100104","100105","100106","100107","100108");
$starterGrade                  = 200201;
                                 
// *****************************************************************************************************************************
// WEEDING

// default values for invoke.php 
// these ought not to be modified in the config file
// but later the settings can be modified in table weeding_dayss
// refer to the docs for more advice (esp on GDPR)

$weedingDaysSearch            = 1;                                                 
$weedingDaysFrequent          = 730;                                                 
$weedingDaysStandard          = 2555;                                                 

// *****************************************************************************************************************************

?>