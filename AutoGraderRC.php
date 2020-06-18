<?php
 /* All Material © Sonfo-Maika Diomande | New Jersey Institute of Technology June 2020 */

    $var = file_get_contents('php://input', true);
    $info = json_decode($var, true);
    
    $answer = $info['Answers']; 
    $SID = $info['SID'];
    $QIDs = $info['QIDs'];
    $TestName = $info['TestName'];
   // echo $TestName;
    
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
    //echo $dbdata;
    
    $url = 'https://web.njit.edu/~mm693/test2.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $dbdata);
    $need = curl_exec($curl);
    curl_close($curl);

    $db = json_decode($need, true);
    //echo json_encode($db);
    //echo $need;
    $func_name = $db['FunctionName'];
   // echo $func_name;
    $cinput = (array) $db['TestCases'];
    //$cin = json_encode($cinput);
   //echo $cin;
    $coutput = (array) $db['TestCaseOutputs'];
    //$cout = json_encode($coutput);
   //echo $cout;
    $constraints = $db['Constraint'];
    $constraint_found = strcmp($constraints, "none") != 0;
    $score = $db['Qpoints'];
    $eachdeduction = round(0.25 * $score);
    $casespointsoff = round((0.25/count($cinput))*$score);
    $totalpoints = $score;
    
    if($constraint_found == 0){
        $eachdeduction = floor(0.333 * $score);
        //$casespointsoff = round(((0.333/count($cinput))*$score)/5, 1)*5;
        $casespointsoff = ceil((0.333/count($cinput))*$score);
    }
    else{
        $eachdeduction = floor(0.25 * $score);
        //$casespointsoff = round(((0.25/count($cinput))*$score)/5, 1)*5;
        $casespointsoff = ceil((0.25/count($cinput))*$score);
    }
   
    //checking for colon here using Regex
    $findColon = preg_match('/(def)\s\w+(\'?|\"?\w?|\W?\'?|\"?,?\w+)*\):/', $answer);
         if($findColon == 1){
           $comment = "Colon Was Found";
           $correct[] = $comment;
           $check = "true"; 
           $checkarr[] = $check; 
           $deduct[] = $eachdeduction;                                  
        }
        else{
            $comment = "Missing Colon";
            $addIn = ":";
            $strpos1 = strpos($answer, ")");
            $answer = substr_replace($answer, $addIn, $strpos1 + 1, 0);
            $totalpoints -= $eachdeduction;
            $correct[] = $comment;
            $check = "false";
            $checkarr[] = $check;
            $deduct[] = $eachdeduction; 
        }
    //Here I will check the function name and handle as needed
        $firstSet = strtok($answer, '(');
        $secondSet = substr($firstSet, 4);
    
       if(strcmp($secondSet, $func_name)== 0){
            $comment = "The Function Name: $func_name , was correct ";
            $correct[] = $comment;
            $check = "true";
            $checkarr[] = $check; 
            $deduct[] = $eachdeduction;  
        }
       else{
        $comment = "The Function Name: $secondSet , was incorrect"; 
        $front_pos = strpos($answer, $secondSet);
        $back_pos = strpos($answer, '(');
        $some_calc = $back_pos - $front_pos;
        $answer = substr_replace($answer, $func_name, $front_pos, $some_calc);
        //the corrected function name
        $totalpoints -= $eachdeduction;
        $correct[] = $comment;  
        $check = "false";
        $checkarr[] = $check;  
        $deduct[] = $eachdeduction;
       }
    //Here I will check for constraints if I find either or both it is handled as needed   
     if($constraint_found == 1){
        if(strstr($answer, "print")){
          $comment = "constraint: print was found";
          $correct[] = $comment;
          $check = "true";
          $checkarr[] = $check; 
          $deduct[] = $eachdeduction;  
        }
        elseif(strstr($answer, "for")){
          $comment = "constraint: for was found";
          $correct[] = $comment;
          $check = "true";
          $checkarr[] = $check; 
          $deduct[] = $eachdeduction; 
        }
        else{
          if(strcmp($constraints, "for")==0){
            $comment = "constraint: for was not found";
            $totalpoints -= $eachdeduction;
          }
          else{
            $comment = "constraint: print was not found";
            $totalpoints -= $eachdeduction;
          }
          $correct[] = $comment;
          $check = "false";
          $checkarr[] = $check;  
          $deduct[] = $eachdeduction;
          }
        }
    //Boolean string compare to check 1. constraint for print is in and 2. exec the python code as needed     
     if($constraint_found == 1){        
       for($i = 0; $i < count($cinput); $i++){
        $cases = $cinput[$i];
        $outputs = $coutput[$i];
        //echo $cases;
        //echo $outputs;
        
        $print = "print";
        $findprint = strpos($answer, $print);
        if($findprint == false){
            $code = $answer."\nprint(". $func_name."(".$cases."))"."\n";
            //echo $code;
            $Output = $outputs;
            
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Expected Output: $func_name($cases)-> $Output matched Output: $run";
            $check = "true"; 
            }
            else{
            $comment = "Expected Output: $func_name($cases)-> $Output Did Not Match Students Output: $run";
            $totalpoints -= $casespointsoff;
            $check = "false";
           }
          }
          else{
            $code = $answer."\n". $func_name."(".$cases.")"."\n";
            //echo $code;
            
            $Output = $outputs;
            
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Expected Output: $func_name($cases)-> $Output matched Output: $run";
            $check = "true";  
          }
            else{
            $comment = "Expected Output: $func_name($cases)-> $Output Did Not Match Students Output: $run";
            $totalpoints -= $casespointsoff;
            $check = "false"; 
          }
        }
            $testcases[] = $comment;
            $deduct[] = $casespointsoff;
            $checkarr[] = $check;  
       }
      }
     // If constraint is toggled off the below code will be handled as needed here
     else{
          for($i = 0; $i < count($cinput); $i++){
          $cases = $cinput[$i];
          //echo $cases;
          $outputs = $coutput[$i];
          //echo $outputs;
  
          $print = "print";
          $findprint = strpos($answer, $print);
        if($findprint == false){
            $code = $answer."\nprint(". $func_name."(".$cases."))"."\n";
           // echo $code;
            
            $Output = $outputs;
            
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Expected Output: $func_name($cases)-> $Output matched Output: $run";
            $check = "true";  
            }
            else{
            $comment = "Expected Output: $func_name($cases)-> $Output Did Not Match Students Output: $run";
            $totalpoints -= $casespointsoff;
            $check = "false";
           }
          }
          else{
            $code = $answer."\n". $func_name."(".$cases.")"."\n";
            //echo $code;
            $Output = $outputs;
            
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Illegal Use Of The Print Statement, Points were Deducted For The Following: $func_name($cases)-> $Output";
            $totalpoints -= $casespointsoff;
            $check = "false";
          }
            else{
            $comment = "Accepted Return Statement For The Following Test Cases: $func_name($cases)-> $Output";
            $check = "true";
          }
        }
            $testcases[] = $comment;
            $checkarr[] = $check;  
            $deduct[] = $casespointsoff;
       }
      }
    // If by chance the student gets a grade less than 0 then it will handle it and just give her/him a 0 
    if($totalpoints < 0){
        $totalpoints = 0;
    }
  // this is just combining all the corrections my autograder did into one variable to send to back    
  $corrections = array_merge($correct, $testcases);

  // checking things are going right---> this is for me on my end --->checking to see it works correctly


// send to back
$toDB = array(
   "SID" => $SID,
   "QIDs" => $QIDs,
   "Score" => $totalpoints,
   "TestName" => $TestName,
   "Corrections" => $corrections,
   "CorrectionsBool" => $checkarr,
   "CorrectionsPoints" => $deduct,
   "Action" => 'GetScore'
   
);

$resultsDB = json_encode($toDB);
echo $resultsDB;
$url = 'https://web.njit.edu/~mm693/test2.php';
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $resultsDB);
$results = curl_exec($curl);

echo $results;

curl_close($curl);

?>
