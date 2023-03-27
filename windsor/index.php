<?php

// index.php
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

$cScript            = get_script_name($_SERVER['SCRIPT_FILENAME']);
$cActiveApp         = get_webapp_name($_SERVER['SCRIPT_FILENAME']);  
$cIPAddress         = get_ip_address($_SERVER['HTTP_CLIENT_IP'],$_SERVER['HTTP_X_FORWARDED_FOR'],$_SERVER['REMOTE_ADDR']);
$cHTTPReferrer      = $_SERVER['HTTP_REFERER'];

if ($cActiveApp == $pairedWebappB) 
    {
        $cWebapp = $pairedWebappA;
    }

// *****************************************************************************************************************************
// 1111VARIABLES SET OR RESET 

$dWebapp                 = ucwords(strtolower($cActiveApp));   // capitalise the first letter of the webapp name
$title                   = "$dWebapp Home";
$echoString              = "";

// *****************************************************************************************************************************
// 2222VALIDATE
        
// *****************************************************************************************************************************
// 3333DATABASE  

// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// ***************************************************************************************
// log the call to index
// 20221003 0729

    // *******************************************************************
    // stage 000
    // preamble
    
    // first check that the database exists
        
    // *******************************************************************
    // stage 001
    // Open connection    
    
    
	
    $echoID        = 21403 ;
    $regularSelect = mysqli_connect("$hostName", "$dbUser","$dbPass","$dbName"); 
    if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}
    /*
    else 
        {
            $echoString = $echoString."<P>$echoID cScript is $cScript ";
            //$echoString = $echoString."<P>$echoID cActiveApp is $cActiveApp ";
            //$echoString = $echoString."<P>$echoID cIPAddress is $cIPAddress ";
            //$echoString = $echoString."<P>$echoID cHTTPReferrer is $cHTTPReferrer ";
        
        }    
    */
    
    //********************************************************************************
    // stage 002
    // query
    
    $query  = "SHOW DATABASES LIKE '$cWebapp'";
    //$query  = "SHOW DATABASES";
    $result = mysqli_query($regularSelect,$query);
    
    
    
    
     while ($row = mysqli_fetch_assoc($result))
         {
            extract ($row); 
  
            $echoID      = 21402 ;
             
            $foundDB = $row["Database ($cWebapp)"];
       
            //$echoString = $echoString."<P>$echoID foundDB is $foundDB";
            //$echoString = $echoString."<P>$echoID cWebapp is $cWebapp";
            //$echoString = $echoString."<P>";
            //$arrayString = print_r($row, TRUE); $echoString = $echoString." $echoID row is $arrayString ";

         }     
    
    if ($foundDB == $cWebapp)
    /*
        {
            $echoString = $echoString."<P>yes";
        }
        else
        {
            $echoString = $echoString."<P>no";
        }
         
    
    if (mysqli_fetch_assoc($result))
    */
        {
            $lNarrative    = "call to index";
            $cActivityCode = 0;
                
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
                
            // ***************************************************************************************
            // redirect to dashboard
            // 20221003 0711
            
            
             
            $target = $accessControlledDir.$userTargetPage; 
            //header("Location:$target"); // if the user is not logged in, then dashboard will redirect to login
            
            $messageString ="<a href='login.php'>Log in here</a>";
            
            $messageString = $messageString."
            
            <P>
            &nbsp;
            
          <table border=0 cellpadding=$cellpadding cellspacing=$cellspacing>
           
           <tr>
            <td style='text-align: left; vertical-align: top; max-width:800px'>
            
            The $dWebapp Edition of Long Term Tracker is an exercise logging app designed to cater to various exercise enthusiasts. 

            </td>
           </tr>
          </table>
            
            <P>
            &nbsp;
            
            ";
        }
    else
        {
            // there is no $cWebapp database
            // remedy it by following the invoke documentation
            
            $lErrCode      = 71233 ;
            
            $messageString = "There is a $lErrCode error. |$cWebapp| Please refer to your sysadmin.";
        }
            
    // *******************************************************************
    // stage 003
    // Close connection    
	
    mysqli_close($regularSelect); 
                

// *****************************************************************************************************************************
// 5555ECHO HTML

require ('header.php');
         
echo"

<center>
<P>
<B>
$title
</B>

<P>
$messageString


</center>

";    

require ('footer.php');

?> 