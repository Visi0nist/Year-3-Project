<?php

// users.php 
// 20221226 2132
// KMB

// BALMORAL 

// a user management suite

// *****************************************************************************************************************************
// DOCUMENTATION

// Balmoral User Management Portal Documentation 00N.docx

////////////////////////////////////////////////////////////////////////////////
// Users                                                                      //
////////////////////////////////////////////////////////////////////////////////
// Add                           // Import                                    //
////////////////////////////////////////////////////////////////////////////////
// Recent                        // Historic                                  //
////////////////////////////////////////////////////////////////////////////////
// Active Most                   // Active Least                              //
////////////////////////////////////////////////////////////////////////////////

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

$getDataRecentHistoric            = 0;
$getDataMostLeast                 = 0;
$checkMailIsUnique                = 0;

$showTableAdd                     = 0;
$showTableImport                  = 0;
$showTableRecent                  = 0;
$showTableHistoric                = 0;
$showTableMost                    = 0;
$showTableLeast                   = 0;
$showNewUserForm                  = 0;
$showNewUserExistsAlready         = 0;
$showNewUserError001              = 0;
$showNewUserPasswordForm          = 0;
$newUserWriteToDB                 = 0;
$addUserWriteSuccess              = 0;
$showSingleUserPage               = 0;

$usersTableAdd                    = "";
$usersTableImport                 = "";
$usersTableRecent                 = "";
$usersTableHistoric               = "";
$usersTableMost                   = "";
$usersTableLeast                  = "";
$usersTableNewUser001             = "";
$usersTableNewUser002             = "";
$usersTableNewUser003             = "";
$usersTableNewUser004             = "";

// *****************************************************************************************************************************
// 2222VALIDATE


   // 20230320 1756 xxx resume search will bring you here with known systemno - show data and recent activity for systemno
   //searchThisTab16537
   //searchNewTab16538


if(1==0)
    {
        //
    }
elseif (    $_POST['fOriginator'] == "dashboardThisTab16510"
         || $_POST['fOriginator'] == "dashboardNewTab16511"
         || $_POST['fOriginator'] == "usersThisTab16518"
         || $_POST['fOriginator'] == "usersNewTab16519"
         || $_POST['fOriginator'] == "usersThisTab16520"
         || $_POST['fOriginator'] == "usersNewTab16521"
         || $_POST['fOriginator'] == "usersThisTab16533"
         || $_POST['fOriginator'] == "usersNewTab16524"
         || $_POST['fOriginator'] == "usersThisTab16535"
         || $_POST['fOriginator'] == "usersNewTab16526"
       )
    {
        // ***************************************************************************************************
        // validation parameters
        
        $validationScore       = 0;
        $validationTarget      = 0;
        
        // ***************************************************************************************************
        // get the data from the form, increment the validationTarget each time
        
        $validationTarget++;   $fOriginator                 = trim(htmlentities($_POST['fOriginator']));        	    
        
        $echoID                = 43578 ;
        
        $echoString            = $echoString."<P>$echoID fOriginator is $fOriginator ";
        $echoString            = $echoString."<P>$echoID userDataPage ";
        
        
        
    }
elseif (    $_POST['fOriginator'] == "usersFinalise16530"
         || $_POST['fOriginator'] == "usersContinue1652"
       )
    {
        // ***************************************************************************************************
        // validation parameters
        
        $validationScore       = 0;
        $validationTarget      = 0;
        
        // ***************************************************************************************************
        // get the data from the form, increment the validationTarget each time
        
        $validationTarget++;   $fOriginator                 = trim(htmlentities($_POST['fOriginator']));        	    
        $validationTarget++;   $fNewUserLoginText           = trim(htmlentities($_POST['fNewUserLoginText']));        	    
        $validationTarget++;   $fNewUserPassword1           = trim(htmlentities($_POST['fNewUserPassword1']));
        $validationTarget++;   $fNewUserPassword2           = trim(htmlentities($_POST['fNewUserPassword2']));
        
        // ***************************************************************************************************
        // validate the data
        
        // fOriginator                              alpha, numeric                       4 to 50 char 
        // fNewUserLoginText                        standard mail test                  2 to N char
        // fNewUserPassword1                        alpha - numeric       !*-+()%&^@    12 to 50 char
        // fNewUserPassword2                        alpha - numeric       !*-+()%&^@    12 to 50 char
        
        // set a fallBackErrCode in case all validation items are OK, but a mismatch between $validationScore and $validationTarget occurs
        
        $fallBackErrCode                                                                                                                                                                                                   = 71228; 
        if (preg_match("/^[a-zA-Z0-9]{4,50}$/",                      $fOriginator))       { $validationScore++; }                                                             else { $fOriginator          = ""; $lErrCode = 71229;}
        if (preg_match("/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i",      $fNewUserLoginText)) { $validationScore++; }                                                             else { $fNewUserLoginText    = ""; $lErrCode = 71230;}
        if (preg_match("/^[a-zA-Z0-9\!\*\-\+\(\)\%\&\^\@]{12,50}$/", $fNewUserPassword1)) { $validationScore++; }                                                             else { $fNewUserPassword1    = ""; $lErrCode = 71231; $lNarrative ="invalid fNewUserPassword1";}
        if (preg_match("/^[a-zA-Z0-9\!\*\-\+\(\)\%\&\^\@]{12,50}$/", $fNewUserPassword2)) { $validationScore++; }                                                             else { $fNewUserPassword2    = ""; $lErrCode = 71232; $lNarrative ="invalid fNewUserPassword2";}
        	    
        // ***************************************************************************************************
        // evaluate validation
        
        if ($fNewUserPassword1 !== $fNewUserPassword2)
            {
                $lErrCode      = 71233;
                $errMsg        = "The passwords did not match - Error code ".$lErrCode;
                $lNarrative    = "fNewUserPassword1 != fNewUserPassword2";
                
                $showNewUserPasswordForm   = 1;
            }
        elseif($validationTarget != 0 && $validationScore == $validationTarget)
            {
                $lNarrative                = "password valid";
                
                $newUserWriteToDB          = 1;
            }
        else
            {
                if ($lErrCode == FALSE) {$lErrCode = $fallBackErrCode;} 
                
                $errMsg                    = "Password validation failed - Error code ".$lErrCode;
                $lNarrative                = "password validation failed";
                
                $showNewUserPasswordForm   = 1;
            }
            
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
            
    }
elseif (    $_POST['fOriginator'] == "usersContinue16525"
         || $_POST['fOriginator'] == "usersBack1629"
       )
    {
        // ***************************************************************************************************
        // validation parameters
        
        $validationScore       = 0;
        $validationTarget      = 0;
        
        // ***************************************************************************************************
        // get the data from the form, increment the validationTarget each time
        
        $validationTarget++;   $fOriginator                 = trim(htmlentities($_POST['fOriginator']));        	    
        $validationTarget++;   $fNewUserLoginText           = trim(htmlentities($_POST['fNewUserLoginText']));  
        
        $echoID                = 43560 ;
        
        //$echoString            = $echoString."<P>$echoID fOriginator is $fOriginator ";
        //$echoString            = $echoString."<P>$echoID fNewUserLoginText is $fNewUserLoginText ";
              	    
        // ***************************************************************************************************
        // validate the data
        
        // fOriginator                              alpha, numeric                       4 to 50 char 
        // fNewUserLoginText                       standard mail test                  2 to N char
        
        // set a fallBackErrCode in case all validation items are OK, but a mismatch between $validationScore and $validationTarget occurs
        
        $fallBackErrCode                                                                                                                                                                                                  = 71224; 
        if (preg_match("/^[a-zA-Z0-9]{4,50}$/",                     $fOriginator))       { $validationScore++; }                                                             else { $fOriginator          = ""; $lErrCode = 71225;}
        if (preg_match("/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i",     $fNewUserLoginText)) { $validationScore++; }                                                             else { $fNewUserLoginText    = ""; $lErrCode = 71226;}
        	    
        // ***************************************************************************************************
        // evaluate validation
        
        if($validationTarget != 0 && $validationScore == $validationTarget)
            {
                $lNarrative              = "email valid";
                $checkMailIsUnique       = 1;
            }
        else
            {
                if ($lErrCode == FALSE) {$lErrCode = $fallBackErrCode;} 
                
                $errMsg         = $errMsg."<P>The data was not understood - Error code ".$lErrCode;
                $lNarrative     = "invalid mail address";
                
                $showNewUserError001     = 1;
            }
        
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
            
    }
elseif (    $_POST['fOriginator'] == "usersNewUser16522"
         || $_POST['fOriginator'] == "usersBack16526"
         || $_POST['fOriginator'] == "usersBack16527"
         || $_POST['fOriginator'] == "usersBack16529"
       )
    {
        // ***************************************************************************************************
        // validation parameters
        
        $validationScore       = 0;
        $validationTarget      = 0;
        
        // ***************************************************************************************************
        // get the data from the form, increment the validationTarget each time
        
        $validationTarget++;   $fOriginator                 = trim(htmlentities($_POST['fOriginator']));        	    
        $validationTarget++;   $fNewUserLoginText           = trim(htmlentities($_POST['fNewUserLoginText']));  
        
        // fOriginator                              alpha, numeric                       4 to 50 char 
        // fNewUserLoginText                        can be empty on a first call
        // fNewUserLoginText                        standard mail test,                  2 to N char
        
        // set a fallBackErrCode in case all validation items are OK, but a mismatch between $validationScore and $validationTarget occurs
        
        // 20221231 1237 xxx resume mod tablelist for table error and log these errCode as they are written
        
        $fallBackErrCode                                                                                                                                                                                                   = 71222; 
        if (preg_match("/^[a-zA-Z0-9]{4,50}$/",                         $fOriginator))       { $validationScore++; }                                                             else { $fOriginator       = ""; $lErrCode = 71223;}
        if($fNewUserLoginText == FALSE)                                                      { $validationScore++; }                                                                                                                   
        elseif (preg_match("/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i",     $fNewUserLoginText)) { $validationScore++; }                                                             else { $fNewUserLoginText = ""; $lErrCode = 71227;}
        	    
        // ***************************************************************************************************
        // evaluate validation
        
        if($validationTarget != 0 && $validationScore == $validationTarget)
            {
                $lNarrative              = "prepare new user form";
                $showNewUserForm         = 1;
            }
        else
            {
                if ($lErrCode == FALSE) {$lErrCode = $fallBackErrCode;} 
                
                $errMsg                  = $errMsg."<P>The data was not understood - Error code ".$lErrCode;  // errors are displayed automatically by header.php
                $lNarrative              = "new user button fOriginator validation failed";
            }
            
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
    }
elseif (    $_POST['fOriginator'] == FALSE
         || $_POST['fOriginator'] == "usersBack16528"
         || $_POST['fOriginator'] == "dashboardUsers16509"
       )
    {
        // in effect, a first call to this script
        $lErrCode            = 0 ; 
        $lNarrative          ="call to $cScript";
                
                        $echoID             = 43542 ;
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
                        
        $echoID      = 43543 ;
        //$echoString  = $echoString."<P>$echoID cActiveApp is $cActiveApp ";
        
        $getDataRecentHistoric    = 1;
        $getDataMostLeast         = 1;
        
        $showTableAdd             = 1;
        $showTableImport          = 1;
        $showTableRecent          = 1;
        $showTableHistoric        = 1;
        $showTableMost            = 1;
        $showTableLeast           = 1;
                        
    }
 
// *****************************************************************************************************************************
// 3333DATABASE WORK

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm

// ***********************************************************************************************************
// getDataMostLeast
// 20230101 1519

if ($getDataMostLeast == 1)
    {
        // *******************************************************************************
        // stage 001
            
        // set variables
        
        $cSystemnoMostLeast        = array();
        
        $cLognoMostLeast           = array();
        $cDateTimeMostLeast        = array();
        $cWebAppMostLeast          = array();
        $cScriptMostLeast          = array();
        $cNarrativeMostLeast       = array();
        
        
        // *******************************************************************************
        // stage 002
        
        //open connection
        $echoID    = 43571 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
        
        // *******************************************************************************
        // stage 003
      
        // Declare query
        $query = " 
                         SELECT 
                                mSystemno, COUNT(*) as frequency 
                           FROM 
                                log 
                       GROUP BY 
                                mSystemno 
                       ORDER BY 
                                frequency 
                          DESC
                 ";
      
        // *******************************************************************************
        // stage 004
      
        // get results 
        
        $result = mysqli_query($mysqli,$query);
        
     
        while ($row = mysqli_fetch_assoc($result))
            {
                extract ($row); 
  
                $echoID       = 43574 ;
     
                //$arrayString = print_r($row, TRUE); $echoString = $echoString."<P>$echoID row is $arrayString ";         
        
                if ($mSystemno != FALSE)
                    {
                        // records where mSystemno is 0 are ignored (casual visitors to non ACL pages)
                        
                        $cSystemnoMostLeast[]    = htmlspecialchars($mSystemno);                 
                    }
            }         
         
         if (mysqli_error($mysqli) != FALSE)
            {
                $echoID     = 43572 ;
                $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
            }          
            
            
        // *******************************************************************************
        // stage 005
        
        // for each cSystemnoMostLeast find most recent activity
        
        /// 20230101 1736 xxx resume this will list 1000 users - limit in to 30 - that needs 2 
        // separate arrays for 30 most and 30 least, so mods to this block and to the 2x show block
        // using their own unique arrays
        
        $cSystemnoMostLeastElements = count($cSystemnoMostLeast);
     
        for ($cSystemnoMostLeastCycle=0; $cSystemnoMostLeastCycle<$cSystemnoMostLeastElements ; $cSystemnoMostLeastCycle++)
            {
                // *******************************************************************************
                // stage 006
      
                // Declare query
                $query = " 
                             SELECT 
                                     mLogno, 
                                     mLogDateTime, 
                                     mWebApp, 
                                     mScript, 
                                     mNarrative 
                                FROM 
                                     log 
                               WHERE 
                                     mSystemno = ? 
                            ORDER BY 
                                     mLogno 
                                DESC
                               LIMIT 
                                     1
                         ";
      
                // *******************************************************************************
                // stage 007
        
                // Prepare statement        
                if($stmt = $mysqli->prepare($query))
                    {
                         // Prepare parameters
                         $mSystemno = $cSystemnoMostLeast[$cSystemnoMostLeastCycle]; 
          
                         // Bind parameters
                         $stmt->bind_param
                             (
                                 "i", 
                                 $mSystemno
                             );
            
                        //Execute it
                        $stmt->execute();
            
                        if (mysqli_error($mysqli) != FALSE)
                             {
                                 $echoID     = 43576 ;
                                 $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                             } 
                     
                        // Bind results 
                        $stmt->bind_result
                            (
                                $oLogno,
                                $oDateTime,
                                $oWebApp,
                                $oScript,
                                $oNarrative
                            );
             
                        // Fetch the value
                        while($stmt->fetch())
                            {
                                $cLognoMostLeast[]             = htmlspecialchars($oLogno);
                                $cDateTimeMostLeast[]          = htmlspecialchars($oDateTime);
                                $cWebAppMostLeast[]            = htmlspecialchars($oWebApp);
                                $cScriptMostLeast[]            = htmlspecialchars($oScript);
                                $cNarrativeMostLeast[]         = allow_permitted_html(htmlspecialchars($oNarrative));
                            }
              
                        // Clear memory of results
                        $stmt->free_result();
              
                        // Close statement
                        $stmt->close();
                
                    }
            }        
        
        $echoID       = 43577 ;
     
        //$arrayString = print_r($cDateTimeMostLeast, TRUE); $echoString = $echoString."<P>$echoID cDateTimeMostLeast is $arrayString ";         
        
        // *******************************************************************************
        // stage 00N
        
        // Close connection
        $mysqli->close();         
        
        // *******************************************************************************
        
        $echoID       = 43573 ;
     
        //$arrayString = print_r($cSystemnoMostLeast, TRUE); $echoString = $echoString."<P>$echoID cSystemnoMostLeast is $arrayString ";         
    }

// ***********************************************************************************************************
// newUserWriteToDB
// 20221231 2155

if ($newUserWriteToDB == 1)
    {
        // *******************************************************************************
        // stage 001
            
        // set variables
	    
	    // basic privileges $starterButtons $starterGrade are set in config
	    
	    $tNewSystemno               = 0;
	    
	    if($fNewUserPassword1 == FALSE)
	        {
                $echoID                 = 43568 ;
                $errMsg                 = $errMsg."<P>$echoID the system encountered an empty string";
    	        $tNewUserHashedPassword = "dummyVar";
	        }
	    else
	        {
    	        $tNewUserHashedPassword = pbkdf2($fNewUserPassword1, $salt, $counter, $keyLength);
	        }
        
        	    
        $echoID       = 43561 ;
     
        $echoString  = $echoString."<P>$echoID newUserWriteToDB is $newUserWriteToDB ";
        //$echoString  = $echoString."<P>$echoID fNewUserPassword1 is $fNewUserPassword1 ";
        //$echoString  = $echoString."<P>$echoID tNewUserHashedPassword is $tNewUserHashedPassword ";
        
        //********************************************************
        // stage 002
        
        // get latest mSystemno for later steps
        
        $query                      = "SHOW TABLE STATUS LIKE 'system'"; 
        $db                         = mysqli_connect("$hostName", "$dbUser","$dbPass","$dbName");
        $result                     = mysqli_query($db,$query);
        $data                       = mysqli_fetch_array($result);
        $tNewSystemno               = htmlspecialchars($data['Auto_increment']);	    
        
        mysqli_close($db);
     
        $echoID      = 43564 ;
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
        //$echoString  = $echoString."<P>$echoID tNewSystemno is $tNewSystemno ";
        
        // *******************************************************************************
        // stage 003
        //open connection
        
        $echoID    = 43562 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}        
        
        // *******************************************************************************
        // stage 004
        // new user systemno
        
        // Prepare statement
        $stmt = $mysqli->prepare ("INSERT INTO system   (
                                                             mUserLoginText, 
                                                             mUserPassword, 
                                                             mSystemStartDateTime 
                                                           ) 
                                                      values 
                                                           (
                                                               ?, 
                                                               ?, 
                                                               ?
                                                            )"
                                 );           
             
                // Prepare parameters
                $mUserLoginText        = $fNewUserLoginText;
                $mUserPassword         = $tNewUserHashedPassword;
                $mSystemStartDateTime  = $sqlNow;
        
                // Bind parameters
                $stmt->bind_param
                    (
                        "sss",
             
                        $mUserLoginText,
                        $mUserPassword,
                        $mSystemStartDateTime
                    );               
                
               // Execute it
               $stmt->execute();
      
               if (mysqli_error($mysqli) != FALSE)
                    {
                        $echoID     = 43563 ;
                        $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                        
                        $errMsg    = $errMsg."<P>$echoID add error - user not written";
                    } 
               else
                    {
                        $addUserWriteSuccess++; 
                        // all of INSERT INTO system, INSERT INTO system_button_bond N times, INSERT INTO system_grade_bond
                    }               
                     
                // Close statement
                $stmt->close();
                
        // *******************************************************************************
        // stage 005
        // new user default navbar buttons
        
        $starterButtonsElements = count($starterButtons);
     
        for ($starterButtonsCycle=0; $starterButtonsCycle<$starterButtonsElements ; $starterButtonsCycle++)
            {
                // Prepare statement
                $stmt = $mysqli->prepare ("INSERT INTO system_button_bond
                                                                          (
                                                                            mSystemno,
                                                                            mButtonDatano,
                                                                            mButtonStartDateTime
                                                                          ) 
                                                                  values 
                                                                          (
                                                                              ?, 
                                                                              ?, 
                                                                              ?
                                                                          )"
                                         );              
             
                 // Prepare parameters
                 $mSystemno              = $tNewSystemno;
                 $mButtonDatano          = $starterButtons[$starterButtonsCycle];
                 $mButtonStartDateTime   = $sqlNow;
        
                 // Bind parameters
                 $stmt->bind_param
                     (
                         "iis",

                         $mSystemno,
                         $mButtonDatano,
                         $mButtonStartDateTime
                     );               
                
                 // Execute it
                 $stmt->execute();
      
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 43565 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                         
                         $errMsg    = $errMsg."<P>$echoID add error - button not written";
                     } 
                 else
                     {
                        $addUserWriteSuccess++; 
                        // all of INSERT INTO system, INSERT INTO system_button_bond N times, INSERT INTO system_grade_bond
                     }
                     
                 // Close statement
                 $stmt->close();
            }
        
        // *******************************************************************************
        // stage 006
        // new user grade
        
        // Prepare statement
        $stmt = $mysqli->prepare ("INSERT INTO system_grade_bond
                                                               (
                                                                 mSystemno,
                                                                 mGradeDatano,
                                                                 mGradeStartDateTime
                                                               ) 
                                                       values 
                                                               (
                                                                   ?, 
                                                                   ?, 
                                                                   ?
                                                               )"
                                 );              
             
         // Prepare parameters
         $mSystemno             = $tNewSystemno;
         $mGradeDatano          = $starterGrade;
         $mGradeStartDateTime   = $sqlNow;
        
         // Bind parameters
         $stmt->bind_param
             (
                 "iis",

                 $mSystemno,
                 $mGradeDatano,
                 $mGradeStartDateTime
             );               
        
         // Execute it
         $stmt->execute();
      
        if (mysqli_error($mysqli) != FALSE)
             {
                 $echoID     = 43567 ;
                 $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                 
                 $errMsg    = $errMsg."<P>$echoID add error - grade not written";
             }
         else
             {
                 $addUserWriteSuccess++; 
                 // all of INSERT INTO system, INSERT INTO system_button_bond N times, INSERT INTO system_grade_bond
             } 
             
         // Close statement
         $stmt->close();
        
        // *******************************************************************************
        // stage 007
        // evaluate writeToDB
        
         if($addUserWriteSuccess == $starterButtonsElements + 2)
             {
                 // all of INSERT INTO system, INSERT INTO system_button_bond N times, INSERT INTO system_grade_bond
        
                 $lNarrative    = "newUserWriteToDB completed";
                 
                 $showSingleUserPage = 1;

              }
         else
              {
                 $lNarrative    = "newUserWriteToDB failed";
                 
                 $errMsg    = $errMsg."<P><font color='red'>A sysadmin must purge any bad records in tables system, system_button_bond, system_grade_bond before trying again.</font>";
              }
                         
                        $cActivityCode = 200120;
               
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
                
                 
        //********************************************************
        // stage 00N
        // Close connection
                
        $mysqli->close();
    }

// ***********************************************************************************************************
// checkMailIsUnique
// 20221231 1515

if ($checkMailIsUnique == 1)
    {
        // *******************************************************************************
        // stage 001
        
        // set variables
        
        $tSystemno = 0;
        
        // *******************************************************************************
        // stage 002
        
        $echoID    = 43551 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
      
        // *******************************************************************************
        // stage 003
      
        // Declare query
        $query = " 
                    SELECT 
                           mSystemno
                      FROM 
                           system 
                     WHERE 
                           mUserLoginText = ?
                 ";
      
        // *******************************************************************************
        // stage 004
      
        // Prepare statement
        if($stmt = $mysqli->prepare($query)) 
            {
                // Prepare parameters
                $mUserLoginText = $fNewUserLoginText; 
              
                $stmt->bind_param
                    (
                        "s",
                        $mUserLoginText
                    );  
          
                //Execute it
                $stmt->execute();
     
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 43552 ;
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
                        $tSystemno          = htmlspecialchars($oSystemno);
                    }
 
                // Clear memory of results
                $stmt->free_result();                    
                  
                // Close statement
                $stmt->close();
            }
 
        if($tSystemno != FALSE)
            {
                $showNewUserExistsAlready  = 1; 
            }
        else
            {
                $showNewUserPasswordForm  = 1; 
            }
        
        //********************************************************
        // stage 00N
      
        // Close connection
              
        $mysqli->close();
        
    }

// **********************************************************************************
// getDataRecentHistoric
// 20221227 0806

if ($getDataRecentHistoric == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // in theory it is possible to have an inactive user, in the user table but not in the log table
        // these are most likely suppliers, and are permitted
        // archaic instances of inactive users are resolved in housekeeping.php
        
        // *******************************************************************************
        // stage 001
        
        // set variables
        
        $cSystemnoHi        = array();
        
        $cLognoHi           = array();
        $cDateTimeHi        = array();
        $cWebAppHi          = array();
        $cScriptHi          = array();
        $cNarrativeHi       = array();
        
        $cLognoLo           = array();
        $cDateTimeLo        = array();
        $cWebAppLo          = array();
        $cScriptLo          = array();
        $cNarrativeLo       = array();
        
        // *******************************************************************************
        // stage 002
        
        //open connection
        $echoID    = 43544 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
        
        // *******************************************************************************
        // stage 003
      
        // Declare query
        $query = " 
                      SELECT 
                             mSystemno 
                        FROM 
                             log 
                    GROUP BY 
                             mSystemno
                 ";
      
        // *******************************************************************************
        // stage 004
      
        // Prepare statement      
        if($stmt = $mysqli->prepare($query))
              {
                  //Execute it
                  $stmt->execute();
            
                  if (mysqli_error($mysqli) != FALSE)
                      {
                          $echoID     = 43545 ;
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
                          //ignore log records with no user
                          
                          if($oSystemno != FALSE)
                              {
                                  $cSystemnoHi[]       = htmlspecialchars($oSystemno);
                              }
                      }
              
                  // Clear memory of results
                  $stmt->free_result();
              
                  // Close statement
                  $stmt->close();
      	      }
      
        $echoID       = 43546 ;
     
        //$arrayString = print_r($cSystemnoHi, TRUE); $echoString = $echoString."<P>$echoID cSystemnoHi is $arrayString ";         
        
        // *******************************************************************************
        // stage 005
        
        // for each cSystemnoHi find most recent activity
        
        $cSystemnoHiElements = count($cSystemnoHi);
     
        for ($cSystemnoHiCycle=0; $cSystemnoHiCycle<$cSystemnoHiElements ; $cSystemnoHiCycle++)
            {
                // *******************************************************************************
                // stage 006
      
                // Declare query
                $query = " 
                             SELECT 
                                     mLogno, 
                                     mLogDateTime, 
                                     mWebApp, 
                                     mScript, 
                                     mNarrative 
                                FROM 
                                     log 
                               WHERE 
                                     mSystemno = ? 
                            ORDER BY 
                                     mLogno 
                                DESC
                               LIMIT 
                                     1
                         ";
      
                // *******************************************************************************
                // stage 007
        
                // Prepare statement        
                if($stmt = $mysqli->prepare($query))
                    {
                         // Prepare parameters
                         $mSystemno = $cSystemnoHi[$cSystemnoHiCycle]; 
          
                         // Bind parameters
                         $stmt->bind_param
                             (
                                 "i", 
                                 $mSystemno
                             );
            
                        //Execute it
                        $stmt->execute();
            
                        if (mysqli_error($mysqli) != FALSE)
                             {
                                 $echoID     = 43547 ;
                                 $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                             } 
                     
                        // Bind results 
                        $stmt->bind_result
                            (
                                $oLogno,
                                $oDateTime,
                                $oWebApp,
                                $oScript,
                                $oNarrative
                            );
             
                        // Fetch the value
                        while($stmt->fetch())
                            {
                                $cLognoHi[]             = htmlspecialchars($oLogno);
                                $cDateTimeHi[]          = htmlspecialchars($oDateTime);
                                $cWebAppHi[]            = htmlspecialchars($oWebApp);
                                $cScriptHi[]            = htmlspecialchars($oScript);
                                $cNarrativeHi[]         = allow_permitted_html(htmlspecialchars($oNarrative));
                            }
              
                        // Clear memory of results
                        $stmt->free_result();
              
                        // Close statement
                        $stmt->close();
                
                    }
            }        
        
        $echoID       = 43548 ;
     
        //$arrayString = print_r($cDateTimeHi, TRUE); $echoString = $echoString."<P>$echoID cDateTimeHi is $arrayString ";         
        
        // *******************************************************************************
        // stage 008
              
        // tidy up the arrays
              
        //                sort the arrays    by $cLognoHi with most recent first - SORT_DESC
        // also clone and sort cloned arrays by $cLognoLo with most recent last  - SORT_ASC
              
        $cDateTimeLo        = $cDateTimeHi         = convert_numeric_to_alphanumeric_keys($cDateTimeHi);
        $cLognoLo           = $cLognoHi            = convert_numeric_to_alphanumeric_keys($cLognoHi);
        $cSystemnoLo        = $cSystemnoHi         = convert_numeric_to_alphanumeric_keys($cSystemnoHi);
        $cWebAppLo          = $cWebAppHi           = convert_numeric_to_alphanumeric_keys($cWebAppHi);
        $cScriptLo          = $cScriptHi           = convert_numeric_to_alphanumeric_keys($cScriptHi);
        $cNarrativeLo       = $cNarrativeHi        = convert_numeric_to_alphanumeric_keys($cNarrativeHi);
        
        array_multisort( 
                         $cDateTimeHi, SORT_DESC, 
                         $cLognoHi,
                         $cSystemnoHi, 
                         $cWebAppHi,
                         $cScriptHi,
                         $cNarrativeHi
                       );
  
        $cDateTimeHi         = convert_alphanumeric_to_numeric_keys($cDateTimeHi);
        $cLognoHi            = convert_alphanumeric_to_numeric_keys($cLognoHi);
        $cSystemnoHi         = convert_alphanumeric_to_numeric_keys($cSystemnoHi);
        $cWebAppHi           = convert_alphanumeric_to_numeric_keys($cWebAppHi);
        $cScriptHi           = convert_alphanumeric_to_numeric_keys($cScriptHi);
        $cNarrativeHi        = convert_alphanumeric_to_numeric_keys($cNarrativeHi);
        
        array_multisort( 
                         $cDateTimeLo, SORT_ASC, 
                         $cLognoLo,
                         $cSystemnoLo, 
                         $cWebAppLo,
                         $cScriptLo,
                         $cNarrativeLo
                       );
  
        $cDateTimeLo         = convert_alphanumeric_to_numeric_keys($cDateTimeLo);
        $cLognoLo            = convert_alphanumeric_to_numeric_keys($cLognoLo);
        $cSystemnoLo         = convert_alphanumeric_to_numeric_keys($cSystemnoLo);
        $cWebAppLo           = convert_alphanumeric_to_numeric_keys($cWebAppLo);
        $cScriptLo           = convert_alphanumeric_to_numeric_keys($cScriptLo);
        $cNarrativeLo        = convert_alphanumeric_to_numeric_keys($cNarrativeLo);
        
        // *******************************************************************************
        // stage 009
              
        // reindex the array keys
              
        $cDateTimeHi         = array_values($cDateTimeHi);
        $cLognoHi            = array_values($cLognoHi);
        $cSystemnoHi         = array_values($cSystemnoHi);          
        $cWebAppHi           = array_values($cWebAppHi);
        $cScriptHi           = array_values($cScriptHi);
        $cNarrativeHi        = array_values($cNarrativeHi);
              
        $cDateTimeLo         = array_values($cDateTimeLo);
        $cLognoLo            = array_values($cLognoLo);
        $cSystemnoLo         = array_values($cSystemnoLo);          
        $cWebAppLo           = array_values($cWebAppLo);
        $cScriptLo           = array_values($cScriptLo);
        $cNarrativeLo        = array_values($cNarrativeLo);
        
        $echoID       = 43549 ;
     
        //$arrayString = print_r($cDateTimeHi, TRUE); $echoString = $echoString."<P>$echoID cDateTimeHi is $arrayString ";         
        //$arrayString = print_r($cLognoHi, TRUE); $echoString = $echoString."<P>$echoID cLognoHi is $arrayString ";         
        //$arrayString = print_r($cSystemnoHi, TRUE); $echoString = $echoString."<P>$echoID cSystemnoHi is $arrayString ";         
        //$arrayString = print_r($cScriptHi, TRUE); $echoString = $echoString."<P>$echoID cScriptHi is $arrayString ";         
        //$arrayString = print_r($cNarrativeHi, TRUE); $echoString = $echoString."<P>$echoID cNarrativeHi is $arrayString ";         

        // *******************************************************************************
        // stage 010
        
        // limit the number of elements to $usersLimitSummary
        // we need temporary arrays for that
        
        $tDateTimeHi        = array();
        $tLognoHi           = array();
        $tSystemnoHi        = array();
        $tWebAppHi          = array();
        $tScriptHi          = array();
        $tNarrativeHi       = array();
        
        $tDateTimeLo        = array();
        $tLognoLo           = array();
        $tSystemnoLo        = array();
        $tWebAppLo          = array();
        $tScriptLo          = array();
        $tNarrativeLo       = array();
        
        $cLognoHiElements = count($cLognoHi);
        
        if ($cLognoHiElements < $usersLimitSummary)
            {
                $usersLimitSummary = $cLognoHiElements;
            }
     
        for ($cLognoHiCycle=0; $cLognoHiCycle<$usersLimitSummary ; $cLognoHiCycle++)
            {
                // push the $usersLimitSummary values into our temporary arrays
                
                $tDateTimeHi[]       = $cDateTimeHi[$cLognoHiCycle];
                $tLognoHi[]          = $cLognoHi[$cLognoHiCycle];
                $tSystemnoHi[]       = $cSystemnoHi[$cLognoHiCycle];
                $tWebAppHi[]         = $cWebAppHi[$cLognoHiCycle];
                $tScriptHi[]         = $cScriptHi[$cLognoHiCycle];
                $tNarrativeHi[]      = $cNarrativeHi[$cLognoHiCycle];
                
                $tDateTimeLo[]       = $cDateTimeLo[$cLognoHiCycle];
                $tLognoLo[]          = $cLognoLo[$cLognoHiCycle];
                $tSystemnoLo[]       = $cSystemnoLo[$cLognoHiCycle];
                $tWebAppLo[]         = $cWebAppLo[$cLognoHiCycle];
                $tScriptLo[]         = $cScriptLo[$cLognoHiCycle];
                $tNarrativeLo[]      = $cNarrativeLo[$cLognoHiCycle];
                
            }        
        
        // make the current arrays equal to the temporary arrays
        
        $cDateTimeHi        = $tDateTimeHi;
        $cLognoHi           = $tLognoHi;
        $cSystemnoHi        = $tSystemnoHi;
        $cWebAppHi          = $tWebAppHi;
        $cScriptHi          = $tScriptHi;
        $cNarrativeHi       = $tNarrativeHi;
                
        $cDateTimeLo        = $tDateTimeLo;
        $cLognoLo           = $tLognoLo;
        $cSystemnoLo        = $tSystemnoLo;
        $cWebAppLo          = $tWebAppLo;
        $cScriptLo          = $tScriptLo;
        $cNarrativeLo       = $tNarrativeLo;
        
        // now we have data for the most recent   N active users
        // now we have data for the most historic N active users
        
        // *******************************************************************************
        // stage 00N
        
        // Close connection
        $mysqli->close();         
        
        
        
        $echoID       = 43550 ;
     
        //$arrayString = print_r($cDateTimeHi, TRUE); $echoString = $echoString."<P>$echoID cDateTimeHi is $arrayString ";                 
        //$arrayString = print_r($cDateTimeLo, TRUE); $echoString = $echoString."<P>$echoID cDateTimeLo is $arrayString ";                 
        
        //$arrayString = print_r($cLognoHi, TRUE); $echoString = $echoString."<P>$echoID cLognoHi is $arrayString ";                 
        //$arrayString = print_r($cLognoLo, TRUE); $echoString = $echoString."<P>$echoID cLognoLo is $arrayString ";                 
        
        //$arrayString = print_r($cSystemnoHi, TRUE); $echoString = $echoString."<P>$echoID cSystemnoHi is $arrayString ";                 
        //$arrayString = print_r($cSystemnoLo, TRUE); $echoString = $echoString."<P>$echoID cSystemnoLo is $arrayString ";                 
        
        //$arrayString = print_r($cScriptHi, TRUE); $echoString = $echoString."<P>$echoID cScriptHi is $arrayString ";                 
        //$arrayString = print_r($cScriptLo, TRUE); $echoString = $echoString."<P>$echoID cScriptLo is $arrayString ";                 
        
        //$arrayString = print_r($cNarrativeHi, TRUE); $echoString = $echoString."<P>$echoID cNarrativeHi is $arrayString ";                 
        //$arrayString = print_r($cNarrativeLo, TRUE); $echoString = $echoString."<P>$echoID cNarrativeLo is $arrayString ";                 
        
    }

// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm

// 20220101 1436 xxx resume $showSingleUserPage 
// takes user to standard single user layout with edit delete tools, just the same as a search would do
// build search.php first

// ***********************************************************************************************************
// showNewUserPasswordForm
// 20221231 1633
    
if ($showNewUserPasswordForm == 1)
    {
        $usersTableNewUser004 = "
        
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            <B>
            Password
            </B>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=6>
            
            <form  style = 'display:inline;'  action = 'users.php'                           method = 'POST'>
            <input type  = 'hidden'           name   = 'fOriginator'                         value  = 'usersBack16529'>
            <input type  = 'hidden'           name   = 'fNewUserLoginText'                   value  = '$fNewUserLoginText'>
            <input type  = 'submit'           class  = 'standardWidthFormButton'             value  = ' < Back 16529 '>
            </form>
            
            </td>
            <td style='text-align: right; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                         
            <form  style = 'display:inline;'  action = 'users.php'                           method = 'POST'>
            <input type  = 'hidden'           name   = 'fOriginator'                         value  = 'usersFinalise16530'>
            <input type  = 'submit'           class  = 'standardWidthFormButton'             value  = ' Finalise > 16530 '>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            </td>
           </tr>
        
           <tr>
            <td style='text-align: center; vertical-align: top; min-width:1px' colspan=7>
            <center>
            <div class='plain_secondary_table'>
    	    <table width=600 border=0 cellpadding=4>
  	         <tr>
    	      <td align=right>
    	      &nbsp;
    	      </td>
    	      <td align=left>
               <P>
               Please check that this mail address has no typos. 
               <P>
               Use the back button if changes are needed.
               
    	      </td>
  	         </tr>
  	         <tr>
    	      <td align=right>
    	      username:
    	      </td>
    	      <td align=left>
    	      <input type='text'        name='fNewUserLoginText'   value='$fNewUserLoginText'  size='50'  readonly   class='greyedOut'>
    	      </td>
  	         </tr>
  	         <tr>
    	      <td align=right>
    	      &nbsp;
    	      </td>
    	      <td align=left>
               <P>
       	       Now choose a password. The minimum length is 12.
               <P>
    	       Passwords may contain only these special characters:
               <P>
       	       ! * - + ( ) % & ^ @ 
               
    	      </td>
  	         </tr>
  	         <tr>
    	      <td align=right>
    	      password:
    	      </td>
    	      <td align=left>
    	      <input type='password'    name='fNewUserPassword1'                                 size='50'>
    	      </td>
  	         </tr>
  	         <tr>
    	      <td align=right>
    	      confirm&nbsp;password:
    	      </td>
    	      <td align=left>
    	      <input type='password'    name='fNewUserPassword2'                                 size='50'>
    	      </td>
  	         </tr>
  	        </table>
  	        </div>
  	        </center>

            </td>
           </tr>
           
          </table>
          </div>
          </form>
          
        ";
    }

// ***********************************************************************************************************
// showNewUserError001
// 20221231 1557
    
if ($showNewUserError001 == 1)
    {
          $usersTableNewUser003  = $usersTableNewUser003."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            <B>
            New user
            </B>
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'>
            <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'usersBack16527'>
            <input type  = 'submit'           class  = 'standardWidthFormButton'                 value  = ' < Back 16527 '>
            </form>
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            <P>
            Please try again - $lNarrative 
            </td>
           </tr>
          
          </table>
          </div>
	    ";
    }



// ***********************************************************************************************************
// showNewUserExistsAlready
// 20221231 1522
    
if ($showNewUserExistsAlready == 1)
    {
          $usersTableNewUser002  = $usersTableNewUser002."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            <B>
            New user
            </B>
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'>
            <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'usersBack16526'>
            <input type  = 'submit'           class  = 'standardWidthFormButton'                 value  = ' < Back 16526 '>
            </form>
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            <P>
            This email address is already in use.
            <P>
            Do you want to reset the password?
            <P>
            20221231 1519 xxx resume reset tool
            </td>
           </tr>
          
          </table>
          </div>
	    ";
    }

// ***********************************************************************************************************
// showNewUserForm  
// 20221231 1306 

if ($showNewUserForm == 1)
    {
	    $usersTableNewUser001 = $usersTableNewUser001."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=3>
            <B>
            New user
            </B>
            </td>
           </tr>
           
           <tr>
           
            <td style='text-align: left; vertical-align: middle; min-width:1px; padding:$dataCellPadding' colspan=1>
            <form  style = 'display:inline;'  action = 'users.php'                           method = 'POST'>
            <input type  = 'hidden'           name   = 'fOriginator'                         value  = 'usersBack16528'>
            <input type  = 'submit'           class  = 'standardWidthFormButton'             value  = ' < Back 16528 '>
            </form>
            </td>
            <td style='text-align: left; vertical-align: middle; min-width:1px; padding:$dataCellPadding' colspan=1>
            <form  style = 'display:inline;'  action = 'users.php'                           method = 'POST'>
            mail address:
            <input type='text' name='fNewUserLoginText'     value = '$fNewUserLoginText' size='50'>
            your mail address will be your username
            </td>
            
            <td style='text-align: right; vertical-align: middle; min-width:1px; padding:$dataCellPadding' colspan=1>
            <input type  = 'hidden'           name   = 'fOriginator'                         value  = 'usersContinue16525'>
            <input type  = 'submit'           class  = 'standardWidthFormButton'             value  = ' Continue > 16525 '>
            </form>
            </td>
            
           </tr>
           
          </table>
          </div>
	    
	    ";
    }

// ***********************************************************************************************************
// showTableLeast  
// 20221231 1137

if ($showTableLeast == 1)
    {
	    $usersTableLeast = $usersTableLeast."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            <B>
            Active - Least - low frequency first
            </B>
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Datetime
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            User
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            &nbsp;
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            WebApp
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Script
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Narrative
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            &nbsp;
            <BR>
            
            </b>
            </td>
           </tr>
           
        ";

        for (end($cDateTimeMostLeast); ($cDateTimeMostLeastCycle=key($cDateTimeMostLeast))!==null; prev($cDateTimeMostLeast))
            {
                // display DateTime exclude seconds and use &nbsp;
                $dDateTimeMostLeast = SQLDateTime_to_bellaDateTime($cDateTimeMostLeast[$cDateTimeMostLeastCycle]);
                $dDateTimeMostLeast = str_replace(" ","&nbsp;",substr($dDateTimeMostLeast, 0, 13)); 
                
                // display userno and name and use &nbsp;
                $dSystemnoMostLeast   = $cSystemnoMostLeast[$cDateTimeMostLeastCycle]."&nbsp;namehere";
                
        	    $usersTableLeast = $usersTableLeast."
                <tr>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $dDateTimeMostLeast
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $dSystemnoMostLeast
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'usersThisTab16535'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemnoMostLeast[$cDateTimeMostLeastCycle]'>
                 <input type='submit' name='16531' value=' $htmlTriangleUp '    class='internalLinkButton'>
                 </form>
                 
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cWebAppMostLeast[$cDateTimeMostLeastCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cScriptMostLeast[$cDateTimeMostLeastCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cNarrativeMostLeast[$cDateTimeMostLeastCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'           target ='_blank'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'usersNewTab16536'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemnoMostLeast[$cDateTimeMostLeastCycle]'>
                 <input type='submit' name='16536' value=' $htmlTriangleRight ' class='internalLinkButton'>
                 </form>
                 
                 </td>
                </tr>
                ";
            }        
            
        $usersTableLeast = $usersTableLeast."            
        
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            
            </td>
           </tr>
           
          </table>
          </div>
	    
	    ";
    }

// ***********************************************************************************************************
// showTableMost  
// 20221231 1134

if ($showTableMost == 1)
    {
	    $usersTableMost = $usersTableMost."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            <B>
            Active - Most - high frequency first
            </B>
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Datetime
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            User
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            &nbsp;
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            WebApp
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Script
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Narrative
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            &nbsp;
            <BR>
            
            </b>
            </td>
           </tr>
           
        ";

        $cDateTimeMostLeastElements = count($cDateTimeMostLeast);
     
        for ($cDateTimeMostLeastCycle=0; $cDateTimeMostLeastCycle<$cDateTimeMostLeastElements ; $cDateTimeMostLeastCycle++)
            {
                // display DateTime exclude seconds and use &nbsp;
                $dDateTimeMostLeast = SQLDateTime_to_bellaDateTime($cDateTimeMostLeast[$cDateTimeMostLeastCycle]);
                $dDateTimeMostLeast = str_replace(" ","&nbsp;",substr($dDateTimeMostLeast, 0, 13)); 
                
                // display userno and name and use &nbsp;
                $dSystemnoMostLeast   = $cSystemnoMostLeast[$cDateTimeMostLeastCycle]."&nbsp;namehere";
                
        	    $usersTableMost = $usersTableMost."
                <tr>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $dDateTimeMostLeast
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $dSystemnoMostLeast
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'usersThisTab16533'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemnoMostLeast[$cDateTimeMostLeastCycle]'>
                 <input type='submit' name='16533' value=' $htmlTriangleUp '    class='internalLinkButton'>
                 </form>
                 
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cWebAppMostLeast[$cDateTimeMostLeastCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cScriptMostLeast[$cDateTimeMostLeastCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cNarrativeMostLeast[$cDateTimeMostLeastCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'           target ='_blank'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'usersNewTab16534'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemnoMostLeast[$cDateTimeMostLeastCycle]'>
                 <input type='submit' name='16534' value=' $htmlTriangleRight ' class='internalLinkButton'>
                 </form>
                 
                 </td>
                </tr>
                ";
            }        
            
        $usersTableMost = $usersTableMost."            
        
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            
            </td>
           </tr>
           
          </table>
          </div>
	    
	    ";
    }

// ***********************************************************************************************************
// showTableImport   
// 20221231 1126

if ($showTableImport == 1)
    {
	    $usersTableImport = $usersTableImport."
	    
        <form  style = 'display:inline;'                            action = 'users.php'       method = 'POST'>
        <input type  = 'hidden'                                     name   = 'fOriginator'     value  = 'usersBulkImport16523'>
           
        <div class='primary_table'> 
        <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
            
         <tr>
          <td style='text-align: left; vertical-align: top; min-width:1px' colspan=2>
          <B>
          Import
          </B>
          </td>
         </tr>
           
         <tr>
          <td style='text-align: left; vertical-align: top; min-width:1px' colspan=1>
          A CSV file will be required.
          </td>
          <td style='text-align: right; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
          <input type  ='submit'            class ='standardWidthFormButton'                    value  = ' Bulk Import 16523 > '>
          </td>    
         </tr>
         
         <tr>
          <td style='text-align: left; vertical-align: top; min-width:1px' colspan=2>
          &nbsp;
          </td>
         </tr>
         
        </table>
        </div>
          
        </form>
        
	    ";
    }


// ***********************************************************************************************************
// showTableAdd   
// 20221231 1115

if ($showTableAdd == 1)
    {
	    // The 'new user' data entry screen is not on the first instance of users.php because this first 
	    // instance (of the users page) shows lots of data about lots of users and may contain confidential info 
	    // whilst super users may be entitled to see confidential info, other users may not
	    // and so (to maintain consistency) any new user (whether super user or user) is added
	    // from a separate instance of users.php - and that way a superuser can start the process, and then
	    // say to the new user (eg staff in the same office) "sit here and enter your data"
        
	    $usersTableAdd = $usersTableAdd."
	    
           
        <div class='primary_table'> 
        <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
            
         <tr>
          <td style='text-align: left; vertical-align: top; min-width:1px' colspan=2>
          <B>
          User Management
          </B>
          </td>
         </tr>
           
         <tr>
          <td style='text-align: left; vertical-align: top; min-width:1px' colspan=1>
          Add - click the New User button
          </td>
          <td style='text-align: right; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
          <form  style = 'display:inline;'                            action = 'users.php'       method = 'POST'>
          <input type  = 'hidden'                                     name   = 'fOriginator'     value  = 'usersNewUser16522'>
          <input type  = 'submit'            class ='standardWidthFormButton'                    value  = ' New User 16522 > '>
          </td>
          </form>    
         </tr>
         
         <tr>
          <td style='text-align: left; vertical-align: top; min-width:1px' colspan=1>
          Edit or Delete - search 
          </td>    
          
          <td style='text-align: right; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                <form  style = 'display:inline;' action = 'search.php'    method = 'POST'>
                <input type  = 'hidden'                                   name   = 'fOriginator'      value = 'usersSearch16524'>
                <input class = 'paleBackground'  type   = 'text'          name   = 'fSearchCriteria'  value = ''           size='35'>
                <input class = 'standardWidthFormButton'    type   = 'submit'                                    value = ' Search 16524 > '>
                </form>
          </td>
         </tr>
         
        </table>
        </div>
          
        
        
	    ";
    }
    
// ***********************************************************************************************************
// showTableRecent  
// 20221227 1011 

if ($showTableRecent == 1)
    {
	    $usersTableRecent = $usersTableRecent."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            <B>
            Active - Recent - newest first
            </B>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Datetime
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            User
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            &nbsp;
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            WebApp
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Script
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Narrative
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            &nbsp;
            <BR>
            
            </b>
            </td>
           </tr>
           
        ";
        
        $cDateTimeHiElements = count($cDateTimeHi);
     
        for ($cDateTimeHiCycle=0; $cDateTimeHiCycle<$cDateTimeHiElements ; $cDateTimeHiCycle++)
            {
                // display DateTime exclude seconds and use &nbsp;
                $dDateTimeHi = SQLDateTime_to_bellaDateTime($cDateTimeHi[$cDateTimeHiCycle]);
                $dDateTimeHi = str_replace(" ","&nbsp;",substr($dDateTimeHi, 0, 13)); 
                
                // display userno and name and use &nbsp;
                $dSystemnoHi   = $cSystemnoHi[$cDateTimeHiCycle]."&nbsp;namehere";
                
        	    $usersTableRecent = $usersTableRecent."
                <tr>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $dDateTimeHi
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $dSystemnoHi
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'usersThisTab16518'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemnoHi[$cDateTimeHiCycle]'>
                 <input type='submit' name='16518' value=' $htmlTriangleUp '    class='internalLinkButton'>
                 </form>
                 
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cWebAppHi[$cDateTimeHiCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cScriptHi[$cDateTimeHiCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cNarrativeHi[$cDateTimeHiCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'           target ='_blank'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'usersNewTab16519'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemnoHi[$cDateTimeHiCycle]'>
                 <input type='submit' name='16519' value=' $htmlTriangleRight ' class='internalLinkButton'>
                 </form>
                 
                 </td>
                </tr>
                ";
            }        
            
        $usersTableRecent = $usersTableRecent."            
        
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            
            </td>
           </tr>
           
          </table>
          </div>
	    
	    ";
    }


// ***********************************************************************************************************
// showTableHistoric  
// 20221227 0942 

if ($showTableHistoric == 1)
    {
	    $usersTableHistoric = $usersTableHistoric."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            <B>
            Active - Historic - oldest first
            </B>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Datetime
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            User
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            &nbsp;
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            WebApp
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Script
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            Narrative
            <BR>
            
            </b>
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <b>
            &nbsp;
            <BR>
            
            </b>
            </td>
           </tr>
           
        ";
        
        $cDateTimeLoElements = count($cDateTimeLo);
     
        for ($cDateTimeLoCycle=0; $cDateTimeLoCycle<$cDateTimeLoElements ; $cDateTimeLoCycle++)
            {
                // display DateTime exclude seconds and use &nbsp;
                $dDateTimeLo = SQLDateTime_to_bellaDateTime($cDateTimeLo[$cDateTimeLoCycle]);
                $dDateTimeLo = str_replace(" ","&nbsp;",substr($dDateTimeLo, 0, 13)); 
                
                // display userno and name and use &nbsp;
                $dSystemnoLo   = $cSystemnoLo[$cDateTimeLoCycle]."&nbsp;namehere";
                
        	    $usersTableHistoric = $usersTableHistoric."
                <tr>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $dDateTimeLo
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $dSystemnoLo
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'usersThisTab16520'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemnoLo[$cDateTimeLoCycle]'>
                 <input type='submit' name='16520' value=' $htmlTriangleUp '    class='internalLinkButton'>
                 </form>
                 
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cWebAppLo[$cDateTimeLoCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cScriptLo[$cDateTimeLoCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cNarrativeLo[$cDateTimeLoCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'           target ='_blank'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'usersNewTab16521'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemnoLo[$cDateTimeLoCycle]'>
                 <input type='submit' name='16521' value=' $htmlTriangleRight ' class='internalLinkButton'>
                 </form>
                 
                 </td>
                </tr>
                ";
            }        
            
        $usersTableHistoric = $usersTableHistoric."            
        
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=7>
            
            </td>
           </tr>
           
          </table>
          </div>
	    
	    ";
    }


// *****************************************************************************************************************************
// 5555ECHO HTML

require ('../header.php');

echo "
          $usersTableNewUser004
          $usersTableNewUser003
          $usersTableNewUser002
          $usersTableNewUser001
          
          <table border=0 cellpadding=4 cellspacing=4 width=100%>
          
           <tr>
            <td width=50% style='text-align: left; vertical-align: top; min-width:1px'>
            $usersTableAdd
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $usersTableImport 
            </td>
           </tr>
           
           <tr>
            <td width=50% style='text-align: left; vertical-align: top; min-width:1px'>
            &nbsp;
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            &nbsp;
            </td>
           </tr>
           
           <tr>
            <td width=50% style='text-align: left; vertical-align: top; min-width:1px'>
            $usersTableRecent
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $usersTableHistoric
            </td>
           </tr>
           
           <tr>
            <td width=50% style='text-align: left; vertical-align: top; min-width:1px'>
            &nbsp;
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            &nbsp;
            </td>
           </tr>
           
           <tr>
            <td width=50% style='text-align: left; vertical-align: top; min-width:1px'>
            $usersTableMost 
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $usersTableLeast 
            </td>
           </tr>
           
           <tr>
            <td width=50% style='text-align: left; vertical-align: top; min-width:1px'>
            &nbsp;
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            &nbsp;
            </td>
           </tr>
           
          </table>
";

require ('../footer.php');

?>