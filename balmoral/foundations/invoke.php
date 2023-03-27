<?php

// invoke.php  
// 20220707 0715
// KMB

// BALMORAL 

// a user management suite

// *****************************************************************************************************************************
// DOCUMENTATION

// Balmoral User Management Portal Documentation 00N.docx

// please read the documentation and do all the prep before running invoke.php
// getting it wrong may lead to security issues

// if the "docs" are in a password protected Word 2016 file use the password: Goldsmiths

// *****************************************************************************************************************************
// 0011HOUSEKEEPING

ini_set('displayErrors', 0);        // toggle 0 for off, 1 for on (1 only to aid devwork, then revert to 0)
//error_reporting(E_ALL);

require ('configbalmoral.php');
require ('commonfns.php');

$cScript            = get_script_name($_SERVER['SCRIPT_FILENAME']);
$cActiveApp            = get_webapp_name($_SERVER['SCRIPT_FILENAME']);  
$cIPAddress         = get_ip_address($_SERVER['HTTP_CLIENT_IP'],$_SERVER['HTTP_X_FORWARDED_FOR'],$_SERVER['REMOTE_ADDR']);
$cHTTPReferrer      = $_SERVER['HTTP_REFERER'];
$echoString         = "";

$echoID             = 43436 ;
//$echoString         = $echoString."<P>$echoID cHTTPReferrer is $cHTTPReferrer ";

// *****************************************************************************************************************************
// 1111VARIABLES SET OR RESET 

// the script relies on a series of flags to indicate progress
// multiple tasks must each register a "success" for the whole invoke process to be completed

$dWebapp                 = ucwords(strtolower($cActiveApp));   // capitalise the first letter of the webapp name
$title                   = "Invoke $dWebapp";

$databaseExists          = 0;
$showStartForm           = 0;
$showInvokedAlready      = 0;
$createTables            = 0;
$invokeInProgress        = 0;
$superUserWriteToDB      = 0;
$defaultsWriteToDB       = 0;
$showNowInvoked          = 0;

$tablesCreateCounter     = 0;
$tablesCreateSuccess     = 0;
$superUserWriteSuccess   = 0;
$logRecordWriteSuccess   = 0;
$invokeSuccessTarget     = 3;
$invokeFailMessage       = "";

$localCounter            = 0;
$lErrCode                = 0;
$lNarrative              = "";

$buttonText              = "Start ";

$invokeTable001          = "";
$invokeTable002          = "";
$invokeTable003          = "";

// *****************************************************************************************************************************
// 2222VALIDATE

if(empty($_POST['fUserLoginText']))
    { 
        //********************************************************
        // stage 000
        // preamble
        
	    // no form data - first visit to this script
	    
	    // 2 checks
	    // is there a db; then
	    // is it empty (safe to assume that if there are no records in table log, then the db is empty)
	    
	    // we need a db and it needs to be empty in order to start invoke
	    
        // *******************************************************************
        // stage 001
        // Open regular connection    
    	
        $echoID        = 43469 ;
        $regularSelect = mysqli_connect("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}    
                
        //********************************************************
	    // stage 002
        // Prepare regular query
        
        $query  = "SHOW DATABASES LIKE '$cWebapp'";
        $result = mysqli_query($regularSelect,$query);
    
        if (mysqli_fetch_assoc($result))
            {
                $databaseExists = 1;
                
                $echoID             = 43468 ;
                //$echoString = $echoString."<P>$echoID cWebapp      $cWebapp";
            }
        else
            {
                // no $cWebapp database
            
                $echoID     = 43464 ;
            
                $echoString = $echoString."<P><font color='red'>$echoID error. Please refer to your sysadmin.</font>"; 
                
                // create an empty database before running invoke
                
                // balmoral
                // utf8_general_mysql500_ci
            }
                
        // *******************************************************************
        // stage 003
        // Close regular connection    
    	
        mysqli_close($regularSelect); 
        
        // *******************************************************************
        // stage 004
        // evaluate
        
        if ($databaseExists == 1)
            {
                // databaseExists but is it empty?
                
                //********************************************************
        	    // stage 005
        	    //open connection
        	    
                $echoID    = 43436 ;
                $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
                if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
        
                //********************************************************
	            // stage 006
                // Prepare statement
                
                if($stmt = $mysqli->prepare ("     SELECT 
                                                          mLogDateTime 
                                                     FROM 
                                                          log 
                                                    WHERE 
                                                          mLogno = ?
                                           ")
                  )
                    {
                        // Prepare parameters
                        $mLogno = 1; 
                             
                        // Bind parameters
                        $stmt->bind_param
                            (
                                "i", 
                                $mLogno
                            );  
                         
                        //Execute it
                        $stmt->execute();
                        
                        if (mysqli_error($mysqli) != FALSE)
                             {
                                 $echoID     = 43437 ;
                                 $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                             }                
                    
                        // Bind results 
                        $stmt->bind_result
                            (
                                $oLogDateTime
                            );
                    
                        // Fetch the value
                        while($stmt->fetch())
                            {
                                $cLogDateTime = htmlspecialchars($oLogDateTime);
                            }
                            
                        // Clear memory of results
                        $stmt->free_result();
                            
                        // Close statement
                        $stmt->close();
                    }	    
                
                //********************************************************
        	    // stage 007
        	    // evaluate
        	    
        	    if ($cLogDateTime == FALSE)
	                {
   	                    $showStartForm      = 1;
           	            
   	                    // no UPDATE_LOG as there are no tables yet
	                }
	            else
	                {
   	                    $showInvokedAlready = 1;
        
                        $lErrCode           = 71201;
                        $lNarrative         = "showInvokedAlready";
        
                        $echoID             = 43438 ;
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
                
	                }
	                
                //********************************************************
        	    // stage 008
                // Close connection
                       
                $mysqli->close();
            }
    }
elseif ($_POST['fOriginator'] == "invoke16501")
    {
        //********************************************************
        // stage 000
        // preamble
        
        // invoke has not previously been completed, and has been correctly called now
        
        //********************************************************
        // stage 001
        // param
        
        $validationScore       = 0;
        $validationTarget      = 0;
        
        // default error info in case validation items OK, but bad code gives mismatch between $validationScore and $validationTarget
        
        $lErrCode              = 71202; 
        $lNarrative            = "validationTarget validationScore unequal";
        
        //********************************************************
        // stage 002
        // data collection
        
        $validationTarget++;   $fUserLoginText                       = trim(htmlentities($_POST['fUserLoginText']));
        $validationTarget++;   $fUserPassword1                       = trim(htmlentities($_POST['fUserPassword1']));
        $validationTarget++;   $fUserPassword2                       = trim(htmlentities($_POST['fUserPassword2']));
        
        $echoID                = 43439 ;
        
        //$echoString = $echoString."<P>$echoID fUserLoginText       $fUserLoginText";
        //$echoString = $echoString."<P>$echoID fUserPassword1       $fUserPassword1";
        //$echoString = $echoString."<P>$echoID fUserPassword2       $fUserPassword2";
        
        //********************************************************
        // stage 003
        // validate
        
        // fUserLoginText                        standard mail test                  2 to N char
        // fUserPassword1                        alpha - numeric       !*-+()%&^@    12 to 50 char
        // fUserPassword1                        alpha - numeric       !*-+()%&^@    12 to 50 char
        
        if (preg_match("/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i",      $fUserLoginText))   { $validationScore++; } else { $fUserLoginText    = ""; $lErrCode = 71203; $lNarrative ="invalid fUserLoginText";}
        if (preg_match("/^[a-zA-Z0-9\!\*\-\+\(\)\%\&\^\@]{12,50}$/", $fUserPassword1))   { $validationScore++; } else { $fUserPassword1    = ""; $lErrCode = 71204; $lNarrative ="invalid fUserPassword1";}
        if (preg_match("/^[a-zA-Z0-9\!\*\-\+\(\)\%\&\^\@]{12,50}$/", $fUserPassword2))   { $validationScore++; } else { $fUserPassword2    = ""; $lErrCode = 71205; $lNarrative ="invalid fUserPassword2";}
        
        //********************************************************
        // stage 004
        // evaluate
        
        
        if ($fUserPassword1 != FALSE && $fUserPassword1 !== $fUserPassword2)
            {
                $lErrCode      = 71206 ;
                $errMsg        = "The passwords did not match - Error code ".$lErrCode;
                $lNarrative    = "mismatch fUserPassword1 fUserPassword2";
                $buttonText    = "Try again ";
                $showStartForm = 1;
            }
        elseif($validationTarget != 0 && $validationScore == $validationTarget)
            {
                // success
                
                $echoID                = 43455 ;
        
                //$echoString = $echoString."<P>$echoID fUserLoginText       $fUserLoginText";
                
                $createTables            = 1;
            }
        else
            {
                $errMsg = "The data was not understood - Error code ".$lErrCode;
                
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
            
        $echoID        = 43456 ;
        
        //$echoString = $echoString."<P>$echoID 202209300751 debug";
        
        //$echoString = $echoString."<P>$echoID fUserLoginText       $fUserLoginText";
        //$echoString = $echoString."<P>$echoID fUserPassword1       $fUserPassword1";
        //$echoString = $echoString."<P>$echoID fUserPassword2       $fUserPassword2";
    }
else 
    {
        //********************************************************
        // stage 001
        // weird 
        
        // this "else" can only be reached by a malicious actor who is experimenting
        // assume invoke was done earlier, and we have an activity log to write to
        
        $lErrCode       = 71207;
        $lNarrative     = "fUserLoginText but no valid fOriginator";
        $headerLocation = "error.php?errorCode=".$lErrCode; 
        
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
        // stage 002
        // safety
        
        header("Location:$headerLocation"); 
          
        exit;     // make sure that code below here does not get executed after we redirect                                      
    }
        
// *****************************************************************************************************************************
// 3333DATABASE WORK

// *****************************************************************************************************************************
//createTables

if ($createTables == 1)
    {
        // *******************************************************************************
        // stage 001
        // variables
        
        require ('tablelist.php');        // gets the array $invokeTableList
        $invokeInProgress = 1;
   
        // *******************************************************************************
        // stage 002
        //open connection
        
        $echoID    = 43439 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}        
        
        // *******************************************************************************
        // stage 003
        // make tables
        
        $invokeTableListElements = count($invokeTableList);
     
        for ($invokeTableListCycle=0; $invokeTableListCycle<$invokeTableListElements ; $invokeTableListCycle++)
            {
                mysqli_query($mysqli,$invokeTableList[$invokeTableListCycle]);
                
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 43440 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                     } 
                 else
                     {
                         $tablesCreateCounter++;
                         
                         $someVar001 = substr($invokeTableList[$invokeTableListCycle],0,35);
                         
                         $echoID                = 43457 ;
          
                         //$echoString = $echoString."<P>$echoID someVar001 is $someVar001";
                         
                     }               
                
                $echoID     = 43441 ;
   
                //$echoString = $echoString."<P>$echoID invokeTableListCycle is $invokeTableListCycle";
                
            }  
        
        // *******************************************************************************
        // stage 004
        // Close connection
                
        $mysqli->close();
        
        // *******************************************************************************
        // stage 005
        // evaluate
        
        if ($tablesCreateCounter == $invokeTableListElements)
            {
                $tablesCreateSuccess = 1;
                
                // now there are more steps which must be done in this order
                
                $superUserWriteToDB      = 1;
                $defaultsWriteToDB       = 1;
                $showNowInvoked          = 1;
                
                
                $lErrCode      = 0 ; // reset the fallback value from validate block
                $lNarrative    = "createTables completed";
                $cActivityCode = 100110;
                
                
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
        else
            {
                $echoID     = 43442 ;
                
                $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - user not written";
                
                // cannot do UPDATE_LOG as there is no log to write to
            }
    }

// *****************************************************************************************************************************
//superUserWriteToDB

if ($superUserWriteToDB == 1)
    {
        // *******************************************************************************
        // stage 000
        // preamble
        
        // seed mSystemno with 100101 for the first user
        // populate various tables
        
        // *******************************************************************************
        // stage 001
        // variables
        
	    $fSystemno = $cSystemno  = 100101;
        $tUserHashedPassword     = pbkdf2($fUserPassword1, $salt, $counter, $keyLength);
        
	    $echoID             = 43443 ;
        //$echoString         = $echoString."<P>$echoID fSystemno is $fSystemno ";
        //$echoString         = $echoString."<P>$echoID fUserLoginText is $fUserLoginText ";
        //$echoString         = $echoString."<P>$echoID fUserPassword1 is $fUserPassword1 ";
        //$echoString         = $echoString."<P>$echoID tUserHashedPassword is $tUserHashedPassword ";
        //$echoString         = $echoString."<P>$echoID sqlNow is $sqlNow ";

        // *******************************************************************************
        // stage 002
        //open connection
        
        $echoID    = 43444 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}        
        
        // *******************************************************************************
        // stage 003
        
        // Prepare statement
        $stmt = $mysqli->prepare ("INSERT INTO system   (
                                                             mSystemno, 
                                                             mUserLoginText, 
                                                             mUserPassword, 
                                                             mSystemStartDateTime 
                                                           ) 
                                                      values 
                                                           (
                                                               ?, 
                                                               ?, 
                                                               ?, 
                                                               ?
                                                            )"
                                 );           
             
                // Prepare parameters
                $mSystemno             = $fSystemno ;
                $mUserLoginText        = $fUserLoginText;
                $mUserPassword         = $tUserHashedPassword;
                $mSystemStartDateTime  = $sqlNow;
        
                // Bind parameters
                $stmt->bind_param
                    (
                        "isss",
             
                        $mSystemno,
                        $mUserLoginText,
                        $mUserPassword,
                        $mSystemStartDateTime
                    );               
                
               // Execute it
               $stmt->execute();
      
               if (mysqli_error($mysqli) != FALSE)
                    {
                        $echoID     = 43445 ;
                        $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                        
                        $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - user not written";
                    } 
               else
                    {
                        $superUserWriteSuccess = 1;
                         
                        $lNarrative    = "superUserWriteToDB completed";
                        $cActivityCode = 100120;

               
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
                     
                // Close statement
                $stmt->close();
                 
        //********************************************************
        // stage 004
        // Close connection
                
        $mysqli->close();
    }    

// *****************************************************************************************************************************
//defaultsWriteToDB

if ($defaultsWriteToDB == 1)
    {
        //********************************************************
        // stage 000
        // preamble
        
        // populate table entity_text
        // populate table system_entity_bond 
        // populate table grade_text 
        // populate table system_grade_bond 
        // populate table status_text 
        // populate table system_status_bond 
        
        // 20230322 0716 there should be some tabel renaming but to avoid risk the schema is this
        
        // balmoral buttons are in     system_button_bond     a better table name would be    ump_button_bond
        // windsor buttons are in      consumer_button_bond
    
        // *******************************************************************************
        // stage 001
        // variables
        
        $cButtonDatano    = array(
                                       "100101",
                                       "100102",
                                       "100103",
                                       "100104",
                                       "100105",
                                       "100106",
                                       "100107",
                                       "100108"
                                 );
                                 
        // 20230322 0706 Windsor items added - Charts Logger Profile Support
                             
        $cButtonText      = array(
                                       "Dashboard",
                                       "Activity",
                                       "Docs",
                                       "Errors",
                                       "Maintenance",
                                       "Tasks",
                                       "Tickets",
                                       "Users",
                                       "Charts ",
                                       "Logger",
                                       "Profile",
                                       "Support"
                                 );        
        
        // 20230322 0706 Windsor items added - 100109 100110 100111 100112
                             
        $cEntityno         = array(
                                       "100101",
                                       "100102",
                                       "100103",
                                       "100104",
                                       "100105",
                                       "100106",
                                       "100107",
                                       "100108",
                                       "100109",
                                       "100110",
                                       "100111",
                                       "100112"
                                  );
                             
        $cEntityText       = array(
                                       "Individual",
                                       "Business",
                                       "Government Body",
                                       "Charity",
                                       "Social Enterprise",
                                       "Asset",
                                       "Other"
                                  );        
                                 
        $cGradeDatano      = array(
                                       "100101",
                                       "200201",
                                       "300301",
                                       "400401",
                                       "500501",
                                       "600601"
                                  );
        
        $cGradeText        = array(
                                       "Customer",
                                       "Staff",
                                       "Standard Director",
                                       "HR Director",
                                       "Senior Director",
                                       "Super User"
                                  );
                                 
        $cStatusDatano     = array(
                                       "100",
                                       "200",
                                       "300",
                                       "400",
                                       "500",
                                       "900"
                                  );
                             
        $cStatusText       = array(
                                       "prospect",
                                       "active",
                                       "suspended",
                                       "banned",
                                       "dormant",
                                       "ceased"
                                  );
                                 
        $cWeedingDatano    = array(
                                       "101",
                                       "102",
                                       "103"
                                  );
                             
        $cWeedingDays     = array(
                                       "1",
                                       "730",
                                       "2555"
                                  );
                             
        $cWeedingText      = array(
                                       "search results",
                                       "short years",
                                       "long years"
                                  );
                                  
        // cActivityCode   is a variable which is one value and is regularly written to table log
        // cActivityNumber is the array (multiple values) used by invoke in order to avoid a conflict with cActivityCode

        $cActivityNumber   = array(                                       
                                       "100100",
                                       "100110",
                                       "100120",
                                       "100130",
                                       "100200",
                                       "100210",
                                       "100250",
                                       "100900",
                                       "200110",
                                       "200120",
                                       "200130",
                                       "200140",
                                       "200150",
                                       "200210",
                                       "200220",
                                       "200230",
                                       "300110",
                                       "300120",
                                       "300130",
                                       "300140",
                                       "300150",
                                       "400100",
                                       "400110",
                                       "400210",
                                       "400220",
                                       "400230",
                                       "400240",
                                       "400250",
                                       "400300",
                                       "400400",
                                       "400410",
                                       "400450",
                                       "400500",
                                       "400510",
                                       "400550"
                                  );                                       

        $cActivityText     = array(                                       
                                       "user login",
                                       "createTables",
                                       "superUserWriteToDB",
                                       "defaultsWriteToDB",
                                       "navbar button click",
                                       "navbar search",
                                       "error lookup",
                                       "user logout",
                                       "user search",
                                       "user add",
                                       "user edit",
                                       "user delete soft",
                                       "user delete hard",
                                       "suspension add",
                                       "suspension edit",
                                       "suspension revoke",
                                       "activity search",
                                       "activity add",
                                       "activity edit",
                                       "activity delete soft",
                                       "activity delete hard",
                                       "maintenance edit",
                                       "weeding years edit",
                                       "frozen IP search",
                                       "frozen IP add",
                                       "frozen IP edit",
                                       "frozen IP delete soft",
                                       "frozen IP delete hard",
                                       "data file generate",
                                       "ticket open",
                                       "ticket edit",
                                       "ticket close",
                                       "bug open",
                                       "bug edit",
                                       "bug close"
                                  );                                       
                                 
        // *******************************************************************************
        // stage 002
        //open connection
        
        $echoID    = 43446 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}        
        
        // *******************************************************************************
        // stage 003
        // table entity_text
        
        $cEntitynoElements = count($cEntityno);
     
        for ($cEntitynoCycle=0; $cEntitynoCycle<$cEntitynoElements ; $cEntitynoCycle++)
           {
               // Prepare statement
               $stmt = $mysqli->prepare ("INSERT INTO entity_text
                                                                   (
                                                                     mEntityno,
                                                                     mEntityText
                                                                   ) 
                                                            values 
                                                                   (
                                                                       ?, 
                                                                       ?
                                                                   )"
                                        );              
             
                // Prepare parameters
                $mEntityno      = $cEntityno[$cEntitynoCycle];
                $mEntityText    = $cEntityText[$cEntitynoCycle];
        
                $echoID    = 43458 ;
                //$echoString         = $echoString."<P>$echoID mEntityno is $mEntityno ";
                //$echoString         = $echoString."<P>$echoID mEntityText is $mEntityText ";
        
                // Bind parameters
                $stmt->bind_param
                    (
                        "is",
                         $mEntityno,
                         $mEntityText
                     );               
                
                // Execute it
                $stmt->execute();
      
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 43447 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                         
                         $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written";
                     } 
                else
                     {
                         //count
                     }
                     
                // Close statement
                $stmt->close();
            }                                 
                
        // *******************************************************************************
        // stage 004
        // table system_entity_bond
        
        // Prepare statement
        $stmt = $mysqli->prepare ("INSERT INTO system_entity_bond
                                                                    (
                                                                      mSystemno,
                                                                      mEntityno
                                                                    ) 
                                                            values 
                                                                    (
                                                                        ?, 
                                                                        ?
                                                                    )"
                                 );              
             
         // Prepare parameters
         $mSystemno      = 100101;
         $mEntityno      = 100101;
        
         // Bind parameters
         $stmt->bind_param
             (
                 "ii",

                 $mSystemno,
                 $mEntityno
             );               
        
         // Execute it
         $stmt->execute();
      
        if (mysqli_error($mysqli) != FALSE)
             {
                 $echoID     = 43448 ;
                 $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                 
                 $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written";
             } 
             
         // Close statement
         $stmt->close();
         
        // *******************************************************************************
        // stage 005
        // table grade_text
        
        $cGradeDatanoElements = count($cGradeDatano);
     
        for ($cGradeDatanoCycle=0; $cGradeDatanoCycle<$cGradeDatanoElements ; $cGradeDatanoCycle++)
            {
                // Prepare statement
                $stmt = $mysqli->prepare ("INSERT INTO grade_text
                                                                    (
                                                                      mGradeDatano,
                                                                      mGradeText
                                                                    ) 
                                                             values 
                                                                    (
                                                                        ?, 
                                                                        ?
                                                                    )"
                                         );              
             
                 // Prepare parameters
                 $mGradeDatano      = $cGradeDatano[$cGradeDatanoCycle];
                 $mGradeText        = $cGradeText[$cGradeDatanoCycle];
        
                 // Bind parameters
                 $stmt->bind_param
                     (
                         "is",

                         $mGradeDatano,
                         $mGradeText
                     );               
                
                 // Execute it
                 $stmt->execute();
      
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 43449 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                         
                         $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written";
                     } 
                     
                // Close statement
                $stmt->close();
            }                                 
                                 
        // *******************************************************************************
        // stage 006
        // table system_grade_bond
        
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
         $mSystemno             = 100101;
         $mGradeDatano          = 600601;         // Super User
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
                 $echoID     = 43450 ;
                 $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                 
                 $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written";
             } 
             
         // Close statement
         $stmt->close();
         
        // *******************************************************************************
        // stage 007
        // table Status_text
        
        $cStatusDatanoElements = count($cStatusDatano);
     
        for ($cStatusDatanoCycle=0; $cStatusDatanoCycle<$cStatusDatanoElements ; $cStatusDatanoCycle++)
            {
                // Prepare statement
                $stmt = $mysqli->prepare ("INSERT INTO status_text
                                                                    (
                                                                      mStatusDatano,
                                                                      mStatusText
                                                                    ) 
                                                             values 
                                                                    (
                                                                        ?, 
                                                                        ?
                                                                    )"
                                         );              
             
                 // Prepare parameters
                 $mStatusDatano      = $cStatusDatano[$cStatusDatanoCycle];
                 $mStatusText        = $cStatusText[$cStatusDatanoCycle];
                 
                $echoID    = 43459 ;
                //$echoString         = $echoString."<P>$echoID mStatusDatano is $mStatusDatano ";
                //$echoString         = $echoString."<P>$echoID mStatusText is $mStatusText ";
                 
        
                 // Bind parameters
                 $stmt->bind_param
                     (
                         "is",

                         $mStatusDatano,
                         $mStatusText
                     );               
                
                 // Execute it
                 $stmt->execute();
      
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 43453 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                         
                         $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written";
                     } 
                     
                // Close statement
                $stmt->close();
            }                                 
            
        // *******************************************************************************
        // stage 008
        // table system_status_bond
        
        // Prepare statement
        $stmt = $mysqli->prepare ("INSERT INTO system_status_bond
                                                               (
                                                                 mSystemno,
                                                                 mStatusDatano,
                                                                 mStatusStartDateTime
                                                               ) 
                                                       values 
                                                               (
                                                                   ?, 
                                                                   ?, 
                                                                   ?
                                                               )"
                                 );              
             
         // Prepare parameters
         $mSystemno              = 100101;
         $mStatusDatano          = 200;           // active
         $mStatusStartDateTime   = $sqlNow;
        
         // Bind parameters
         $stmt->bind_param
             (
                 "iis",

                 $mSystemno,
                 $mStatusDatano,
                 $mStatusStartDateTime
             );               
        
         // Execute it
         $stmt->execute();
      
        if (mysqli_error($mysqli) != FALSE)
            {
                $echoID     = 43454 ;
                $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                
                $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written";
            } 
             
         // Close statement
         $stmt->close();
         
        // *******************************************************************************
        // stage 009
        // table weeding_days
        
        $cWeedingDatanoElements = count($cWeedingDatano);
     
        for ($cWeedingDatanoCycle=0; $cWeedingDatanoCycle<$cWeedingDatanoElements ; $cWeedingDatanoCycle++)
            {
                // Prepare statement
                $stmt = $mysqli->prepare ("INSERT INTO weeding_days
                                                                    (
                                                                      mWeedingDatano,
                                                                      mWeedingDays,
                                                                      mWeedingText
                                                                    ) 
                                                             values 
                                                                    (
                                                                        ?, 
                                                                        ?, 
                                                                        ?
                                                                    )"
                                         );              
             
                 // Prepare parameters
                 $mWeedingDatano      = $cWeedingDatano[$cWeedingDatanoCycle];
                 $mWeedingDays        = $cWeedingDays[$cWeedingDatanoCycle];
                 $mWeedingText        = $cWeedingText[$cWeedingDatanoCycle];
        
                 // Bind parameters
                 $stmt->bind_param
                     (
                         "ids",

                         $mWeedingDatano,
                         $mWeedingDays,
                         $mWeedingText
                     );               
                
                 // Execute it
                 $stmt->execute();
      
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 43460 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                         
                         $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written";
                     } 
                     
                // Close statement
                $stmt->close();
            }                                 

        // *******************************************************************************
        // stage 010
        // table maintenance
        
        // Prepare statement
        $stmt = $mysqli->prepare ("INSERT INTO maintenance
                                                               (
                                                                 mMaintenanceDatano,
                                                                 mMaintenanceFlagUMP
                                                               ) 
                                                       values 
                                                               (
                                                                   ?, 
                                                                   ?
                                                               )"
                                 );              
             
         // Prepare parameters
         $mMaintenanceDatano    = 1;
         $mMaintenanceFlagUMP   = 0;
        
         // Bind parameters
         $stmt->bind_param
             (
                 "ii",

                 $mMaintenanceDatano,
                 $mMaintenanceFlagUMP
             );               
        
         // Execute it
         $stmt->execute();
      
        if (mysqli_error($mysqli) != FALSE)
            {
                $echoID     = 43461 ;
                $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                
                $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written";
            } 
             
         // Close statement
         $stmt->close();
         
        // *******************************************************************************
        // stage 011
        // table activity_code
        
        $cActivityNumberElements = count($cActivityNumber);
        
        $localCounter = 0;
     
        for ($cActivityNumberCycle=0; $cActivityNumberCycle<$cActivityNumberElements ; $cActivityNumberCycle++)
            {
                // Prepare statement
                $stmt = $mysqli->prepare ("INSERT INTO activity_code
                                                                    (
                                                                      mActivityCode,
                                                                      mActivityText
                                                                    ) 
                                                             values 
                                                                    (
                                                                        ?, 
                                                                        ?
                                                                    )"
                                         );              
             
                 // Prepare parameters
                 $mActivityCode       = $cActivityNumber[$cActivityNumberCycle];
                 $mActivityText       = $cActivityText[$cActivityNumberCycle];
                 
                $echoID    = 43462 ;
                //$echoString         = $echoString."<P>$echoID mActivityCode is $mActivityCode ";
                //$echoString         = $echoString."<P>$echoID mActivityText is $mActivityText ";
                 
        
                 // Bind parameters
                 $stmt->bind_param
                     (
                         "is",

                         $mActivityCode,
                         $mActivityText
                     );               
                
                 // Execute it
                 $stmt->execute();
      
                if (mysqli_error($mysqli) != FALSE)
                    {
                         $echoID     = 43463 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                         
                         $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written";
                    }
                else
                    {
                        $localCounter++;
                    }
                     
                // Close statement
                $stmt->close();
            }
            
        if ($localCounter == $cActivityNumberElements)
            {
                $logRecordWriteSuccess = 1;
                
                $lNarrative    = "defaultsWriteToDB completed";
                $cActivityCode = 100130;
                
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

        // *******************************************************************************
        // stage 012
        // table button_text
        // table system_button_bond
        
        $cButtonDatanoElements = count($cButtonDatano);
     
        for ($cButtonDatanoCycle=0; $cButtonDatanoCycle<$cButtonDatanoElements ; $cButtonDatanoCycle++)
            {
                // Prepare statement
                $stmt = $mysqli->prepare ("INSERT INTO button_text
                                                                    (
                                                                      mButtonDatano,
                                                                      mButtonText
                                                                    ) 
                                                             values 
                                                                    (
                                                                        ?, 
                                                                        ?
                                                                    )"
                                    );              
             
                 // Prepare parameters
                 $mButtonDatano      = $cButtonDatano[$cButtonDatanoCycle];
                 $mButtonText        = $cButtonText[$cButtonDatanoCycle];
        
                 // Bind parameters
                 $stmt->bind_param
                     (
                         "is",

                         $mButtonDatano,
                         $mButtonText
                     );               
                
                 // Execute it
                 $stmt->execute();
      
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 43507 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                         
                         $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written to log";
                     } 
                     
                // Close statement
                $stmt->close();
                 
                // give the superuser bonds for all the buttons 
                 
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
                 $mSystemno              = 100101;
                 $mButtonDatano          = $cButtonDatano[$cButtonDatanoCycle];
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
                         $echoID     = 43508 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                         
                         $invokeFailMessage = $invokeFailMessage."<P>$echoID invoke error - record not written to log";
                     } 
                     
                 // Close statement
                 $stmt->close();
                 
            }
                        
        //********************************************************
	    // stage 00N
        // Close connection
        
        $mysqli->close();
    }    
        
// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// *****************************************************************************************************************************
//showInvokedAlready

if ($showInvokedAlready == 1)
    {
        $invokeTable003 = $invokeTable003."
        
        <P>
        $dWebapp has been invoked already
        
        <P>
        Visit the login page to continue
        
        <P>
        <form method ='post' action ='../login.php'>
   	    <input type='submit' class='formButton' value='Continue 16503'>
        </form>        
        
        ";
    }

// *****************************************************************************************************************************
//showNowInvoked

if ($showNowInvoked == 1)
    {
        
        if ($tablesCreateSuccess + $superUserWriteSuccess + $logRecordWriteSuccess == $invokeSuccessTarget)
           {
                $invokeTable002 = $invokeTable002."
        
                <P>
                $dWebapp now invoked
                
                <P>
                Visit the login page to continue
                
                <P>
                <form method='post' action='../login.php'>
        	    <input type='submit' class='formButton' value='Continue 16502'>
                </form>        
                
                ";
           }
       else
           {
                $invokeTable002 = $invokeTable002."
        
                <P>
                Oops - something went wrong.
                
                $invokeFailMessage
                
                <P>
                Examine the mySQL log file and remedy any defects.       
                
                ";
           }
    }

// *****************************************************************************************************************************
//showStartForm

if($showStartForm == 1)
    { 
        // **************************************************************************
    
	    $invokeTable001 = $invokeTable001."
	    
	    <form  action ='invoke.php'                         method ='POST'>
	    <input type   ='hidden'                             name   ='fOriginator'           value ='invoke16501'>
	    
	    <table width=600 border=0 cellpadding=4>
  	    <tr>
    	    <td align=right>mail:</td>
    	    <td align=left width=500><input type='text'     name='fUserLoginText' size='80' value=''></td>
  	    </tr>
  	    <tr>
    	    <td align=right>password:</td>
    	    <td align=left><input type='password'           name='fUserPassword1' size='80'></td>
  	    </tr>
  	    <tr>
    	    <td align=right>confirm&nbsp;password:</td>
    	    <td align=left><input type='password'           name='fUserPassword2' size='80'></td>
  	    </tr>
  	    <tr>
    	    <td align=right>&nbsp;</td>
    	    <td align=center>
    	    <BR><BR>
    	    Check your mail address is entered correctly before you start
    	    <BR><BR>
    	    Password length must be min 12 and max 50 characters
    	    <BR><BR>
    	    Passwords may contain conventional text and 
    	    <BR>
    	    may include only these special characters
    	    <BR><BR>
    	    ! * - + ( ) % & ^ @ 
    	    <BR><BR>
    	    </td>
  	    </tr>
  	    <tr>
    	    <td align=right>&nbsp;</td>
    	    <td align=center>
    	    <input type='submit' class='formButton' value='$buttonText 16501'>
    	    </td>
  	    </tr>  
	    </table>  
	    
	    </form>
	    
        ";
    }
        
// *****************************************************************************************************************************
// 5555ECHO HTML

require ('../header.php');

echo "

<center>

<P>
&nbsp;

<h2>
$title 
</h2>

<P>
$invokeTable001
$invokeTable002
$invokeTable003

</center>

";

require ('../footer.php');

?>