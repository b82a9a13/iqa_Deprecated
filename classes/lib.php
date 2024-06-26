<?php
/**
 * @package     local_iqa
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */
namespace local_iqa;
use stdClass;

class lib{
    //Get all learners that don't have a iqa and coaches that can be assigned as iqa
    public function get_no_iqa_learners(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT ra.id as id, c.id as courseid, c.fullname as fullname, eu.userid as userid, eu.firstname as firstname, eu.lastname as lastname, ra.roleid as roleid FROM {course} c
        INNER JOIN {context} ctx ON c.id = ctx.instanceid
        INNER JOIN {role_assignments} ra ON ra.contextid = ctx.id AND (ra.roleid = 5 OR ra.roleid = 4 OR ra.roleid = 3)
        INNER JOIN (
            SELECT e.courseid, ue.userid, u.firstname, u.lastname FROM {enrol} e
            INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status != 1
            INNER JOIN {user} u ON u.id = ue.userid
        ) eu ON c.id = eu.courseid AND ra.userid = eu.userid');
        $array = [[],[],[]];
        $keys = [];
        foreach($records as $record){
            if(!$DB->record_exists('iqa_assignment', [$DB->sql_compare_text('learnerid') => $record->userid, $DB->sql_compare_text('courseid') => $record->courseid]) || $record->roleid != 5){
                $key = $record->courseid;
                if(!array_key_exists($key, $array[0])){
                    $array[0][$key] = array();
                    array_push($keys, $key);
                }
                if(!array_key_exists($key, $array[1])){
                    $array[1][$key] = array();
                }
                if($record->roleid == 5){
                    array_push($array[0][$key], [$record->firstname.' '.$record->lastname, $record->userid]);
                } else {
                    array_push($array[1][$key], [$record->firstname.' '.$record->lastname, $record->userid]);
                }
                if(!in_array([$record->fullname, $record->courseid], $array[2])){
                    array_push($array[2], [$record->fullname, $record->courseid]);
                }
            }
        }
        usort($array[2], function($a, $b){
            return strcmp($a[0], $b[0]);
        });
        foreach($keys as $ke){
            for($i = 0; $i < 2; $i++){
                usort($array[$i][$ke], function($a, $b){
                    return strcmp($a[0], $b[0]);
                });
            }
        }
        return $array;
    }

    //Check if a learner is enrolled in a specified course
    public function check_learner_enrolment($cid, $uid): bool{
        global $DB;
        $records = $DB->get_records_sql('SELECT ra.id as id, c.id as courseid, c.fullname as fullname, eu.userid as userid, eu.firstname as firstname, eu.lastname as lastname, ra.roleid as roleid FROM {course} c
        INNER JOIN {context} ctx ON c.id = ctx.instanceid
        INNER JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.roleid = 5
        INNER JOIN (
            SELECT e.courseid, ue.userid, u.firstname, u.lastname FROM {enrol} e
            INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status != 1
            INNER JOIN {user} u ON u.id = ue.userid
        ) eu ON c.id = eu.courseid AND ra.userid = eu.userid AND eu.userid = ? AND eu.courseid = ?',[$uid, $cid]);
        if(count($records) > 0){
            return true;
        } else {
            return false;
        }
    }

    //Used to create a iqa_assignment record to log the iqa for a user and course
    public function create_iqa($course, $learner, $iqa): bool{
        if(!$this->check_learner_enrolment($course, $learner)){
            return false;
        } else {
            global $DB;
            $record = new stdClass();
            $record->courseid = $course;
            $record->learnerid = $learner;
            $record->iqaid = $iqa;
            if($DB->record_exists('iqa_assignment', [$DB->sql_compare_text('learnerid') => $learner, $DB->sql_compare_text('courseid') => $course])){
                return false;
            } elseif($DB->insert_record('iqa_assignment', $record) === false){
                return false;
            }
            return true;
        }
    }

    //Used to get all learners which have a iqa for all learners and courses
    public function get_iqa(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT i.id as id, i.iqaid as iqaid, i.learnerid as learnerid, u.firstname as ufirstname, u.lastname as ulastname, ua.firstname as uafirstname, ua.lastname as ualastname, c.fullname as fullname, c.id as courseid FROM {iqa_assignment} i 
            LEFT JOIN {user} u ON u.id = i.iqaid
            LEFT JOIN {user} ua ON ua.id = i.learnerid
            LEFT JOIN {course} c ON c.id = i.courseid'
        );
        $array = [[],[]];
        $keys = [];
        foreach($records as $record){
            $key = $record->courseid;
            if(!in_array([$record->fullname, $key], $array[0])){
                array_push($array[0], [$record->fullname, $key]);
            }
            if(!array_key_exists($key, $array[1])){
                $array[1][$key] = array();
                array_push($keys, $key);
            }
            array_push($array[1][$key], [$record->uafirstname.' '.$record->ualastname, $record->learnerid, $record->iqaid, $record->ufirstname.' '.$record->ulastname]);
        }
        usort($array[0], function($a, $b){
            return strcmp($a[0], $b[0]);
        });
        foreach($keys as $ke){
            usort($array[1][$ke], function($a, $b){
                return strcmp($a[0], $b[0]);
            });
        }
        return $array;
    }
}