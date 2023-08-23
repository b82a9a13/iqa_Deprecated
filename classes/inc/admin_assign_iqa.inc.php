<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_iqa\lib;
$lib = new lib();
$p = 'local_iqa';

$returnText = new stdClass();
if(!isset($_SESSION['iqa_admin'])){
    $returnText->error = get_string('missing_rv', $p);
} elseif($_SESSION['iqa_admin'] != true){
    $returnText->error = get_string('missing_rv', $p);
} else {
    if(!isset($_POST['c'])){
        $returnText->error = get_string('missing_cv', $p);
    } else {
        $numMatch = "/^[0-9]*$/";
        $c = $_POST['c'];
        if(!preg_match($numMatch, $c) || empty($c)){
            $returnText->error = get_string('invalid_cv', $p);
        } elseif(!isset($_POST['l'])){
            $returnText->error = get_string('missing_lv', $p);
        } else {
            $l = $_POST['l'];
            if(!preg_match($numMatch, $l) || empty($l)){
                $returnText->error = get_string('invalid_lv', $p);
            } elseif(!isset($_POST['i'])){
                $returnText->error = get_string('missing_iv', $p);
            } else {
                $i = $_POST['i'];
                if(!preg_match($numMatch, $i) || empty($i)){
                    $returnText->error = get_string('invalid_iv', $p);
                } else {
                    $returnText->return = $lib->create_iqa($c, $l, $i);
                }
            }
        }
    }
}

echo(json_encode($returnText));