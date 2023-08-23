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
    $array = $lib->get_no_iqa_learners();
    $course = '<option value="" disabled selected>'.get_string('choose_ac', $p).'</option>';
    $learner = '';
    $iqa = '';
    for($i = 0; $i < count($array[2]); $i++){
        $learnertmp = '';
        $iqatmp = '';
        $coursetmp = "<option value='".$array[2][$i][1]."'>".$array[2][$i][0]."</option>";
        if(!empty($array[0][$array[2][$i][1]])){
            $learnertmp .= '<select cat="'.$array[2][$i][1].'" class="iqa-learner" style="display:none;"><option value="" disabled selected>'.get_string('choose_al', $p).'</option>';
            foreach($array[0][$array[2][$i][1]] as $arr){
                $learnertmp .= "<option value='$arr[1]'>$arr[0]</option>";
            }
            $learnertmp .= '</select>';
        }
        if(!empty($array[1][$array[2][$i][1]])){
            $iqatmp .= '<select cat="'.$array[2][$i][1].'" class="iqa-iqa" style="display:none;"><option value="" disabled selected>'.get_string('choose_aiqa', $p).'</option>';
            foreach($array[1][$array[2][$i][1]] as $arr){
                $iqatmp .= "<option value='$arr[1]'>$arr[0]</option>";
            }
            $iqatmp .= '</select>';
        } else {
            $iqatmp .= '<p cat="'.$array[2][$i][1].'" class="iqa-iqa text-danger">'.get_string('no_ca', $p).'</p>';
        }
        if($learnertmp != ''){
            $course .= $coursetmp;
            $learner .= $learnertmp;
            $iqa .= $iqatmp;
        }
    }
    $returnText->course = $course;
    $returnText->learner = $learner;
    $returnText->iqa = $iqa;
}
echo(json_encode($returnText));