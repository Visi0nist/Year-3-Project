<?php

// dashboard.php 
// 20221003 0637
// KMB

// BALMORAL 

// a user management suite

// *****************************************************************************************************************************
// DOCUMENTATION

// Balmoral User Management Portal Documentation 00N.docx

////////////////////////////////////////////////////////////////////////////////
// Dashboard                                                                  //
////////////////////////////////////////////////////////////////////////////////
// Tickets                           // Frozen                                //
////////////////////////////////////////////////////////////////////////////////
// Activity                          // Users                                 //
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

$getDataActivity                  = 0;
$getDataUser                      = 0;
$getDataTicket                    = 0;
$getDataFrozen                    = 0;

$dashboardTableActivity           = "";
$dashboardTableUser               = "";
$dashboardTableTicket             = "";
$dashboardTableFrozen             = "";

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

        $echoID      = 43523 ;
        $echoString  = $echoString."<P>$echoID fOriginator is $fOriginator ";
        	    
    }
else 
    {
        // first call to this script either by login process or by navbar button        
        $lErrCode            = 0 ; 
        $lNarrative          ="call to $cScript";
                
                        $echoID             = 43522 ;
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
                        
        $echoID      = 43527 ;
        //$echoString  = $echoString."<P>$echoID cActiveApp is $cActiveApp ";
        
        $getDataActivity   = 1;
        $getDataUser       = 1;
        $getDataTicket     = 1;
        $getDataFrozen     = 1;
                        
    }
 
// *****************************************************************************************************************************
// 3333DATABASE WORK

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm

// ***********************************************************************************************************
// getDataFrozen  
// 20221226 2118

if ($getDataFrozen == 1)
    {
        // 20221226 2118 xxx resume - do the DB work

	    $dashboardTableFrozen = $dashboardTableFrozen."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellpadding cellspacing=$cellspacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=8>
            
            <form  style='display:inline;' action='frozen.php'           method='POST'>
            <input type='hidden'           name='fOriginator'              value='dashboardFrozen16515'>
            <input type='submit'                                           value=' Frozen 16515 '     class='standardWidthFormButton' >
            </form>             
            
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=9>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Datetime
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            User
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            &nbsp;
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Webapp
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Script
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Operand
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Narrative
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            &nbsp;
            </td>
           </tr>
          </table>
          
         ";
    }
    

// ***********************************************************************************************************
// getDataTicket  
// 20221226 2118

if ($getDataTicket == 1)
    {
        // 20221226 2118 xxx resume - do the DB work


	    $dashboardTableTicket = $dashboardTableTicket."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellpadding cellspacing=$cellspacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=8>
            
            <form  style='display:inline;' action='tickets.php'           method='POST'>
            <input type='hidden'           name='fOriginator'              value='dashboardTickets16512'>
            <input type='submit'                                           value=' Tickets 16512 '     class='standardWidthFormButton' >
            </form>             
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=9>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Datetime
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            User
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            &nbsp;
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Webapp
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Script
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Operand
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Narrative
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            &nbsp;
            </td>
           </tr>
          </table>
          
         ";
    }
    
// ***********************************************************************************************************
// getDataUser  
// 20221225 1719 

if ($getDataUser == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // find all users from activity log
        // use GROUP BY to limit array elements to one element per user
        // order that by recent activity
        // then limit the list by recentUserLimit
        // that gives the most recent users only
        
        // this cannot be done by piggy backing on getDataActivity because that routine
        // wants recent users by activity, and has a display limit in the SQL query
        // this routine (probably) needs more records so that it can find user N who
        // has not been active recently (as far as activity goes), but is one of the 
        // users who needs to appear in the list of recent users by user
        
        // *******************************************************************************
        // stage 001
        
        // set variables
        
        $cLogno              = array();
        $cSystemno           = array();
        $cLogDateTime        = array();
        $cOperand            = array();
        $cScript             = array();
        $cWebapp             = array();
        $cNarrative          = array(); 
        
        $cGradeDatano        = array();
        
        // *******************************************************************************
        // stage 002
        
        //open connection
        $echoID    = 43532 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
        
        // *******************************************************************************
        // stage 003
   
        // find all users from activity log
        
        // Prepare statement        
        if($stmt = $mysqli->prepare ("     SELECT 
                                                  mSystemno
                                             FROM 
                                                  log
                                         GROUP BY 
                                                  mSystemno
                                    ")
          )
              {
                   //Execute it
                   $stmt->execute();
            
                   // Bind results 
                   $stmt->bind_result
                       (
                           $oSystemno
                       );
             
                   // Fetch the value
                   while($stmt->fetch())
                       {
                           // ignore instances of user Zero
                           
                           if($oSystemno != FALSE)
                               {
                                   $cSystemno[]       = htmlspecialchars($oSystemno);
                               }
                           
                       }
              
                   // Clear memory of results
                   $stmt->free_result();
              
                   // Close statement
                   $stmt->close();
      	      }
      
        $echoID       = 43533 ;
     
        //$arrayString = print_r($cSystemno, TRUE); $echoString = $echoString."<P>$echoID cSystemno is $arrayString ";         
        
        // *******************************************************************************
        // stage 004
        
        // for each cSystemno find most recent activity        
        
        $cSystemnoElements = count($cSystemno);
     
        for ($cSystemnoCycle=0; $cSystemnoCycle<$cSystemnoElements ; $cSystemnoCycle++)
            {
                // Prepare statement        
                if($stmt = $mysqli->prepare ("     SELECT 
                                                          mLogno, 
                                                          mLogDateTime, 
                                                          mWebapp, 
                                                          mScript, 
                                                          mNarrative,
                                                          mOperand 
                                                     FROM 
                                                          log
                                                    WHERE 
                                                          mSystemno = ? 
                                                 ORDER BY 
                                                          mLogno 
                                                     DESC
                                                    LIMIT 
                                                          1
                                            ")
                  )
                      {
                           // Prepare parameters
                           $mSystemno = $cSystemno[$cSystemnoCycle]; 
                  
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
                                   $echoID     = 43583 ;
                                   $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                               } 
                             
                          // Bind results 
                          $stmt->bind_result
                              (
                                  $oLogno,
                                  $oLogDateTime,
                                  $oWebapp,
                                  $oScript,
                                  $oNarrative,
                                  $oOperand
                              );
             
                          // Fetch the value
                          while($stmt->fetch())
                              {
                                  $cLogno[]             = htmlspecialchars($oLogno);
                                  $cLogDateTime[]       = htmlspecialchars($oLogDateTime);
                                  $cWebapp[]            = htmlspecialchars($oWebapp);
                                  $cScript[]            = htmlspecialchars($oScript);
                                  $cNarrative[]         = allow_permitted_html(htmlspecialchars($oNarrative));
                                  $cOperand[]           = htmlspecialchars($oOperand);
                              }
              
                          // Clear memory of results
                          $stmt->free_result();
              
                          // Close statement
                          $stmt->close();
      	              }

                        
                 $echoID     = 43534 ;

                //$echoString = $echoString."<P>$echoID cSystemno is $cSystemno[$cSystemnoCycle]";
                        
            }                
                
        $echoID       = 43535 ;
     
        //$arrayString = print_r($cLogDateTime, TRUE); $echoString = $echoString."<P>$echoID cLogDateTime is $arrayString ";   
        
        // *******************************************************************************
        // stage 005
        
        // for each cSystemno find mUserGrade
        
        
        // Declare query
        $query = " 
                    SELECT 
                           mGradeDatano
                      FROM 
                           system_grade_bond
                     WHERE 
                           mSystemno = ?
                       AND
                           mGradeEndDateTime
                   IS NULL
                 ";
        
        // *******************************************************************************
        // stage 006        
        
        $cSystemnoElements = count($cSystemno);
        
        for ($cSystemnoCycle=0; $cSystemnoCycle<$cSystemnoElements ; $cSystemnoCycle++)
            {
                // Prepare statement        
                if($stmt = $mysqli->prepare($query))
                    {
                         // Prepare parameters
                         $mSystemno = $cSystemno[$cSystemnoCycle];
                  
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
                                 $echoID     = 43536 ;
                                 $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                             } 
                     
                        // Bind results 
                        $stmt->bind_result
                            (
                                $oGradeDatano
                            );
                            
                        // *********************************************************
                        // safety mechanism to enable us to track cases of 0 results
                      
                        $localResultCounter = 0;  
                        // *********************************************************
                            
                        
                        // Fetch the value
                        while($stmt->fetch())
                            {
                                $cGradeDatano[]           = htmlspecialchars($oGradeDatano);
                                
                                // *********************************************************
                                // safety mechanism to enable us to track cases of 0 results
                              
                                $localResultCounter++;
                              
                                // *********************************************************                                
                            }
                            
                        if ($localResultCounter == 0)
                            {
                                // *********************************************************
                                // safety mechanism to enable us to track cases of 0 results
                              
                                // no result was fetched - we need to represent that with a NULL element 
                                // *********************************************************
                              
                                $cGradeDatano[]           = NULL;
                              
                            }                            
                            
                        // Clear memory of results
                        $stmt->free_result();
             
                        // Close statement
                        $stmt->close();
        
                    }
            }
         
        $echoID     = 43537 ;
                      
        //$arrayString = print_r($cSystemno, TRUE); $echoString = $echoString."<P>$echoID cSystemno is $arrayString ";              
        //$arrayString = print_r($cGradeDatano, TRUE); $echoString = $echoString."<P>$echoID cGradeDatano is $arrayString ";              
        
        // *******************************************************************************
        // stage 007
        
        // tidy up the arrays
              
        // sort the arrays by $cLogno with most recent first - SORT_DESC
        // MULTISORT - CAUTION!
              
              
        // for pitfalls and an explanation of converting keys
        // see https://www.electronical.ly/paul/doku.php?id=php_array_multisort
        // must be done in the order CONVERT TO ALPHA then SORT then CONVERT TO NUMERIC
        
        $cLogno              = convert_numeric_to_alphanumeric_keys($cLogno);
        $cSystemno           = convert_numeric_to_alphanumeric_keys($cSystemno);
        $cLogDateTime        = convert_numeric_to_alphanumeric_keys($cLogDateTime);
        $cOperand            = convert_numeric_to_alphanumeric_keys($cOperand);
        $cScript             = convert_numeric_to_alphanumeric_keys($cScript);
        $cWebapp             = convert_numeric_to_alphanumeric_keys($cWebapp);
        $cNarrative          = convert_numeric_to_alphanumeric_keys($cNarrative);
        $cGradeDatano        = convert_numeric_to_alphanumeric_keys($cGradeDatano);
        
        array_multisort( 
                         $cLogno, SORT_DESC,
                         $cSystemno, 
                         $cLogDateTime, 
                         $cOperand,
                         $cScript,
                         $cWebapp,
                         $cNarrative,
                         $cGradeDatano
                       );
        
        $cLogno              = convert_alphanumeric_to_numeric_keys($cLogno);
        $cSystemno           = convert_alphanumeric_to_numeric_keys($cSystemno);
        $cLogDateTime        = convert_alphanumeric_to_numeric_keys($cLogDateTime);
        $cOperand            = convert_alphanumeric_to_numeric_keys($cOperand);
        $cScript             = convert_alphanumeric_to_numeric_keys($cScript);
        $cWebapp             = convert_alphanumeric_to_numeric_keys($cWebapp);
        $cNarrative          = convert_alphanumeric_to_numeric_keys($cNarrative);
        $cGradeDatano        = convert_alphanumeric_to_numeric_keys($cGradeDatano);
        
        // *******************************************************************************
        // stage 008
              
        // reindex the array keys
              
        $cLogno              = array_values($cLogno);
        $cSystemno           = array_values($cSystemno);
        $cLogDateTime        = array_values($cLogDateTime);
        $cOperand            = array_values($cOperand);
        $cScript             = array_values($cScript);
        $cWebapp             = array_values($cWebapp);
        $cNarrative          = array_values($cNarrative);
        $cGradeDatano        = array_values($cGradeDatano);
              
        $echoID       = 43538 ;
     
        //$arrayString = print_r($cLogno, TRUE); $echoString = $echoString."<P>$echoID cLogno is $arrayString ";                 
        //$arrayString = print_r($cLogDateTime, TRUE); $echoString = $echoString."<P>$echoID cLogDateTime is $arrayString ";                 
        //$arrayString = print_r($cSystemno, TRUE); $echoString = $echoString."<P>$echoID cSystemno is $arrayString ";                 
        //$arrayString = print_r($cWebapp, TRUE); $echoString = $echoString."<P>$echoID cWebapp is $arrayString ";                 
        //$arrayString = print_r($cScript, TRUE); $echoString = $echoString."<P>$echoID cScript is $arrayString ";                 
        //$arrayString = print_r($cOperand, TRUE); $echoString = $echoString."<P>$echoID cOperand is $arrayString ";                 
        //$arrayString = print_r($cNarrative, TRUE); $echoString = $echoString."<P>$echoID cNarrative is $arrayString ";                 
        //$arrayString = print_r($cGradeDatano, TRUE); $echoString = $echoString."<P>$echoID cGradeDatano is $arrayString ";         
        
        // *******************************************************************************
        // stage 009
        
        // limit the number of elements to $recentUserLimit
        // we need temporary arrays for that
        
        $tLogno              = array();
        $tSystemno           = array();
        $tLogDateTime        = array();
        $tOperand            = array();
        $tScript             = array();
        $tWebapp             = array();
        $tNarrative          = array();
        $tUserGrade          = array();
        
        $cLognoElements = count($cLogno);
                
        if ($cLognoElements < $dashboardLimitUsers)
            {
                $recentUserLimit = $cLognoElements;
            }
     
        for ($cLognoCycle=0; $cLognoCycle<$recentUserLimit ; $cLognoCycle++)
            {
                $echoID     = 43539 ;

                //$echoString = $echoString."<P>$echoID cLogno is $cLogno[$cLognoCycle]";
                
                // push the $recentUserLimit values into our temporary arrays
                
                $tLogno[]             = $cLogno[$cLognoCycle];
                $tLogDateTime[]       = $cLogDateTime[$cLognoCycle];
                $tSystemno[]          = $cSystemno[$cLognoCycle];
                $tWebapp[]            = $cWebapp[$cLognoCycle];
                $tScript[]            = $cScript[$cLognoCycle];
                $tNarrative[]         = $cNarrative[$cLognoCycle];
                $tUserGrade[]         = $cUserGrade[$cLognoCycle];
                $tOperand[]           = $cOperand[$cLognoCycle];
            }        
        
        // make the current arrays equal to the temporary arrays
        
        $cLogno              = $tLogno;
        $cLogDateTime        = $tLogDateTime;
        $cSystemno           = $tSystemno;
        $cWebapp             = $tWebapp;
        $cScript             = $tScript;
        $cNarrative          = $tNarrative;
        $cUserGrade          = $tUserGrade;
        $cOperand            = $tOperand;
                
        // now we have data for the most recent N active users
        
        // *******************************************************************************
        // stage 00N
        
        // Close connection
        $mysqli->close();         
        
        // now we can show results to user
        // show N most recent users
        
	    $dashboardTableUser = $dashboardTableUser."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellpadding cellspacing=$cellspacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=8>
            
            <form  style='display:inline;' action='users.php'              method='POST'>
            <input type='hidden'           name='fOriginator'              value='dashboardUsers16509'>
            <input type='submit'                                           value=' Users 16509 '     class='standardWidthFormButton' >
            </form>           
            
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=8>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Datetime
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            User
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            &nbsp;
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Webapp
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Script
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Operand
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Narrative
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            &nbsp;
            </td>
           </tr>
           
        ";
        
        $cLognoElements = count($cLogno);
     
        for ($cLognoCycle=0; $cLognoCycle<$cLognoElements ; $cLognoCycle++)
            {
                $echoID     = 43540 ;

                //$echoString = $echoString."<P>$echoID cLogno is $cLogno[$cLognoCycle]";
                
                // *******************************************
                // display format
                
                // display DateTime exclude seconds and use &nbsp;
                
                $dLogDateTime = SQLDateTime_to_bellaDateTime($cLogDateTime[$cLognoCycle]);
                $dLogDateTime = str_replace(" ","&nbsp;",substr($dLogDateTime, 0, 13)); 
                
                // *******************************************
                // build TR                
                
        	    $dashboardTableUser = $dashboardTableUser."
                <tr>
                
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $dLogDateTime
                 </td>
                 
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cSystemno[$cLognoCycle] 
                 </td>
                 
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'dashboardThisTab16510'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemno[$cLognoCycle]'>
                 <input type='submit' name='16510' value=' $htmlTriangleUp '    class='internalLinkButton'>
                 </form>
                 </td>
                 
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cWebapp[$cLognoCycle]
                 </td>
                 
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cScript[$cLognoCycle]
                 </td>
                 
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cOperand[$cLognoCycle]
                 </td>
                 
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cNarrative[$cLognoCycle]
                 </td>
                 
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 <form  style = 'display:inline;'  action = 'users.php'                               method = 'POST'           target ='_blank'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'dashboardNewTab16511'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemno[$cLognoCycle]'>
                 <input type='submit' name='16511' value=' $htmlTriangleRight ' class='internalLinkButton'>
                 </form>
                 </td>
                 
                </tr>
        ";
                
            }
                    
        $dashboardTableUser = $dashboardTableUser."            
        
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=8>
            
            </td>
           </tr>
           
          </table>
          </div>
	    
	    ";
    
    }

// ***********************************************************************************************************
// getDataActivity   

if ($getDataActivity == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // get the N most recent activities, as per $dashboardLimitActivity
        
        // |datetime|user|uparrow|webapp|script|narrative|rightarrow|
        
        // mLogno 	mSystemno 	mActivityCode 	mLogDateTime 	mOperand 	mScript 	mWebapp 	
        // mErrCode 	mNarrative 	mIPAddress 	mHTTPReferrer
        
        // *******************************************************************************
        // stage 001
        
        // set variables
        
        $cLogno        = array();
        $cLogDateTime  = array();
        $cSystemno     = array();
        $cWebapp       = array();
        $cScript       = array();
        $cOperand      = array();
        $cNarrative    = array();
        
        // *******************************************************************************
        // stage 002
        
        //open connection
        $echoID    = 43524 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
        
        // *******************************************************************************
        // stage 003
        
        // Declare query
        $query = " 
                     SELECT 
                             mLogno,
                             mLogDateTime,
                             mSystemno,
                             mWebapp,
                             mScript,
                             mOperand,
                             mNarrative
                       FROM 
                             log
                   ORDER BY 
                             mLogno 
                       DESC
                      LIMIT 
                             0,$dashboardLimitActivity
                 ";  
                
        // *******************************************************************************
        // stage 004
        
        // Prepare statement
             // Prepare statement        
        if($stmt = $mysqli->prepare ($query))
              {
                  //Execute it
                  $stmt->execute();
          
                  // Bind results 
                  $stmt->bind_result
                      (
                          $oLogno,
                          $oLogDateTime,
                          $oSystemno,
                          $oWebapp,
                          $oScript,
                          $oOperand,
                          $oNarrative
                      );
           
                  // Fetch the value
                  while($stmt->fetch())
                      {
                          $cLogno[]        = htmlspecialchars($oLogno);
                          $cLogDateTime[]  = htmlspecialchars($oLogDateTime);
                          $cSystemno[]     = htmlspecialchars($oSystemno);
                          $cWebapp[]       = htmlspecialchars($oWebapp);
                          $cScript[]       = htmlspecialchars($oScript);
                          $cOperand[]      = htmlspecialchars($oOperand);
                          $cNarrative[]    = allow_permitted_html(htmlspecialchars($oNarrative));
                      }
            
                  // Clear memory of results
                  $stmt->free_result();
            
                  // Close statement
                  $stmt->close();
              }        
   
        //********************************************************
	    // stage 00N
        // Close connection
        
        $mysqli->close();
        
        $echoID       = 43525 ;
     
        //$arrayString = print_r($cLogno, TRUE); $echoString = $echoString."<P>$echoID cLogno is $arrayString ";                 
        //$arrayString = print_r($cLogDateTime, TRUE); $echoString = $echoString."<P>$echoID cLogDateTime is $arrayString ";                 
        //$arrayString = print_r($cSystemno, TRUE); $echoString = $echoString."<P>$echoID cSystemno is $arrayString ";                 
        //$arrayString = print_r($cWebapp, TRUE); $echoString = $echoString."<P>$echoID cWebapp is $arrayString ";                 
        //$arrayString = print_r($cScript, TRUE); $echoString = $echoString."<P>$echoID cScript is $arrayString ";                 
        //$arrayString = print_r($cOperand, TRUE); $echoString = $echoString."<P>$echoID cOperand is $arrayString ";                 
        //$arrayString = print_r($cNarrative, TRUE); $echoString = $echoString."<P>$echoID cNarrative is $arrayString ";                 
        
        // now we can show results to user
        
        //********************************************************
	    // stage 001
        
        // show $dashboardLimitActivity most recent activities
        
	    $dashboardTableActivity = $dashboardTableActivity."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellpadding cellspacing=$cellspacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=8>
            
            <form  style='display:inline;' action='activity.php'           method='POST'>
            <input type='hidden'           name='fOriginator'              value='dashboardActivity16508'>
            <input type='submit'                                           value=' Activity 16508 '     class='standardWidthFormButton' >
            </form>             
            
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=9>
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Datetime
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            User
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            &nbsp;
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Webapp
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Script
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Operand
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            <B>
            Narrative
            </B>
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
            &nbsp;
            </td>
           </tr>
           
        ";
        
        // simplify cActivitySessionID just in order to echo it
        // no change to underlying data
        // 6 chars capitalised
        

        $cLognoElements = count($cLogno);
        
        for ($cLognoCycle=0; $cLognoCycle<$cLognoElements ; $cLognoCycle++)
            {
                // *******************************************
                // display format
                
                // display DateTime exclude seconds and use &nbsp;
                
                $dLogDateTime = SQLDateTime_to_bellaDateTime($cLogDateTime[$cLognoCycle]);
                $dLogDateTime = str_replace(" ","&nbsp;",substr($dLogDateTime, 0, 13)); 
                
                // *******************************************
                // build TR                
                
        	    $dashboardTableActivity = $dashboardTableActivity."
                <tr>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $dLogDateTime
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cSystemno[$cLognoCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = 'activity.php'                            method = 'POST'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'dashboardThisTab16507'>
                 <input type  = 'hidden'           name   = 'fLogno'                              value  = '$cLogno[$cLognoCycle]'>
                 <input type='submit' name='16507' value=' $htmlTriangleUp '    class='internalLinkButton'>
                 </form>
                 
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cWebapp[$cLognoCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cScript[$cLognoCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cOperand[$cLognoCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cNarrative[$cLognoCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = 'activity.php'                            method = 'POST'           target ='_blank'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'dashboardNewTab16506'>
                 <input type  = 'hidden'           name   = 'fLogno'                              value  = '$cLogno[$cLognoCycle]'>
                 <input type='submit' name='16506' value=' $htmlTriangleRight ' class='internalLinkButton'>
                 </form>
                 
                 </td>
                </tr>
               ";
            }
                    
        $dashboardTableActivity = $dashboardTableActivity."            
        
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=9>
            
            </td>
           </tr>
           
          </table>
          </div>
	    
	    ";
    }

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
            $dashboardTableTicket
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $dashboardTableFrozen
            </td>
           </tr>
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $dashboardTableActivity
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $dashboardTableUser
            </td>
           </tr>
          </table>
            

";

require ('../footer.php');

?>