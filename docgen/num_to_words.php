<?php

    function get_num_name($num){
        switch($num){
            case 1:return 'nje';
            case 2:return 'dy';
            case 3:return 'tre';
            case 4:return 'kater';
            case 5:return 'pese';
            case 6:return 'gjashte';
            case 7:return 'shtate';
            case 8:return 'tete';
            case 9:return 'nente';
        }
    }

    function num_to_words($number, $real_name, $decimal_digit, $decimal_name){
        $res = ''; $resminus = '';
        $real = 0;
        $decimal = 0;

        if($number == 0)
            return 'Zero'.(($real_name == '')?'':' '.$real_name);
        if($number >= 0){
            $real = floor($number);
            $decimal = number_format($number - $real, $decimal_digit, '.', ',');
        }else{
            $resminus = 'minus ';
            $real = ceil($number) * (-1);
            $number = abs($number);
            $decimal = number_format($number - $real, $decimal_digit, '.', ',');
        }
        $decimal = substr($decimal, strpos($decimal, '.') +1);

        $unit_name[0] = '';
        $unit_name[1] = 'mije e';
        $unit_name[2] = 'milion e';
        $unit_name[3] = 'bilion e';
        $unit_name[4] = 'trilion e';

        $packet = array();

        $number = strrev($real);
        $packet = str_split($number,3);

        for($i=0;$i<count($packet);$i++){
            $tmp = strrev($packet[$i]);
            $unit = $unit_name[$i];
            if((int)$tmp == 0)
                continue;
            $tmp_res = '';
            if(strlen($tmp) >= 2){
                $tmp_proc = substr($tmp,-2);
                switch($tmp_proc){
                    case '10':
                        $tmp_res = 'dhjete';
                        break;
                    case '11':
                        $tmp_res = 'njembedhjete';
                        break;
                    case '12':
                        $tmp_res = 'dymbedhjete';
                        break;
                    case '13':
                        $tmp_res = 'trembedhjete';
                        break;
                    case '14':
                        $tmp_res = 'katermbedhjete';
                        break;
                    case '15':
                        $tmp_res = 'pesembedhjete';
                        break;
                    case '16':
                        $tmp_res = 'gjashtembedhjete';
                        break;
                    case '17':
                        $tmp_res = 'shtatembedhjete';
                        break;
                    case '18':
                        $tmp_res = 'tetembedhjete';
                        break;
                    case '19':
                        $tmp_res = 'nentembedhjete';
                        break;
                    case '20':
                        $tmp_res = 'njezet';
                        break;
                    case '30':
                        $tmp_res = 'tridhjete';
                        break;
                    case '40':
                        $tmp_res = 'dyzet';
                        break;
                    case '50':
                        $tmp_res = 'pesedhjete';
                        break;
                    case '60':
                        $tmp_res = 'gjashtedhjete';
                        break;
                    case '70':
                        $tmp_res = 'shtetedhjete';
                        break;
                    case '80':
                        $tmp_res = 'tetedhjete';
                        break;
                    case '90':
                        $tmp_res = 'nentedhjete';
                        break;
                    default:
                        $tmp_begin = substr($tmp_proc,0,1);
                        $tmp_end = substr($tmp_proc,1,1);

                        if($tmp_begin == '1')
                            $tmp_res = get_num_name($tmp_end).'dhjete';
                        elseif($tmp_begin == '0')
                            $tmp_res = get_num_name($tmp_end);
                        elseif($tmp_end == '0')
                            $tmp_res = get_num_name($tmp_begin).'ty';
                        else{
                            if($tmp_begin == '2')
                                $tmp_res = 'njezet';
                            elseif($tmp_begin == '3')
                                $tmp_res = 'tridhjete';
                            elseif($tmp_begin == '4')
                                $tmp_res = 'dyzet';
                            elseif($tmp_begin == '5')
                                $tmp_res = 'pesedhjete';
                            elseif($tmp_begin == '6')
                                $tmp_res = 'gjashtedhjete';
                            elseif($tmp_begin == '7')
                                $tmp_res = 'shtatedhjete';
                            elseif($tmp_begin == '8')
                                $tmp_res = 'tetedhjete';
                            elseif($tmp_begin == '9')
                                $tmp_res = 'nentedhjete';

                            $tmp_res = $tmp_res.' e '.get_num_name($tmp_end);
                        }
                        break;
                }

                if(strlen($tmp) == 3){
                    $tmp_begin = substr($tmp,0,1);

                    $space = '';
                    if(substr($tmp_res,0,1) != ' ' && $tmp_res != '')
                        $space = ' ';

                    if($tmp_begin != 0){
                        if($tmp_begin != '0'){
                            if($tmp_res != '')
                                $tmp_res = 'e'.$space.$tmp_res;
                        }
                        $tmp_res = get_num_name($tmp_begin).' qind'.$space.$tmp_res;
                    }
                }
            }else
                $tmp_res = get_num_name($tmp);
            $space = '';
            if(substr($res,0,1) != ' ' && $res != '')
                $space = ' ';
            $res = $tmp_res.' '.$unit.$space.$res;
        }

        $space = '';
        if(substr($res,-1) != ' ' && $res != '')
            $space = ' ';

        if($res)
            $res .= $space.$real_name.(($real > 1 && $real_name != '')?'s':'');

        if($decimal > 0)
            $res .= ' pike '.num_to_words($decimal, '', 0, '').' '.$decimal_name.(($decimal > 1 && $decimal_name != '')?'s':'');

        return $resminus . $res;
    }

?>