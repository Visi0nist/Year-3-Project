<?php

// commonfns.php
// 20220815 1716
// KMB

// BALMORAL 

// a user management suite

// *****************************************************************************************************************************
// HOUSEKEEPING

// bellaDateTime was known as myFormat, where the string has one space and is represented as YYYYMMDD HHmm
// it is a minor variation of the ISO8601 standard (no seconds) 

// continue this session

// session_start();

// *****************************************************************************************************************************
// FUNCTIONS

// newest at the top

// *****************************************************************************************************************************
// allow_permitted_html                   
// 20230320 0551
    
function allow_permitted_html($inputValue)
{
    // find instances of safe or double html
    // correct it, for example 
                       
    // &lt;P&gt;   becomes   <p>
    // &lt;p&gt;   becomes   <p>
    // &lt;/p&gt;  becomes   </p>
    // &amp;#039;  becomes   &#039;
    
    // some tags feature twice in order to accommodate upper and lower case - see P and p in mysqliFetchedHTML
                       
    $mysqliFetchedHTML  = array (
                                    "&ndash;",
                                    "&mdash;",
                                    "&lsquo;",
                                    "&rsquo;",
                                    "&ldquo;",
                                    "&rdquo;",
                                    "&lt;P&gt;",
                                    "&lt;/P&gt;",
                                    "&lt;B&gt;",
                                    "&lt;/B&gt;",
                                    "&lt;I&gt;",
                                    "&lt;/I&gt;",
                                    "&lt;p&gt;",
                                    "&lt;/p&gt;",
                                    "&lt;b&gt;",
                                    "&lt;/b&gt;",
                                    "&lt;i&gt;",
                                    "&lt;/i&gt;",
                                    "&amp;#039;",
                                    "&amp;#39;",
                                    "&amp;apos;",
                                    "&amp;amp;",
                                    "&amp;quot;",
                                    "&amp;pound;",
                                    "&lt;LI&gt;",
                                    "&lt;/LI&gt;",
                                    "&lt;UL&gt;",
                                    "&lt;/UL&gt;",
                                    "&lt;li&gt;",
                                    "&lt;/li&gt;",
                                    "&lt;ul&gt;",
                                    "&lt;/ul&gt;"
                                );
                       
    $permittedHTML      = array (
                                    "-",
                                    "-",
                                    "&apos;",
                                    "&apos;",
                                    "&quot;",
                                    "&quot;",
                                    "<p>",
                                    "</p>",
                                    "<b>",
                                    "</b>",
                                    "<i>",
                                    "</i>",
                                    "<p>",
                                    "</p>",
                                    "<b>",
                                    "</b>",
                                    "<i>",
                                    "</i>",
                                    "&apos;",
                                    "&apos;",
                                    "&apos;",
                                    "&amp;",
                                    "&quot;",
                                    "&pound;",
                                    "<li>",
                                    "</li>",
                                    "<ul>",
                                    "</ul>",
                                    "<li>",
                                    "</li>",
                                    "<ul>",
                                    "</ul>"
                                 );
   
    $mysqliFetchedHTMLElements = count($mysqliFetchedHTML);
     
    for ($mysqliFetchedHTMLCycle=0; $mysqliFetchedHTMLCycle<$mysqliFetchedHTMLElements ; $mysqliFetchedHTMLCycle++)
        {
    	   $inputValue = str_replace($mysqliFetchedHTML[$mysqliFetchedHTMLCycle], $permittedHTML[$mysqliFetchedHTMLCycle], $inputValue);
        }
        
    $outputValue = $inputValue;
    
    return $outputValue;
}

// *****************************************************************************************************************************
// get_user_data
// 20221219 0544

function get_user_data()
    {
        // ***********************************************************
        // stage 000
        
        // preamble
        
        // get user's mSystemno and name, etc from analysing $cTokenLogin
        
        // ***********************************************************
        // stage 001
        
        // variables
        
        $cTokenLogin        = $_COOKIE['cookieLogin'];
        
        //********************************************************
        // stage 002
        
        // Open connection
    
        require ('configbalmoral.php');
	    
        $mysqli = new mysqli("$hostName", "$dbUser","$dbPass","$dbName");
        
        // ***********************************************************
        // stage 003
        
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
        
        // ***********************************************************
        // stage 004
        
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
                        $echoID     = 43528 ;
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
        
        // ***********************************************************
        // stage 005
        
        // Declare query
        $query = " 
                     SELECT 
                             mSystemno
                       FROM 
                             system_token_bond
                      WHERE 
                             mTokenDatano      = ?
                  ";  
        
        // ***********************************************************
        // stage 006
        
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
                        $echoID     = 43529 ;
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
        
        // ***********************************************************
        // stage 007
        
        // Declare query
        $query = " 
                     SELECT 
                             mNameDatano
                       FROM 
                             system_name_bond
                      WHERE 
                             mSystemno      = ?
                        AND
                             mNameEndDate
                    IS NULL
                  ";  
        
        // ***********************************************************
        // stage 008
        
        // Prepare statement
        if($stmt = $mysqli->prepare($query)) 
            {
                // Prepare parameters
                $mSystemno   = $cSystemno;
             
                // Bind parameters
                $stmt->bind_param
                    (
                        "i",
                          
                        $mSystemno
                    );               
                    
                // Execute it
                $stmt->execute();
           
                if (mysqli_error($mysqli) != FALSE)
                    {
                        $echoID     = 43529 ;
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
                        $cNameDatano   = htmlspecialchars($oNameDatano);
                    }
   
                // Clear memory of results
                $stmt->free_result();
                
                // Close statement
                $stmt->close();
            }
        
        // ***********************************************************
        // stage 009
        
        // Declare query
        $query = " 
                     SELECT 
                             mNameSurname,
                             mNameFirstName,
                             mNameMiddleName,
                             mNamePronunciation,
                             mNameKnownAs 
                       FROM 
                             name_text
                      WHERE 
                             mNameDatano      = ?
                  ";  
        
        // ***********************************************************
        // stage 010
        
        // Prepare statement
        if($stmt = $mysqli->prepare($query)) 
            {
                // Prepare parameters
                $mNameDatano   = $cNameDatano;
             
                // Bind parameters
                $stmt->bind_param
                    (
                        "i",
                          
                        $mNameDatano
                    );               
                    
                // Execute it
                $stmt->execute();
           
                if (mysqli_error($mysqli) != FALSE)
                    {
                        $echoID     = 43530 ;
                        $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                    }               
                  
                // Bind results 
                $stmt->bind_result
                    (
                        $oNameSurname,
                        $oNameFirstName,
                        $oNameMiddleName,
                        $oNamePronunciation,
                        $oNameKnownAs 
                    );
       
                // Fetch the value
                while($stmt->fetch())
                    {
                        $cNameSurname         = htmlspecialchars($oNameSurname);
                        $cNameFirstName       = htmlspecialchars($oNameFirstName);
                        $cNameMiddleName      = htmlspecialchars($oNameMiddleName);
                        $cNamePronunciation   = htmlspecialchars($oNamePronunciation);
                        $cNameKnownAs         = htmlspecialchars($oNameKnownAs);
                    }
   
                // Clear memory of results
                $stmt->free_result();
        
                // Close statement
                $stmt->close();
            }
        
        //********************************************************
        // Close connection
        // stage 011
                
        $mysqli->close();
        
        // ***********************************************************
        // stage 00N
        
        // package the output
        // consistent var first, function specific var second
      
        $outputValue = array (
                                 "commonFnsErrMsg"                          => $errMsg,
                                 "commonFnsEchoString"                      => $echoString,
                             
                                 "cSystemno"                                => $cSystemno,
                                 "cNameSurname"                             => $cNameSurname,
                                 "cNameFirstName"                           => $cNameFirstName,
                                 "cNameMiddleName"                          => $cNameMiddleName,
                                 "cNamePronunciation"                       => $cNamePronunciation,
                                 "cNameKnownAs"                             => $cNameKnownAs,
                                 "cSomeVar002"                              => $cSomeVar002
                             );
                          
        return $outputValue;
    }

// *****************************************************************************************************************************
// random_letter_generator
// 20221211 1235

function random_letter_generator($inputValue)
    {
        // ***********************************************************
        // stage 000
        
        // preamble
        
        // inputValue is a small integer, say 6
        // outputValue is a single string with (say) 6 random letters
        
        // ***********************************************************
        // stage 001
        
        // generate
        
        $outputValue = ""; 
        $chars = "abcdefghijklmnopqrstuvwxyz"; 
        $charArray = str_split($chars); 
      
        for ($randomLetterCounter = 0; $randomLetterCounter < $inputValue; $randomLetterCounter++)
            { 
              $randItem = array_rand($charArray);
              $outputValue .= "".$charArray[$randItem];
            }
    
        // ***********************************************************
        // stage 00N
        
        return $outputValue;
    }
    
// *****************************************************************************************************************************
// get_webapp_path
// 20221128 1900
                       
function get_webapp_path($inputValue)
    {
        // ***********************************************************
        // stage 000
        
        // preamble
        
        // the objective is to indentify a string like . . .
        // https://www.electronical.ly/restricted/balmoral/
        
        // from anything that looks like . . .
        // https://www.electronical.ly/restricted/balmoral/login.php
        
        // or looks like . . .
        // https://www.electronical.ly/restricted/balmoral/vault/dashboard.php
        
        // no matter how deeply embedded the calling script is
        // this info, "the core webapp path", is needed in order to find consentmanager.php
        // which resides in "the core webapp path", and which (after handling a cookie) will 
        // take the user back to wherever they wanted to be 
        
        // https://www.electronical.ly/restricted/balmoral/
        //   ^             ^               ^         ^
        //   1             2               3         4
        
        // the core webapp dir is at a depth of 4
        // get_webapp_path needs to find the 5th occurence of a forward slash and eliminate everything after that
        // it does that by incrementally eliminating from the back to the front using 
        // foward slash as the target
        
        // ***********************************************************
        // stage 001
        
        $myString       = $inputValue;
        $dirDepth       = 4;          
        
        // ***********************************************************
        // stage 002
        
        // how many foward slash?
        // if there are more than $whileLoopMax then the saftey mechanism will kick in first
        
        $findMe   = '/';
        
        $whileLoopCounter     = 0;          // safety mechanism part 1
        $whileLoopMax         = 20;         // safety mechanism part 2
                
        do
            {
                $charCount = substr_count($myString, $findMe);
        
                $trailingString = strrchr($myString, $findMe);
                
                // remove $trailingString from $myString
                
                //$myVar = str_replace("oldText", "newText", $myVar);
    
                $myString = str_replace($trailingString, "", $myString);                
                
                $whileLoopCounter++;
            }
            
        while ($charCount > $dirDepth+1 && $whileLoopCounter < $whileLoopMax );
                
        // ***********************************************************
        // stage 003
        
        // add on the final forward slash again
        
        $myString = $myString.$findMe;
        
        // ***********************************************************
        // stage 004
           
        // ***********************************************************
        // stage 005
        
        //$outputValue = $trailingString;
        //$outputValue = $charCount;
        $outputValue = $myString;
        
        // ***********************************************************
        // stage 006
        
        return $outputValue;
    }


// *****************************************************************************************************************************
// get_webapp_name
// 20220925 0902
                       
function get_webapp_name($inputValue)
    {
        // ***********************************************************
        // stage 001
        
        // NB boundary = oblique in windows and backslash in linux
   
        $myString       = $inputValue;
        
        // adjust the process for scripts within the 'foundations' or 'vault' sub directory
        // this will work as long as the webapp itself is not called 'foundations' or 'vault'
        
        $myString     = str_replace("\\foundations", "", $myString);    // linux
        $myString     = str_replace("/foundations", "", $myString);    // windows
        $myString     = str_replace("\\vault", "", $myString);    // linux
        $myString     = str_replace("/vault", "", $myString);    // windows
        
        // ***********************************************************
        // stage 002
        
        // where is the last backslash?
        
        $findMe   = '\\';
        $pos      = strripos($myString, $findMe, 0);        
        
        if ($pos === FALSE)
           {
               // windows not linux 
               
               // where is the last oblique?
               
               $findMe   = '/';
               $pos      = strripos($myString, $findMe, 0);        
           }
                
        // ***********************************************************
        // stage 003
        
        // start at the last occurence of boundary, remove that and everything after
        
        // substr - starts at char N1 and grabs N2 characters
        
        $myString = substr($inputValue, 0, $pos); 
        
        // ***********************************************************
        // stage 004
        
        // again, where is the last backslash?
        
        $findMe   = '\\';
        $pos      = strripos($myString, $findMe, 0);        
        
        if ($pos === FALSE)
           {
               // windows not linux 
               
               // where is the last oblique?
               
               $findMe   = '/';
               $pos      = strripos($myString, $findMe, 0);        
           }
           
        // ***********************************************************
        // stage 005
        
        // start at the last occurence of boundary, take everything after that
        
        $myStringLength    = strlen($myString);
        
        // substr - starts at char N1 and grabs N2 characters
        
        $outputValue = substr($myString, $pos+1, $myStringLength); 
        
        // ***********************************************************
        // stage 006
        
        return $outputValue;
    }
    

// *****************************************************************************************************************************
// get_ip_address
// 20220923 1948

function get_ip_address($serverClientIP,$xFowarded,$remoteAddr)
    {
        // ***********************************************************
        // stage 000
        // preamble
        
        // this function lives in commonfns primarily to minimise the clutter under 0011HOUSEKEEPING in each script
        // by putting this here, we need only one line of code in the calling script to get $cIPAddress
        
        // ***********************************************************
        // stage 001
        // evaluate
        
        if (!empty($serverClientIP)) 
            {
                $outputValue = $serverClientIP;
            } 
        elseif (!empty($xFowarded)) 
            {
                $outputValue = $xFowarded;
            } 
        else 
            {
                $outputValue = $remoteAddr;
            }
        
        // ***********************************************************
        // stage 002
        
        return $outputValue;
    }


// *****************************************************************************************************************************
// get_script_name
// 20220923 0651
                       
function get_script_name($inputValue)
    {
        // ***********************************************************
        // stage 001
        
        // NB boundary = oblique in windows and backslash in linux
   
        $myString       = $inputValue;
        $inputLength    = strlen($inputValue);
        
        // ***********************************************************
        // stage 002
        
        // where is the last backslash?
        
        $findMe   = '\\';
        $pos      = strripos($myString, $findMe, 0);        
        
        if ($pos === FALSE)
           {
               // windows not linux 
               
               // where is the last oblique?
               
               $findMe   = '/';
               $pos      = strripos($myString, $findMe, 0);        
           }
                
        // ***********************************************************
        // stage 003
        
        // start at the last occurence of boundary, take everything after that
        
        // substr - starts at char N1 and grabs N2 characters
        
        $outputValue = substr($inputValue, $pos+1, $inputLength); 
        
        // ***********************************************************
        // stage 004
        
        // eliminate the expression ".php"
        
        //$myVar = str_replace("oldText", "newText", $myVar);
            
        $outputValue = str_replace(".php", "", $outputValue); 
    
        // ***********************************************************
        // stage 005
        
        return $outputValue;
    }

// *****************************************************************************************************************************
// update_log
// 20220830 2020
    
function update_log($logData) 
{
    //********************************************************
    // Open connection
    
    require ('configbalmoral.php');
	    
    $mysqli = new mysqli("$hostName", "$dbUser","$dbPass","$dbName");
                
    //********************************************************
    // Prepare statement      
    
    $stmt = $mysqli->prepare ("INSERT INTO log (
                                                   mSystemno, 
                                                   mActivityCode, 
                                                   mLogDateTime, 
                                                   mOperand, 
                                                   mScript, 
                                                   mWebapp, 
                                                   mErrCode, 
                                                   mNarrative, 
                                                   mIPAddress, 
                                                   mHTTPReferrer
                                               ) 
                                        values 
                                               (
                                                   ?, 
                                                   ?, 
                                                   ?, 
                                                   ?, 
                                                   ?, 
                                                   ?,
                                                   ?, 
                                                   ?, 
                                                   ?, 
                                                   ?
                                               )
                              ");           
    // Prepare parameters
    // unpack
      
    $mSystemno       = $logData["mSystemno"];
    $mActivityCode   = $logData["mActivityCode"];
    $mLogDateTime    = $logData["mLogDateTime"];
    $mOperand        = $logData["mOperand"];
    $mScript         = $logData["mScript"];
    $mWebapp         = $logData["mWebapp"];
    $mErrCode        = $logData["mErrCode"];
    $mNarrative      = $logData["mNarrative"];
    $mIPAddress      = $logData["mIPAddress"];
    $mHTTPReferrer   = $logData["mHTTPReferrer"];
       
    // Bind parameters
    $stmt->bind_param
            (
                "iisississs",
              
            $mSystemno,
            $mActivityCode,
            $mLogDateTime,
            $mOperand,
            $mScript,
            $mWebapp,
            $mErrCode,
            $mNarrative,
            $mIPAddress,
            $mHTTPReferrer       
             );               
                
    //Execute it
    $stmt->execute();
    
    if (mysqli_error($mysqli) != FALSE)
        {
            $echoID     = 43434 ;
            $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
        }     
      
    // Close statement
    $stmt->close();
    
    //********************************************************
    // weed old records
    // plain statement
    
    // 20220924 1306 xxx resume do not use the config var $weedingDaysFrequent here, check the DB table weeding_days and use that value
        
    $deleteRecordsOlderThanDays = $weedingDaysFrequent;
        
    $query = " 
                DELETE 
                  FROM 
                       log 
                 WHERE 
                       mLogDateTime < DATE_SUB(NOW(), INTERVAL $deleteRecordsOlderThanDays DAY)
             ";        
      
    if($stmt = $mysqli->prepare($query))
          {
              //Execute it
              $stmt->execute();
            
              if (mysqli_error($mysqli) != FALSE)
                   {
                       $echoID     = 43435 ;
                       $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                   } 
              
              // Close statement
              $stmt->close();
  	      }
                           
    //********************************************************
    // Close connection
                
    $mysqli->close();
                
    //********************************************************
    // return
    // 20220922 1250
    
    // package the output
    // consistent var first, function specific var second
      
    $outputValue = array (
                             "commonFnsErrMsg"                          => $errMsg,
                             "commonFnsEchoString"                      => $echoString,
                             
                             "cSomeVar001"                              => $cSomeVar001,
                             "cSomeVar002"                              => $cSomeVar002
                         );
                          
    return $outputValue;
}

// *****************************************************************************************************************************
// bellaDateTime_to_SQLDateTime
// 20220815 1723

function bellaDateTime_to_SQLDateTime($inputValue)
{
    $dateValue =  substr($inputValue, 0, 4)."-".substr($inputValue, 4, 2)."-".substr($inputValue, 6, 2);
    
    $timeValue =  substr($inputValue, 9, 2).":".substr($inputValue, 11, 2).":00";
    
    $outputValue = $dateValue." ".$timeValue;

    return $outputValue;
}

// *****************************************************************************************************************************
// SQLDateTime_to_bellaDateTime
// 20191126 1031

function SQLDateTime_to_bellaDateTime($inputValue)
{
    // improved version for all lengths of datetime
    
    $inputValue  = strtotime($inputValue);
     
    if (date("s", $inputValue) == FALSE)
        {
            // no seconds
            if (date("Hi", $inputValue) == FALSE)
                {
                    // no hours and minutes
                    $outputValue = date("Ymd", $inputValue);
                }
            else
                {
                    // keep hours and minutes
                    // eliminate empty seconds
                    $outputValue = date("Ymd Hi", $inputValue);
                }
        }
    else
        {
            // full string with hours mins secs
            $outputValue = date("Ymd Hi s", $inputValue);
        }

    return $outputValue;
}

// *****************************************************************************************************************************
// convert_alphanumeric_to_numeric_keys
// 20150924 0820

function convert_alphanumeric_to_numeric_keys($inputValue)
    {
        // convert_numeric_to_alphanumeric_keys and convert_alphanumeric_to_numeric_keys must be used as a pair
    
        // there is no need to use this for arrays which already have alphanumeric keys
        // restore alphanumeric keys to numeric keys
        // basically remove the "a" which we put in earlier by removing all alpha characters
     
        $outputValue = array();
        
        foreach ($inputValue as $key => $value) 
           {
               $numericKey               = preg_replace("/[^0-9]/", '', $key);
   	           $outputValue[$numericKey] = $value;
           }            
       
        return $outputValue;
    }

// *****************************************************************************************************************************
// convert_numeric_to_alphanumeric_keys
// 20150924 0820

function convert_numeric_to_alphanumeric_keys($inputValue)
    {
        // convert_numeric_to_alphanumeric_keys and convert_alphanumeric_to_numeric_keys must be used as a pair
    
        // there is no need to use this for arrays which already have alphanumeric keys
        // make numeric keys into alphanumeric keys 
        // basically prefix an "a" now, then do array_multisort in the calling script, then do convert_alphanumeric_to_numeric_keys
     
        $outputValue = array();
            
        foreach ($inputValue as $key => $value) 
           {
               $alphaNumericKey               = "a".$key;
               $outputValue[$alphaNumericKey] = $value;
           }            
           
        return $outputValue;
    }


// *****************************************************************************************************************************
// pbkdf2
// 20100612 1758

// cloned from http://www.itnewb.com/v/Encrypting-Passwords-with-PHP-for-Storage-Using-the-RSA-PBKDF2-Standard 

function pbkdf2( $p, $s, $c, $kl, $a = 'sha256' ) 
    {
        $hl = strlen(hash($a, null, true)); # Hash length
        $kb = ceil($kl / $hl);              # Key blocks to compute
        $dk = '';                           # Derived key
    
        # Create key
        for ( $block = 1; $block <= $kb; $block ++ ) 
            {
    
                # Initial hash for this block
                $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);
    
                # Perform block iterations
                for ( $i = 1; $i < $c; $i ++ ) 
                    {
                        # XOR each iterate
                        
                        // https://www.techopedia.com/definition/3472/exclusive-or-xor
                        // Exclusive or (XOR, EOR or EXOR) is a logical operator which results true when either of the 
                        // operands are true (one is true and the other one is false) but both are not true and both 
                        // are not false. In logical condition making, the simple "or" is a bit ambiguous when both 
                        // operands are true.
                        
                        $ib ^= ($b = hash_hmac($a, $b, $p, true));
                        $dk .= $ib; # Append iterated block
                    }
            }
    
        # Return derived key of correct length
        return substr($dk, 0, $kl);
    }
   
// *****************************************************************************************************************************


?>