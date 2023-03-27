<?php

// logout.php 
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

// get cSystemno from cookie
     
$cTokenLogin        = $_COOKIE['cookieLogin'];

                // *******************************************************************************
                // stage 001
        
        	    // Open connection
	    
                $echoID    = 43568 ;
                $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
                if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}          
                
                // *******************************************************************************
                // stage 002
        
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
                // stage 003
        
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
                                 $echoID     = 43569 ;
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
                // stage 004
                
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
                        // stage 005
        
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
                                                 $echoID     = 43570 ;
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
                                    
                        $echoID             = 43571 ;
                        //$echoString         = $echoString."<P>$echoID cSystemno is $cSystemno ";  

                // *******************************************************************************
                // stage 00N
        
                // Close connection
                $mysqli->close();


// now destroy session

session_regenerate_id();

//unset cookie

        $cookieName   = "cookieLogin";    // the name of the actual cookie
        $cookieValue  = "dummy";          // the param inside the cookie
        $cookieExpiry = 1;                // once second past unix epoch
        
        setcookie($cookieName, $cookieValue, $cookieExpiry, "/");

                $lErrCode            = 0 ; 
                $lNarrative          ="logged out";
                
                        $echoID             = 43520 ;
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
        


// *****************************************************************************************************************************
// 5555ECHO HTML


require ('header.php');

$allCookies            = $_SERVER["HTTP_COOKIE"];


echo "

<center>
<P>&nbsp;
<P>
<b>
Logged out.
</b>
<P>
Thank you for using $dWebapp. 
</center>
<P>&nbsp;
<P>&nbsp;
<P>&nbsp;
<P>&nbsp;

";

require ('footer.php');

?>