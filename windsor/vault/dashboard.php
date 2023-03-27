<?php

// dashboard.php 
// 20230321 1924
// KMB

// WINDSOR
// an add in for BALMORAL 

// Long Term Tracker

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

// $tableDataCellcounter is the main way that we track our progress through loops (for multiple charts)
// it ensures that we have unique data and unique identifiers within each TD (and esp for each canvas)
          
$tableDataCellcounter               = 0;

$getDataRecentPerformance           = 0;
$getDataRecentWeight                = 0;

$showGraphPerformance               = 0;
$showGraphWeight                    = 0;

$dashboardTableGraphPerformance     = "";
$dashboardTableGraphWeight          = "";

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

        $echoID      = 21501 ;
        $echoString  = $echoString."<P>$echoID fOriginator is $fOriginator ";
        	    
    }
else 
    {
        // first call to this script either by login process or by navbar button        
        $lErrCode            = 0 ; 
        $lNarrative          ="call to $cScript";
                
                        $echoID             = 21502 ;
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
                        
        $echoID      = 21503 ;
        //$echoString  = $echoString."<P>$echoID cActiveApp is $cActiveApp ";
        
        $getDataRecentPerformance   = 1;
        $getDataRecentWeight        = 1;
                        
    }
 
// *****************************************************************************************************************************
// 3333DATABASE WORK

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm

// **********************************************************************************
// getDataRecentWeight
// 20230325 2132

if ($getDataRecentWeight == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // find the most recent case of a mWeightno which has at least 10 of the same mKilograms
        // collate that data and build a chart
        
        // *******************************************************************************
        // stage 001
        
        // set variables
        
        $recentWeightQuota         = 10;
        
        $cWeightDate               = array();
        $cKilograms                = array();
        
        $tWeightDate               = array();
        $tKilograms                = array();
        
        // *******************************************************************************
        // stage 002
        // open connection
        
        $echoID    = 21504 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}  
                        
        // *******************************************************************************
        // stage 003
        
        // Declare query
        
        // get $recentWeightQuota records for that $mostCommonKilograms
        // to get the most recent we need to order by DESC
        // but then, for the chart, we need to reorder the 10 most recent by ASC
        // first, get them (by DESC)        
        
        $query = " 
                    SELECT 
                           mWeightDate,
                           mKilograms
                      FROM 
                           weight 
                     WHERE 
                           mSystemno = ?
                  ORDER BY 
                           mWeightDate 
                      DESC            
                 ";
      
        // *******************************************************************************
        // stage 004
      
        // Prepare statement
        if($stmt = $mysqli->prepare($query)) 
            {
                // Prepare parameters
                $mSystemno        = $cSystemno; 
              
                $stmt->bind_param
                    (
                        "i",
                        $mSystemno
                    );  
          
                //Execute it
                $stmt->execute();
     
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 21505 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                     }
 
                // Bind results 
                $stmt->bind_result
                    (
                        $oWeightDate,
                        $oKilograms
                    );
     
                // Fetch the value
                while($stmt->fetch())
                    {
                        // dates needed, not times, hence substring
                        
                        $cWeightDate[]     = substr(htmlspecialchars($oWeightDate),0,10);
                        $cKilograms[]      = htmlspecialchars($oKilograms);
                    }
 
                // Clear memory of results
                $stmt->free_result();                    
                  
                // Close statement
                $stmt->close();
            }        
      
        $echoID       = 21506 ;
     
        //$arrayString = print_r($cWeightDate, TRUE); $echoString = $echoString."<P>$echoID cWeightDate is $arrayString ";         
        //$arrayString = print_r($cKilograms, TRUE); $echoString = $echoString."<P>$echoID cKilograms is $arrayString ";         
        
        // *******************************************************************************
        // stage 005
        
        // limit the number of elements (using temp array, then assign that to current array)
        
        for ($loopCounter001=0; $loopCounter001<$recentWeightQuota; $loopCounter001++)
            {
                $tWeightDate[] = $cWeightDate[$loopCounter001];
                $tKilograms[]  = $cKilograms[$loopCounter001];
            }        
        
        $cWeightDate = $tWeightDate;
        $cKilograms  = $tKilograms;
        
        // *******************************************************************************
        // stage 006
        
        // reverse order of array elements
        
        $cWeightDate      = array_reverse($cWeightDate);
        $cKilograms       = array_reverse($cKilograms);
        
        $echoID       = 21507 ;

        //$arrayString = print_r($cWeightDate, TRUE); $echoString = $echoString."<P>$echoID cWeightDate is $arrayString ";         
        //$arrayString = print_r($cKilograms, TRUE); $echoString = $echoString."<P>$echoID cKilograms is $arrayString ";   
        
        // *******************************************************************************
        // stage 007
        
        if (count($cWeightDate) < $recentWeightQuota)
            {
                $echoID     = 21508 ;
                $echoString = $echoString."<P>$echoID Too few weight recordslogged. No chart!";
            }
        else
            {
                $showGraphWeight = 1;
            }
        
        //********************************************************
        // stage 00N
        // Close connection
                
        $mysqli->close();
    }

// **********************************************************************************
// getDataRecentPerformance
// 20230324 1956

if ($getDataRecentPerformance == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // find the most recent case of a mPursuitno which has at least 10 of the same mPursuitDistance
        // collate that data and build a chart
        
        // *******************************************************************************
        // stage 001
        
        // set variables
        
        $recentPerformanceQuota     = 10;
        $hiFrequencyPursuitDistance = array();
        $mostCommonPursuitDistance  = 0;
        
        $cPursuitDate               = array();
        $cPursuitDuration           = array();
        
        // *******************************************************************************
        // stage 002
        // open connection
        
        $echoID    = 21509 ;
        $mysqli    = new mysqli("$hostName", "$dbUser","$dbPass","$dbName"); 
        if ($mysqli->connect_error) {$echoString = $echoString."<P>$echoID connect_error";}  
        
        // *******************************************************************************
        // stage 003
        
        // Declare query
        
        $query = " 
                       SELECT 
                              mPursuitDistance, COUNT(*) as frequency
                         FROM 
                              performance 
                        WHERE
                              mSystemno = $cSystemno
                     GROUP BY 
                              mPursuitDistance 
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
  
                $echoID       = 21510 ;
     
                //$arrayString = print_r($row, TRUE); $echoString = $echoString."<P>$echoID row is $arrayString ";         
        
                if ($row['frequency'] > $recentPerformanceQuota)
                    {
                        $hiFrequencyPursuitDistance[]    = htmlspecialchars($mPursuitDistance);                 
                    }
            }         
         
         if (mysqli_error($mysqli) != FALSE)
            {
                $echoID     = 21511 ;
                $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
            }          
        
        $echoID       = 21512 ;
     
        //$arrayString = print_r($hiFrequencyPursuitDistance, TRUE); $echoString = $echoString."<P>$echoID hiFrequencyPursuitDistance is $arrayString ";         
        //$arrayString = print_r($cPursuitText, TRUE); $echoString = $echoString."<P>$echoID cPursuitText is $arrayString ";         
        
        // *******************************************************************************
        // stage 005
      
        // take element 0 as the most common mPursuitDistance
        
        if (count($hiFrequencyPursuitDistance) == FALSE)
            {
                $echoID     = 21513 ;
                $echoString = $echoString."<P>$echoID No distances logged. No chart!";
            }
        else
            {
                $mostCommonPursuitDistance = $hiFrequencyPursuitDistance[0];
                
                $showGraphPerformance = 1;
            }
        
        $echoID     = 21514 ;
        //$echoString = $echoString."<P>$echoID mostCommonPursuitDistance is $mostCommonPursuitDistance";
                        
        // *******************************************************************************
        // stage 006
        
        // Declare query
        
        // get $recentPerformanceQuota records for that $mostCommonPursuitDistance
        // to get the most recent we need to order by DESC
        // but then, for the chart, we need to reorder the 10 most recent by ASC
        // first, get them (by DESC)        
        
        $query = " 
                    SELECT 
                           mPursuitDate,
                           mPursuitDuration
                      FROM 
                           performance 
                     WHERE 
                           mSystemno = ?
                       AND
                           mPursuitDistance = ?
                  ORDER BY 
                           mPursuitDate 
                       DESC            
                     LIMIT 
                           0,$recentPerformanceQuota
                 ";
      
        // *******************************************************************************
        // stage 007
      
        // Prepare statement
        if($stmt = $mysqli->prepare($query)) 
            {
                // Prepare parameters
                $mSystemno        = $cSystemno; 
                $mPursuitDistance = $mostCommonPursuitDistance; 
              
                $stmt->bind_param
                    (
                        "is",
                        $mSystemno,
                        $mostCommonPursuitDistance
                    );  
          
                //Execute it
                $stmt->execute();
     
                if (mysqli_error($mysqli) != FALSE)
                     {
                         $echoID     = 21515 ;
                         $echoString = $echoString."<P>$echoID mysql error: ".mysqli_error($mysqli);
                     }
 
                // Bind results 
                $stmt->bind_result
                    (
                        $oPursuitDate,
                        $oPursuitDuration
                    );
     
                // Fetch the value
                while($stmt->fetch())
                    {
                        // dates needed, not times, hence substring
                        
                        $cPursuitDate[]          = substr(htmlspecialchars($oPursuitDate),0,10);
                        $cPursuitDuration[]      = htmlspecialchars($oPursuitDuration);
                    }
 
                // Clear memory of results
                $stmt->free_result();                    
                  
                // Close statement
                $stmt->close();
            }        
      
        $echoID       = 21516 ;
     
        //$arrayString = print_r($cPursuitDate, TRUE); $echoString = $echoString."<P>$echoID cPursuitDate is $arrayString ";         
        //$arrayString = print_r($cPursuitDuration, TRUE); $echoString = $echoString."<P>$echoID cPursuitDuration is $arrayString ";         
        
        // *******************************************************************************
        // stage 008
        
        // reverse order of array elements
        
        $cPursuitDate      = array_reverse($cPursuitDate);
        $cPursuitDuration  = array_reverse($cPursuitDuration);
      
        $echoID       = 21517 ;

        //$arrayString = print_r($cPursuitDate, TRUE); $echoString = $echoString."<P>$echoID cPursuitDate is $arrayString ";         
        //$arrayString = print_r($cPursuitDuration, TRUE); $echoString = $echoString."<P>$echoID cPursuitDuration is $arrayString ";         
        
        //********************************************************
        // stage 00N
        // Close connection
                
        $mysqli->close();
    }

// *****************************************************************************************************************************
// 4444PREPARE PHP HTML

// **********************************************************************************
// someRoutine
// YYYYMMDDhhmm

// **********************************************************************************
// showGraphWeight
// 20230325 2142

if ($showGraphWeight == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // CANVAS - see config file for standard param
        
        // turn the data into a graph
        // rebase duration to 100 on day 1 (element zero of cKilograms)
        // round the rebasing factor to 8 decimal places - trial and error showed 8 is best
        
        // the PHP array             - $cKilograms
        // will become the PHP array - $rKilograms - rebased to 100
        // will become a JSON object - $jRebasedValue (effectively it's $jKilograms)

        // *******************************************************************************
        // stage 001
        
        // set variables
        
        // the array plottableInvertedValues allows the graph to appear to start at the bottom left
        // whereas a JS canvas actually starts at the top left
        
        $rebasingDecimalPlaces      = 8; 
        $graphDecimalPrecision      = 3; 
        
        $rKilograms                 = array();
        
        $plottableValues            = array();
        $plottableInvertedValues    = array();
        
        $chartTitle = "Weight Trend";  // 20230325 2116 xxx resume add something here about pursuit text and distance
        
        // *******************************************************************************
        // stage 002
        
        // make the raw data usable by JS
        
        $cKilogramsElements = count($cKilograms);
     
        // now get a start and end date for the X axis, and how many divisions
        
        $dStartDate           = substr(SQLDateTime_to_bellaDateTime($cWeightDate[0]),0,9);
        $dEndDate             = substr(SQLDateTime_to_bellaDateTime($cWeightDate[$cKilogramsElements-1]),0,9);
        $graphCanvasDivisor   = $cKilogramsElements-1;
        
        $rebasingFactor = round(100/$cKilograms[0],$rebasingDecimalPlaces);
        
        // ****************************************************************
        // stage 003
        
        // rebase
        
        $cKilogramsElements = count($cKilograms);
     
        for ($cKilogramsCycle=0; $cKilogramsCycle<$cKilogramsElements ; $cKilogramsCycle++)
            {
                $rKilograms[] = round(($rebasingFactor * $cKilograms[$cKilogramsCycle]),$graphDecimalPrecision);
            }
        
        // ****************************************************************
        // stage 004
        
        // plottableValue
        
        unset ($plottableValues);
        unset ($plottableInvertedValues);
        
        $rKilogramsElements = count($rKilograms);
        
        for($rKilogramsCycle=0; $rKilogramsCycle<$rKilogramsElements; $rKilogramsCycle++)
           {
               // modify values so that they map on to our canvas easily 
               // assign modified rebased values to new array plottableValues 
  
               $plottableValue                 = $rKilograms[$rKilogramsCycle];
               
               // 100 is our first rebased value
               // varianceRebasedPlottable is the difference between 100 and the current value we are working with
               
               $varianceRebasedPlottable       = round($plottableValue - 100,$graphDecimalPrecision);
               $varianceScaledRebasedPlottable = round($varianceRebasedPlottable*(($graphCanvasHeight/2)/$heightAsPercentage),$graphDecimalPrecision);
               $rawCanvasValue                 = round($plottableValue+$varianceScaledRebasedPlottable,$graphDecimalPrecision);    
               $plottableCanvasValue           = round($rawCanvasValue*(($graphCanvasHeight/2)/100),$graphDecimalPrecision);
           
               $plottableValues[] = $plottableCanvasValue; 
  
               $plottableInvertedValues[] = round($graphCanvasHeight - $plottableCanvasValue,$graphDecimalPrecision);
  
               //$plottableValues[] = $rawCanvasValue; 
  
               $echoID = 21518 ;
  
               //$echoString = $echoString."<P>$echoID plottableCanvasValue is $plottableCanvasValue";
               //$echoString = $echoString."<P>$echoID rawCanvasValue is $rawCanvasValue";
           }  
        
        $jRebasedValue = json_encode($plottableInvertedValues); 
        
        $echoID       = 21519 ;

        //$echoString = $echoString."<P>$echoID sKilograms[0] is ".$sKilograms[0];        
        //$echoString = $echoString."<P>$echoID rebasingFactor is $rebasingFactor ";        
        //$arrayString = print_r($jRebasedValue, TRUE); $echoString = $echoString."<P>$echoID jRebasedValue is $arrayString ";         
        //$arrayString = print_r($rKilograms, TRUE); $echoString = $echoString."<P>$echoID rKilograms is $arrayString ";         
        //$arrayString = print_r($plottableValues, TRUE); $echoString = $echoString."<P>$echoID plottableValues is $arrayString ";         
        //$arrayString = print_r($plottableInvertedValues, TRUE); $echoString = $echoString."<P>$echoID plottableInvertedValues is $arrayString ";         
        //$arrayString = print_r($jRebasedValue, TRUE); $echoString = $echoString."<P>$echoID jRebasedValue is $arrayString ";         
        
        // *******************************************************************************
        // stage 005
        
        // regression analysis 
        
        // establish a trend line - the regression analysis is in commonfns - code has been hand rolled
        // pack required variables for the function 
  
        $canvasParameters  = array(
                                    $graphCanvasHeight,
                                    $graphCanvasWidth, 
                                    $heightAsPercentage,
                                  );
  
        $regressionPackage = array(
                                    $canvasParameters,
                                    $plottableInvertedValues
                                  );
      
        $firstAndLastXAndY = regression_analysis($regressionPackage);
  
        // unpack the variables returned by the function
  
        $firstY   = $firstAndLastXAndY[0];
        $lastY    = $firstAndLastXAndY[1];
        $firstX   = $firstAndLastXAndY[2];
        $lastX    = $firstAndLastXAndY[3];        
        
        $echoID       = 21520 ;

        //$arrayString = print_r($firstAndLastXAndY, TRUE); $echoString = $echoString."<P>$echoID firstAndLastXAndY is $arrayString ";         
        
        // *******************************************************************************
        // stage 005
        
        // show a canvas containing the graph
      
        // use a <style> to label the Y axis vertically            
              
        $currentCanvas = "myCanvas004";
              
        $dashboardTableGraphWeight = "
              
        <table border=0 cellpadding=4 cellspacing=4>
         <tr>
          <td  colspan=4>
          <center>
          <b>
          $chartTitle
          </b>
          </center>
          </td>
         </tr>
          <tr>
            <td>
              
            <style>
            div.yAxisLabel 
            {
            width: 15px;
            height: 15px;
            -mx-transform:     scale(0.8) rotate(-90deg) translate(-40px, -4px); /* IE 9*/
            -webkit-transform: scale(0.8) rotate(-90deg) translate(-40px, -4px); /* Safari 3-8 */
            transform:         scale(0.8) rotate(-90deg) translate(-40px, -4px);
            }
            </style>
              
            <div class='yAxisLabel'>
            Rebased&nbsp;Value&nbsp;Kilograms
            </div>
            </td>
            <td colspan=3>
  
            <canvas id='$currentCanvas' width='$graphCanvasWidth' height='$graphCanvasHeight'
            style='border:1px solid #d3d3d3;'>
            Your browser does not support the Canvas element
            </canvas>
                 
            <script>
     
            var jRebasedValue           = $jRebasedValue;
            
            var currentCanvas           = '$currentCanvas';
     
            var xValueNow               = 0;
            var yPlotNow                = 0;
            var xPlotPrior              = 0;
            var yPlotPrior              = 0;
     
            var firstY                  = $firstY;
            var lastY                   = $lastY;
            var firstX                  = $firstX;
            var lastX                   = $lastX;
     
            var canvas2                 = document.getElementById(currentCanvas);
            var chartLine               = canvas2.getContext('2d');
     
            var graphCanvasWidth        = $graphCanvasWidth;
            var graphCanvasHeight       = $graphCanvasHeight;
            var heightAsPercentage      = $heightAsPercentage;
            var graphCanvasDivisor      = $graphCanvasDivisor;
  
            for (xValueNow = 0; xValueNow < jRebasedValue.length; xValueNow++)
                {
                    // xValue is integer from 0 to N depending on length of array
                    // xValue is needed in order to cycle through the array
                    
                    // assume that we have 10 values and 500px canvas width
                    
                    // xValue has to be 50 times bigger for plotting on graph
                
                    // Adjust the scale factor according to the amount of data and the size of your canvas
  
                    // We have ten X values, first plotted on the y axis and the last plotted on the
                    // far right of the canvas. That means that our canvas has 9 divisions not 10 and 
                    // intuitively we would put a 10 in the next calculation, but actually we need a 9 
  
                    xPlotNow = xValueNow*(graphCanvasWidth/graphCanvasDivisor);
  
                    yPlotNow = jRebasedValue[xValueNow];
  
                    chartLine.lineWidth = 1;
                    chartLine.beginPath();
                    chartLine.strokeStyle = 'green';
                    
                    if (xPlotNow == 0)
                 {
                     // first plot
                     // do not use 0,0 but use NOW values, because we don't want a line from 0,0 
                     // to the first X,Y value, as that suggests that from day 0 to day 1 the 
                     // share value leapt from nothing to something significant
                     // and we dont want a line on our canvas depicting this 
      
                     chartLine.moveTo(xPlotNow,yPlotNow);
                     chartLine.lineTo(xPlotNow,yPlotNow);
                 }                  
                    else
                 {
                     // we're not on the first plot, therefore normal plot
      
                     chartLine.moveTo(xPlotPrior,yPlotPrior);
                     chartLine.lineTo(xPlotNow,yPlotNow);
  
                 }                  
                      
                    chartLine.stroke();
                    
                    // get ready for the next invocation of this loop by
                    // noting what we plotted 'last time'
  
                    xPlotPrior = xPlotNow;
                    yPlotPrior = yPlotNow;
                }
  
            // add trend line to graph
  
            chartLine.strokeStyle = '#cc0000';
  
            chartLine.beginPath(); chartLine.moveTo(firstX,firstY);
            chartLine.lineTo(lastX,lastY); chartLine.stroke();
  
            // add deciles
            
            // 20230325 2102 tried to use varaibles here instead of hard coded numbers like (600,100)
            // it borked the canvas - no images at all - hence leaving this as is
  
            chartLine.lineWidth = 0.33;
            chartLine.strokeStyle = 'gray';
  
            chartLine.beginPath(); chartLine.moveTo(0,100); chartLine.lineTo(600,100); chartLine.stroke();
            chartLine.beginPath(); chartLine.moveTo(0,200); chartLine.lineTo(600,200); chartLine.stroke();
            chartLine.beginPath(); chartLine.moveTo(0,300); chartLine.lineTo(600,300); chartLine.stroke();
  
            // add labels
  
            chartLine.font = '10px Arial'
  
            chartLine.textBaseline = 'top';    chartLine.fillText('115%', 5, 2);
            chartLine.textBaseline = 'top';    chartLine.fillText('107%', 5, 102);
            chartLine.textBaseline = 'top';    chartLine.fillText('100%', 5, 202);
            chartLine.textBaseline = 'top';    chartLine.fillText('93%',  5, 302);
            chartLine.textBaseline = 'bottom'; chartLine.fillText('85%',  5, 398);
            
            // end of canvas
  
            </script>
            
            </td> 
           </tr>
           
           <tr>
             <td>&nbsp;</td>
             <td style='text-align:left'  ><font size=1>From:<font size=3> $dStartDate   </font></td>
             <td>&nbsp;</td>
             <td style='text-align:right' ><font size=1>To:<font size=3> $dEndDate     </font></td>
           </tr>
         </table>    
         
         ";
    }
        
    

// **********************************************************************************
// showGraphPerformance
// 20230325 0418

if ($showGraphPerformance == 1)
    {
        // *******************************************************************************
        // stage 000
        
        // preamble
        
        // CANVAS - see config file for standard param
        
        // turn the data into a graph
        // rebase duration to 100 on day 1 (element zero of cPursuitDuration)
        // round the rebasing factor to 8 decimal places - trial and error showed 8 is best
        
        // the PHP array             - $cPursuitDuration - in SQL time
        // will become the PHP array - $sPursuitDuration - in seconds
        // will become the PHP array - $rPursuitDuration - rebased to 100
        // will become a JSON object - $jRebasedValue (effectively it's $jPursuitDuration)

        // *******************************************************************************
        // stage 001
        
        // set variables
        
        // the array plottableInvertedValues allows the graph to appear to start at the bottom left
        // whereas a JS canvas actually starts at the top left
        
        $rebasingDecimalPlaces      = 8; 
        $graphDecimalPrecision      = 3; 
        
        $sPursuitDuration           = array();
        $rPursuitDuration           = array();
        
        $plottableValues            = array();
        $plottableInvertedValues    = array();
        
        $chartTitle = "Recent Performance";  // 20230325 2116 xxx resume add something here about pursuit text and distance
        
        // *******************************************************************************
        // stage 002
        
        // make the raw data usable by JS
        
        $cPursuitDurationElements = count($cPursuitDuration);
     
        // now get a start and end date for the X axis, and how many divisions
        
        $dStartDate           = substr(SQLDateTime_to_bellaDateTime($cPursuitDate[0]),0,9);
        $dEndDate             = substr(SQLDateTime_to_bellaDateTime($cPursuitDate[$cPursuitDurationElements-1]),0,9);
        $graphCanvasDivisor   = $cPursuitDurationElements-1;
        
        for ($cPursuitDurationCycle=0; $cPursuitDurationCycle<$cPursuitDurationElements ; $cPursuitDurationCycle++)
            {
                $echoID       = 21521 ;
                
                //$echoString = $echoString."<P>$echoID cPursuitDuration is $cPursuitDuration[$cPursuitDurationCycle]";
                //$echoString = $echoString."<P>$echoID SQLTime_to_seconds is ".SQLTime_to_seconds($cPursuitDuration[$cPursuitDurationCycle]);
                
                $sPursuitDuration[] = SQLTime_to_seconds($cPursuitDuration[$cPursuitDurationCycle]);
            }        
        
        $rebasingFactor = round(100/$sPursuitDuration[0],$rebasingDecimalPlaces);
        
        // ****************************************************************
        // stage 003
        
        // rebase
        
        $sPursuitDurationElements = count($sPursuitDuration);
     
        for ($sPursuitDurationCycle=0; $sPursuitDurationCycle<$sPursuitDurationElements ; $sPursuitDurationCycle++)
            {
                $rPursuitDuration[] = round(($rebasingFactor * $sPursuitDuration[$sPursuitDurationCycle]),$graphDecimalPrecision);
            }
        
        // ****************************************************************
        // stage 004
        
        // plottableValue
        
        unset ($plottableValues);
        unset ($plottableInvertedValues);
        
        $rPursuitDurationElements = count($rPursuitDuration);
        
        for($rPursuitDurationCycle=0; $rPursuitDurationCycle<$rPursuitDurationElements; $rPursuitDurationCycle++)
           {
               // modify values so that they map on to our canvas easily 
               // assign modified rebased values to new array plottableValues 
  
               $plottableValue                 = $rPursuitDuration[$rPursuitDurationCycle];
               
               // 100 is our first rebased value
               // varianceRebasedPlottable is the difference between 100 and the current value we are working with
               
               $varianceRebasedPlottable       = round($plottableValue - 100,$graphDecimalPrecision);
               $varianceScaledRebasedPlottable = round($varianceRebasedPlottable*(($graphCanvasHeight/2)/$heightAsPercentage),$graphDecimalPrecision);
               $rawCanvasValue                 = round($plottableValue+$varianceScaledRebasedPlottable,$graphDecimalPrecision);    
               $plottableCanvasValue           = round($rawCanvasValue*(($graphCanvasHeight/2)/100),$graphDecimalPrecision);
           
               $plottableValues[] = $plottableCanvasValue; 
  
               $plottableInvertedValues[] = round($graphCanvasHeight - $plottableCanvasValue,$graphDecimalPrecision);
  
               //$plottableValues[] = $rawCanvasValue; 
  
               $echoID = 21522 ;
  
               //$echoString = $echoString."<P>$echoID plottableCanvasValue is $plottableCanvasValue";
               //$echoString = $echoString."<P>$echoID rawCanvasValue is $rawCanvasValue";
           }  
        
        $jRebasedValue = json_encode($plottableInvertedValues); 
        
        $echoID       = 21523 ;

        //$echoString = $echoString."<P>$echoID sPursuitDuration[0] is ".$sPursuitDuration[0];        
        //$echoString = $echoString."<P>$echoID rebasingFactor is $rebasingFactor ";        
        //$arrayString = print_r($jRebasedValue, TRUE); $echoString = $echoString."<P>$echoID jRebasedValue is $arrayString ";         
        //$arrayString = print_r($rPursuitDuration, TRUE); $echoString = $echoString."<P>$echoID rPursuitDuration is $arrayString ";         
        //$arrayString = print_r($plottableValues, TRUE); $echoString = $echoString."<P>$echoID plottableValues is $arrayString ";         
        //$arrayString = print_r($plottableInvertedValues, TRUE); $echoString = $echoString."<P>$echoID plottableInvertedValues is $arrayString ";         
        //$arrayString = print_r($jRebasedValue, TRUE); $echoString = $echoString."<P>$echoID jRebasedValue is $arrayString ";         
        
        // *******************************************************************************
        // stage 005
        
        // regression analysis 
        
        // establish a trend line - the regression analysis is in commonfns - code has been hand rolled
        // pack required variables for the function 
  
        $canvasParameters  = array(
                                    $graphCanvasHeight,
                                    $graphCanvasWidth, 
                                    $heightAsPercentage,
                                  );
  
        $regressionPackage = array(
                                    $canvasParameters,
                                    $plottableInvertedValues
                                  );
      
        $firstAndLastXAndY = regression_analysis($regressionPackage);
  
        // unpack the variables returned by the function
  
        $firstY   = $firstAndLastXAndY[0];
        $lastY    = $firstAndLastXAndY[1];
        $firstX   = $firstAndLastXAndY[2];
        $lastX    = $firstAndLastXAndY[3];        
        
        $echoID       = 21524 ;

        //$arrayString = print_r($firstAndLastXAndY, TRUE); $echoString = $echoString."<P>$echoID firstAndLastXAndY is $arrayString ";         
        
        // *******************************************************************************
        // stage 005
        
        // show a canvas containing the graph
      
        // use a <style> to label the Y axis vertically            
              
        $currentCanvas = "myCanvas003";
              
        $dashboardTableGraphPerformance = "
              
        <table border=0 cellpadding=4 cellspacing=4>
         <tr>
          <td  colspan=4>
          <center>
          <b>
          $chartTitle
          </b>
          </center>
          </td>
         </tr>
          <tr>
            <td>
              
            <style>
            div.yAxisLabel 
            {
            width: 15px;
            height: 15px;
            -mx-transform:     scale(0.8) rotate(-90deg) translate(-40px, -4px); /* IE 9*/
            -webkit-transform: scale(0.8) rotate(-90deg) translate(-40px, -4px); /* Safari 3-8 */
            transform:         scale(0.8) rotate(-90deg) translate(-40px, -4px);
            }
            </style>
              
            <div class='yAxisLabel'>
            Rebased&nbsp;Value&nbsp;Time
            </div>
            </td>
            <td colspan=3>
  
            <canvas id='$currentCanvas' width='$graphCanvasWidth' height='$graphCanvasHeight'
            style='border:1px solid #d3d3d3;'>
            Your browser does not support the Canvas element
            </canvas>
                 
            <script>
     
            var jRebasedValue           = $jRebasedValue;
            
            var currentCanvas           = '$currentCanvas';
     
            var xValueNow               = 0;
            var yPlotNow                = 0;
            var xPlotPrior              = 0;
            var yPlotPrior              = 0;
     
            var firstY                  = $firstY;
            var lastY                   = $lastY;
            var firstX                  = $firstX;
            var lastX                   = $lastX;
     
            var canvas2                 = document.getElementById(currentCanvas);
            var chartLine               = canvas2.getContext('2d');
     
            var graphCanvasWidth        = $graphCanvasWidth;
            var graphCanvasHeight       = $graphCanvasHeight;
            var heightAsPercentage      = $heightAsPercentage;
            var graphCanvasDivisor      = $graphCanvasDivisor;
  
            for (xValueNow = 0; xValueNow < jRebasedValue.length; xValueNow++)
                {
                    // xValue is integer from 0 to N depending on length of array
                    // xValue is needed in order to cycle through the array
                    
                    // assume that we have 10 values and 500px canvas width
                    
                    // xValue has to be 50 times bigger for plotting on graph
                
                    // Adjust the scale factor according to the amount of data and the size of your canvas
  
                    // We have ten X values, first plotted on the y axis and the last plotted on the
                    // far right of the canvas. That means that our canvas has 9 divisions not 10 and 
                    // intuitively we would put a 10 in the next calculation, but actually we need a 9 
                    
                    xPlotNow = xValueNow*(graphCanvasWidth/graphCanvasDivisor);
  
                    yPlotNow = jRebasedValue[xValueNow];
  
                    chartLine.lineWidth = 1;
                    chartLine.beginPath();
                    chartLine.strokeStyle = 'green';
                    
                    if (xPlotNow == 0)
                 {
                     // first plot
                     // do not use 0,0 but use NOW values, because we don't want a line from 0,0 
                     // to the first X,Y value, as that suggests that from day 0 to day 1 the 
                     // share value leapt from nothing to something significant
                     // and we dont want a line on our canvas depicting this 
      
                     chartLine.moveTo(xPlotNow,yPlotNow);
                     chartLine.lineTo(xPlotNow,yPlotNow);
                 }                  
                    else
                 {
                     // we're not on the first plot, therefore normal plot
      
                     chartLine.moveTo(xPlotPrior,yPlotPrior);
                     chartLine.lineTo(xPlotNow,yPlotNow);
  
                 }                  
                      
                    chartLine.stroke();
                    
                    // get ready for the next invocation of this loop by
                    // noting what we plotted 'last time'
  
                    xPlotPrior = xPlotNow;
                    yPlotPrior = yPlotNow;
                }
  
            // add trend line to graph
  
            chartLine.strokeStyle = '#cc0000';
  
            chartLine.beginPath(); chartLine.moveTo(firstX,firstY);
            chartLine.lineTo(lastX,lastY); chartLine.stroke();
  
            // add deciles
            
            // 20230325 2102 tried to use varaibles here instead of hard coded numbers like (600,100)
            // it borked the canvas - no images at all - hence leaving this as is
  
            chartLine.lineWidth = 0.33;
            chartLine.strokeStyle = 'gray';
  
            chartLine.beginPath(); chartLine.moveTo(0,100); chartLine.lineTo(600,100); chartLine.stroke();
            chartLine.beginPath(); chartLine.moveTo(0,200); chartLine.lineTo(600,200); chartLine.stroke();
            chartLine.beginPath(); chartLine.moveTo(0,300); chartLine.lineTo(600,300); chartLine.stroke();
  
            // add labels
  
            chartLine.font = '10px Arial'
  
            chartLine.textBaseline = 'top';    chartLine.fillText('115%', 5, 2);
            chartLine.textBaseline = 'top';    chartLine.fillText('107%', 5, 102);
            chartLine.textBaseline = 'top';    chartLine.fillText('100%', 5, 202);
            chartLine.textBaseline = 'top';    chartLine.fillText('93%',  5, 302);
            chartLine.textBaseline = 'bottom'; chartLine.fillText('85%',  5, 398);
            
            // end of canvas
  
            </script>
            
            </td> 
           </tr>
           
           <tr>
             <td>&nbsp;</td>
             <td style='text-align:left'  ><font size=1>From:<font size=3> $dStartDate   </font></td>
             <td>&nbsp;</td>
             <td style='text-align:right' ><font size=1>To:<font size=3> $dEndDate     </font></td>
           </tr>
         </table>    
         
         ";
    }
        
// *****************************************************************************************************************************
// 5555ECHO HTML

require ('../header.php');

   // 20230325 2231 xxx resume add tabular data above charts

echo "
          <center>
          <h2>
          The Latest Data
          </h2>
          </center>
          
          <table border=0 cellpadding=4 cellspacing=4 width=100%>
           <tr>
            <td style='text-align: center; vertical-align: top; min-width:1px'>
            $dashboardTableGraphPerformance
            </td>
            <td style='text-align: center; vertical-align: top; min-width:1px'>
            $dashboardTableGraphWeight
            </td>
           </tr>
          </table>

";

require ('../footer.php');

?>