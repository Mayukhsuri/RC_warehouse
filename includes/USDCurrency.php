<?php

    function commaFormat($amount,$symbol = '$ ') {
        $output = '';
        $intvalue = floor($amount);
        $centvalue = floor($amount * 100) - ($intvalue * 100);
        if($centvalue < 10) {$centvalue = '0' . $centvalue; }
        if($centvalue == 0) {$centvalue = '00'; }
        $output = '.' . $centvalue;
        $digitcount = -1;
        for($i=strlen($intvalue)-1;$i>=0;$i--) {
            $digitcount++;
            if($digitcount == 3) {
                $digitcount = 0;
                $output = ',' . $output;
            }
            $output = substr($intvalue,$i,1) . $output;
        }
        return $symbol . $output;
    }

    function parseSingle($number) {
        switch($number) {
            case 1: $output .= ' one'; break;
            case 2: $output .= ' two'; break;
            case 3: $output .= ' three'; break;
            case 4: $output .= ' four'; break;
            case 5: $output .= ' five'; break;
            case 6: $output .= ' six'; break;
            case 7: $output .= ' seven'; break;
            case 8: $output .= ' eight'; break;
            case 9: $output .= ' nine'; break;
            default: $output = '';
        }
        return $output;
    }

    function parseTriplet($number) {
        $output = '';
        $spot1 = 0;
        if($number > 100) {
            $spot1 = floor($number/100);
            switch($spot1) {
                case 1: $output .= ' one hundred'; break;
                case 2: $output .= ' two hundred'; break;
                case 3: $output .= ' three hundred'; break;
                case 4: $output .= ' four hundred'; break;
                case 5: $output .= ' five hundred'; break;
                case 6: $output .= ' six hundred'; break;
                case 7: $output .= ' seven hundred'; break;
                case 8: $output .= ' eight hundred'; break;
                case 9: $output .= ' nine hundred'; break;
                default: $output .= '';
            }
            $number = $number - ($spot1 * 100);
        }
        if($number < 10) {
            $output .= parseSingle($number);
        }
        elseif($number < 20) {
            switch($number) {
                case 10: $output .= ' ten'; break;
                case 11: $output .= ' eleven'; break;
                case 12: $output .= ' twelve'; break;
                case 13: $output .= ' thirteen'; break;
                case 14: $output .= ' fourteen'; break;
                case 15: $output .= ' fifteen'; break;
                case 16: $output .= ' sixteen'; break;
                case 17: $output .= ' seventeen'; break;
                case 18: $output .= ' eighteen'; break;
                case 19: $output .= ' nineteen'; break;
                default: $output .= '';
            }
        } else {
            $spot2 = floor($number/10);
            switch($spot2) {
                case 2: $output .= ' twenty'; break;
                case 3: $output .= ' thirty'; break;
                case 4: $output .= ' forty'; break;
                case 5: $output .= ' fifty'; break;
                case 6: $output .= ' sixty'; break;
                case 7: $output .= ' seventy'; break;
                case 8: $output .= ' eighty'; break;
                case 9: $output .= ' ninety'; break;
                default: $output .= '';
            }
            $number = $number - ($spot2 * 10);
            $output .= parseSingle($number);
        }
        return $output;
    }

    function currencyText($amount) {
        $output = '';
        $intvalue = floor($amount);
        $centvalue = floor($amount * 100) - ($intvalue * 100);
        if($intvalue == 0) {
            $output .= 'zero dollars and';
        } else {
            $trillions = floor($intvalue/1000000000000);
            $billions = floor($intvalue/1000000000) - ($trillions * 1000);
            $millions = floor($intvalue/1000000) - ($trillions * 1000000) - ($billions * 1000);
            $thousands = floor($intvalue/1000) - ($trillions * 1000000000) - ($billions * 1000000) - ($millions * 1000);
            $hundreds = $intvalue - ($trillions * 1000000000000) - ($billions * 1000000000) - ($millions * 1000000) - ($thousands * 1000);
            if($trillions > 0) {$output .= parseTriplet($trillions) . ' trillion'; }
            if($billions > 0) {$output .= parseTriplet($billions) . ' billion'; }
            if($millions > 0) {$output .= parseTriplet($millions) . ' million'; }
            if($thousands > 0) {$output .= parseTriplet($thousands) . ' thousand'; }
            if($hundreds > 0) {$output .= parseTriplet($hundreds); }
            $output .= ' dollars and';
        }
        if($centvalue == 0) {
            $output .= ' zero cents';
        } else {
            $output .= parseTriplet($centvalue) . ' cents';
        }
        return $output;
    }

?>
