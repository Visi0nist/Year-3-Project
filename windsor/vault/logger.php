<?php

// logger.php 
// 20230322 0738
// KMB

// WINDSOR
// an add in for BALMORAL 

// Long Term Tracker

    // 20230324 1910 xxx resume todo edit record delete record

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

$getPursuitText                     = 0;
$showTableCollection                = 0;
$showDoubleCheckData                = 0;
$writeToDB                          = 0;
$logPerformance                     = 0;
$logWeight                          = 0;
$showWrittenToDB                    = 0;

$loggerTableCollection              = "";
$loggerTableDoubleCheck             = "";
$loggerTableWritten                 = "";

$optionsPursuit                     = "";
$optionsHours                       = "";
$optionsMinutes                     = "";
$optionsSeconds                     = "";

$dPursuitText                       = "";
$selectedToggle                     = "";
        
// *****************************************************************************************************************************
// 2222VALIDATE

if ($_POST['fOriginator'] == "someVar")
    {
        $echoID      = 21404 ;
        $echoString  = $echoString."<P>$echoID fOriginator is $fOriginator ";
    }
elseif ($_POST['fOriginator'] == "loggerContinue34110"
     || $_POST['fOriginator'] == "loggerBack34111"
     || $_POST['fOriginator'] == "loggerFinalise34112")
    {
        // ***************************************************************************************************
        // validation parameters
        
        $validationScore       = 0;
        $validationTarget      = 0;
        
        // ***************************************************************************************************
        // get the data from the form, increment the validationTarget each time

        $validationTarget++;   $fOriginator                 = trim(htmlentities($_POST['fOriginator']));        	    
        $validationTarget++;   $fSystemno                   = trim(htmlentities($_POST['fSystemno']));     
        $validationTarget++;   $fDateTime                   = trim(htmlentities($_POST['fDateTime']));     
        $validationTarget++;   $fDistance                   = trim(htmlentities($_POST['fDistance']));     
        $validationTarget++;   $fPursuitno                  = trim(htmlentities($_POST['fPursuitno']));     
        $validationTarget++;   $fHours                      = trim(htmlentities($_POST['fHours']));     
        $validationTarget++;   $fMinutes                    = trim(htmlentities($_POST['fMinutes']));     
        $validationTarget++;   $fSeconds                    = trim(htmlentities($_POST['fSeconds']));     
        $validationTarget++;   $fWeight                     = trim(htmlentities($_POST['fWeight']));     

        // fDateTime - remove non numeric char from string, / -, etc
        
        $fDateTime = preg_replace('/[^0-9]/', '', $fDateTime);
        
        $echoID      = 21456 ;
        
        //$echoString  = $echoString."<P>$echoID fOriginator is $fOriginator ";
        //$echoString  = $echoString."<P>$echoID fSystemno is $fSystemno ";
        //$echoString  = $echoString."<P>$echoID fDateTime is $fDateTime ";
        
        // ***************************************************************************************************
        // validate the data
        
        // wiki - php validation standard tests 
        
        // fOriginator                              alpha, numeric                       4 to 50 char 
        // fSystemno                                numeric                              4-11 char
        // fDateTime                                numeric                              8-12 char
        // fDistance                                numeric, fullstop, empty             1-10 char
        // fPursuitno                               numeric                              3 char
        // fHours                                   numeric                              1-2 char
        // fMinutes                                 numeric                              1-2 char
        // fSeconds                                 numeric                              1-2 char
        // fWeight                                  numeric, fullstop, empty             1-10 char
        
        // set a fallBackErrCode in case all validation items are OK, but a mismatch between $validationScore and $validationTarget occurs
        
        $fallBackErrCode                                                                                                                                                                                                      = 52154; 
        if (preg_match("/^[a-zA-Z0-9]{4,50}$/",                      $fOriginator))          { $validationScore++; }                                                             else { $fOriginator          = ""; $lErrCode = 52155;}
        if (preg_match("/^[0-9]{4,11}$/",                            $fSystemno))            { $validationScore++; }                                                             else { $fSystemno            = ""; $lErrCode = 52156;}
        if (preg_match("/^[0-9]{8,12}$/",                            $fDateTime))            { $validationScore++; }                                                             else { $fDateTime            = ""; $lErrCode = 52157;}
        if (preg_match("/^[0-9.]{0,10}$/",                           $fDistance))            { $validationScore++; } elseif ($fDistance     == FALSE) { $validationScore++; }    else { $fDistance            = ""; $lErrCode = 52158;}
        if (preg_match("/^[0-9]{3}$/",                               $fPursuitno))           { $validationScore++; }                                                             else { $fPursuitno           = ""; $lErrCode = 52159;}
        if (preg_match("/^[0-9]{1,2}$/",                             $fHours))               { $validationScore++; }                                                             else { $fHours               = ""; $lErrCode = 52160;}
        if (preg_match("/^[0-9]{1,2}$/",                             $fMinutes))             { $validationScore++; }                                                             else { $fMinutes             = ""; $lErrCode = 52161;}
        if (preg_match("/^[0-9]{1,2}$/",                             $fSeconds))             { $validationScore++; }                                                             else { $fSeconds             = ""; $lErrCode = 52162;}
        if (preg_match("/^[0-9.]{0,10}$/",                           $fWeight))              { $validationScore++; } elseif ($fWeight       == FALSE) { $validationScore++; }    else { $fWeight              = ""; $lErrCode = 52163;}
        
        // ***************************************************************************************************
        // now that validation has been performed
        
        // restore space to $fDateTime for aesthetics
        
        $fDateTime = SQLDateTime_to_bellaDateTime($fDateTime);  
        
        // ignore seconds
        
        $fDateTime = substr($fDateTime, 0, 13);
        
        // ***************************************************************************************************
        // evaluate validation
        
        if($fDistance == FALSE && $fWeight  == FALSE)
            {
                // no data entered 
                
                $errMsg                = "No valid data could be found for distance or weight.<BR>One of those should be logged.<BR>Only numeric values are permitted.";
                $lNarrative            = "distance or weight missing";
                $lErrCode              = 52164;
                
                $echoID                = 21457 ;
                
                //$echoString            = $echoString."<P>$echoID authenticateSeniorStaffPassword is $authenticateSeniorStaffPassword ";
            }
        elseif($validationTarget != 0 && $validationScore == $validationTarget)
            {
                // good
                
                $getPursuitText        = 1;
                
                // which button brought us here?
                
                if ($fOriginator == "loggerFinalise34112")
                    {
                        // move forward
                        
                        $lNarrative            = "writeToDB";
                
                        $writeToDB             = 1;
                        
                        $echoID                = 21465 ;
                
                        //$echoString            = $echoString."<P>$echoID writeToDB is $writeToDB ";
                    }
                elseif ($fOriginator == "loggerContinue34110")
                    {
                        // move forward
                        
                        $lNarrative            = "showDoubleCheckData";
                
                        $showDoubleCheckData   = 1;
                    }
                else
                    {    
                        // loggerBack34111
                        // start anew but with fields populated
                        
                        $lNarrative            = "back to start of $cScript";
                        
                        $showTableCollection   = 1;
                    }
                
                $echoID                = 21458 ;
                
                //$echoString            = $echoString."<P>$echoID authenticateSeniorStaffPassword is $authenticateSeniorStaffPassword ";
            }
        else
            {
                // bad
                
                if ($lErrCode == FALSE) {$lErrCode = $fallBackErrCode;} 
                
                $errMsg         = "The data was not understood - Error code ".$lErrCode;
                $lNarrative     = "validation failed";
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
else 
    {
        // first call to this script either by login process or by navbar button        
        $lErrCode            = 0 ; 
        $lNarrative          = "call to $cScript";
                
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
        
        $getPursuitText        = 1;
        $showTableCollection   = 1;
    }
 
// *****************************************************************************************************************************
// 3333DATABASE WORK

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm

// **********************************************************************************
// writeToDB
// 20230324 0758

if ($writeToDB == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // modify fDateTime and fHours fMinutes fSeconds, to fit mPursuitDate and mPursuitDuration
        // then writeToDB
        
        // *******************************************************************************
        // stage 001
        
        // set variables
        
        // pack duration as a single array
        
        $inputValue = array (
                              "fHours"    => $fHours,
                              "fMinutes"  => $fMinutes,
                              "fSeconds"  => $fSeconds
                            );
                            
        // convert to SQL
                            
        $cPursuitDate     = $cWeightDate = bellaDateTime_to_SQLDateTime($fDateTime);
        $cPursuitDuration =                bellaTime_to_SQLTime($inputValue);
        
        $echoID                = 21462 ;
                
        //$echoString            = $echoString."<P>$echoID fDateTime is $fDateTime ";
        //$echoString            = $echoString."<P>$echoID cPursuitDate is $cPursuitDate ";
        //$echoString            = $echoString."<P>$echoID cPursuitDuration is $cPursuitDuration ";
        
        // *******************************************************************************
        // stage 002
        // open connection
        
        $echoID    = 21463 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}  
        
        // *******************************************************************************
        // stage 003
        
        // log what?
        
        // we may have only one set of data, check if performance and/or weight to be logged
        
        if($fDistance != FALSE && $fWeight  != FALSE)
            {
                $logPerformance = 1;
                $logWeight      = 1;
            }
        elseif($fDistance  != FALSE)
            {
                $logPerformance = 1;
            }
        else
            {
                $logWeight      = 1;
            }
        
        // *******************************************************************************
        // stage 004
        
        // logPerformance
        
        if($logPerformance == 1)
            {
                // Prepare statement  
                $stmt = $mysqli->prepare ("INSERT INTO performance (
                                                                     mSystemno, 
                                                                     mPursuitno,
                                                                     mPursuitDate,
                                                                     mPursuitDistance,
                                                                     mPursuitDuration 
                                                                   ) 
                                                              values 
                                                                   (
                                                                       ?, 
                                                                       ?, 
                                                                       ?, 
                                                                       ?, 
                                                                       ?
                                                                    )"
                                         );           
             
                 // Prepare parameters
                 $mSystemno          = $fSystemno;
                 $mPursuitno         = $fPursuitno;
                 $mPursuitDate       = $cPursuitDate;
                 $mPursuitDistance   = $fDistance;
                 $mPursuitDuration   = $cPursuitDuration;
        
                 // Bind parameters
                 $stmt->bind_param
                     (
                         "iisss",
              
                         $mSystemno,
                         $mPursuitno,
                         $mPursuitDate,
                         $mPursuitDistance,
                         $mPursuitDuration
                     );               
                
                 // Execute it
                 $stmt->execute();
      
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 21464 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                         
                         $lErrCode   = 52165 ;
                         $executeMsg = $executeMsg."<P>Something went wrong - error code $lErrCode ";
                     } 
                 else
                     {
                         $showWrittenToDB = 1;
                     }
                     
                 // Close statement
                 $stmt->close();
            }
                 
        // *******************************************************************************
        // stage 005
        
        // logWeight
        
        if($logWeight == 1)
            {
                // Prepare statement  
                $stmt = $mysqli->prepare ("INSERT INTO weight     (
                                                                     mSystemno, 
                                                                     mWeightDate, 
                                                                     mKilograms 
                                                                   ) 
                                                              values 
                                                                   (
                                                                       ?, 
                                                                       ?, 
                                                                       ?
                                                                    )"
                                         );           
             
                 // Prepare parameters
                 $mSystemno          = $fSystemno;
                 $mWeightDate        = $cWeightDate;
                 $mKilograms         = $fWeight;
        
                 // Bind parameters
                 $stmt->bind_param
                     (
                         "iss",
              
                         $mSystemno,
                         $mWeightDate,
                         $mKilograms
                     );               
                
                 // Execute it
                 $stmt->execute();
      
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 21466 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                         
                         $lErrCode   = 52166 ;
                         $executeMsg = $executeMsg."<P>Something went wrong - error code $lErrCode ";
                     } 
                 else
                     {
                         $showWrittenToDB = 1;
                     }
                     
                 // Close statement
                 $stmt->close();
            }
                 
        //********************************************************
        // stage 00N
        // Close connection
                
        $mysqli->close();
    }

// **********************************************************************************
// getPursuitText
// 20230323 1705

if ($getPursuitText == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // getPursuitText to build a drop down list for the html form 
        // two concurrent arrays are used to make the html assembly smoother
        
        // *******************************************************************************
        // stage 001
        
        // set variables
        
        $cPursuitno          = array();
        $cPursuitText        = array();
        
        // *******************************************************************************
        // stage 002
        
        //open connection
        $echoID    = 21453 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
        
        // *******************************************************************************
        // stage 003
      
        // Declare query
        $query = " 
                      SELECT 
                             mPursuitno,
                             mPursuitText 
                        FROM 
                             pursuit 
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
                          $echoID     = 21454 ;
                          $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                      } 
                     
                  // Bind results 
                  $stmt->bind_result
                      (
                          $oPursuitno,
                          $oPursuitText
                      );
             
                  // Fetch the value
                  while($stmt->fetch())
                      {
                          $cPursuitno[]         = htmlspecialchars($oPursuitno);
                          $cPursuitText[]       = ucwords(strtolower(htmlspecialchars($oPursuitText))); // capitalise the first letter of the pursuit
                      }
              
                  // Clear memory of results
                  $stmt->free_result();
              
                  // Close statement
                  $stmt->close();
      	      }
      
        $echoID       = 21455 ;
     
        //$arrayString = print_r($cPursuitno, TRUE); $echoString = $echoString."<P>$echoID cPursuitno is $arrayString ";         
        //$arrayString = print_r($cPursuitText, TRUE); $echoString = $echoString."<P>$echoID cPursuitText is $arrayString ";         
    }

// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm

// **********************************************************************************
// showWrittenToDB
// 20230323 1907

if ($showWrittenToDB == 1)
    {
        // *******************************************************************************
        // stage 001
        
        // build table
        
        $loggerTableWritten = $loggerTableWritten."
         
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            <td style='text-align: right; vertical-align: top; min-width:1px; padding:8px' colspan=7>
            
            <form  style='display:inline;' action='logger.php'             method='POST'>
            <input type='hidden'           name='fOriginator'              value='loggerAddAnother34114'>
            <input type='hidden'           name='fSystemno'                value='$cSystemno'>
            <input type='submit'                                           value=' Add Another 34114 > '     class='standardWidthFormButton' >
            </form>
            
            </td>
           </tr>
           <tr>
            <td style='text-align:left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            
            <ul>
            <li>Your data has been added to the log.
            <BR>
            &nbsp;
            </ul>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align:left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            &nbsp;
            </td>
           </tr>
          </table>
         
          </div> 
                
        ";
    }

// **********************************************************************************
// showDoubleCheckData
// 20230323 1907

if ($showDoubleCheckData == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // use cPursuitText in place of cPursuitno
        
        $cPursuitnoElements = count($cPursuitno);
     
        for ($cPursuitnoCycle=0; $cPursuitnoCycle<$cPursuitnoElements ; $cPursuitnoCycle++)
            {
                if($cPursuitno[$cPursuitnoCycle] == $fPursuitno)
                    {
                        $dPursuitText = $cPursuitText[$cPursuitnoCycle];
                    }
            }
        
        // *******************************************************************************
        // stage 001
        
        // build table
        
        $loggerTableDoubleCheck = $loggerTableDoubleCheck."
         
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:8px' colspan=3>
            
            <form  style='display:inline;' action='logger.php'             method='POST'>
            <input type='hidden'           name='fOriginator'              value='loggerBack34111'>
            <input type='hidden'           name='fSystemno'                value='$cSystemno'>
            <input type='hidden'           name='fDateTime'                value='$fDateTime'>
            <input type='hidden'           name='fDistance'                value='$fDistance'>
            <input type='hidden'           name='fPursuitno'               value='$fPursuitno'>
            <input type='hidden'           name='fHours'                   value='$fHours'>
            <input type='hidden'           name='fMinutes'                 value='$fMinutes'>
            <input type='hidden'           name='fSeconds'                 value='$fSeconds'>
            <input type='hidden'           name='fWeight'                  value='$fWeight'>
            <input type='submit'                                           value=' < Back 34111 '     class='standardWidthFormButton' >
            </form>
            
            </td>
            <td style='text-align: right; vertical-align: top; min-width:1px; padding:8px' colspan=4>
            
            <form  style='display:inline;' action='logger.php'             method='POST'>
            <input type='hidden'           name='fOriginator'              value='loggerFinalise34112'>
            <input type='hidden'           name='fSystemno'                value='$cSystemno'>
            <input type='hidden'           name='fDateTime'                value='$fDateTime'>
            <input type='hidden'           name='fDistance'                value='$fDistance'>
            <input type='hidden'           name='fPursuitno'               value='$fPursuitno'>
            <input type='hidden'           name='fHours'                   value='$fHours'>
            <input type='hidden'           name='fMinutes'                 value='$fMinutes'>
            <input type='hidden'           name='fSeconds'                 value='$fSeconds'>
            <input type='hidden'           name='fWeight'                  value='$fWeight'>
            <input type='submit'                                           value=' Finalise 34112 > '     class='standardWidthFormButton' >
            </form>
            
            </td>
           </tr>
           <tr>
            <td style='text-align:left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            
            <ul>
            <li>Please check that these details are correct:
            <BR>
            &nbsp;
            </ul>
            
            </td>
           </tr>
           
           <tr>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
             <td style='background:#E8FFC6; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             DateTime
             </b>
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=3>
             <b>
             Performance
             </b>
             </td>
             <td style='background:#EDF9D9; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             Weight
             </b>
             </td>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
           </tr>        
           
           <tr>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
             <td style='background:#E8FFC6; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             &nbsp;
             </b>
             <BR>YYYYMMD
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             Pursuit
             </b>
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             Distance
             </b>
             <BR>Decimal (#0.000)
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             Duration
             </b>
             <BR>Hours : Mins : Secs
             </td>
             <td style='background:#EDF9D9; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             &nbsp;
             </b>
             <BR>Kilograms (#0.000)
             </td>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
           </tr>        
           <tr>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
             <td style='background:#E8FFC6; text-align:left; vertical-align:top; min-width:1px; padding:4px' colspan=1>
             $fDateTime &nbsp;
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:4px' colspan=1>
             $dPursuitText &nbsp;
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:4px' colspan=1>
             $fDistance &nbsp;     
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:4px' colspan=1>
             $fHours : $fMinutes : $fSeconds &nbsp;
             </td>
             <td style='background:#EDF9D9; text-align:left; vertical-align:top; min-width:1px; padding:4px' colspan=1>
             $fWeight &nbsp;
             </td>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
           </tr>        
           
           <tr>
            <td style='text-align:left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            &nbsp;
            </td>
           </tr>
          </table>
         
          </div> 
                
        ";
    }

// **********************************************************************************
// showTableCollection
// 20230322 1331

if ($showTableCollection == 1)
    {
        // *******************************************************************************
        // stage 001
        
        // assemble HTML drop down lists
        // option 999 is added to get a consistent width on the html form
        
        // cPursuitno and cPursuitText are the same length so one loop will do
        
        $optionsPursuit = "<select name='fPursuitno'>";
        
        // pursuit
        
        $cPursuitnoElements = count($cPursuitno);
     
        for ($cPursuitnoCycle=0; $cPursuitnoCycle<$cPursuitnoElements ; $cPursuitnoCycle++)
            {
                if($fPursuitno == $cPursuitno[$cPursuitnoCycle])
                    {
                        // $fPursuitno is not FALSE, so we came here via a back button
                        $selectedToggle = "selected";
                    }
                else                   
                    {
                        $selectedToggle = "";
                    }
                    
                $optionsPursuit = $optionsPursuit."<option value=".$cPursuitno[$cPursuitnoCycle]." $selectedToggle>".$cPursuitText[$cPursuitnoCycle]."</option>";
            }        
      
        $optionsPursuit = $optionsPursuit."<option value=999>Please select</option></select>";
        
        // hours
        
        $optionsHours = "<select name='fHours'>";
        
        for ($cHoursCycle=0; $cHoursCycle<25; $cHoursCycle++)
            {
                $echoID      = 21459 ;
        
                //$echoString  = $echoString."<P>$echoID fHours fMinutes fSeconds is $fHours : $fMinutes : $fSeconds ";
                        
                if($fHours == FALSE && $fMinutes == FALSE && $fSeconds == FALSE)
                    {
                        // overall duration is FALSE
                        
                        $selectedToggle = "";
                    }
                else                   
                    {
                        // overall duration is not FALSE, so we came here via a back button
                        // but any component of duration could still be a valid 0
                        // deeper check needed 
                
                        if($fHours == $cHoursCycle)
                            {
                                // $fHours is not FALSE
                                
                                $echoID      = 21460 ;
        
                                //$echoString  = $echoString."<P>$echoID fHours fMinutes fSeconds is $fHours : $fMinutes : $fSeconds ";
                                
                                $selectedToggle = "selected";
                            }
                        else
                            {
                                $echoID      = 21461 ;
        
                                //$echoString  = $echoString."<P>$echoID fHours fMinutes fSeconds is $fHours : $fMinutes : $fSeconds ";
                                
                                $selectedToggle = "";
                            }
                    }
                    
                $optionsHours = $optionsHours."<option value=$cHoursCycle $selectedToggle>$cHoursCycle</option>";
            }        
            
        $optionsHours = $optionsHours."<option value=999>select</option></select>";
        
        // mins
        
        $optionsMinutes = "<select name='fMinutes'>";
        
        for ($cMinutesCycle=0; $cMinutesCycle<61; $cMinutesCycle++)
            {
                $echoID      = 21459 ;
        
                //$echoString  = $echoString."<P>$echoID fMinutes fMinutes fSeconds is $fMinutes : $fMinutes : $fSeconds ";
                        
                if($fHours == FALSE && $fMinutes == FALSE && $fSeconds == FALSE)
                    {
                        // overall duration is FALSE
                        
                        $selectedToggle = "";
                    }
                else                   
                    {
                        // overall duration is not FALSE, so we came here via a back button
                        // but any component of duration could still be a valid 0
                        // deeper check needed 
                
                        if($fMinutes == $cMinutesCycle)
                            {
                                // $fMinutes is not FALSE
                                
                                $echoID      = 21460 ;
        
                                //$echoString  = $echoString."<P>$echoID fMinutes fMinutes fSeconds is $fMinutes : $fMinutes : $fSeconds ";
                                
                                $selectedToggle = "selected";
                            }
                        else
                            {
                                $echoID      = 21461 ;
        
                                //$echoString  = $echoString."<P>$echoID fMinutes fMinutes fSeconds is $fMinutes : $fMinutes : $fSeconds ";
                                
                                $selectedToggle = "";
                            }
                    }
                    
                $optionsMinutes = $optionsMinutes."<option value=$cMinutesCycle $selectedToggle>$cMinutesCycle</option>";
            }        
            
        $optionsMinutes = $optionsMinutes."<option value=999>select</option></select>";
        
        // secs
        
        $optionsSeconds = "<select name='fSeconds'>";
        
        for ($cSecondsCycle=0; $cSecondsCycle<61; $cSecondsCycle++)
            {
                $echoID      = 21459 ;
        
                //$echoString  = $echoString."<P>$echoID fSeconds fSeconds fSeconds is $fSeconds : $fSeconds : $fSeconds ";
                        
                if($fHours == FALSE && $fMinutes == FALSE && $fSeconds == FALSE)
                    {
                        // overall duration is FALSE
                        
                        $selectedToggle = "";
                    }
                else                   
                    {
                        // overall duration is not FALSE, so we came here via a back button
                        // but any component of duration could still be a valid 0
                        // deeper check needed 
                
                        if($fSeconds == $cSecondsCycle)
                            {
                                // $fSeconds is not FALSE
                                
                                $echoID      = 21460 ;
        
                                //$echoString  = $echoString."<P>$echoID fSeconds fSeconds fSeconds is $fSeconds : $fSeconds : $fSeconds ";
                                
                                $selectedToggle = "selected";
                            }
                        else
                            {
                                $echoID      = 21461 ;
        
                                //$echoString  = $echoString."<P>$echoID fSeconds fSeconds fSeconds is $fSeconds : $fSeconds : $fSeconds ";
                                
                                $selectedToggle = "";
                            }
                    }
                    
                $optionsSeconds = $optionsSeconds."<option value=$cSecondsCycle $selectedToggle>$cSecondsCycle</option>";
            }        
            
        $optionsSeconds = $optionsSeconds."<option value=999>select</option></select>";
        
        // *******************************************************************************
        // stage 002
        
        $loggerTableCollection = $loggerTableCollection."
    
         <form  style='display:inline;' action='logger.php'             method='POST'>
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellPadding cellspacing=$cellSpacing width=100%>
          
           <tr>
            <td style='text-align: right; vertical-align: top; min-width:1px; padding:8px' colspan=7>
            
            <input type='hidden'           name='fOriginator'              value='loggerContinue34110'>
            <input type='hidden'           name='fSystemno'                value='$cSystemno'>
            <input type='submit'                                           value=' Continue 34110 > '     class='standardWidthFormButton' >
            
            </td>
           </tr>
           <tr>
            <td style='text-align:left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            
            <ul>
            <li>Please add some data. Datetime is required in all cases. 
            <li>Performance and/or weight can be added together or separately. When adding performance, all fields must be completed.
            </ul>
            
            </td>
           </tr>
           
           <tr>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
             <td style='background:#E8FFC6; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             DateTime
             </b>
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=3>
             <b>
             Performance
             </b>
             </td>
             <td style='background:#EDF9D9; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             Weight
             </b>
             </td>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
           </tr>        
           
           
           <tr>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
             <td style='background:#E8FFC6; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             &nbsp;
             </b>
             <BR>YYYYMMDD permitted
             <BR>YYYYMMDD HHMM permitted
             <BR>punctuation is ignored
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             Pursuit
             </b>
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             Distance
             </b>
             <BR>Decimal (#0.000)
             <BR>Up to 3 decimal places permitted but not required
             <BR>Alpha characters are not permitted
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             Duration
             </b>
             <BR>Hours : Mins : Secs
             </td>
             <td style='background:#EDF9D9; text-align:left; vertical-align:top; min-width:1px; padding:$dataCellPadding' colspan=1>
             <b>
             &nbsp;
             </b>
             <BR>Kilograms (#0.000)
             <BR>Up to 3 decimal places permitted but not required
             <BR>Alpha characters are not permitted
             </td>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
           </tr>        
           <tr>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
             <td style='background:#E8FFC6; text-align:left; vertical-align:top; min-width:1px; padding:4px' colspan=1>
             <input  type='text'            name='fDateTime'                  value='$fDateTime'  size=20> 
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:4px' colspan=1>
             $optionsPursuit
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:4px' colspan=1>
             <input  type='text'            name='fDistance'                  value='$fDistance'  size=20> 
             
             </td>
             <td style='background:#DCF2BC; text-align:left; vertical-align:top; min-width:1px; padding:4px' colspan=1>
             $optionsHours
             $optionsMinutes
             $optionsSeconds
             </td>
             <td style='background:#EDF9D9; text-align:left; vertical-align:top; min-width:1px; padding:4px' colspan=1>
             <input  type='text'            name='fWeight'                  value='$fWeight'  size=20> 
             
             </td>
             <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
             &nbsp;
             </td>
           </tr>        
           <tr>
            <td style='text-align:left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=7>
            &nbsp;
            </td>
           </tr>
          </table>
         
          </div> 
         </form>             
        
        ";
    }

// *****************************************************************************************************************************
// 5555ECHO HTML

require ('../header.php');

echo "
          <table border=0 cellpadding=4 cellspacing=4 width=100%>
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $loggerTableCollection
            $loggerTableDoubleCheck
            $loggerTableWritten
            </td>
           </tr>
          </table>

";

require ('../footer.php');

?>