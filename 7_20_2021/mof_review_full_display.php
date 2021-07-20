<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A form for cohort upload.
 *
 * @package    core_cohort
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/phlcohort/lib.php');
require_once($CFG->dirroot.'/phlcohort/upload_form.php');
require_once($CFG->libdir . '/csvlib.class.php');

$contextid = optional_param('contextid', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_URL);

require_login();

if ($contextid) {
    $context = context::instance_by_id($contextid, MUST_EXIST);
} else {
    $context = context_system::instance();
}
if ($context->contextlevel != CONTEXT_COURSECAT && $context->contextlevel != CONTEXT_SYSTEM) {
    print_error('invalidcontext');
}

require_capability('moodle/cohort:manage', $context);

$PAGE->set_context($context);
$baseurl = new moodle_url('/phlcohort/upload.php', array('contextid' => $context->id));
$PAGE->set_url($baseurl);
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_pagelayout('admin');

if ($context->contextlevel == CONTEXT_COURSECAT) {
    $PAGE->set_category_by_id($context->instanceid);
    navigation_node::override_active_url(new moodle_url('/phlcohort/manager.php', array('contextid' => $context->id)));
} else {
    navigation_node::override_active_url(new moodle_url('/phlcohort/manager.php', array()));
}

$uploadform = new cohort_upload_form(null, array('contextid' => $context->id, 'returnurl' => $returnurl));

$returnurl = new moodle_url('/phlcohort/upload.php');
if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = new moodle_url('/phlcohort/manager.php', array('contextid' => $context->id));
}

if ($uploadform->is_cancelled()) {
    redirect($returnurl);
}

$strheading = get_string('review', 'cohort');
$PAGE->navbar->add($strheading);

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($strheading, 'uploadcohorts', 'cohort');

if ($editcontrols = cohort_edit_controls_phl_mof($context, $baseurl)) {
    echo $OUTPUT->render($editcontrols);
}
echo "<form action='mof_review.php' method='post'>
Search tên lớp: <input type='text' name='name'>
<input type='submit'>
</form>";
$name_cohort = $_POST["name"];
$search = html_writer::start_div('row');
$search .= html_writer::start_div('col-md-10');
$search .= html_writer::end_div();
$search .= html_writer::start_div('col-md-2');
$search .= html_writer::end_div();
$search .= html_writer::end_div();
echo $search;

echo "<table class='admintable generaltable' id='users'>
<thead>
<tr>
<th class='header c1 centeralign' style='' scope='col'>Cohort</th>
<th class='header c1 centeralign' style='' scope='col'>Họ & tên</th>
<th class='header c1 centeralign' style='' scope='col'>Ngày sinh</th>
<th class='header c1 centeralign' style='' scope='col'>Số CMTND</th>
<th class='header c1 centeralign' style='' scope='col'>Ngày cấp</th>
<th class='header c1 centeralign' style='' scope='col'>Nơi cấp</th>
<th class='header c1 centeralign' style='' scope='col'>Điểm</th>
</tr>
</thead>
<tbody>";
if(empty($name_cohort)){
    $mof_array = $DB->get_records_sql('select * from {mof_phl}',array());
}
else {
    $mof_cohort_array = $DB->get_records_sql('select * from {cohort} where name = ?', array('name' =>$name_cohort));
    foreach ($mof_cohort_array as $mof_cohort){
        $mof_cohort_id = $mof_cohort->id;
    }
    $mof_array = $DB->get_records_sql('select * from {mof_phl} where cohortid = ?',array('cohortid'=>$mof_cohort_id));
}
foreach ($mof_array as $mof) {
    $user_array = $DB->get_records_sql('select * from {user} where id=?', array('id' =>$mof->userid));
    foreach ($user_array as $user) {
        $firstname = $user->firstname;
        $lastname = $user->lastname;
        $datebirth = $user->datebirth;
        $cmtnd = $user->username;
    }
    $user_info_array1 = $DB->get_records_sql('select * from {user_info_data} where fieldid = 1 and userid=?', array('userid' =>$mof->userid));
    foreach ($user_info_array1 as $user_info1){
        $date = $user_info1->data;
    }
    $user_info_array2 = $DB->get_records_sql('select * from {user_info_data} where fieldid = 2 and userid=?', array('userid' =>$mof->userid));
    foreach ($user_info_array2 as $user_info2){
        $month = $user_info2->data;
    }
    $user_info_array3 = $DB->get_records_sql('select * from {user_info_data} where fieldid = 3 and userid=?', array('userid' =>$mof->userid));
    foreach ($user_info_array3 as $user_info3){
        $year = $user_info3->data;
    }
    //mof
    $mof_cohort_array = $DB->get_records_sql('select * from {cohort} where id = ?', array('id' =>$mof->cohortid));
    foreach ($mof_cohort_array as $mof_cohort){
        $mof_cohort_name = $mof_cohort->name;
    }
    //
    $date_arrange = date_create($mof->dategranted);
    $arranging_granting_date = date_format($date_arrange, 'd/m/Y');
    $newDate = date("d/m/Y", $mof->dategranted);
    $birthday = date("d/m/Y", $mof->birthday);
    echo "<tr class=''>
<td class='cell c0' style=''>$mof_cohort_name</td>
<td class='cell c1' style=''>$mof->full_name</td>
<td class='cell c2' style=''>$birthday</td>
<td class='cell c3' style=''>$mof->idnumber</td>
<td class='cell c4' style=''>$newDate</td>
<td class='cell c5' style=''>$mof->idplace</td>
<td class='cell c6' style=''>$mof->score</td>
</tr>";
}
echo "
</tbody>
</table>";

echo $OUTPUT->footer();


