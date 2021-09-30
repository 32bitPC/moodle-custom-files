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
 * Course completion progress report
 *
 * @package    report
 * @subpackage completion
 * @copyright  2009 Catalyst IT Ltd
 * @author     Aaron Barnes <aaronb@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__.'/../../../config.php');
require_once("{$CFG->libdir}/completionlib.php");

/**
 * Configuration
 */
define('COMPLETION_REPORT_PAGE',        25);
define('COMPLETION_REPORT_COL_TITLES',  true);

/*
 * Setup page, check permissions
 */

// Get course
$quizid = required_param('quizid', PARAM_INT);
$format = optional_param('format','',PARAM_ALPHA);
$sort = optional_param('sort','',PARAM_ALPHA);
$cohortid11 =optional_param('cohortid', '', PARAM_RAW);
$cohortid = 11;
if($quizid=='47mof')
{
    $course = 47;
    redirect(new moodle_url("http://training-uat.phuhunglife.com/report/report/completion/index.php?cohortid=$cohortid11&course=$course&format=excelcsv"));
}
//$context = context_course::instance($course->id);

$url = new moodle_url('/phlcohort/report/completion/quiz.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');

$excel = ($format == 'excelcsv');
$csv = ($format == 'csv' || $excel);

// Load CSV library
if ($csv) {
    require_once("{$CFG->libdir}/csvlib.class.php");
}



// Check permissions
//require_login($course);

//require_capability('report/completion:view', $context);

// Get group mode

//echo $CFG->mofexams[$course->id][0].$CFG->mofexams[$course->id][1].in_array($course->id,$CFG->mofids);
/*Custom code*/
$sql = "
select distinct c.fullname
from mdl232x0_course c, mdl232x0_enrol e,
mdl232x0_user_enrolments ue, mdl232x0_user u, mdl232x0_cohort_members cm,
mdl232x0_cohort co, mdl232x0_quiz q
where co.id = cm.cohortid
and cm.userid = u.id
and u.id = ue.userid
and ue.enrolid = e.id
and e.courseid = c.id
and q.course = c.id
and q.id = $quizid
";
$get_name_file = $DB->get_record_sql($sql,array());
foreach($get_name_file as $rec){
    $name = $rec->fullname;
}
if ($csv) {
    
    //$shortname = format_string($course->shortname, true, array('context' => $context));
    //$shortname = preg_replace('/[^a-z0-9-]/', '_',core_text::strtolower(strip_tags($shortname)));
    
    $export = new csv_export_writer();
    $export->set_filename("default");
    
}
// The CSV headers
$row = array();
$row[] = "Họ Tên";
$row[] = "Số Điện Thoại";
$row[] = "CMTND";
$row[] = "Mã Lớp";
$row[] = "Thời gian hoàn thành";
$row[] = "Số điểm đạt được";
$row[] = "Kết quả";
//$row[] = "Lần thực hiện";
$row[] = "AD";
$row[] = "Văn Phòng";
$row[] = "Mã Đại Lý";
$row[] = "Điểm";
$row[] = "Supervisor";
$row[] = "Văn phòng ADO";
$row[] = "Khu vực";
$row[] = "TD";
$row[] = "RD";
$row[] = "AD";
$export->add_data($row);
global $DB;

    $sql="select u.id,u.firstname + ' ' + u.lastname as fullname, u.username,u.phone1, c.idnumber, q.timefinish,q.sumgrades,q.attempt ";
    $sql.="from mdl232x0_quiz_attempts q";
    $sql.=" left join mdl232x0_user u on u.id=q.userid";
    $sql.=" left join mdl232x0_cohort_members cm on cm.userid=u.id";
    $sql.=" left join mdl232x0_cohort c on c.id=cm.cohortid , mdl232x0_grade_items gi";
    $sql.=" where c.idnumber LIKE N'%$cohortid11%'";
    $sql.=" and q.quiz=".$quizid;
    $sql.=" and q.quiz = gi.iteminstance " ;
    $sql.=" and q.timefinish in (select max(o.timefinish)
from mdl232x0_quiz_attempts o where o.userid = u.id
and o.quiz = q.quiz
)";
    $sql.=" order by c.idnumber,u.id desc";
    //echo $sql;
    
    $records = $DB->get_recordset_sql($sql,array());
    $quiz=GetQuizByID($quizid);
    $gradepass=$quiz->gradepass;
    //var_dump($records);
    foreach ($records as $record){
        $row = array();
        $row[] = $record->fullname;
        $newUser=GetUserProfileField($record->id);
        $row[] = "'".$newUser->phone1;
        $row[] = "'".$newUser->profile['cmtnd'];
        $row[] = $record->idnumber;
        $row[] = userdate($record->timefinish, get_string('strftimedatetimeshort', 'langconfig'));
        $row[] = $record->sumgrades;
        $row[] = ($record->sumgrades>=$gradepass?"Đạt":"Rớt");
        //$row[] = $record->attempt;
        
        $row[] = $newUser->profile['DA'];
        $row[] = $newUser->profile['vanphong'];
        //echo $newUser->profile['cmtnd']."AAA";
        $ADO = GetADO_DATA($newUser->profile['cmtnd']);
        if (isset($ADO)){
            $row[] = $ADO->agent_code;
            $row[] = $ADO->grade;
            $row[] = $ADO->supervisor;
            $row[] = $ADO->office;
            $row[] = $ADO->area;
            $row[] = $ADO->lmao1;
            $row[] = $ADO->lmao2;
            $row[] = $ADO->lmao3;
        }
        //$agentInfo=GetAgentInfo($newUser->profile['cmtnd']);
        //if (isset($agentInfo)){
        //    $row[] = $agentInfo->agent_number;
        //    $row[] = $agentInfo->agent_name;
        //    $row[] = $agentInfo->supervisor_code;
        //    $row[] = $agentInfo->supervisor_name;
        //    $row[] = $agentInfo->parent_supervisor_code;
        //    $row[] = $agentInfo->parent_supervisor_name;
        //    $row[] = $agentInfo->area_name;
        //    $row[] = $agentInfo->sales_unit;
        //}
        $export->add_data($row);
    }

$sql="select u.id,u.firstname + ' ' + u.lastname as fullname, u.username,u.phone1, c.idnumber from ";
$sql.="mdl232x0_user u ";
$sql.="left join mdl232x0_cohort_members cm on cm.userid=u.id ";
$sql.="left join mdl232x0_cohort c on c.id=cm.cohortid ";
$sql.="where u.id not in( ";
$sql.="select u.id ";
$sql.="from mdl232x0_quiz_attempts q ";
$sql.="left join mdl232x0_user u on u.id=q.userid ";
$sql.="left join mdl232x0_cohort_members cm on cm.userid=u.id ";
$sql.="left join mdl232x0_cohort c on c.id=cm.cohortid ";
$sql.=" where c.idnumber LIKE N'%$cohortid11%' ";
$sql.=" and [state]='finished' ";
$sql.=" and quiz=".$quizid.") ";
$sql.=" and c.idnumber LIKE N'%$cohortid11%' ";
$records2 = $DB->get_recordset_sql($sql);
foreach ($records2 as $record){
    $row = array();
    $row[] = $record->fullname;
    $newUser=GetUserProfileField($record->id);
    $row[] = "'".$newUser->phone1;
    $row[] = "'".$newUser->profile['cmtnd'];
    $row[] = $record->idnumber;
    $row[] = "";
    $row[] = "";
    $row[] = "Không Đạt";
    //$row[] = $record->attempt;
    
    $row[] = $newUser->profile['DA'];
    $row[] = $newUser->profile['vanphong'];
    //echo $newUser->profile['cmtnd']."AAA";
    $ADO = GetADO_DATA($newUser->profile['cmtnd']);
    if (isset($ADO))
    {
        $row[] = $ADO->agent_code;
        $row[] = $ADO->grade;
        $row[] = $ADO->supervisor;
        $row[] = $ADO->office;
        $row[] = $ADO->area;
        $row[] = $ADO->lmao1;
        $row[] = $ADO->lmao2;
        $row[] = $ADO->lmao3;
    }
    //$agentInfo=GetAgentInfo($newUser->profile['cmtnd']);
    //if (isset($agentInfo)){
    //    $row[] = $agentInfo->agent_number;
    //    $row[] = $agentInfo->agent_name;
    //    $row[] = $agentInfo->supervisor_code;
    //    $row[] = $agentInfo->supervisor_name;
    //    $row[] = $agentInfo->parent_supervisor_code;
    //    $row[] = $agentInfo->parent_supervisor_name;
    //     $row[] = $agentInfo->area_name;
    //    $row[] = $agentInfo->sales_unit;
    //}
    $export->add_data($row);
}

if ($csv) {
    
    //$export->add_data($row);
    $export->download_file();
}
if ($csv) {
    
    //$export->add_data($row);
    $export->download_file();
}



// Trigger a report viewed event.
$event = \report_completion\event\report_viewed::create(array('context' => $context));
$event->trigger();

function GetADO_DATA($username)
{
    global $DB;
    $records = $DB->get_records_sql("SELECT agent_code,grade,supervisor,office,area,TD as lmao1,RD as lmao2,AD as lmao3 FROM ADO_DATA where idnumber like '%".$username."%'");
    foreach ($records as $record){
        return $record;
    }
}

function GetAgentInfo($username)
{
    global $DB;
    $records = $DB->get_records_sql("SELECT * FROM Agents_Info where  ID_Number like '%".$username."%'");
    foreach ($records as $record){
        return $record;
    }
    
}
function GetQuizByID($quizid)
{
    global $DB;
    $records = $DB->get_records_sql("select * from mdl232x0_grade_items where iteminstance=".$quizid);
    foreach ($records as $record){
        return $record;
    }
    
}

function GetUserProfileField($userid)
{
    global $DB;
    
    require_once(__DIR__.'/../../../user/profile/lib.php');
    $newUser = $DB->get_record('user', array('id' => $userid));
    profile_load_custom_fields($newUser);
    return $newUser;
}



function GetTimeStamp($MySqlDate,$isDateTo)
{
    /*
     Take a date in yyyy-mm-dd format and return it to the user in a PHP timestamp
     Robin 06/10/1999
     */
    
    
    $date_array = explode("/",$MySqlDate); // split the array
    
    $var_year = $date_array[2];
    $var_month = $date_array[1];
    $var_day = $date_array[0];
    /*
     if($isDateTo)
     {
     $var_day +=1;
     if($var_month = 12)
     {
     $var_month=1;
     $var_year+=1;
     }
     }
     
     return strtotime($var_year."-".$var_month."-".$var_day);
     */
    
    $temp=date("Y-m-d", strtotime($var_year."-".$var_month."-".$var_day));
    if($isDateTo)
        $var_timestamp = mktime(23,59,59,$var_month,$var_day,$var_year);
        else
            $var_timestamp = mktime(0,0,1,$var_month,$var_day,$var_year);
            //return($var_day); // return it to the user
            //echo $var_timestamp."XXX";
            return $var_timestamp;
}
