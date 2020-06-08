<?php
   /* All Material © Sonfo-Maika Diomande | New Jersey Institute of Technology June 2020 */

    $var = file_get_contents('php://input', true);
    $info = json_decode($var, true);
    
    $answer = $info['Answers']; 
    $SID = $info['SID'];
    $QIDs = $info['QIDs'];
    //$constraints = $info['Constraints'];
    //$score = $info['Qpoints'];
    //$constraint_found = strcmp($constraints, "None") != 0;
    //$grade = $score;
    
    $grade = 60;//dummy data to see
    
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
    $cinput = $db['TestCases'];
    $coutput = $db['TestCaseOutput'];
    $test_input = explode(' ', $cinput);
    $test_output = explode(' ', $coutput);
    
    $findColon = preg_match('/(def)\s\w+(\'?|\"?\w?|\W?\'?|\"?,?\w+)*\):/', $answer);
         if($findColon == 1){
           $comment = "Colon Was Found";
           $grade = $grade + 0;
           $correct[] = $comment;
        }
        else{
            $comment = "Missing Colon";
            $addIn = ":";
            $strpos1 = strpos($answer, ")");
            $answer = substr_replace($answer, $addIn, $strpos1 + 1, 0);
            $grade = $grade - 5;
            $correct[] = $comment;
        }

        $firstSet = strtok($answer, '(');
        $secondSet = substr($firstSet, 4);
    
       if(strcmp($secondSet, $func_name)== 0){
            $comment = "The Function name was correct ";
            $grade = $grade + 0;
            $correct[] = $comment;
        }
       else{
        $comment = "The Function name \"$secondSet\" was incorrect"; 
        $front_pos = strpos($answer, $secondSet);
        $back_pos = strpos($answer, '(');
        $some_calc = $back_pos - $front_pos;
        $answer = substr_replace($answer, $func_name, $front_pos, $some_calc);
        $grade = $grade - 5;
        $correct[] = $comment;  
       }
       
     if($constraint_found == 1){
        if(strstr($answer, "print")){
          $comment = "constraint 'print' was found";
          $correct[] = $comment;
        }
        elseif(strstr($answer, "for")){
          $comment = "constraint 'for' was found";
          $correct[] = $comment;
        }
        elseif(strstr($answer, "print") || strstr($answer, "for")){
          $comment = "constraints were found";
          $correct[] = $comment;
        }
        else{
          $comment = "constraint was not found";
          $grade = $grade - 3;
          $correct[] = $comment;
          }
        }
         
  if($constraint_found == 1){        
        $iterator = new MultipleIterator;
        $iterator->attachIterator(new ArrayIterator($test_input));
        $iterator->attachIterator(new ArrayIterator($test_output));
        $print = "print";
        $findprint = strpos($answer, $print);
    foreach ($iterator as $values) {
            $key = $values[0];
            $value = $values[1];
        if($findprint == false){
            $code = $answer."\nprint(". $func_name."(".$key."))"."\n";
            
            $Output = $value;
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Expected output matched test case: ($key)";
            $grade = $grade + 0;
            }
            else{
            $comment = "Expected output did not match test case: ($key)";
            $grade = $grade - 1;
           }
        }
        else{
            $code = $answer."\n". $func_name."(".$key.")"."\n";
            $Output = $value;
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Expected output matched test case: ($key)";
            $grade = $grade + 0;
          }
            else{
            $comment = "Expected output did not match test case: ($key)";
            $grade = $grade - 1;
          }
        }
        $testcases[] = $comment;
      }
     }
     else{
        $iterator = new MultipleIterator;
        $iterator->attachIterator(new ArrayIterator($test_input));
        $iterator->attachIterator(new ArrayIterator($test_output));
        $print = "print";
        $findprint = strpos($answer, $print);
        foreach ($iterator as $values) {
            $key = $values[0];
            $value = $values[1];
        if($findprint == false){
            $code = $answer."\nprint(". $func_name."(".$key."))"."\n";
            $Output = $value;
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Expected output matched test case: ($key)";
            $grade = $grade + 0;
           }
            else{
            $comment = "Expected output did not match test case: ($key)";
            $grade = $grade - 1;
          }
        }
        else{
            $code = $answer."\n". $func_name."(".$key.")"."\n";
            $Output = $value;
            $runFile = "runFile.py";
            $fp = fopen("$runFile", "a") or die ("Unable to open file");
            fwrite($fp, $code);
            $run = exec("python $runFile 2>&1");
            fclose($fp);
            if($run == $Output){
            $comment = "Use of Illegal Constraint -Points were Deducted";
            $grade = $grade - 1;
            }
            else{
            $comment = "Use of Accepted Key Word For The Following: ($key)";
            $grade = $grade + 0;
           }
          }
        $testcases[] = $comment;
      }
     }
     
    if($grade < 0){
        $grade = 0;
    }
      
  $letsee = array_merge($correct, $testcases);
  $corrections = implode("\n", $letsee);
  
$pp = fopen("hi.txt", "w") or die ("Unable to open file");
        fwrite($pp, $corrections);
        fclose($pp);


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

