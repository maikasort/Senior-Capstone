<?php
 /* All Material © Sonfo-Maika Diomande | New Jersey Institute of Technology June 2020 */
    $var = file_get_contents('php://input', true);
    $info = json_decode($var, true);
    
    $answer = $info['Answers']; 
    $SID = $info['SID'];
    $QIDs = $info['QIDs'];
    $constraints = $info['Constraint'];
    $score = $info['Qpoints'];
    $constraint_found = strcmp($constraints, "none") != 0;
    
    //$grade = 60;//dummy data to see
    
    $url = 'https://web.njit.edu/~mm693/test2.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $var);
    $need = curl_exec($curl);
    curl_close($curl);
    
    $info["Action"] = 'AutoGrade';
    $dbdata = json_encode($info);
    echo $dbdata;
    
    $url = 'https://web.njit.edu/~mm693/test2.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $dbdata);
    $need = curl_exec($curl);
    curl_close($curl);

    $db = json_decode($need, true);
    //echo $db;
    $func_name = $db['FunctionName'];
    $cinput = json_decode($db['TestCases']);
    $coutput = json_decode($db['TestCaseOutputs']);
    $score = $db['Qpoints'];

    $eachdeduction = 0.25;
    $casespointsoff = $eachdeduction/count($cinput);
    
    //checking for colon here using Regex
    $findColon = preg_match('/(def)\s\w+(\'?|\"?\w?|\W?\'?|\"?,?\w+)*\):/', $answer);
         if($findColon == 1){
           $comment = "Colon Was Found";
           $correct[] = $comment; // this $comment you will see it throughout the code 
                                  //and that is because I'm making an array of corrections to send
        }
        else{
            $comment = "Missing Colon";
            $addIn = ":";
            $strpos1 = strpos($answer, ")");
            $answer = substr_replace($answer, $addIn, $strpos1 + 1, 0);
            $score -= $eachdeduction;
            $correct[] = $comment;
        }
    //Here I will check the function name and handle as needed
        $firstSet = strtok($answer, '(');
        $secondSet = substr($firstSet, 4);
    
       if(strcmp($secondSet, $func_name)== 0){
            $comment = "The Function Name was correct ";
            $correct[] = $comment;
        }
       else{
        $comment = "The Function Name \"$secondSet\" was incorrect"; 
        $front_pos = strpos($answer, $secondSet);
        $back_pos = strpos($answer, '(');
        $some_calc = $back_pos - $front_pos;
        $answer = substr_replace($answer, $func_name, $front_pos, $some_calc);
        //the corrected function name
        $score -= $eachdeduction;
        $correct[] = $comment;  
       }
    //Here I will check for constraints if I find either or both it is handled as needed   
     if($constraint_found == 1){
        if(strstr($answer, "print")){
          $comment = "constraint print was found";
          $correct[] = $comment;
        }
        elseif(strstr($answer, "for")){
          $comment = "constraint for was found";
          $correct[] = $comment;
        }
        elseif(strstr($answer, "print") || strstr($answer, "for")){
          $comment = "constraints were found";
          $correct[] = $comment;
        }
        else{
          $comment = "constraint was not found";
          $score -= $eachdeduction;
          $correct[] = $comment;
          }
        }
    //Boolean string compare to check 1. constraint for print is in and 2. exec the python code as needed     
     if($constraint_found == 1){        
       for($i = 0; $i < count($cinput); $i++){
        $cases = $cinput[$i];
        $outputs = $coutput[$i];
        
        $print = "print";
        $findprint = strpos($answer, $print);
        if($findprint == false){
            $code = $answer."\nprint(". $func_name."(".$cases."))"."\n";
            
            $Output = $outputs;
            
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Expected output matched test case: ($cases)";
            }
            else{
            $comment = "Expected output did not match test case: ($cases)";
            $score -= $casespointsoff;
           }
          }
          else{
            $code = $answer."\n". $func_name."(".$cases.")"."\n";
            $Output = $outputcases;
            
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Expected output matched test case: ($cases)";
            
          }
            else{
            $comment = "Expected output did not match test case: ($cases)";
            $score -= $casespointsoff;
          }
        }
            $testcases[] = $comment;
       }
      }
     // If constraint is toggled off the below code will be handled as needed here
     else{
          for($i = 0; $i < count($cinput); $i++){
          $cases = $cinput[$i];
          $outputs = $coutput[$i];
        
          $print = "print";
          $findprint = strpos($answer, $print);
          if($findprint == false){
            $code = $answer."\nprint(". $func_name."(".$cases."))"."\n";
            
            $Output = $outputs;
            
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Expected output matched test case: ($cases)";
            }
            else{
            $comment = "Expected output did not match test case: ($cases)";
            $score -= $casespointsoff;
           }
          }
          else{
            $code = $answer."\n". $func_name."(".$cases.")"."\n";
            $Output = $outputcases;
            
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Illegal Use Of 'print' - Points were Deducted: ($cases)";
            $score -= $casespointsoff;
          }
            else{
            $comment = "Accepted Key Word For The Following: ($cases)";
          }
        }
            $testcases[] = $comment;
       }
      }
    // If by chance the student gets a grade less than 0 then it will handle it and just give her/him a 0 
    if($score < 0){
        $score = 0;
    }
  // this is just combining all the corrections my autograder did into one variable to send to back    
  $letsee = array_merge($correct, $testcases);
  $corrections = implode("\n", $letsee);

  // checking things are going right---> this is for me on my end --->checking to see it works correctly
$pp = fopen("hi.txt", "w") or die ("Unable to open file");
        fwrite($pp, $corrections);
        fclose($pp);

// send to back
$toDB = array(
   "SID" => $SID,
   "QIDs" => $QIDs,
   "Score" => $grade,
   "Corrections" => $corrections,
   "Action" => 'GetScore'
   
);

$resultsDB = json_encode($toDB);

$url = 'https://web.njit.edu/~mm693/test2.php';
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $resultsDB);
$results = curl_exec($curl);

echo $results;

curl_close($curl);

?>
