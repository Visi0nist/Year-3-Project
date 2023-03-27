<?php

// search.php 
// 20230101 2222
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

$showTableResults        = 0;
$searchForCriteria       = 0;

$invalidSearchMsg        = "";
$tenCharCriteria         = "";
$searchTableResults      = "";

// *****************************************************************************************************************************
// 2222VALIDATE



    if (    $_POST['fOriginator'] == "usersSearch16524"
         || $_POST['fOriginator'] == "headerSearch16533"
       )
    {
        // ***************************************************************************************************
        // validation parameters
        
        $validationScore       = 0;
        $validationTarget      = 0;
        
        // ***************************************************************************************************
        // get the data from the form, increment the validationTarget each time
        
        $validationTarget++;   $fOriginator                 = trim(htmlentities($_POST['fOriginator']));        	    
        $validationTarget++;   $fSearchCriteria             = trim(htmlentities($_POST['fSearchCriteria']));        	    
        
        // ***************************************************************************************************
        // pre validate the data
        
        if (strlen($fSearchCriteria) == 0)
            {
                $invalidSearchMsg = "it seems no search criteria was entered";
            }
        elseif (strlen($fSearchCriteria) == 1)
            {
                $invalidSearchMsg = "single character searches are not permitted";
            }
        
        // ***************************************************************************************************
        // validate the data
        
        // fOriginator                              alpha, numeric                       4 to 50 char 
        // fSearchCriteria                          alpha - numeric - space allowed - apostrophe allowed - hyphen allowed - @ allowed - fullstop allowed - oblique allowed - 2 to 200 char 
        
        // set a fallBackErrCode in case all validation items are OK, but a mismatch between $validationScore and $validationTarget occurs
        $fallBackErrCode                                                                                                                                                                                                   = 71234; 
        if (preg_match("/^[a-zA-Z0-9]{4,50}$/",                      $fOriginator))       { $validationScore++; }                                                             else { $fOriginator          = ""; $lErrCode = 71235;}
        if (preg_match("/^[a-zA-Z0-9 \'\-\@\.\/]{2,200}$/",          $fSearchCriteria))   { $validationScore++; }                                                             else { $fSearchCriteria      = ""; $lErrCode = 71236;}
        
        // ***************************************************************************************************
        // evaluate validation
        
        if($validationTarget != 0 && $validationScore == $validationTarget)
            {
                // for the log get the first 10 char of fSearchCriteria
                // dummyLongString becomes dummyLongS
                
                $tenCharCriteria = substr($fSearchCriteria, 0, 10);
                
                $lNarrative     = "&quot;$tenCharCriteria&quot; search";
                
                $searchForCriteria          = 1;
                $showTableResults           = 1;
            }
        else
            {
                if ($lErrCode == FALSE) {$lErrCode = $fallBackErrCode;} 
                if ($invalidSearchMsg == FALSE) {$invalidSearchMsg = "the data was not understood";} 
                
                $errMsg         = "Error code ".$lErrCode." - ".$invalidSearchMsg;
                $lNarrative     = "fSearchCriteria validation failed";
            }
    }
    
else 
    {
        // first call to this script by user who entered the URL without using a search button
        // if not logged in, header will have redirected them already
        // if logged in and they really want a blank page, give them a blank page
        
        $lErrCode            = 0 ; 
        $lNarrative          ="URL call to $cScript";
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
   
 
// *****************************************************************************************************************************
// 3333DATABASE WORK

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm

// **********************************************************************************
// searchForCriteria
// 20230320 0649

if ($searchForCriteria == 1)
    {
        // *******************************************************************************
        // stage 001
            
        // preamble
        
        // this routine runs in sections related to what the appropriate action is
        // i.e. the nextscript to go to
        
        // users
        // activity
        // errors
        // tickets
        // documents, etc
                
        // search results are written to DB as that is the easy way to handle many results
        // ones older than $weedingDaysSearch are weeded
        // table by table searches are done for specific tables
        // local arrays are assembled and reset prior to working each table
        // result data is prepared (examing more tables if needed - eg match a mNameKnownAs to a mSystemno)
        // all result data is pushed to master arrays
        // once all tables are searched, master arrays are written to Table: search_results
        
        // dStrings are assembled from Table: search_results
        // echoed to screen
        
        // likely places to look
        
        // table system
        // mSystemno
        // mUserLoginText
        // mLocalRefno
        
        // table    name_text
        // mNameSurname
        // mNameFirstName
        // mNamePronunciation
        // mNameKnownAs
        
        // 20230320 0920 xxx resume later add more tables to search - like 
        // activity log (who searched for what, added, edited, deleted another user, worked what ticket, etc)
        
        // table    status_text
        // mStatusText
         
        // table    grade_text
        // mGradeText
        
        // table error_code
        // mErrorCode
        // mErrorText
        
        // table activity_code
        // mActivityCode
        // mActivityText
        
        // *******************************************************************************
        // stage 001
            
        // set variables
        
        $cSearchno                 = 0;  // default
        
        $cResultAction             = array();
        $cResultText               = array();
        
        // *******************************************************************************
        // stage 002
        
        //open connection
        $echoID    = 43579 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
        
        // *******************************************************************************
        // stage 003
        
        // mSearchno
        
        // find the largest searchno, prepare a new one
        // SELECT MAX is fussy - no extra spaces nor EOL - get it all on one line
        
        $query = "SELECT MAX(mSearchno) FROM search_results";
        
        // *******************************************************************************
        // stage 004
    
        // Prepare statement
        if($stmt = $mysqli->prepare($query)) 
            {
                //Execute it
                $stmt->execute();
   
                if (mysqli_error($mysqli) != FALSE)
                    {
                        $echoID     = 43581 ;
                        $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                    }
  
                // Bind results 
                $stmt->bind_result
                    (
                        $oSearchno
                    );
   
                // Fetch the value
                while($stmt->fetch())
                    {
                          $cSearchno   = htmlspecialchars($oSearchno);
                    }
  
                // Clear memory of results
                $stmt->free_result();                    
         
                // Close statement
                $stmt->close();
            }        
        
        // prepare new cSearchno
        
        $cSearchno++;
        
        // *******************************************************************************
        // stage 005
      
        // Decide action
        
        // where would you go next if you had a result
        // someScript.php without the .php
        
        $relevantAction = "users";
        
        // 20230320 1324 zzdeleteme next 4 lines
        $cLocalRefno               = array();
        $cNameSurname              = array();
        $cNameFirstName            = array();
        $cNameKnownAs              = array();
        
        // *******************************************************************************
        // stage 006
        
        // find matches by
        
        // mSystemno
        // mUserLoginText
        // mLocalRefno
      
        $cSystemno                 = array();
        $cSystemno                 = array();
        $cSystemno                 = array();
        
        // Declare query
        if($stmt = $mysqli->prepare ("     SELECT 
                                                  mSystemno
                                             FROM 
                                                  system
                                            WHERE 
                                                  mSystemno REGEXP ?
                                               OR 
                                                  mUserLoginText REGEXP ?
                                               OR 
                                                  mLocalRefno REGEXP ?
                                              AND
                                                  mSystemEndDateTime
                                          IS NULL
                                    ")
          )
              {
                   // Prepare parameters
                   $mSystemno      = $fSearchCriteria; 
                   $mUserLoginText = $fSearchCriteria; 
                   $mLocalRefno    = $fSearchCriteria; 
                  
                   // Bind parameters
                   $stmt->bind_param
                       (
                           "sss", 
                           $mSystemno,
                           $mUserLoginText,
                           $mLocalRefno
                       );
                               
                   //Execute it
                   $stmt->execute();
            
                   if (mysqli_error($mysqli) != FALSE)
                        {
                            $echoID     = 43584 ;
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
                           $cSystemno[]       = htmlspecialchars($oSystemno);
                       }
              
                   // Clear memory of results
                   $stmt->free_result();
              
                   // Close statement
                   $stmt->close();
      	      }
      	      
        // *******************************************************************************
        // stage 007
        
        // find matches by
        
        // mNameSurname
        // mNameFirstName
        // mNamePronunciation
        // mNameKnownAs
        
        // name_text
      
        $cNameDatano               = array();
        
        // Declare query
        if($stmt = $mysqli->prepare ("     SELECT 
                                                  mNameDatano
                                             FROM 
                                                  name_text
                                            WHERE 
                                                  mNameSurname REGEXP ?
                                               OR 
                                                  mNameFirstName REGEXP ?
                                               OR 
                                                  mNamePronunciation REGEXP ?
                                               OR 
                                                  mNameKnownAs REGEXP ?
                                    ")
          )
              {
                   // Prepare parameters
                   $mNameSurname        = $fSearchCriteria; 
                   $mNameFirstName      = $fSearchCriteria; 
                   $mNamePronunciation  = $fSearchCriteria; 
                   $mNameKnownAs        = $fSearchCriteria; 
                  
                   // Bind parameters
                   $stmt->bind_param
                       (
                           "ssss", 
                           $mNameSurname,
                           $mNameFirstName,
                           $mNamePronunciation,
                           $mNameKnownAs
                       );
                               
                   //Execute it
                   $stmt->execute();
            
                   if (mysqli_error($mysqli) != FALSE)
                        {
                            $echoID     = 43586 ;
                            $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                        } 
            
                   // Bind results 
                   $stmt->bind_result
                       (
                           $oNameDatano
                       );
             
                   // Fetch the value
                   while($stmt->fetch())
                       {
                           $cNameDatano[]       = htmlspecialchars($oNameDatano);
                       }
              
                   // Clear memory of results
                   $stmt->free_result();
              
                   // Close statement
                   $stmt->close();
      	      }
        
        // *******************************************************************************
        // stage 008
        
        // match cNameDatano to cSystemno from system_name_bond where mNameEndDate is NULL
        
        $cNameDatanoElements = count($cNameDatano);
        
        for ($cNameDatanoCycle=0; $cNameDatanoCycle<$cNameDatanoElements ; $cNameDatanoCycle++)
            {
                $echoID     = 43587 ;
                
                //$echoString = $echoString."<P>$echoID cNameDatano is $cNameDatano[$cNameDatanoCycle]";
                
                // Prepare statement        
                if($stmt = $mysqli->prepare ("     SELECT 
                                                          mSystemno 
                                                     FROM 
                                                          system_name_bond
                                                    WHERE 
                                                          mNameDatano = ? 
                                                      AND
                                                          mNameEndDate
                                                  IS NULL
                                            ")
                  )
                      {
                           // Prepare parameters
                           $mNameDatano = $cNameDatano[$cNameDatanoCycle]; 
                  
                           // Bind parameters
                           $stmt->bind_param
                               (
                                   "i", 
                                   $mNameDatano
                               );
                    
                          //Execute it
                          $stmt->execute();
            
                          if (mysqli_error($mysqli) != FALSE)
                               {
                                   $echoID     = 43588 ;
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
                                  $cSystemno[]           = htmlspecialchars($oSystemno);
                              }
              
                          // Clear memory of results
                          $stmt->free_result();
              
                          // Close statement
                          $stmt->close();
      	              }
                
                
            }
        
        // *******************************************************************************
        // stage 009
        
        // eliminate duplicates
       
        $echoID       = 43582 ; 
        
        //$arrayString  = print_r($cSystemno, TRUE); $echoString = $echoString."<P>$echoID cSystemno is $arrayString ";   
        //$arrayString  = print_r($cNameDatano, TRUE); $echoString = $echoString."<P>$echoID cNameDatano is $arrayString ";   
        
        $cSystemno = array_unique($cSystemno);
        $cSystemno = array_values($cSystemno);    // reindex the array keys
      
        $echoID       = 43591 ; 
        
        //$arrayString  = print_r($cSystemno, TRUE); $echoString = $echoString."<P>$echoID cSystemno is $arrayString ";   
        
        // *******************************************************************************
        // stage 010
      
        // add detail
        // reset various arrays
        
        // we have set of cSystemno with no duplicates
        // for each cSystemno find . . .  mNameDatano from system_name_bond where mNameEndDate is NULL
        
        $cNameDatano              = array();
        
        // then from name_text find 
        
        $cNameSurname              = array();
        $cNameFirstName            = array();
        $cNameKnownAs              = array();
            
        $cSystemnoElements = count($cSystemno);
     
        for ($cSystemnoCycle=0; $cSystemnoCycle<$cSystemnoElements ; $cSystemnoCycle++)
            {
                // Prepare statement        
                if($stmt = $mysqli->prepare ("     SELECT 
                                                          mNameDatano 
                                                     FROM 
                                                          system_name_bond
                                                    WHERE 
                                                          mSystemno = ? 
                                                      AND
                                                          mNameEndDate
                                                  IS NULL
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
                                  $echoID     = 43592 ;
                                  $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                              } 
                             
                          // Bind results 
                          $stmt->bind_result
                              (
                                  $oNameDatano
                              );
             
                          // *********************************************************
                          // safety mechanism to enable us to track cases of 0 results
                          $localResultCounter = 0;    
                          // *********************************************************
                                                
                          // Fetch the value
                          while($stmt->fetch())
                              {
                                  $cNameDatano[]           = htmlspecialchars($oNameDatano);
                                  
                                  $echoID     = 43593 ;
                                  
                                  //$echoString = $echoString."<P>$echoID oNameDatano is $oNameDatano";
                                  
                                  $localResultCounter++;
                              }
                              
                          if ($localResultCounter == 0)
                              {
                                  // *********************************************************
                                  // safety mechanism to enable us to track cases of 0 results
                                  // no result was fetched - we need to represent that in our array with a NULL element 
                                  $cNameDatano[]        = NULL;
                                  // *********************************************************
                              
                              }                              
              
                          // Clear memory of results
                          $stmt->free_result();
              
                          // Close statement
                          $stmt->close();
      	              }
      	              
                // *******************************************************************
                // stage 011
                
                // get names
                
                // Prepare statement        
                if($stmt = $mysqli->prepare ("     SELECT 
                                                          mNameSurname, 
                                                          mNameFirstName,
                                                          mNameKnownAs 
                                                     FROM 
                                                          name_text
                                                    WHERE 
                                                          mNameDatano = ? 
                                            ")
                  )
                      {
                           // Prepare parameters
                           $mNameDatano = $cNameDatano[$cSystemnoCycle]; 
                  
                           // Bind parameters
                           $stmt->bind_param
                               (
                                   "i", 
                                   $mNameDatano
                               );
                    
                          //Execute it
                          $stmt->execute();
            
                          if (mysqli_error($mysqli) != FALSE)
                              {
                                  $echoID     = 43594 ;
                                  $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                              } 
                             
                          // Bind results 
                          $stmt->bind_result
                              (
                                  $oNameSurname,
                                  $oNameFirstName,
                                  $oNameKnownAs
                              );
             
                          // *********************************************************
                          // safety mechanism to enable us to track cases of 0 results
                          $localResultCounter = 0;    
                          // *********************************************************
                                                
                          // Fetch the value
                          while($stmt->fetch())
                              {
                                  $cNameSurname[]           = htmlspecialchars($oNameSurname);
                                  $cNameFirstName[]         = htmlspecialchars($oNameFirstName);
                                  $cNameKnownAs[]           = htmlspecialchars($oNameKnownAs);
                                  
                                  $localResultCounter++;
                              }
                              
                          if ($localResultCounter == 0)
                              {
                                  // *********************************************************
                                  // safety mechanism to enable us to track cases of 0 results
                                  // no result was fetched - we need to represent that in our array with a NULL element 
                                  $cNameSurname[]        = NULL;
                                  $cNameFirstName[]      = NULL;
                                  $cNameKnownAs[]        = NULL;
                                  // *********************************************************
                              
                              }                              
              
                          // Clear memory of results
                          $stmt->free_result();
              
                          // Close statement
                          $stmt->close();
      	              
                        // *******************************************************************
                        // stage 012
                
                        // poplulate
                        // $cResultAction
                        // $cResultText
                
                        // users identified by systemno
                
                        $cResultAction[] = $relevantAction;
                        
                        if ($oNameKnownAs != NULL)
                            {
                                $cResultText[]   = $cSystemno[$cSystemnoCycle]." ".$oNameSurname." ".$oNameFirstName." (".$oNameKnownAs.")";
                            }
                        else
                            {
                                $cResultText[]   = $cSystemno[$cSystemnoCycle]." ".$oNameSurname." ".$oNameFirstName;
                            }
      	              }
            }                     
      	      
        // *******************************************************************************
        // Stage 013
        
        // write results to DB
        
        $cSystemnoElements = count($cSystemno);
     
        for ($cSystemnoCycle=0; $cSystemnoCycle<$cSystemnoElements ; $cSystemnoCycle++)
            {
                // *******************************************************************************
                // stage 014
     
                // Declare query
                $query = " 
                           INSERT INTO 
                                       search_results                            
                                           (
                                               mSearchno, 
                                               mSearchCriteria, 
                                               mSearchDate, 
                                               mSystemno, 
                                               mAction, 
                                               mResultText
                                           ) 
                                    values
                                           (
                                               ?, 
                                               ?, 
                                               ?, 
                                               ?, 
                                               ?, 
                                               ?
                                           )
                         "; 
          
                // *******************************************************************************
                // stage 015
           
                // Prepare statement
                if($stmt = $mysqli->prepare($query))                 
                    {
                           // Prepare parameters 
                           $mSearchno        = $cSearchno; 
                           $mSearchCriteria  = $fSearchCriteria; 
                           $mSearchDate      = $sqlNow; 
                           $mSystemno        = $cSystemno[$cSystemnoCycle]; 
                           $mAction          = $cResultAction[$cSystemnoCycle]; 
                           $mResultText      = $cResultText[$cSystemnoCycle]; 
                  
                           // Bind parameters
                           $stmt->bind_param
                               (
                                   "ississ", 
                                   $mSearchno,
                                   $mSearchCriteria,
                                   $mSearchDate,
                                   $mSystemno,
                                   $mAction,
                                   $mResultText
                               );
                    
                          //Execute it
                          $stmt->execute();
            
                          if (mysqli_error($mysqli) != FALSE)
                              {
                                  $echoID     = 43596 ;
                                  $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                              } 
                              
                          // Close statement
                          $stmt->close();
                    }
            }
        
        // *******************************************************************************
        // stage 016
        
        // 20230320 1753 xxx resume weed older than $weedingDaysSearch  
        
        // *******************************************************************************
        // stage 00N
        
        // Close connection
        $mysqli->close();         
        
        // *******************************************************************************
        
        $echoID       = 43580 ; 
        
        //$echoString   = $echoString."<P>$echoID cSearchno is $cSearchno ";
        //$echoString   = $echoString."<P>$echoID fSearchCriteria is $fSearchCriteria ";
        //$arrayString  = print_r($cSystemno, TRUE); $echoString = $echoString."<P>$echoID cSystemno is $arrayString ";   
        //$arrayString  = print_r($cNameDatano, TRUE); $echoString = $echoString."<P>$echoID cNameDatano is $arrayString ";   
        //$arrayString  = print_r($cNameSurname, TRUE); $echoString = $echoString."<P>$echoID cNameSurname is $arrayString ";   
        //$arrayString  = print_r($cNameFirstName, TRUE); $echoString = $echoString."<P>$echoID cNameFirstName is $arrayString ";   
        //$arrayString  = print_r($cNameKnownAs, TRUE); $echoString = $echoString."<P>$echoID cNameKnownAs is $arrayString ";   
        //$arrayString  = print_r($cResultAction, TRUE); $echoString = $echoString."<P>$echoID cResultAction is $arrayString ";   
        //$arrayString  = print_r($cResultText, TRUE); $echoString = $echoString."<P>$echoID cResultText is $arrayString ";   
        
    }
    
// **********************************************************************************
// showTableResults
// 20230320 1706

if ($showTableResults == 1)
    {
        // *******************************************************************************
        // stage 001
            
        // preamble
        
        // collate records from Table: search_results
        
        //  mSystemno 	mAction 	mResultText
        
        // 20230320 1709 xxx resume - manage 30+ results on N pages
        // for now - all results on 1 page
        
        // *******************************************************************************
        // stage 001
            
        // set variables
        
        $cSystemno        = array();
        $cAction          = array();
        $cResultText      = array();
        
        // *******************************************************************************
        // stage 002
        
        //open connection
        $echoID    = 43597 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
        
        // *******************************************************************************
        // stage 003
        
        // mSearchno 
        
        // Prepare statement        
        if($stmt = $mysqli->prepare ("     SELECT 
                                                  mSearchCriteria,
                                                  mSystemno,
                                                  mAction,
                                                  mResultText
                                             FROM 
                                                  search_results
                                            WHERE 
                                                  mSearchno = ?
                                    ")
          )
              {
                   // Prepare parameters
                   $mSearchno      = $cSearchno;
                   
                  
                   // Bind parameters
                   $stmt->bind_param
                       (
                           "i", 
                           $mSearchno
                       );
                               
                   //Execute it
                   $stmt->execute();
            
                   if (mysqli_error($mysqli) != FALSE)
                        {
                            $echoID     = 43598 ;
                            $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                        } 
            
                   // Bind results 
                   $stmt->bind_result
                       (
                           $oSearchCriteria,
                           $oSystemno,
                           $oAction,
                           $oResultText
                       );
             
                   // Fetch the value
                   while($stmt->fetch())
                       {
                           // ignore instances of user Zero
                           
                           if($oSystemno != FALSE)
                               {
                                   $cSearchCriteria   = htmlspecialchars($oSearchCriteria);
                                   $cSystemno[]       = htmlspecialchars($oSystemno);
                                   $cAction[]         = htmlspecialchars($oAction);
                                   $cResultText[]     = htmlspecialchars($oResultText);
                               }
                           
                       }
              
                   // Clear memory of results
                   $stmt->free_result();
              
                   // Close statement
                   $stmt->close();                   
              }
              
              
        // *******************************************************************************
        // stage 00N
        
        // Close connection
        $mysqli->close();         
        
        // *******************************************************************************
      
        $echoID       = 43599 ;
        
        //$echoString = $echoString."<P>$echoID cSearchCriteria is $cSearchCriteria";
     
        //$arrayString = print_r($cSystemno, TRUE); $echoString = $echoString."<P>$echoID cSystemno is $arrayString ";         
        //$arrayString = print_r($cAction, TRUE); $echoString = $echoString."<P>$echoID cAction is $arrayString ";         
        //$arrayString = print_r($cResultText, TRUE); $echoString = $echoString."<P>$echoID cResultText is $arrayString ";         
        
    }


// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm

// **********************************************************************************
// showTableResults
// 20230320 1706

if ($showTableResults == 1)
    {
        //********************************************************
	    // stage 001
        
	    $searchTableResults = $searchTableResults."
	    
          <div class='primary_table'> 
          <table border=0 cellpadding=$cellpadding cellspacing=$cellspacing width=100%>
          
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=3>
            
            Results for . . . <b>$cSearchCriteria</b>
            
            
            </td>
           </tr>
           
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=3>
            &nbsp;
            </td>
           </tr>
        ";
        
        $cSystemnoElements = count($cSystemno);
        
        for ($cSystemnoCycle=0; $cSystemnoCycle<$cSystemnoElements ; $cSystemnoCycle++)
            {
                // *******************************************
                // action
                               
                $buttonAction = $cAction[$cSystemnoCycle].".php";
                
                // *******************************************
                // build TR                
                
        	    $searchTableResults = $searchTableResults."
                <tr>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = '$buttonAction'                           method = 'POST'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'searchThisTab16537'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemno[$cSystemnoCycle]'>
                 <input type='submit' name='16537' value=' $htmlTriangleUp '    class='internalLinkButton'>
                 </form>
                 
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 $cResultText[$cSystemnoCycle]
                 </td>
                 <td style='text-align: left; vertical-align: top; min-width:1px; padding:$dataCellPadding' colspan=1>
                 
                 <form  style = 'display:inline;'  action = '$buttonAction'                           method = 'POST'           target ='_blank'>
                 <input type  = 'hidden'           name   = 'fOriginator'                             value  = 'searchNewTab16538'>
                 <input type  = 'hidden'           name   = 'fSystemno'                               value  = '$cSystemno[$cSystemnoCycle]'>
                 <input type='submit' name='16538' value=' $htmlTriangleRight ' class='internalLinkButton'>
                 </form>
                 
                 </td>
                </tr>
               ";
            }
                    
        $searchTableResults = $searchTableResults."            
        
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px' colspan=3>
            
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
          <table border=0 cellpadding=4 cellspacing=4 width=100%>
           <tr>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            $searchTableResults
            
            </td>
            <td style='text-align: left; vertical-align: top; min-width:1px'>
            &nbsp;
            
            </td>
           </tr>
          </table>
            

";

require ('../footer.php');

?>