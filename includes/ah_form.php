<?php

    error_reporting(E_ALL); ini_set("display_errors",1);
    
    class ah_form {
        
        protected $formHead;
        protected $formMain;
        protected $formHTML;
        protected $errorBlock;
        
        protected $options;
        protected $checkset;
        
        protected $inputValidationType;
        protected $passwordValidationType;
        protected $passwordValidationLength;
        protected $min;
        protected $max;
        protected $allowBlank;
        protected $instructions;
        protected $startYear;
        protected $endYear;
        protected $minuteInterval;
        
        public $formValidated;
        public $formSubmitted;
        public $validated;
        
        
        
        
        /**
         * Sanitizes input from an HTML form.
         * ----------------------------------
         * @param string $input is the string to be sanitized.
         */
        private function cleanText($input) {
            $valid = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz 0123456789$-.</>@";
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
        
        /**
         * Counts characters from a specific group within a variable.
         * ----------------------------------------------------------
         * @param string $input is the string to search.
         * @param string $valid are the characters to count.
         */
        private function countValidCharacters($input,$valid="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz 0123456789$-.</>@") {
            $length = strlen($input);
            $count = 0;
            for($i=0; $i<$length; $i++) {if(strpos($valid,substr($input,$i,1)) !== FALSE) {$count++; } }
            return $count;
        }
        
        /**
         * Validates text input in an HTML form.
         * Outputs an array containing the following:
         *      array['clean'] is the sanitized version of the input.
         *      array['error'] is the errormessage *OR* -1 for error-free.
         * ------------------------------------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param string $input is the string to be validated.
         */
        private function validateText($name,$label,$input) {
            $error = -1;
            $input = $this->cleanText($input);
            if($this->inputValidationType == 1) {if(!is_numeric($input)) {$error = 'Field must be a numeric value.'; } }
            if($this->inputValidationType == 2) {if(!is_numeric($input) || $input != intval($input)) {$error = 'Field must be a numeric whole-number value.'; } }
            if($error == -1 && $this->inputValidationType != 3) {
                if($input < $this->min || $input > $this->max) {$error = 'Entry must be a number between ' . $this->min . ' and ' . $this->max . '.'; }
            }
            if($this->inputValidationType == 3) {
                if(strlen($input) < $this->min || strlen($input) > $this->max) {$error = 'Your entry must be between ' . $this->min . ' and ' . $this->max . ' characters long.'; }
                if($this->allowBlank == 1 && strlen($input) == 0) {$error = -1; }
            }
            if($this->allowBlank != 1) {if(strlen($input) < 1 || !isset($_POST[$name])) {$error = 'Field requires an entry.'; } }
            if($error != -1) {$this->errorBlock .= '<br/>- [' . $label . '] ' . $error; $this->formValidated = 0; }
            $this->validated[$name]['clean'] = $input;
            $this->validated[$name]['error'] = $error;
            return $this->validated[$name];
        }
        
        /**
         * Validates a password setting input in an HTML form.
         * Outputs an array containing the following:
         *      array['clean'] is the sanitized version of the input.
         *      array['error'] is the errormessage *OR* -1 for error-free.
         * ------------------------------------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param string $input is the string to be validated.
         * $param string $salt is the salt to attach when encrypting.
         */
        private function validatePasswordSet($name,$label,$input,$repeated,$salt) {
            $error = -1;
            $input = $this->cleanText($input);
            $repeated = $this->cleanText($repeated);
            $special = $this->countValidCharacters($input,"$-.</>@ ");
            $lowercase = $this->countValidCharacters($input,"abcdefghijklmnopqrstuvwxyz");
            $uppercase = $this->countValidCharacters($input,"ABCDEFGHIJKLMNOPQRSTUVWXYZ");
            $numeric = $this->countValidCharacters($input,"0123456789");
            $characters = strlen($input);
            $alpha = $lowercase+$uppercase;
            if($input != $repeated) {$error = 'Passwords do not match.'; }
            if($this->passwordValidationType == 2) {
                if($numeric == 0 || $alpha == 0) {$error = 'Password must contain a mixture of letters and numbers.'; }
            }
            if($this->passwordValidationType == 3) {
                if($numeric == 0 || $lowercase == 0 || $uppercase == 0) {
                    $error = 'Password must contain a mixture of numbers and both uppercase and lowercase letters.';
                }
            }
            if($this->passwordValidationType == 4) {
                if($numeric == 0 || $lowercase == 0 || $uppercase == 0 || $special == 0) {
                    $error = 'Password must contain a mixture of numbers, both uppercase and lowercase letters plus special character(s): <$-./@ >.';
                }
            }
            if($characters < $this->passwordValidationLength) {$error = 'Password must contain a minimum of ' . $this->passwordValidationLength . ' characters.'; }
            if($error != -1) {$this->errorBlock .= '<br/>- [' . $label . '] ' . $error; $this->formValidated = 0; }
            $this->validated[$name]['clean'] = $this->hashup($salt,$input);
            $this->validated[$name]['error'] = $error;
            return $this->validated[$name];
        }
        
        /**
         * Validates a password setting input in an HTML form.
         * Outputs an array containing the following:
         *      array['clean'] is the sanitized version of the input.
         *      array['error'] is the errormessage *OR* -1 for error-free.
         * ------------------------------------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param string $input is the string to be validated.
         * @param string $salt is the salt to attach when encrypting.
         * @param string $verify is the pre-hashed password to match against.
         */
        private function validatePassword($name,$label,$input,$salt,$verify) {
            $error = -1;
            $input = $this->hashup($salt,$this->cleanText($input));
            if($input != $verify) {$error = 'Username or password incorrect or not found in system.  Please retry.'; }
            if($error != -1) {$this->errorBlock .= '<br/>- [' . $label . '] ' . $error; $this->formValidated = 0; }
            $this->validated[$name]['clean'] = $this->hashup($salt,$input);
            $this->validated[$name]['error'] = $error;
            return $this->validated[$name];
        }
        
        /**
         * Validates boxes checked in a checkbox array.
         * Outputs an array containing the following:
         *      array['clean'] is the sanitized version of the input array.
         *      array['error'] is the errormessage *OR* -1 for error-free.
         * ------------------------------------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param array $input is the input array to be validated.
         */
        private function validateChecks($name,$label,$inputArray) {
            $error = -1;
            $boxeschecked = 0;
            $cleanArray = array();
            foreach($this->options as $key=>$value) {
                if(in_array($key,$inputArray)) {
                    $cleanArray[$key] = 1;
                    $boxeschecked++;
                    $this->checkset[$key] = 1;
                } else {
                    $this->checkset[$key] = 0;
                }
            }
            if($this->min != 1) {$mintext = $this->min . ' boxes.'; } else {$mintext = '1 box.'; }
            if($this->max != 1) {$maxtext = $this->max . ' boxes.'; } else {$maxtext = '1 box.'; }
            if($this->allowBlank != 1) {
                if($boxeschecked < $this->min) {$error = 'You must check at least ' . $mintext; }
                if($boxeschecked < $this->min && $this->min < 1) {$error = 'You must check at least 1 box.'; }
                if($boxeschecked > $this->max) {$error = 'Please do not select more than ' . $maxtext; }
            } else {
                if($boxeschecked < $this->min && $boxeschecked > 0) {$error = 'When responding, you must check at least ' . $mintext; }
                if($boxeschecked > $this->max) {$error = 'Please do not select more than ' . $maxtext; }
            }
            if($error != -1) {$this->errorBlock .= '<br/>- [' . $label . '] ' . $error; $this->formValidated = 0; }
            $this->validated[$name]['clean'] = $cleanArray;
            $this->validated[$name]['error'] = $error;
            return $this->validated[$name];
        }
        
        /**
         * Validates option selected in a radio or select input element.
         * Outputs an array containing the following:
         *      array['clean'] is the sanitized version of the input array.
         *      array['error'] is the errormessage *OR* -1 for error-free.
         * ------------------------------------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param array $input is the input array to be validated.
         */
        private function validateOption($name,$label,$input) {
            $error = -1;
            $selectionmade = 0;
            $selection = '';
            foreach($this->options as $key=>$value) {
                if($key == $input && $key != -1) {
                    $selection = $input;
                    $selectionmade = 1;
                    $this->checkset[$key] = 1;
                } else {
                    $this->checkset[$key] = 0;
                }
            }
            if($this->allowBlank != 1 && $selectionmade == 0) {$error = 'You must make a selection.'; }
            if($error != -1) {$this->errorBlock .= '<br/>- [' . $label . '] ' . $error; $this->formValidated = 0; }
            $this->validated[$name]['clean'] = $selection;
            $this->validated[$name]['error'] = $error;
            return $this->validated[$name];
        }
        
        /**
         * Validates a date array from a datepicker element.
         * Outputs an array containing the following:
         *      array['clean'] is the sanitized version of the input array.
         *      array['error'] is the errormessage *OR* -1 for error-free.
         * ------------------------------------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param array $date is the date array to be validated.
         */
        private function validateDate($name,$label,$date) {
            $error = -1;
            $datestamp = -1;
            $fieldsvalidated = 0;
            $mm = intval($date['mm']);
            $dd = intval($date['dd']);
            $yy = intval($date['yy']);
            if(is_numeric($mm) && $mm >= 1 && $mm <= 12) {$fieldsvalidated++; }
            if(is_numeric($dd) && $dd >= 1 && $dd <= 31) {$fieldsvalidated++; }
            if(is_numeric($yy) && strlen($yy) == 4) {$fieldsvalidated++; }
            if($this->allowBlank != 1 && $fieldsvalidated < 3) {$error = 'You must select a full date.'; }
            if($fieldsvalidated == 3) {$datestamp = strtotime($mm . '/' . $dd . '/' . $yy); }
            if($error != -1) {$this->errorBlock .= '<br/>- [' . $label . '] ' . $error; $this->formValidated = 0; }
            $this->validated[$name]['clean'] = $datestamp;
            $this->validated[$name]['error'] = $error;
            return $this->validated[$name];
        }
        
        /**
         * Validates a time array from a timepicker element.
         * Outputs an array containing the following:
         *      array['clean'] is the sanitized version of the input array.
         *      array['error'] is the errormessage *OR* -1 for error-free.
         * ------------------------------------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param array $date is the time array to be validated.
         */
        private function validateTime($name,$label,$time) {
            $error = -1;
            $timestamp = -1;
            $fieldsvalidated = 0;
            $hh = intval($time['hh']);
            $mm = intval($time['mm']);
            $ap = intval($time['ap']);
            if(is_numeric($hh) && $hh >= 1 && $hh <= 12) {$fieldsvalidated++; }
            if(is_numeric($mm) && $mm >= 0 && $mm <= 59) {$fieldsvalidated++; }
            if($ap == 0 || $ap == 1) {$fieldsvalidated++; }
            if($this->allowBlank != 1 && $fieldsvalidated < 3) {$error = 'You must select a full time.'; }
            if($ap == 1) {$hoursadjust = 12; } else {$hoursadjust = 0; }
            if($hh == 12) {$hoursadjust = $hoursadjust - 12; }
            if($fieldsvalidated == 3) {$timestamp = (($hh + $hoursadjust) * 3600) + ($mm * 60); }
            if($error != -1) {$this->errorBlock .= '<br/>- [' . $label . '] ' . $error; $this->formValidated = 0; }
            $this->validated[$name]['clean'] = $timestamp;
            $this->validated[$name]['error'] = $error;
            return $this->validated[$name];
        }
        
        /**
         * Initiates the creation of a new HTML form.
         * ------------------------------------------
         * @param string $name is the name of the form.
         */
        public function __construct($name) {
            $this->formValidated = 0;
            $this->formSubmitted = 0;
            if(isset($_POST['formsubmitted'])) {$this->formSubmitted = 1; $this->formValidated = 1; }
            $action = pathinfo($_SERVER['REQUEST_URI'],2);
            $this->formHead = '<form class="ah_form" name="' . $name . '" action="' . $action . '" method="POST">';
            $this->formHead .= '<div class="headline"></div>';
            $this->errorBlock = '';
            $this->formHTML = '';
            $this->inputValidationType = 3;
            $this->passwordValidationType = 1;
            $this->passwordValidationLength = 5;
            $this->min = 0;
            $this->max = 99999;
            $this->allowBlank = 0;
            $this->instructions = -1;
            $this->resetOptions();
            $this->startYear = date('Y',time()) - 75;
            $this->endYear = date('Y',time());
            $this->minuteInterval = 1;
        }
        
        /**
         * Adds an optional title block (title & description) to form.
         * -----------------------------------------------------------
         * @param string $title is the title of the form.
         * @param string $description is the description / special instructions for the form.
         */
        public function addTitle($title,$description='') {
            $this->formHead .= '<h2>' . $title . '</h2>';
            $this->formHead .= '<div class="spacer"></div>';
        }
        
        /**
         * Adds a spacer to the form.
         */
        public function addSpacer() {
            $this->formMain .= '<div class="spacer"></div>';
        }
        
        /**
         * Adds a form break to form.
         * --------------------------
         * This method takes no parameters.
         */
        public function addBreak() {
            $this->formHTML .= '<div class="formbreak"></div>';
        }
        
        /**
         * Sets validation type for succeeding text / textarea inputs.
         * -----------------------------------------------------------
         * @param int $type is a numeric representation of the validation type:
         *      1 - Numeric Only
         *      2 - Numeric Only (Force Integer)
         *      3 - Text
         */
        public function setValidationType($type) {
            $this->inputValidationType = $type;
        }
        
        /**
         * Sets validation length and type for succeeding password inputs.
         * ---------------------------------------------------------------
         * @param int $length is the minimum number of characters required.
         * @param int $type is a numeric representation of the validation type:
         *      1 - LOW STRENGTH - Character count only.
         *      2 - MEDIUM STRENGTH - Mixture of numbers and letters.
         *      3 - HIGH STRENGTH - Mixture of numbers, lowercase and uppercase.
         *      4 - VERY HIGH STRENGTH - Mixture of numbers, lowercase, uppercase and special.
         */
        public function setPasswordValidation($length,$type) {
            $this->passwordValidationLength = $length;
            $this->passwordValidationType = $type;
        }
        
        /**
         * Creates a sha256 encrypted hash value for an input.
         * ---------------------------------------------------
         * @param string $salt is the salt to be attached to the input.
         * @param string $input is the text to be encrypted.
         */
        function hashup($salt,$input) {
            return hash("sha256",$salt.$input);
        }
        
        /**
         * Sets the boundaries for succeeding input elements.
         * --------------------------------------------------
         * @param int $min is the minimum value (or character count) allowed.
         * @param int $max is the maximum value (or character count) allowed.
         * @param int #allowBlank defines whether the field is allowed to be blank:
         *      1 - Field may be left blank.
         *      0 - Field must be completed.
         */
        public function setBounds($min,$max,$allowBlank) {
            $this->min = $min;
            $this->max = $max;
            $this->allowBlank = $allowBlank;
        }
        
        /**
         * Sets the minimum bound for succeeding input elements.
         * -----------------------------------------------------
         * @param int $min is the minimum value (or character count) allowed.
         */
        public function setMin($min) {
            $this->min = $min;
        }
        
        /**
         * Sets the maximum bound for succeeding input elements.
         * -----------------------------------------------------
         * @param int $max is the maximum value (or character count) allowed.
         */
        public function setMax($max) {
            $this->max = $max;
        }
        
        /**
         * Sets whether succeeding input elements are allowed to be left blank.
         * --------------------------------------------------------------------
         * @param int #allowBlank defines whether the field is allowed to be blank:
         *      1 - Field may be left blank.
         *      0 - Field must be completed.
         */
        public function setAllowBlank($allowBlank) {
            $this->allowBlank = $allowBlank;
        }
        
        /**
         * Adds special instructions for the succeeding input element.
         * -----------------------------------------------------------
         * @param string $instructions are the instructions to be added.
         */
        public function addInstructions($instructions) {
            $this->instructions = $instructions;
        }
        
        /**
         * Sets a date (year) range for succeeding datepicker elements.
         * ------------------------------------------------------------
         * @param int $start is the start year for the datepicker.
         * @param int $end is the end year for the datepicker.
         */
        public function setDateRange($start,$end) {
            if(is_numeric($start)) {$this->startYear = intval($start); }
            if(is_numeric($end)) {$this->endYear = intval($end); }
        }
        /**
         * Sets a minute interval for succeeding timepicker elements.
         * ----------------------------------------------------------
         * @param int $interval is the selectable minute interval.
         */
        public function setMinuteInterval($interval) {
            if(is_numeric($interval)) {$this->minuteInterval = intval($interval); }
        }
        
        /**
         * Creates an text input element.
         * ------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param string $default (OPTIONAL) is the default value of the field.
         */
        public function textInput($name,$label,$default='') {
            $valuetext = '';
            if($default != '') {$valuetext = ' value="' . $default . '"'; }
            if(isset($_POST[$name])) {
                $this->validated[$name] = $this->validateText($name,$label,$_POST[$name]);
                $valuetext = ' value="' . $this->validated[$name]['clean'] . '"';
            }
            $classname = 'formline'; $errorline = '';
            if($this->formSubmitted == 1) {
                if($this->validated[$name]['error'] != -1) {$classname = 'formline error'; $errorline = '<div class="errorline">' . $this->validated[$name]['error'] . '</div>'; }
            }
            $this->formMain .= '<div class="' . $classname . '"><div class="text">';
            if($this->instructions != -1) {$this->formMain .= '<div class="instruction">' . $this->instructions . '</div>'; $this->instructions = -1; }
            $this->formMain .= '<label>' . $label . '</label>';
            $this->formMain .= '<input type="text" name="' . $name . '"' . $valuetext . '></input>';
            $this->formMain .= $errorline;
            $this->formMain .= '</div></div>';
        }
        
        /**
         * Creates a password setting input element.
         * -----------------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param string $salt is the salt to attach when encrypting.
         */
        public function passwordSetInput($name,$label,$salt='') {
            if(isset($_POST[$name])) {$this->validated[$name] = $this->validatePasswordSet($name,$label,$_POST[$name],$_POST[$name.'_repeated'],$salt); }
            $classname = 'formline'; $errorline = '';
            if($this->formSubmitted == 1) {
                if($this->validated[$name]['error'] != -1) {$classname = 'formline error'; $errorline = '<div class="errorline">' . $this->validated[$name]['error'] . '</div>'; }
            }
            $this->formMain .= '<div class="' . $classname . '"><div class="password">';
            if($this->instructions != -1) {$this->formMain .= '<div class="instruction">' . $this->instructions . '</div>'; $this->instructions = -1; }
            $this->formMain .= '<label>' . $label . '</label>';
            $this->formMain .= '<input type="password" name="' . $name . '"></input>';
            $this->formMain .= '<br/><label>Repeat</label>';
            $this->formMain .= '<input type="password" name="' . $name . '_repeated"></input>';
            $this->formMain .= $errorline;
            $this->formMain .= '</div></div>';
        }
        
        /**
         * Creates a password input element.
         * ---------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param string $salt is the salt to attach when encrypting.
         * @param string $verify is the password hash to verify against.
         */
        public function passwordInput($name,$label,$salt='',$verify) {
            if(isset($_POST[$name])) {$this->validated[$name] = $this->validatePassword($name,$label,$_POST[$name],$salt,$verify); }
            $classname = 'formline'; $errorline = '';
            if($this->formSubmitted == 1) {
                if($this->validated[$name]['error'] != -1) {$classname = 'formline error'; $errorline = '<div class="errorline">' . $this->validated[$name]['error'] . '</div>'; }
            }
            $this->formMain .= '<div class="' . $classname . '"><div class="password">';
            if($this->instructions != -1) {$this->formMain .= '<div class="instruction">' . $this->instructions . '</div>'; $this->instructions = -1; }
            $this->formMain .= '<label>' . $label . '</label>';
            $this->formMain .= '<input type="password" name="' . $name . '"></input>';
            $this->formMain .= $errorline;
            $this->formMain .= '</div></div>';
        }
        
        /**
         * Creates an option within an checkbox, radio or select element.
         * (Create options prior to calling the above methods.)
         * Note:  DO NOT use '-1' as a key.  Doing so will break validation.
         * -----------------------------------------------------------------
         * @param string $key is the key value for this option within the element.
         * @param string $optiontext is the text for this option within the element.
         * @param string $default is set to 1 if the option should be checked / selected.
         */
        public function addOption($key,$optiontext,$default='') {
            $this->options[$key] = $optiontext;
            if($default != '') {$this->checkset[$key] = 1; } else {$this->checkset[$key] = 0; }
        }
        
        /**
         * Resets the options and checkset arrays.
         * ---------------------------------------
         * This function takes no paramaters.
         */
        public function resetOptions() {
            $this->options = array();
            $this->checkset = array();
        }
        
        /**
         * Creates a checkbox input element.
         * ---------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         */
        public function checkInput($name,$label) {
            if(isset($_POST['formsubmitted'])) {
                if(!isset($_POST[$name])) {$_POST[$name] = array(); }
                $this->validated[$name] = $this->validateChecks($name,$label,$_POST[$name]);
            }
            $classname = 'formline'; $errorline = '';
            if($this->formSubmitted == 1) {
                if($this->validated[$name]['error'] != -1) {$classname = 'formline error'; $errorline = '<div class="errorline">' . $this->validated[$name]['error'] . '</div>'; }
            }
            $this->formMain .= '<div class="' . $classname . '"><div class="checkbox">';
            if($this->instructions != -1) {$this->formMain .= '<div class="instruction">' . $this->instructions . '</div>'; $this->instructions = -1; }
            $this->formMain .= '<label>' . $label . '</label>';
            $this->formMain .= '<div class="optionarray">';
            foreach($this->options as $key=>$value) {
                if($this->checkset[$key] == 1) {$checked = ' checked'; } else {$checked = ''; }
                $this->formMain .= '<div class="optionline"><input type="checkbox" name="' . $name . '[]" value="' . $key . '"' . $checked . '></input><label>' . $value . '</label></div>';
            }
            $this->formMain .= $errorline;
            $this->formMain .= '</div></div></div>';
            $this->resetOptions();
        }
        
        /**
         * Creates a radio input element.
         * ------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         */
        public function radioInput($name,$label) {
            if(isset($_POST['formsubmitted'])) {
                if(!isset($_POST[$name])) {$_POST[$name] = -1; }
                $this->validated[$name] = $this->validateOption($name,$label,$_POST[$name]);
            }
            $classname = 'formline'; $errorline = '';
            if($this->formSubmitted == 1) {
                if($this->validated[$name]['error'] != -1) {$classname = 'formline error'; $errorline = '<div class="errorline">' . $this->validated[$name]['error'] . '</div>'; }
            }
            $this->formMain .= '<div class="' . $classname . '"><div class="radio">';
            if($this->instructions != -1) {$this->formMain .= '<div class="instruction">' . $this->instructions . '</div>'; $this->instructions = -1; }
            $this->formMain .= '<label>' . $label . '</label>';
            $this->formMain .= '<div class="optionarray">';
            foreach($this->options as $key=>$value) {
                if($this->checkset[$key] == 1) {$checked = ' checked'; } else {$checked = ''; }
                $this->formMain .= '<div class="optionline"><input type="radio" name="' . $name . '" value="' . $key . '"' . $checked . '></input><label>' . $value . '</label></div>';
            }
            $this->formMain .= $errorline;
            $this->formMain .= '</div></div></div>';
            $this->resetOptions();
        }
        
        /**
         * Creates a select input element.
         * -------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param string $listheader (OPTIONAL) displays a header in the select field.
         */
        public function selectInput($name,$label,$listheader=-1) {
            if(isset($_POST['formsubmitted'])) {
                if(!isset($_POST[$name])) {$_POST[$name] = -1; }
                $this->validated[$name] = $this->validateOption($name,$label,$_POST[$name]);
            }
            $classname = 'formline'; $errorline = '';
            if($this->formSubmitted == 1) {
                if($this->validated[$name]['error'] != -1) {$classname = 'formline error'; $errorline = '<div class="errorline">' . $this->validated[$name]['error'] . '</div>'; }
            }
            $this->formMain .= '<div class="' . $classname . '"><div class="select">';
            if($this->instructions != -1) {$this->formMain .= '<div class="instruction">' . $this->instructions . '</div>'; $this->instructions = -1; }
            $this->formMain .= '<label>' . $label . '</label>';
            $this->formMain .= '<select name="' . $name . '">';
            if($listheader != -1) {$this->formMain .= '<option value="-1">' . $listheader . '</option><option value="-1">' . str_repeat('-',intval(strlen($listheader)*1.25)) . '</option>'; }
            foreach($this->options as $key=>$value) {
                if($this->checkset[$key] == 1) {$selected = ' selected="selected"'; } else {$selected = ''; }
                $this->formMain .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
            }
            $this->formMain .= '</select>';
            $this->formMain .= $errorline;
            $this->formMain .= '</div></div>';
            $this->resetOptions();
        }
        
        /**
         * Creates an textarea input element.
         * ----------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param string $default (OPTIONAL) is the default value of the field.
         */
        public function textareaInput($name,$label,$default='') {
            $valuetext = '';
            if(isset($_POST[$name])) {
                $temptype = $this->inputValidationType;
                $this->setValidationType(3);
                $this->validated[$name] = $this->validateText($name,$label,$_POST[$name]);
                $breaks = array('<br/>','<br />','<br/>');
                $default = str_ireplace($breaks,"\r\n",$this->validated[$name]['clean']);
                $this->inputValidationType = $temptype;
            }
            $classname = 'formline'; $errorline = '';
            if($this->formSubmitted == 1) {
                if($this->validated[$name]['error'] != -1) {$classname = 'formline error'; $errorline = '<div class="errorline">' . $this->validated[$name]['error'] . '</div>'; }
            }
            $this->formMain .= '<div class="' . $classname . '"><div class="textarea">';
            if($this->instructions != -1) {$this->formMain .= '<div class="instruction">' . $this->instructions . '</div>'; $this->instructions = -1; }
            $this->formMain .= '<label>' . $label . '</label>';
            $this->formMain .= '<textarea name="' . $name . '">' . $default . '</textarea>';
            $this->formMain .= $errorline;
            $this->formMain .= '</div></div>';
        }
        
        /**
         * Creates an datepicker input element.
         * ------------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param string $setcurrent (OPTIONAL) determines the default:
         *      0 - No date specified & headers in fields.
         *      1 - Current date is default - no headers.
         */
        public function datePickerInput($name,$label,$setcurrent=0) {
            if($setcurrent == 1) {
                $mmDefault = date('n',time());
                $ddDefault = date('j',time());
                $yyDefault = date('Y',time());
                $mmOptions = '';
                $ddOptions = '';
                $yyOptions = '';
            } else {
                $mmDefault = -1;
                $ddDefault = -1;
                $yyDefault = -1;
                $mmOptions = '<option value="-1">MM</option><option value="-1">--------------</option>';
                $ddOptions = '<option value="-1">DD</option><option value="-1">-----</option>';
                $yyOptions = '<option value="-1">YYYY</option><option value="-1">------</option>';
            }
            $valuetext = '';
            if(isset($_POST['formsubmitted'])) {
                $date['mm'] = $this->cleanText($_POST[$name . '_mm']);
                $date['dd'] = $this->cleanText($_POST[$name . '_dd']);
                $date['yy'] = $this->cleanText($_POST[$name . '_yy']);
                $this->validated[$name] = $this->validateDate($name,$label,$date);
                if(is_numeric($date['mm'])) {$mmDefault = $date['mm']; }
                if(is_numeric($date['dd'])) {$ddDefault = $date['dd']; }
                if(is_numeric($date['yy'])) {$yyDefault = $date['yy']; }
            }
            for($i=1;$i<=12;$i++) {
                if($i == $mmDefault) {$selectedtext = ' selected="selected"'; } else {$selectedtext = ''; }
                $mmOptions .= '<option value="' . $i . '"' . $selectedtext . '>' . str_pad($i,2,'0',STR_PAD_LEFT) . '-' . date("F",mktime(0,0,0,$i,1,1970)) . '</option>';
            }
            for($i=1;$i<=31;$i++) {
                if($i == $ddDefault) {$selectedtext = ' selected="selected"'; } else {$selectedtext = ''; }
                $ddOptions .= '<option value="' . $i . '"' . $selectedtext . '>' . $i . '</option>';
            }
            for($i=$this->endYear;$i>=$this->startYear;$i--) {
                if($i == $yyDefault) {$selectedtext = ' selected="selected"'; } else {$selectedtext = ''; }
                $yyOptions .= '<option value="' . $i . '"' . $selectedtext . '>' . $i . '</option>';
            }
            $classname = 'formline'; $errorline = '';
            if($this->formSubmitted == 1) {
                if($this->validated[$name]['error'] != -1) {$classname = 'formline error'; $errorline = '<div class="errorline">' . $this->validated[$name]['error'] . '</div>'; }
            }
            $this->formMain .= '<div class="' . $classname . '"><div class="datepicker">';
            if($this->instructions != -1) {$this->formMain .= '<div class="instruction">' . $this->instructions . '</div>'; $this->instructions = -1; }
            $this->formMain .= '<label>' . $label . '</label>';
            
            $this->formMain .= '<select class="mm" name="' . $name . '_mm">' . $mmOptions . '</select>';
            $this->formMain .= '<select class="dd" name="' . $name . '_dd">' . $ddOptions . '</select>';
            $this->formMain .= '<select class="yyyy" name="' . $name . '_yy">' . $yyOptions . '</select>';
            
            $this->formMain .= $errorline;
            $this->formMain .= '</div></div>';
        }
        
        /**
         * Creates an timepicker input element.
         * ------------------------------------
         * @param string $name is the name of the input element.
         * @param string $label is the label/prompt for the input element.
         * @param string $setcurrent (OPTIONAL) determines the default:
         *      0 - No time specified & headers in fields.
         *      1 - Current time is default - no headers.
         */
        public function timePickerInput($name,$label,$setcurrent=0) {
            if($setcurrent == 1) {
                $hhDefault = date('g',time());
                $mmPerfect = date('i',time());
                for($i=0;$i<=59;$i=$i+$this->minuteInterval) {if($i < $mmPerfect) {$mmDefault = $i; } }
                if(date('a',time()) == 'am') {$apDefault = 0; } else {$apDefault = 1; }
                $hhOptions = '';
                $mmOptions = '';
                $apOptions = '';
            } else {
                $hhDefault = -1;
                $mmDefault = -1;
                $apDefault = -1;
                $hhOptions = '<option value="-1">H</option><option value="-1">---</option>';
                $mmOptions = '<option value="-1">MM</option><option value="-1">---</option>';
                $apOptions = '<option value="-1">AM/PM</option><option value="-1">------</option>';
            }
            $valuetext = '';
            if(isset($_POST['formsubmitted'])) {
                $time['hh'] = $this->cleanText($_POST[$name . '_hh']);
                $time['mm'] = $this->cleanText($_POST[$name . '_mm']);
                $time['ap'] = $this->cleanText($_POST[$name . '_ap']);
                $this->validated[$name] = $this->validateTime($name,$label,$time);
                if(is_numeric($time['hh'])) {$hhDefault = $time['hh']; }
                if(is_numeric($time['mm'])) {$mmDefault = $time['mm']; }
                if(is_numeric($time['ap'])) {$apDefault = $time['ap']; }
            }
            for($i=1;$i<=12;$i++) {
                if($i == $hhDefault) {$selectedtext = ' selected="selected"'; } else {$selectedtext = ''; }
                $hhOptions .= '<option value="' . $i . '"' . $selectedtext . '>' . $i . '</option>';
            }
            for($i=0;$i<=59;$i=$i+$this->minuteInterval) {
                if($i == $mmDefault) {$selectedtext = ' selected="selected"'; } else {$selectedtext = ''; }
                $mmOptions .= '<option value="' . $i . '"' . $selectedtext . '>' . str_pad($i,2,'0',STR_PAD_LEFT) . '</option>';
            }
            if($apDefault == 0) {$amselected = ' selected="selected"'; } else {$amselected = ''; }
            if($apDefault == 1) {$pmselected = ' selected="selected"'; } else {$pmselected = ''; }
            $apOptions .= '<option value="0"' . $amselected . '>AM</option><option value="1"' . $pmselected . '>PM</option>';
            $classname = 'formline'; $errorline = '';
            if($this->formSubmitted == 1) {
                if($this->validated[$name]['error'] != -1) {$classname = 'formline error'; $errorline = '<div class="errorline">' . $this->validated[$name]['error'] . '</div>'; }
            }
            $this->formMain .= '<div class="' . $classname . '"><div class="timepicker">';
            if($this->instructions != -1) {$this->formMain .= '<div class="instruction">' . $this->instructions . '</div>'; $this->instructions = -1; }
            $this->formMain .= '<label>' . $label . '</label>';
            
            $this->formMain .= '<select class="hh" name="' . $name . '_hh">' . $hhOptions . '</select>';
            $this->formMain .= '<select class="mm" name="' . $name . '_mm">' . $mmOptions . '</select>';
            $this->formMain .= '<select class="ampm" name="' . $name . '_ap">' . $apOptions . '</select>';
            
            $this->formMain .= $errorline;
            $this->formMain .= '</div></div>';
        }
        
        /**
         * Outputs the form to HTML.
         * -------------------------
         * @param string $buttontext is the text to display on the submit button.
         */
        public function displayform($buttontext) {
            if($this->errorBlock != '') {$this->errorBlock = '<div class="errorblock">Please correct the following errors:' . $this->errorBlock . '</div>'; }
            $this->formHTML .= $this->formHead;
            $this->formHTML .= $this->errorBlock;
            $this->formHTML .= $this->formMain;
            $this->formHTML .= '<div class="formline"><div class="formsubmit"><label></label><input type="submit" value="' . $buttontext . '"></input></div></div>';
            $this->formHTML .= '<input type="hidden" name="formsubmitted" value="1"></input>';
            $this->formHTML .= '<div class="footline"></div>';
            $this->formHTML .= '</form>';
            echo $this->formHTML;
        }
        
    }
    
?>
