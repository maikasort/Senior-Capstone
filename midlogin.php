<?php

/*$user = $_POST['username'];
$pass = $_POST['password'];*/
//$role = 'student';

//$data = array( 'role' => $role);

//$cred = json_decode(file_get_contents('php://input'), true);

$var = file_get_contents('php://input');

$url = 'https://web.njit.edu/~mm693/test.php';

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $var);
$results = curl_exec($curl);

$data = array ('role' => $results);

$info = json_encode($data);

echo $info;

curl_close($curl);

?>
