<?php

    $getthis = file_get_contents('php://input', true);
    $stuff = json_decode($getthis, true);
    
    $url = 'https://web.njit.edu/~mm693/test2.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $getthis);
    $something = curl_exec($curl);
    curl_close($curl);
    
    $stuff["Action"] = 'TotalScore';
    $getpoints = json_encode($stuff);
    
    $url = 'https://web.njit.edu/~mm693/test2.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $getpoints);
    $something = curl_exec($curl);
    curl_close($curl);

    $allPoints = json_decode($something, true);

    $timesOneHundred = 100;

    $studentsPoints = array_sum($allPoints[0]);

    $worthEachQuestion = array_sum($allPoints[1]);

    $studentScore = $studentsPoints/$worthEachQuestion;

    $studentsPercentage = $studentScore * $timesOneHundred;

    $studentsPercentage = ceil($studentsPercentage);

    echo $studentsPercentage;

?>