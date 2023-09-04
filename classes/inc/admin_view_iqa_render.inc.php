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
    $array = $lib->get_iqa();
    if(count($array[0]) > 0){
        $returnText->return = "<h2>".get_string('view_iqa', $p)."</h2><table class='table table-bordered table-striped table-hover'>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Learner</th>
                    <th>IQA</th>
                </tr>
            </thead>
            <tbody>
        ";
        foreach($array[0] as $arr){
            foreach($array[1][$arr[1]] as $ar){
                $returnText->return .= "<tr>
                    <td><a href='./../../course/view.php?id=$arr[1]' target='_blank'>$arr[0]</a></td>
                    <td><a href='./../../user/profile.php?id=$ar[1]' target='_blank'>$ar[0]</a></td>
                    <td><a href='./../../user/profile.php?id=$ar[2]' target='_blank'>$ar[3]</a></td>
                </tr>";
            }
        }
        $returnText->return .= "</tbody></table>";
        $returnText->return = str_replace("  ","",$returnText->return);
    }
}
echo(json_encode($returnText));