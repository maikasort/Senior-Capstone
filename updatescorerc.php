<?php

$getFront = file_get_contents('php://input', true);
$fromFront = json_decode($getFront, true);



$studentsPoints = array_sum($fromFront['CorrectionsPointsEarned']);


$fromFront['Score']=$studentsPoints;


$sendUpdate = json_encode($fromFront);


$url = 'https://web.njit.edu/~mm693/test2.php';
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $sendUpdate);
$stuResults = curl_exec($curl);

echo $stuResults;

curl_close($curl);


?>