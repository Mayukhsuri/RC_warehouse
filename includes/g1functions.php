<?php

function write_log($file,$line,$s = 'LINETRACE') {
    if (WRITELOG) {
        $datetime = date('Y-m-d H:i:s');
        $file = basename($file);
        $file = str_pad($file,15);
        $line = str_pad($line, 4, ' ', STR_PAD_LEFT);
        if (file_exists(LOGFILE)) {
            file_put_contents(LOGFILE, "$datetime | $file | $line | $s\n", FILE_APPEND | LOCK_EX);
        } else {
            file_put_contents(LOGFILE, "$datetime | $file | $line | $s\n");
        }
    }
  }


#write_log(__FILE__,__LINE__);

function lastID($res) {
    sqlsrv_next_result($res);
    sqlsrv_fetch($res);
    $ID = sqlsrv_get_field($res, 0);
 //   write_log (__FILE__,__LINE__, "function: lastID: " . $ID);
    return $ID;
}

    function hashup($bigpepper,$input) {
        return hash("sha256",$bigpepper.$input);
    }
    
    function truncate($string,$maxlength,$endmark='...',$endatspace=true) {
        if(strlen($string) > $maxlength) {
            $maxlength = $maxlength - strlen($endmark);
            $output = substr($string,0,$maxlength);
            if($endatspace == true || $endatspace == 1) {
                $lastspacepos = strrpos($output,' ');
                if($lastspacepos != false) {
                    $output = substr($output,0,$lastspacepos);
                }
            }
            return $output . $endmark;
        } else {
            return $string;
        }
    }
    
    function cleantext($input,$extracharacters='') {
        $valid = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz 0123456789$-." . $extracharacters;
        $length = strlen($input);
        $output = "";
        for($i=0; $i<$length; $i++) {
            if(strpos($valid,substr($input,$i,1)) === FALSE) {
                $output .= '';
            } else {
                $output .= substr($input,$i,1);
            }
        }
        return $output;
    }
    
    function cleantextarea($input) {
        $valid = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz 0123456789$-.</>";
        $input = nl2br($input);
        $length = strlen($input);
        $output = "";
        for($i=0; $i<$length; $i++) {
            if(strpos($valid,substr($input,$i,1)) === FALSE) {
                $output .= '';
            } else {
                $output .= substr($input,$i,1);
            }
        }
        return $output;
    }
    
    function valtext($input, $specialtest, $specialerror) {
        $valid = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz 0123456789$-.";
        if(isset($specialtest)) {$valid = $specialtest; }
        $errormessage = "Please do not use special characters.";
        if(isset($specialerror)) {$errormessage = $specialerror; }
        $length = strlen($input);
        $errorcount = 0;
        for($i=0; $i<$length; $i++) {
            if(strpos($valid,substr($input,$i,1)) === FALSE) {
                $errorcount++;
            }
        }
        if(empty($input)) {$errorcount++; $errormessage = "Please complete all form fields."; }
        if($errorcount > 0) {$output['error'] = $errormessage; } else {$output['error'] = 0; }
        $output['value'] = $input;
        $output['sql'] = mysql_real_escape_string($input);
        return $output;
    }
    
    # Optional Text Function (Not a required field...)
    function valtextopt($input, $specialtest, $specialerror) {
        $valid = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz 0123456789$-.";
        if(isset($specialtest)) {$valid = $specialtest; }
        $errormessage = "Please do not use special characters.";
        if(isset($specialerror)) {$errormessage = $specialerror; }
        $length = strlen($input);
        $errorcount = 0;
        for($i=0; $i<$length; $i++) {
            if(strpos($valid,substr($input,$i,1)) === FALSE) {
                $errorcount++;
            }
        }
        if($errorcount > 0) {$output['error'] = $errormessage; } else {$output['error'] = 0; }
        $output['value'] = $input;
        $output['sql'] = mysql_real_escape_string($input);
        return $output;
    }
    
    function valnumeric($input) {
        if(is_numeric($input)) {
            $output['error'] = 0;
            $output['value'] = $input;
            $output['sql'] = mysql_real_escape_string($input);
        } else {
            $output['error'] = "Please enter a valid number.";
            $output['value'] = $input;
            $output['sql'] = mysql_real_escape_string($input);
        }
        return $output;
    }
    
    function valcurrency($input) {
        $errorcount = 0;
        if(!is_numeric($input)) {$errorcount++; }
        if(is_numeric($input)) {
            $inputrounded = (intval($input*100))/100;
        }
        if($errorcount == 0) {
            $output['error'] = 0;
            $output['value'] = $inputrounded;
            $output['sql'] = mysql_real_escape_string($inputrounded);
        } else {
            $output['error'] = "Please enter a valid amount.";
            $output['value'] = $input;
            $output['sql'] = ".01";
        }
        return $output;
    }
    
    function valdate($input) {
        $testresult = 0;
        $seps = array('/', '-', '.');
        if(is_numeric(str_replace($seps, '', $input))) {
            $date = str_replace($seps, '-', $input);
            $date = explode('-', $date);
            $month = $date[0];
            $day = $date[1];
            $year = $date[2];
            if($month > 0 && $month < 13) {
                if($day > 0 && $day < 32) {
                    if(strlen($year) == 4) {
                        $testresult = 1;
                    }
                }
            }
        }
        if($testresult == 1) {
            if($month == 1) {$month = 'January'; }
            if($month == 2) {$month = 'February'; }
            if($month == 3) {$month = 'March'; }
            if($month == 4) {$month = 'April'; }
            if($month == 5) {$month = 'May'; }
            if($month == 6) {$month = 'June'; }
            if($month == 7) {$month = 'July'; }
            if($month == 8) {$month = 'August'; }
            if($month == 9) {$month = 'September'; }
            if($month == 10) {$month = 'October'; }
            if($month == 11) {$month = 'November'; }
            if($month == 12) {$month = 'December'; }
            $output['error'] = 0;
            $output['value'] = $input;
            $output['sql'] = strtotime($month . ' ' . $day . ', ' . $year);
        } else {
            $output['error'] = "Please enter a date in mm/dd/yyyy format.";
            $output['value'] = $input;
            $output['sql'] = 0;
        }
        return $output;
    }
    
    function passcheck($input, $bigpepper) {
        $valid = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz 0123456789";
        $validnum = "0123456789";
        $validalpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz ";
        $errorcount = 0;
        $length = strlen($input);
        if($length < 8) {$errorcount++; }
        $numcount = 0;
        $alphacount = 0;
        for($i=0; $i<$length; $i++) {
            if(strpos($valid,substr($input,$i,1)) === FALSE) {
                $errorcount++;
            } else {
                if(strpos($validnum,substr($input,$i,1)) >= 0) {$numcount++; }
                if(strpos($validalpha,substr($input,$i,1)) >= 0) {$alphacount++; }
            }
        }
        if($numcount < 1) {$errorcount++; }
        if($alphacount < 1) {$errorcount++; }
        if($errorcount == 0) {
            $output['error'] = 0;
            $output['value'] = $input;
            $output['sql'] = hash("sha256",$bigpepper.$input);
        } else {
            $output['error'] = "Your password must be at least 8 characters in length and contain at least 1 letter and 1 number.";
            $output['value'] = $input;
            $output['sql'] = hash("sha256",$bigpepper.$bigpepper.$bigpepper);
        }
        return $output;
    }
    
    function generatetoken($charactercount) {
        $tokencharacters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $length = strlen($tokencharacters);
        $output = "";
        for($i=1; $i<=$charactercount; $i++) {
            $charloc = rand(0,strlen($tokencharacters))-1;
            $output = $output . substr($tokencharacters,$charloc,1);
        }
        return $output;
    }
    
    function humantime($time,$totalelements=2,$abbrev=0) {
        $timeremaining = $time;
        $output = "";
        $elementcount = 0;
        if($abbrev == 0) {$ytext = " year"; $wtext = " week"; $dtext = " day"; $htext = " hour"; $mtext = " minute"; $stext = " second"; }
        if($abbrev == 1) {$ytext = " yr"; $wtext = " wk"; $dtext = " day"; $htext = " hr"; $mtext = " min"; $stext = " sec"; }
        // Years
        $year = 60 * 60 * 24 * 365.25;
        if(floor($timeremaining/$year) >= 1) {
            $elementcount++;
            if($elementcount <= $totalelements) {
                $timechunk = floor($timeremaining/$year);
                $timeremaining = $timeremaining - ($timechunk * $year);
                $plural = ""; if($timechunk > 1) {$plural = "s"; }
                $comma = ""; if($elementcount <= ($totalelements - 1)) {$comma = ", "; }
                $output .= $timechunk . $ytext . $plural . $comma; 
            }
        }
        // Weeks
        $weeks = 60 * 60 * 24 * 7;
        if(floor($timeremaining/$weeks) >= 1) {
            $elementcount++;
            if($elementcount <= $totalelements) {
                $timechunk = floor($timeremaining/$weeks);
                $timeremaining = $timeremaining - ($timechunk * $weeks);
                $plural = ""; if($timechunk > 1) {$plural = "s"; }
                $comma = ""; if($elementcount <= ($totalelements - 1)) {$comma = ", "; }
                $output .= $timechunk . $wtext . $plural . $comma;
            }
        }
        // Days
        $days = 60 * 60 * 24;
        if(floor($timeremaining/$days) >= 1) {
            $elementcount++;
            if($elementcount <= $totalelements) {
                $timechunk = floor($timeremaining/$days);
                $timeremaining = $timeremaining - ($timechunk * $days);
                $plural = ""; if($timechunk > 1) {$plural = "s"; }
                $comma = ""; if($elementcount <= ($totalelements - 1)) {$comma = ", "; }
                $output .= $timechunk . $dtext . $plural . $comma;
            }
        }
        // Hours
        $hours = 60 * 60;
        if(floor($timeremaining/$hours) >= 1) {
            $elementcount++;
            if($elementcount <= $totalelements) {
                $timechunk = floor($timeremaining/$hours);
                $timeremaining = $timeremaining - ($timechunk * $hours);
                $plural = ""; if($timechunk > 1) {$plural = "s"; }
                $comma = ""; if($elementcount <= ($totalelements - 1)) {$comma = ", "; }
                $output .= $timechunk . $htext . $plural . $comma;
            }
        }
        // Minutes
        $minutes = 60;
        if(floor($timeremaining/$minutes) >= 1) {
            $elementcount++;
            if($elementcount <= $totalelements) {
                $timechunk = floor($timeremaining/$minutes);
                $timeremaining = $timeremaining - ($timechunk * $minutes);
                $plural = ""; if($timechunk > 1) {$plural = "s"; }
                $comma = ""; if($elementcount <= ($totalelements - 1)) {$comma = ", "; }
                $output .= $timechunk . $mtext . $plural . $comma;
            }
        }
        // Seconds
        $seconds = 1;
        if(floor($timeremaining/$seconds) >= 1) {
            $elementcount++;
            if($elementcount <= $totalelements) {
                $timechunk = floor($timeremaining/$seconds);
                $timeremaining = $timeremaining - ($timechunk * $seconds);
                $plural = ""; if($timechunk > 1) {$plural = "s"; }
                $output .= $timechunk . $stext . $plural;
            }
        }
        if(substr($output,(strlen($output)-2),2) == ", ") {$output = substr($output,0,(strlen($output) - 2)); }
        return $output;
    }

?>
