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
require_once($CFG->libdir . '/adminlib.php');
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
//require_capability('moodle/cohort:manage', $context);

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
$strheading = 'Xuáº¥t bÃ¡o cÃ¡o';
$PAGE->navbar->add($strheading);
$date = $_POST["day"];
$month = $_POST["month"];
$year = $_POST["year"];
$date1 = $_POST["day1"];
$month1 = $_POST["month1"];
$year1 = $_POST["year1"];

$current_day = date("d");
$current_month = date("m");
$current_year = date("Y");

$current_day1 = date("d");
$current_month1 = date("m");
$current_year1 = date("Y");
if(!empty($date)&&!empty($month)&&!empty($year)&&!empty($date1)&&!empty($month1)&&!empty($year1))
{
    $current_day = $date;
    $current_month = $month;
    $current_year = $year;
    $current_day1 = $date1;
    $current_month1 = $month1;
    $current_year1 = $year1;
}
echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($strheading, 'uploadcohorts', 'cohort');
echo "<form action='./download_dt_general.php' method='post'>
<div class='fcontainer clearfix' id='yui_3_17_2_1_1626599077576_192'>
    <div class='col-md-12' id='yui_3_17_2_1_1626599077576_191'>
  <div class='col-md-6'><div class='form-group row  fitem   ' data-groupname='ngayhoctu'>
      <div class='col-md-3'>
          <span class='pull-xs-right text-nowrap'>
          
          
          
          </span>
          <label class='col-form-label d-inline ' for='id_ngayhoctu'>Tá»« ngÃ y</label>
      </div>
      <div class='col-md-9 form-inline felement' data-fieldtype='date_selector'>
          <span class='fdate_selector' id='yui_3_17_2_1_1626599077576_114'>
          
              <div class='form-group  fitem  '>
      <label class='col-form-label sr-only' for='id_ngayhoctu_day'>
          NgÃ y
          
          
      </label>
      <span data-fieldtype='select'>
      <select class='custom-select
      
                     ' name='day' id='id_ngayhoctu_day'>
            <option value='$current_day' selected>$current_day</option>
<option value=''></option>
          <option value='1'>1</option>
          <option value='2'>2</option>
          <option value='3'>3</option>
          <option value='4'>4</option>
          <option value='5'>5</option>
          <option value='6'>6</option>
          <option value='7'>7</option>
          <option value='8'>8</option>
          <option value='9'>9</option>
          <option value='10'>10</option>
          <option value='11'>11</option>
          <option value='12'>12</option>
          <option value='13'>13</option>
          <option value='14'>14</option>
          <option value='15'>15</option>
          <option value='16'>16</option>
          <option value='17'>17</option>
          <option value='18'>18</option>
          <option value='19'>19</option>
          <option value='20'>20</option>
          <option value='21'>21</option>
          <option value='22'>22</option>
          <option value='23'>23</option>
          <option value='24'>24</option>
          <option value='25'>25</option>
          <option value='26'>26</option>
          <option value='27'>27</option>
          <option value='28'>28</option>
          <option value='29'>29</option>
          <option value='30'>30</option>
          <option value='31'>31</option>
      </select>
      </span>
      <div class='form-control-feedback' id='id_error_day' style='display: none;'>
      
      </div>
  </div>
              &nbsp;
              <div class='form-group  fitem  '>
      <label class='col-form-label sr-only' for='id_ngayhoctu_month'>
          ThÃ¡ng
          
          
      </label>
      <span data-fieldtype='select'>
      <select class='custom-select
      
                     ' name='month' id='id_ngayhoctu_month'>
<option value='$current_month' selected>ThÃ¡ng $current_month</option>
<option value=''></option>
          <option value='1'>ThÃ¡ng 1</option>
          <option value='2'>ThÃ¡ng 2</option>
          <option value='3'>ThÃ¡ng 3</option>
          <option value='4'>ThÃ¡ng 4</option>
          <option value='5'>ThÃ¡ng 5</option>
          <option value='6'>ThÃ¡ng 6</option>
          <option value='7'>ThÃ¡ng 7</option>
          <option value='8'>ThÃ¡ng 8</option>
          <option value='9'>ThÃ¡ng 9</option>
          <option value='10'>ThÃ¡ng 10</option>
          <option value='11'>ThÃ¡ng 11</option>
          <option value='12'>ThÃ¡ng 12</option>
      </select>
      </span>
      <div class='form-control-feedback' id='id_error_month' style='display: none;'>
      
      </div>
  </div>
              &nbsp;
              <div class='form-group  fitem  '>
      <label class='col-form-label sr-only' for='id_ngayhoctu_year'>
          NÄƒm
          
          
      </label>
      <span data-fieldtype='select'>
      <select class='custom-select
      
                     ' name='year' id='id_ngayhoctu_year'>
<option value='$current_year' selected>$current_year</option>
<option value=''></option>
          <option value='2018'>2018</option>
          <option value='2019'>2019</option>
          <option value='2020'>2020</option>
          <option value='2021'>2021</option>
          <option value='2022'>2022</option>
          <option value='2023'>2023</option>
          <option value='2024'>2024</option>
          <option value='2025'>2025</option>
      </select>
      </span>
      <div class='form-control-feedback' id='id_error_year' style='display: none;'>
      
      </div>
  </div>
              &nbsp;
              <a class='visibleifjs' name='ngayhoctu[calendar]' href='#' id='id_ngayhoctu_calendar'><i class='icon fa fa-calendar fa-fw ' aria-hidden='true' title='LÃƒÆ’Ã‚Â¡Ãƒâ€šÃ‚Â»ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¹ch' aria-label='LÃƒÆ’Ã‚Â¡Ãƒâ€šÃ‚Â»ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¹ch'></i></a>
          </span>
          <div class='form-control-feedback' id='id_error_' style='display: none;'>
          
          </div>
      </div>
  </div></div>
<div class='col-md-6'><div class='form-group row  fitem   ' data-groupname='ngayhoctu'>
      <div class='col-md-3'>
          <span class='pull-xs-right text-nowrap'>
          
          
          
          </span>
          <label class='col-form-label d-inline ' for='id_ngayhoctu'>Äáº¿n ngÃ y</label>
      </div>
      <div class='col-md-9 form-inline felement' data-fieldtype='date_selector'>
          <span class='fdate_selector' id='yui_3_17_2_1_1626599077576_114'>
          
              <div class='form-group  fitem  '>
      <label class='col-form-label sr-only' for='id_ngayhoctu_day1'>
          Äáº¿n ngÃ y
          
          
      </label>
      <span data-fieldtype='select'>
      <select class='custom-select
      
                     ' name='day1' id='id_ngayhoctu_day1'>
            <option value='$current_day1' selected>$current_day1</option>
<option value=''></option>
          <option value='1'>1</option>
          <option value='2'>2</option>
          <option value='3'>3</option>
          <option value='4'>4</option>
          <option value='5'>5</option>
          <option value='6'>6</option>
          <option value='7'>7</option>
          <option value='8'>8</option>
          <option value='9'>9</option>
          <option value='10'>10</option>
          <option value='11'>11</option>
          <option value='12'>12</option>
          <option value='13'>13</option>
          <option value='14'>14</option>
          <option value='15'>15</option>
          <option value='16'>16</option>
          <option value='17'>17</option>
          <option value='18'>18</option>
          <option value='19'>19</option>
          <option value='20'>20</option>
          <option value='21'>21</option>
          <option value='22'>22</option>
          <option value='23'>23</option>
          <option value='24'>24</option>
          <option value='25'>25</option>
          <option value='26'>26</option>
          <option value='27'>27</option>
          <option value='28'>28</option>
          <option value='29'>29</option>
          <option value='30'>30</option>
          <option value='31'>31</option>
      </select>
      </span>
      <div class='form-control-feedback' id='id_error_day1' style='display: none;'>
      
      </div>
  </div>
              &nbsp;
              <div class='form-group  fitem  '>
      <label class='col-form-label sr-only' for='id_ngayhoctu_month1'>
          ThÃ¡ng
          
          
      </label>
      <span data-fieldtype='select'>
      <select class='custom-select
      
                     ' name='month1' id='id_ngayhoctu_month1'>
            <option value='$current_month1' selected>ThÃ¡ng $current_month1</option>
<option value=''></option>
          <option value='1'>ThÃ¡ng 1</option>
          <option value='2'>ThÃ¡ng 2</option>
          <option value='3'>ThÃ¡ng 3</option>
          <option value='4'>ThÃ¡ng 4</option>
          <option value='5'>ThÃ¡ng 5</option>
          <option value='6'>ThÃ¡ng 6</option>
          <option value='7'>ThÃ¡ng 7</option>
          <option value='8'>ThÃ¡ng 8</option>
          <option value='9'>ThÃ¡ng 9</option>
          <option value='10'>ThÃ¡ng 10</option>
          <option value='11'>ThÃ¡ng 11</option>
          <option value='12'>ThÃ¡ng 12</option>      </select>
      </span>
      <div class='form-control-feedback' id='id_error_month1' style='display: none;'>
      
      </div>
  </div>
              &nbsp;
              <div class='form-group  fitem  '>
      <label class='col-form-label sr-only' for='id_ngayhoctu_year1'>
          NÄƒm
          
          
      </label>
      <span data-fieldtype='select'>
      <select class='custom-select
      
                     ' name='year1' id='id_ngayhoctu_year1'>
<option value='$current_year1' selected>$current_year1</option>
<option value=''></option>
          <option value='2018'>2018</option>
          <option value='2019'>2019</option>
          <option value='2020'>2020</option>
          <option value='2021'>2021</option>
          <option value='2022'>2022</option>
          <option value='2023'>2023</option>
          <option value='2024'>2024</option>
          <option value='2025'>2025</option>
      </select>
      </span>
      <div class='form-control-feedback' id='id_error_year1' style='display: none;'>
      
      </div>
  </div>
              &nbsp;
              <a class='visibleifjs' name='ngayhoctu[calendar]' href='#' id='id_ngayhoctu_calendar'><i class='icon fa fa-calendar fa-fw ' aria-hidden='true' title='LÃƒÂ¡Ã‚Â»Ã¢â‚¬Â¹ch' aria-label='LÃƒÂ¡Ã‚Â»Ã¢â‚¬Â¹ch'></i></a>
          </span>
          <div class='form-control-feedback' id='id_error_' style='display: none;'>
          
          </div>
      </div>
  </div></div>
    </div>
		</div>
<input type='text' class='form-control ' name='download_dt2' id='id_mof_cohort' value='$date' size='50' maxlength='254' width='283' hidden>
<input type='text' class='form-control ' name='download_dt3' id='id_mof_cohort' value='$month' size='50' maxlength='254' width='283' hidden>
<input type='text' class='form-control ' name='download_dt4' id='id_mof_cohort' value='$year' size='50' maxlength='254' width='283' hidden>

<input type='text' class='form-control ' name='download_dt5' id='id_mof_cohort' value='$date1' size='50' maxlength='254' width='283' hidden>
<input type='text' class='form-control ' name='download_dt6' id='id_mof_cohort' value='$month1' size='50' maxlength='254' width='283' hidden>
<input type='text' class='form-control ' name='download_dt7' id='id_mof_cohort' value='$year1' size='50' maxlength='254' width='283' hidden>

<input type='text' class='form-control ' name='download_dt' id='id_mof_cohort' value='$malop' size='50' maxlength='254' width='283' hidden>
<input type='text' class='form-control ' name='download_dt1' id='id_mof_cohort' value='$chuongtrinh' size='50' maxlength='254' width='283' hidden>
<input type='submit' value='Download'>
</form>";
//echo dirname(__FILE__)."<br>";
echo $OUTPUT->footer();



