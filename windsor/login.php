<?php

// login.php 
// 20230321 0934
// KMB

// WINDSOR
// an add in for BALMORAL 

// Long Term Tracker


// *****************************************************************************************************************************
// 0011HOUSEKEEPING

session_start();                    // no code, only comments, allowed above the session_start line

ini_set('displayErrors', 0);        // toggle 0 for off, 1 for on (1 only to aid devwork, then revert to 0)
//error_reporting(E_ALL);

require ('foundations/configwindsor.php');
require ('foundations/commonfns.php');

$cSessionID         = session_id();
$cScript            = get_script_name($_SERVER['SCRIPT_FILENAME']);
$cActiveApp         = get_webapp_name($_SERVER['SCRIPT_FILENAME']);  
$cIPAddress         = get_ip_address($_SERVER['HTTP_CLIENT_IP'],$_SERVER['HTTP_X_FORWARDED_FOR'],$_SERVER['REMOTE_ADDR']);
$cHTTPReferrer      = $_SERVER['HTTP_REFERER'];

$signal             = $_GET['signal']; // if any, occurs only when a user asks for an access controlled page when not already logged in



// *****************************************************************************************************************************
// 1111VARIABLES SET OR RESET 

$dWebapp                 = ucwords(strtolower($cActiveApp));   // capitalise the first letter of the webapp name
$dPageName               = ucwords(strtolower($cScript));   // capitalise the first letter of the script
$title                   = "$dWebapp $dPageName";

$showLoginForm           = 0;
$authenticateLoginData   = 0;
$manageLoginAttempts     = 0;
$generateTokenLogin      = 0;
$takeUserToDesiredPage   = 0;
$fLoginCounterCurrent    = 0;

$echoString              = "";
$loginTable001           = "";
$errMsg                  = "";
$fUserLoginText          = "";
$fUserPassword           = "";
$divertedMessage         = "";

$buttonText              = "Try again";
$loginLockOutTimeText    = $loginLockOutTimeMins." minutes";

// cookieCalledPage serialize

$userCalledPage     = $_COOKIE['cookieCalledPage'];

$echoID                = 21526 ;
//$echoString            = $echoString."<P><B>$echoID cHTTPReferrer is $cHTTPReferrer</b>";
//$echoString            = $echoString."<P><B>$echoID fullDirScriptPath is $fullDirScriptPath</b>";
//$echoString            = $echoString."<P><B>$echoID userCalledPage is $userCalledPage</b>";

// *****************************************************************************************************************************
// 2222VALIDATE

if(empty($_POST['fUserLoginText']))
    {
        // ***************************************************************************************************
	    // no form data - first visit to this script
	    
	    $showLoginForm           = 1;
        $fLoginCounterCurrent    = 1;
	    $buttonText              = "Login";
	    
                        $lErrCode           = 0 ;
                        $lNarrative         = "first call to login";
        
                        $echoID             = 43518 ;
                        //$echoString = $echoString."<P>$echoID tSystemno      $tSystemno";
           	            
                        // ****************************************************************
                        // ****************************************************************
                        // UPDATE_LOG
                        // 20220922 1251
                        // package the data
                        $logData = array (
                                              "mSystemno"      => "$tSystemno ", 
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
elseif($_POST['fOriginator'] == "login16504") 
    {
        // ***************************************************************************************************
        // validation parameters
        
        $validationScore       = 0;
        $validationTarget      = 0;
        
        // set lErrCode (here, early on) in case all validation items are OK, but we somehow 
        // have a mismatch between $validationScore and $validationTarget (due to bad code not bad data)
        
        $lErrCode              = 51167 ; 
        $lNarrative            = "login fail";

        // ***************************************************************************************************
        // get the data from the form, increment the validationTarget each time
        
        $validationTarget++;   $fLoginCounterCurrent                 = trim(htmlentities($_POST['fLoginCounterCurrent']));
        $validationTarget++;   $fUserLoginText                       = trim(htmlentities($_POST['fUserLoginText']));
        $validationTarget++;   $fUserPassword                        = trim(htmlentities($_POST['fUserPassword']));
        
	    $echoID                = 21527 ;
        
        //$echoString = $echoString."<P>$echoID fLoginCounterCurrent    $fLoginCounterCurrent";
        //$echoString = $echoString."<P>$echoID fUserLoginText          $fUserLoginText";
        //$echoString = $echoString."<P>$echoID fUserPassword           $fUserPassword";
   
        // ***************************************************************************************************
        // validate the data
        
        // fLoginCounterCurrent                 numeric                              1 char exactly
        // fUserLoginText                       standard mail test                   2 to N char
        // fUserPassword                        alpha - numeric       !*-+()%&^@     12 to 50 char
        
        if (preg_match("/^[0-9]{1,1}$/",                               $fLoginCounterCurrent)) { $validationScore++; } else { $fLoginCounterCurrent    = ""; $lErrCode = 52173; $lNarrative ="login fail invalid fLoginCounterCurrent";}
        if (preg_match("/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i",        $fUserLoginText))       { $validationScore++; } else { $fUserLoginText          = ""; $lErrCode = 52174; $lNarrative ="login fail invalid fUserLoginText";}
        if (preg_match("/^[a-zA-Z0-9\!\*\-\+\(\)\%\&\^\@]{12,50}$/",   $fUserPassword))        { $validationScore++; } else { $fUserPassword           = ""; $lErrCode = 52175; $lNarrative ="login fail invalid fUserPassword";}

        // ***************************************************************************************************
        // did all the data pass all the validation?
        
        if($validationTarget != 0 && $validationScore == $validationTarget)
            {
                $authenticateLoginData = 1;
                
        	    $echoID                = 21528 ;
        
                //$echoString         = $echoString."<P>$echoID authenticateLoginData is $authenticateLoginData ";
            }
        else
            {
                // invalid data - preg_match failed
                
                // $lErrCode   already set at the preg_match line above
                // $lNarrative already set at the preg_match line above
                
                $manageLoginAttempts   = 1;
                
        	    $echoID                = 21529 ;
        
                //$echoString         = $echoString."<P>$echoID manageLoginAttempts is $manageLoginAttempts ";
                //$echoString         = $echoString."<P>$echoID lErrCode is $lErrCode ";
                //$echoString         = $echoString."<P>$echoID lNarrative is $lNarrative ";
            }        
        
	    
    }
else
    {
	    // there is no else
	    
	    $lErrCode   = 52168; 
	    $lNarrative = "login fail invalid fUserLoginText fOriginator";
	    $errMsg     = "unexpected error - refer to your sysadmin";
    }
 
// *****************************************************************************************************************************
// 3333DATABASE WORK

// **********************************************************************************
// authenticateLoginData
// 20221208 0647

if ($authenticateLoginData == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // check the password is right
        // check if current mGradeDatano is sufficient, only staff can use this portal, not clients
        // any clients who find this login page need to be given an error message
        
        // 20221208 0711 xxx resume check user is not suspended - table and mechanism TBA
        
        // *******************************************************************************
        // stage 001
        
        // variables
        
        $tHashedUserPassword     = pbkdf2($fUserPassword, $salt, $counter, $keyLength);
        
        // *******************************************************************************
        // stage 002
        // open connection
        
        $echoID    = 21529 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}  
        
        // *******************************************************************************
        // stage 003
        // Declare query
        // password
        
        $query = " 
                      SELECT 
                             mSystemno, 
                             mUserPassword,
                             mSystemEndDateTime
                        FROM 
                             system
                       WHERE 
                             mUserLoginText
                           = 
                             ?
                ";  
        
        //********************************************************************************
        // stage 004
        
        // Prepare statement  
        if($stmt = $mysqli->prepare($query)) 
            {
                // Prepare parameters
                $mUserLoginText = $fUserLoginText; 
                
                // Bind parameters
                $stmt->bind_param
                    (
                        "s",
                        $mUserLoginText
                    );  
                    
                //Execute it
                $stmt->execute();
                    
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 21530 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                     } 
                    
                // Bind results 
                $stmt->bind_result
                    (
                        $oSystemno, 
                        $oStoredUserPassword,
                        $oSystemEndDateTime 
                    );
       
                // Fetch the value
                while($stmt->fetch())
                    {
                        $tSystemno            = htmlspecialchars($oSystemno);
                        $tStoredUserPassword  = $oStoredUserPassword;                   // not escaped because the raw data is needed for a comparison (and is not echoed)
                        $tSystemEndDateTime   = htmlspecialchars($oSystemEndDateTime);
                    }
   
                // Clear memory of results
                $stmt->free_result();
        
                // Close statement
                $stmt->close();
                
        	    $echoID                = 21531 ;
        
                //$echoString = $echoString."<P>$echoID tSystemno               $tSystemno";
                //$echoString = $echoString."<P>$echoID tStoredUserPassword               $tStoredUserPassword";
            }
        
        // *******************************************************************************
        // stage 005
        // Declare query
        // grade
        
        $query = " 
                    SELECT 
                            mGradeDatano
                       FROM 
                            system_grade_bond
                      WHERE 
                            mSystemno        
                          = 
                            ?
                        AND
                            mGradeEndDateTime
                    IS NULL
                ";  
        
        //********************************************************************************
        // stage 006
        
        // Prepare statement  
        if($stmt = $mysqli->prepare($query)) 
            {
                // Prepare parameters
                $mSystemno = $tSystemno; 
                
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
                         $echoID     = 21532 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                     } 
                    
                // Bind results 
                $stmt->bind_result
                    (
                        $oGradeDatano 
                    );
       
                // Fetch the value
                while($stmt->fetch())
                    {
                        $tGradeDatano            = htmlspecialchars($oGradeDatano);
                    }
   
                // Clear memory of results
                $stmt->free_result();
        
                // Close statement
                $stmt->close();
                
        	    $echoID                = 21533 ;
        
                //$echoString = $echoString."<P>$echoID tGradeDatano               $tGradeDatano";
            }
        
        //********************************************************
        // stage 00N
        // Close connection
                
        $mysqli->close();
        
        // there is more authenticateLoginData under 4 4 4 4 section
        
    }


// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm

// **********************************************************************************
// signal
// 20221214 0638

if ($signal == "please")
    {
        
        $divertedMessage = "
        
  	    <tr>
    	    <td align=right>
    	    &nbsp;
    	    </td>
    	    <td align=center width=500>
    	    <font color='red'><b>Please log in first - your requested page will follow in a moment</b></font>
    	    <P>&nbsp;
    	    </td>
  	    </tr>
        
        ";
    }


// **********************************************************************************
// authenticateLoginData
// 20221208 0726

if ($authenticateLoginData == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // waterfall procedure
        // these routines must reside in this order
        // * authenticateLoginData
        // * manageLoginAttempts 
        // * generateTokenLogin
        // * takeUserToDesiredPage
        // * invokeLoginFreeze
        // * showLoginForm
        
        // check the password is right
        // check if current mGradeDatano is sufficient, only staff can use this portal, not clients
        // any clients who find this login page need to be given an error message
        
        // 20221208 0711 xxx resume check user is not suspended - table and mechanism TBA
        // 20221208 0730 xxx resume build a way to deal with persistent attempts by clients to access this staff portal
        
        // *******************************************************************************
        // stage 001
        
        // validate
        
        // $tStoredUserPassword is the value previously stored in the DB
        // $tHashedUserPassword is the hash of the login form value for $fUserPassword
        
	    $echoID                = 21534 ;
        
        //$echoString = $echoString."<P>$echoID tStoredUserPassword    $tStoredUserPassword";
        //$echoString = $echoString."<P>$echoID tHashedUserPassword    $tHashedUserPassword";
        
        if($tHashedUserPassword != $tStoredUserPassword)
            {
                // fail - invalid password
                
        	    $echoID                = 21535 ;
       
                //$echoString = $echoString."<P>$echoID login fail password mismatch";
                //$echoString = $echoString."<P>$echoID fUserLoginText is $fUserLoginText";
                //$echoString = $echoString."<P>$echoID tHashedUserPassword is $tHashedUserPassword";
                //$echoString = $echoString."<P>$echoID tStoredUserPassword is $tStoredUserPassword";
                
                $lErrCode            = 52169; 
                $lNarrative          ="login fail password mismatch";
                $manageLoginAttempts = 1;
                
                        $echoID             = 43519 ;
                        //$echoString = $echoString."<P>$echoID tSystemno      $tSystemno";
           	            
                        // ****************************************************************
                        // ****************************************************************
                        // UPDATE_LOG
                        // 20220922 1251
                        // package the data
                        $logData = array (
                                              "mSystemno"      => "$tSystemno ", 
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
        elseif($tGradeDatano  < 200201)
            {
                // fail - not staff
                
        	    $echoID                = 21536 ;
       
                $echoString = $echoString."<P>$echoID login fail not staff";
                
                $lErrCode            = 52176; 
                $lNarrative          ="login fail not staff";
                $manageLoginAttempts = 1;
                
                        $echoID             = 21537 ;
                        //$echoString = $echoString."<P>$echoID tSystemno      $tSystemno";
           	            
                        // ****************************************************************
                        // ****************************************************************
                        // UPDATE_LOG
                        // 20220922 1251
                        // package the data
                        $logData = array (
                                              "mSystemno"      => "$tSystemno ", 
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
        else
            {
                // pass
                // by default a succesful login takes the user to the dashboard
                // but might go to another user selected page
                // see the docs for cookieCalledPage 
                
        	    $echoID                = 21538 ;
       
                $echoString = $echoString."<P>$echoID login pass";
                
                $generateTokenLogin    = 1;
                $takeUserToDesiredPage = 1;
                
                $lErrCode            = 0 ; 
                $lNarrative          ="logged in";
                
                        $echoID             = 21539 ;
                        //$echoString = $echoString."<P>$echoID tSystemno      $tSystemno";
           	            
                        // ****************************************************************
                        // ****************************************************************
                        // UPDATE_LOG
                        // 20220922 1251
                        // package the data
                        $logData = array (
                                              "mSystemno"      => "$tSystemno ", 
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
    }

// **********************************************************************************
// manageLoginAttempts
// 20221208 0726

if ($manageLoginAttempts == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // waterfall procedure
        // these routines must reside in this order
        // * authenticateLoginData
        // * manageLoginAttempts 
        // * generateTokenLogin
        // * takeUserToDesiredPage
        // * invokeLoginFreeze
        // * showLoginForm
        
        // has the user tried to login too many times?
        // the login process is frozen for the user IP address if loginAttemptsLimit is exceeded
        // the penultimate try and the final try show warning messages on screen
                
        // *****************************************************************
        // stage 001
                
        // track attempts
                
        $fLoginCounterCurrent++;
                
        if ($fLoginCounterCurrent > $loginAttemptsLimit)
            {
                // too many attempts 
                        
                $lNarrative       = $lNarrative." login lock out";
                $lErrCode         = 52177 ;
                $errMsg           = $errMsg."<P>The log in facility has been frozen for "
                                             .$loginLockOutTimeText.
                                             "<BR>Do not refresh this page. It will start the &quot;frozen&quot; timer again.<P><a href='"
                                             .$cScript.
                                             ".php'>Start a new login</a><P>Error code "
                                             .$lErrCode;
            }
        elseif ($fLoginCounterCurrent == $loginAttemptsLimit)
            {
    	        $lNarrative       = $lNarrative." login final retry";
                $lErrCode         = 52170 ;
                $errMsg           = $errMsg."<P>This is the final chance to log in.";
            }
        elseif ($fLoginCounterCurrent == ($loginAttemptsLimit - 1))
            {
    	        $lNarrative       = $lNarrative." login warn too many";
                $lErrCode         = 52171 ;
                $errMsg           = $errMsg."<P>The log in facility may be frozen after too many log in attempts.";
            }
        else
            {
    	        $lNarrative       = $lNarrative." permit retry";
                $errMsg           = $errMsg."<P>The data was not understood - Error code ".$lErrCode;
            }
                    
        // *****************************************************************
        // stage 002
                
        // decide what to show to the user
                
        if ($lErrCode == 52172)
            {
                // login frozen
                    
    	        $showLoginForm     = 0;
    	        $invokeLoginFreeze = 1;
            }
        else
            {
    	        $showLoginForm   = 1;
            }                
    }

// **********************************************************************************
// generateTokenLogin
// 20221211 0814

if ($generateTokenLogin == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // waterfall procedure
        // these routines must reside in this order
        // * authenticateLoginData
        // * manageLoginAttempts 
        // * generateTokenLogin
        // * takeUserToDesiredPage
        // * invokeLoginFreeze
        // * showLoginForm
        
        // generate login token - six random leters concatenated with unix time
        // write the token to cookieLogin **and** write it to DB table
        // both write exercises need to work correctly in order to continue
        // later, tokenLogin in the cookie is compared to tokenLogin in the DB
        // and the DB also provides the session ID and systemno so that 
        // cross checks can be done to ensure
        // * this user
        // * this browser session
        // * this token
        // and that prevents users from gaining unauthroised access by tamperning
        // with the cookie on their local machine    
        
        // *******************************************************************************
        // stage 001
        
        // login is valid
        
        $uniqueTokenLogin = random_letter_generator(6).$unixNow;
        
   	    $echoID                = 21540 ;
        
        $echoString = $echoString."<P>$echoID uniqueTokenLogin    $uniqueTokenLogin";
        
        // *******************************************************************************
        // stage 002
        
        // writeCookie
        
        //setcookie("cookieDummy", "cookieDummy202212111321", time() + 60, "/");
        
        $cookieName   = "cookieLogin";                // the name of the actual cookie
        $cookieValue  = $uniqueTokenLogin;            // the param inside the cookie
        $cookieExpiry = time() + $cookieLifeLogin;    // see config
        
        setcookie($cookieName, $cookieValue, $cookieExpiry, "/");

        // *******************************************************************************
        // stage 003
        
	    // Open connection
	    
        $echoID    = 21541 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}          
        
        //********************************************************************************
        // stage 004
        
        // writeToken
        
        // Declare query
        $query = " 
                     INSERT INTO 
                                  token                            
                                        (
                                            mTokenLogin, 
                                            mSession
                                        ) 
                                    values
                                        (
                                            ?, 
                                            ?
                                        )
                 ";  
                        
        // *******************************************************************************
        // stage 005
   
        // Prepare statement
        if($stmt = $mysqli->prepare($query)) 
            {
                 // Prepare parameters
                 $mTokenLogin    = $uniqueTokenLogin;
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
                         $echoID     = 21542 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                     }               
                  
                 // Close statement
                 $stmt->close();
             }
             
        // *******************************************************************************
        // stage 006       
             
        // Close connection - end prepared stmt
        $mysqli->close();
         
        // *******************************************************************************
        // stage 007
         
        // check tTokenDatano - it is Auto_increment minus one - regular stmt
         
        $query = "
                   SHOW TABLE STATUS LIKE 
                                          'token'
                 "; 
               
        $db                         = mysqli_connect("$hostName", "$dbUser","$dbPass","$dbName");
         
        $result                     = mysqli_query($db,$query);
        $data                       = mysqli_fetch_array($result);
        $tTokenDatano              = (htmlspecialchars($data['Auto_increment'])) - 1;
            
        mysqli_close($db);
     
        $echoID      = 21543 ;
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}     
        
        // *******************************************************************************
        // stage 008
        
	    // Open connection
	    
        $echoID    = 21544 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}          
        
        // *******************************************************************************
        // stage 009
        
        // Declare query
        $query = " 
                     INSERT INTO 
                                  system_token_bond                            
                                                    (
                                                        mSystemno, 
                                                        mTokenDatano, 
                                                        mTokenStartDate
                                                    ) 
                                             values
                                                    (
                                                        ?, 
                                                        ?, 
                                                        ?
                                                    )
                 ";  
                        
        // *******************************************************************************
        // stage 010
   
        // Prepare statement
        if($stmt = $mysqli->prepare($query)) 
            {
                 // Prepare parameters
                 $mSystemno        = $tSystemno;
                 $mTokenDatano     = $tTokenDatano;
                 $mTokenStartDate  = $sqlNow;
     
                 // Bind parameters
                 $stmt->bind_param
                     (
                         "iss",
                   
                         $mSystemno,
                         $mTokenDatano,
                         $mTokenStartDate
                     );               
             
                 // Execute it
                 $stmt->execute();
   
                 if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 21545 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                     }               
                  
                 // Close statement
                 $stmt->close();
             }
     
        // *******************************************************************************
        // stage 011
        
        // 20221212 0621 xxx resume weed old tokens, more cookieLifeLogin + 1 day
   
        // *******************************************************************************
        // stage 00N
             
        // Close connection
        $mysqli->close();
        
    }

// **********************************************************************************
// takeUserToDesiredPage
// 20221211 0814

if ($takeUserToDesiredPage == 1)
//if (1 == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // default userTargetPage is set in config and is probably dashboard.php
        // default may be overuled by cookieCalledPage         
        // check cookieCalledPage, evaluate, and redirect
        
        // *******************************************************************************
        // stage 001
        
        // cookieCalledPage
        
        
        if ($userCalledPage == FALSE)
            {
                $target = $webappPath.$accessControlledDir."dashboard.php"; 
            }
        else
            {
                //$target = unserialize(base64_decode($_COOKIE['cookieCalledPage']));
                $target = $webappPath.$accessControlledDir.$userCalledPage.".php"; 
            }
        
	    $echoID                = 21546 ;
        
        //$echoString = $echoString."<P>$echoID _COOKIE['cookieCalledPage']    $_COOKIE['cookieCalledPage']";
        //$echoString = $echoString."<P>$echoID target    $target";
        
        header("Location:$target");
        exit;
    }
    
// **********************************************************************************
//showLoginForm
// 20221127 1538

if($showLoginForm == 1)
    { 
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // waterfall procedure
        // these routines must reside in this order
        // * authenticateLoginData
        // * manageLoginAttempts 
        // * generateTokenLogin
        // * takeUserToDesiredPage
        // * invokeLoginFreeze
        // * showLoginForm
    
        // *******************************************************************************
        // stage 001
        
        // 20221213 0909 xxx resume say "Please log in first" if we arrive here via cookieCalledPage relating to the vault
        // use cookieLoginFirst to drive this behaviour
        
	    $loginTable001 = $loginTable001."
	    
	    <form  action ='login.php'                          method ='POST'>
	    <input type   ='hidden'                             name   ='fOriginator'                   value ='login16504'>
	    
	    <table width=600 border=0 cellpadding=4>
	    
	    $divertedMessage
	    
  	    <tr>
    	    <td align=right>
    	    &nbsp;
    	    </td>
    	    <td align=center>
    	    <B>
    	    $title
    	    </B>
    	    </td>
  	    </tr>  
  	    <tr>
    	    <td align=right>
    	    email:&nbsp;<font color='red'>*</font>
    	    </td>
    	    <td align=left width=500>
    	    <input type='text'     class = 'paleBackground'   name='fUserLoginText'       size='80'   value=''>
    	    </td>
  	    </tr>
  	    <tr>
    	    <td align=right>
    	    password:&nbsp;<font color='red'>*</font>
    	    </td>
    	    <td align=left>
    	    <input type='password' class = 'paleBackground'   name='fUserPassword'        size='80'>
    	    <input type='hidden'                              name='fLoginCounterCurrent'             value='$fLoginCounterCurrent'>
    	    </td>
  	    </tr>
  	    <tr>
    	    <td align=right>
    	    &nbsp;
    	    </td>
    	    <td align=center>
    	    <input type='submit' class='formButton'                                                 value='$buttonText 16504'>
    	    </td>
  	    </tr>  
	    </table>  
	    
	    </form>
	    
        ";
    }
else
    {
        $loginTable001 = "";
    }
      

// *****************************************************************************************************************************
// 5555ECHO HTML

require ('header.php');

$allCookies            = $_SERVER["HTTP_COOKIE"];

echo "

<center>
<P>&nbsp;

<P>
$loginTable001

<P>&nbsp;
<P>&nbsp;
<P>&nbsp;
</center>
";

require ('footer.php');

?>
