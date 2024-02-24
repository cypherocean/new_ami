<?php
    if(!function_exists('_site_title')){
        function _site_title(){
            return 'AMI Enterprise';
        }
    }

    if(!function_exists('_site_title_sf')){
        function _site_title_sf(){
            return 'AE';
        }
    }

    if(!function_exists('_mail_from')){
        function _mail_from(){
            return 'info@amienterprise.com';
        }
    }    

    if(!function_exists('_converter')){
        function _converter($unit, $value){
            $meter = '40';
            $feet = '12';
            $inch = '1';
            $total = '';

            if($unit == 'meter'){
                $total = $meter * $value;
            }elseif($unit == 'feet'){
                $total = $feet * $value;
            }else{
                $total = $inch * $value;
            }

            return $total;
        }
    }

    if(!function_exists('_converter_reverse')){
        function _converter_reverse($unit, $value){
            $meter = '40';
            $feet = '12';
            $inch = '1';
            $total = '';

            if($unit == 'meter'){
                $total = $meter / $value;
            }elseif($unit == 'feet'){
                $total = $feet / $value;
            }else{
                $total = $inch / $value;
            }

            return $total;
        }
    }
?>