<?php

$var = file_get_contents('php://input');
$info = json_decode($var, true);
$click = $info["Action"];

switch($click){

case "CreateQ":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;
	
case "CreateT":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "AddQ":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "LoadT":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "TakeT":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "PopulateT":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "ReviewT":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "AddC":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "AddCorr":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "ShowT":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "SubmitT":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "AutoGrade":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "ShowS":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "SendAnswer":
	$url = 'https://web.njit.edu/~mm693/test2.php';
break;

case "UpdateT":
    $url = 'https://web.njit.edu/~mm693/test2.php';
break;

default:

echo("Some Error");

}

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $var);
$results = curl_exec($curl);
curl_close($curl);
echo $results;

?>






