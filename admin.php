<?php
/**
 * @package     local_iqa
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

require_once(__DIR__.'/../../config.php');
require_login();
$context = context_system::instance();
require_capability('local/iqa:admin', $context);
use local_iqa\lib;
$lib = new lib;
$p = 'local_iqa';
$title = get_string('iqa_a', $p);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/iqa/admin.php'));
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

$template = (Object)[
    'title' => $title,
    'choose_ac' => get_string('choose_ac', $p),
    'choose_al' => get_string('choose_al', $p),
    'choose_aiqa' => get_string('choose_aiqa', $p),
    'submit' => get_string('submit', $p),
    'course' => get_string('course', $p),
    'learner' => get_string('learner', $p),
    'iqa' => get_string('iqa', $p),
    'assign_iqa' => get_string('assign_iqa', $p)
];
echo $OUTPUT->render_from_template('local_iqa/admin', $template);

echo $OUTPUT->footer();
$_SESSION['iqa_admin'] = true;