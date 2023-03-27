<?php
 
// 20221209 2021
// index.php
// KMB
  
//$title          ="404 Error";
$title          ="Long Term Tracker V001";
$domain         = "https://www.longtermtracker.com";

$carriageReturn = "\r\n";
$htmlString     = '<!DOCTYPE html>'.$carriageReturn.'<html lang="en">';

$imageFile      = $domain."/404.jpg";
$homePage       = $domain."/index.php";
 
echo "$htmlString";
 
echo"
 
<head>
<title>
$title
</title>
</head>

<body>
<center>

<P>&nbsp;
<P>&nbsp;
<h2>
$domain
</h2>
<P>&nbsp;

</center>
</body>
 
</html>";

?> 