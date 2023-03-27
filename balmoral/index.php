<?php

// index.php
// 20221003 0635
// KMB

// BALMORAL 

// a user management suite

// generally the index page will automatically redirect to the dashboard
// first, it checks that there is a balmoral database
// if there is, then it logs the visit by the user to this index page, and then immediately redirects
// if there is no database, then presumably the site is still being set up by the sysadmin
// and a numeric error message is shown enabling the sysadmin to track the error in the scripts
// and remedy it by following the invoke documentation

// *****************************************************************************************************************************
// DOCUMENTATION

// Balmoral User Management Portal Documentation 00N.docx

// *****************************************************************************************************************************
// 0011HOUSEKEEPING

ini_set('displayErrors', 0);        // toggle 0 for off, 1 for on (1 only to aid devwork, then revert to 0)
//error_reporting(E_ALL);

require ('foundations/configbalmoral.php');
require ('foundations/commonfns.php');

$cScript            = get_script_name($_SERVER['SCRIPT_FILENAME']);
$cActiveApp         = get_webapp_name($_SERVER['SCRIPT_FILENAME']);  
$cIPAddress         = get_ip_address($_SERVER['HTTP_CLIENT_IP'],$_SERVER['HTTP_X_FORWARDED_FOR'],$_SERVER['REMOTE_ADDR']);
$cHTTPReferrer      = $_SERVER['HTTP_REFERER'];

            $echoID      = 45602 ;
            //$echoString = $echoString."<P>$echoID cActiveApp is $cActiveApp";
            //$echoString = $echoString."<P>$echoID cWebapp is $cWebapp";

//if ($cActiveApp == $pairedWebappA) {$cWebapp = $cActiveApp;}
//if ($cActiveApp == $pairedWebappB) {$cWebapp = $pairedWebappA;}

if ($cActiveApp == $pairedWebappB) 
    {
        $cWebapp = $pairedWebappA;
    }
else
    {
       // $cWebapp = $cActiveApp;
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
	
    $echoID        = 43467 ;
    $regularSelect = mysqli_connect("$hostName", "$dbUser","$dbPass","$dbName"); 
    if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}    
    
    //********************************************************************************
    // stage 002
    // query
    
    $query  = "SHOW DATABASES LIKE '$cWebapp'";
    //$query  = "SHOW DATABASES LIKE '$cActiveApp'";
    $result = mysqli_query($regularSelect,$query);
    
            $echoID      = 45601 ;
            //$echoString = $echoString."<P>$echoID cActiveApp is $cActiveApp";
            //$echoString = $echoString."<P>$echoID cWebapp is $cWebapp";
            
    
    $gotcha = "";
    
     while ($row = mysqli_fetch_assoc($result))
         {
            extract ($row); 
  
            $echoID      = 45600 ;
             
            $foundDB = $row["Database ($cWebapp)"];
            
            if ($foundDB == $cActiveApp)
                {
                    
                    $gotcha = $cActiveApp;
                }
       
            //$echoString = $echoString."<P>$echoID foundDB is $foundDB";
            //$echoString = $echoString."<P>$echoID gotcha is $gotcha";
            //$echoString = $echoString."<P>$echoID cWebapp is $cWebapp";
            //$echoString = $echoString."<P>";
            //$arrayString = print_r($row, TRUE); $echoString = $echoString." $echoID row is $arrayString ";

         }     
    
    if ($gotcha == $cActiveApp)
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
            
            Balmoral is a User Management Portal webapp. It provides a range of tools enabling a systems administrator (and the 
            admin team) to exercise control over who has what level of access to a webapp; to add, edit and delete users; 
            to examine the activity log; and to carry out some maintenance procedures. 

            <P>
            Balmoral is a single, standalone, discrete webapp which is designed to be paired with a Consumer Portal webapp. 
            The implementation of the User Management Portal is designed to be sufficiently flexible so as to allow for 
            pairing with a wide range of Consumer Portal webapps, for example a discussion forum, a resource library, or 
            an e-commerce shop. 

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
            
            $messageString = "There is a $lErrCode error. Please refer to your sysadmin.";
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