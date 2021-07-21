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
$strheading = get_string('dt', 'cohort');
$PAGE->navbar->add($strheading);
$date = $_POST["day"];
$month = $_POST["month"];
$year = $_POST["year"];
$malop = $_POST["malop"];
$chuongtrinh = $_POST["chuongtrinh"];
echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($strheading, 'uploadcohorts', 'cohort');
echo "<form action='dt_report.php' method='post'>
<div class='fcontainer clearfix' id='yui_3_17_2_1_1626599077576_192'>
<div class='col-md-12' id='yui_3_17_2_1_1626599077576_191'>
    <div class='col-md-6'><div class='form-group row  fitem   '>
    <div class='col-md-3'>
        <span class='pull-xs-right text-nowrap'>
        
        
        
        </span>
        <label class='col-form-label d-inline ' for='id_chuongtrinh'>
            KhÃ³a há»c
        </label>
    </div>
    <div class='col-md-9 form-inline felement' data-fieldtype='text'>
        <input type='text' class='form-control ' name='chuongtrinh' id='id_chuongtrinh' value='$chuongtrinh' size='50' maxlength='254' width='283'>
        <div class='form-control-feedback' id='id_error_chuongtrinh' style='display: none;'>
        
        </div>
    </div>
</div></div>
    </div>
    <div class='col-md-12' id='yui_3_17_2_1_1626599077576_191'>
  <div class='col-md-6'><div class='form-group row  fitem   ' data-groupname='ngayhoctu'>
      <div class='col-md-3'>
          <span class='pull-xs-right text-nowrap'>
          
          
          
          </span>
          <label class='col-form-label d-inline ' for='id_ngayhoctu'>NgÃ y táº¡o lá»›p</label>
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
            <option value='$date'>$date</option>
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
            <option value='$month'>ThÃ¡ng $month</option>
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
<option value='$year'>$year</option>
          <option value='1900'>1900</option>
          <option value='1901'>1901</option>
          <option value='1902'>1902</option>
          <option value='1903'>1903</option>
          <option value='1904'>1904</option>
          <option value='1905'>1905</option>
          <option value='1906'>1906</option>
          <option value='1907'>1907</option>
          <option value='1908'>1908</option>
          <option value='1909'>1909</option>
          <option value='1910'>1910</option>
          <option value='1911'>1911</option>
          <option value='1912'>1912</option>
          <option value='1913'>1913</option>
          <option value='1914'>1914</option>
          <option value='1915'>1915</option>
          <option value='1916'>1916</option>
          <option value='1917'>1917</option>
          <option value='1918'>1918</option>
          <option value='1919'>1919</option>
          <option value='1920'>1920</option>
          <option value='1921'>1921</option>
          <option value='1922'>1922</option>
          <option value='1923'>1923</option>
          <option value='1924'>1924</option>
          <option value='1925'>1925</option>
          <option value='1926'>1926</option>
          <option value='1927'>1927</option>
          <option value='1928'>1928</option>
          <option value='1929'>1929</option>
          <option value='1930'>1930</option>
          <option value='1931'>1931</option>
          <option value='1932'>1932</option>
          <option value='1933'>1933</option>
          <option value='1934'>1934</option>
          <option value='1935'>1935</option>
          <option value='1936'>1936</option>
          <option value='1937'>1937</option>
          <option value='1938'>1938</option>
          <option value='1939'>1939</option>
          <option value='1940'>1940</option>
          <option value='1941'>1941</option>
          <option value='1942'>1942</option>
          <option value='1943'>1943</option>
          <option value='1944'>1944</option>
          <option value='1945'>1945</option>
          <option value='1946'>1946</option>
          <option value='1947'>1947</option>
          <option value='1948'>1948</option>
          <option value='1949'>1949</option>
          <option value='1950'>1950</option>
          <option value='1951'>1951</option>
          <option value='1952'>1952</option>
          <option value='1953'>1953</option>
          <option value='1954'>1954</option>
          <option value='1955'>1955</option>
          <option value='1956'>1956</option>
          <option value='1957'>1957</option>
          <option value='1958'>1958</option>
          <option value='1959'>1959</option>
          <option value='1960'>1960</option>
          <option value='1961'>1961</option>
          <option value='1962'>1962</option>
          <option value='1963'>1963</option>
          <option value='1964'>1964</option>
          <option value='1965'>1965</option>
          <option value='1966'>1966</option>
          <option value='1967'>1967</option>
          <option value='1968'>1968</option>
          <option value='1969'>1969</option>
          <option value='1970'>1970</option>
          <option value='1971'>1971</option>
          <option value='1972'>1972</option>
          <option value='1973'>1973</option>
          <option value='1974'>1974</option>
          <option value='1975'>1975</option>
          <option value='1976'>1976</option>
          <option value='1977'>1977</option>
          <option value='1978'>1978</option>
          <option value='1979'>1979</option>
          <option value='1980'>1980</option>
          <option value='1981'>1981</option>
          <option value='1982'>1982</option>
          <option value='1983'>1983</option>
          <option value='1984'>1984</option>
          <option value='1985'>1985</option>
          <option value='1986'>1986</option>
          <option value='1987'>1987</option>
          <option value='1988'>1988</option>
          <option value='1989'>1989</option>
          <option value='1990'>1990</option>
          <option value='1991'>1991</option>
          <option value='1992'>1992</option>
          <option value='1993'>1993</option>
          <option value='1994'>1994</option>
          <option value='1995'>1995</option>
          <option value='1996'>1996</option>
          <option value='1997'>1997</option>
          <option value='1998'>1998</option>
          <option value='1999'>1999</option>
          <option value='2000'>2000</option>
          <option value='2001'>2001</option>
          <option value='2002'>2002</option>
          <option value='2003'>2003</option>
          <option value='2004'>2004</option>
          <option value='2005'>2005</option>
          <option value='2006'>2006</option>
          <option value='2007'>2007</option>
          <option value='2008'>2008</option>
          <option value='2009'>2009</option>
          <option value='2010'>2010</option>
          <option value='2011'>2011</option>
          <option value='2012'>2012</option>
          <option value='2013'>2013</option>
          <option value='2014'>2014</option>
          <option value='2015'>2015</option>
          <option value='2016'>2016</option>
          <option value='2017'>2017</option>
          <option value='2018'>2018</option>
          <option value='2019'>2019</option>
          <option value='2020'>2020</option>
          <option value='2021'>2021</option>
          <option value='2022'>2022</option>
          <option value='2023'>2023</option>
          <option value='2024'>2024</option>
          <option value='2025'>2025</option>
          <option value='2026'>2026</option>
          <option value='2027'>2027</option>
          <option value='2028'>2028</option>
          <option value='2029'>2029</option>
          <option value='2030'>2030</option>
          <option value='2031'>2031</option>
          <option value='2032'>2032</option>
          <option value='2033'>2033</option>
          <option value='2034'>2034</option>
          <option value='2035'>2035</option>
          <option value='2036'>2036</option>
          <option value='2037'>2037</option>
          <option value='2038'>2038</option>
          <option value='2039'>2039</option>
          <option value='2040'>2040</option>
          <option value='2041'>2041</option>
          <option value='2042'>2042</option>
          <option value='2043'>2043</option>
          <option value='2044'>2044</option>
          <option value='2045'>2045</option>
          <option value='2046'>2046</option>
          <option value='2047'>2047</option>
          <option value='2048'>2048</option>
          <option value='2049'>2049</option>
          <option value='2050'>2050</option>
      </select>
      </span>
      <div class='form-control-feedback' id='id_error_year' style='display: none;'>
      
      </div>
  </div>
              &nbsp;
              <a class='visibleifjs' name='ngayhoctu[calendar]' href='#' id='id_ngayhoctu_calendar'><i class='icon fa fa-calendar fa-fw ' aria-hidden='true' title='Lá»‹ch' aria-label='Lá»‹ch'></i></a>
          </span>
          <div class='form-control-feedback' id='id_error_' style='display: none;'>
          
          </div>
      </div>
  </div></div>
    </div>
		<div class='col-md-12' id='yui_3_17_2_1_1626599077576_191'>
    <div class='col-md-6'><div class='form-group row  fitem   '>
    <div class='col-md-3'>
        <span class='pull-xs-right text-nowrap'>
        
        
        
        </span>
        <label class='col-form-label d-inline ' for='id_malop'>
            MaÌƒ LÆ¡Ìp
        </label>
    </div>
    <div class='col-md-9 form-inline felement' data-fieldtype='text'>
        <input type='text' class='form-control ' name='malop' id='id_malop' value='$malop' size='50' maxlength='254' width='283'>
        <div class='form-control-feedback' id='id_error_malop' style='display: none;'>
        
        </div>
    </div>
</div></div>
    </div>
		</div>
<input type='submit' value='Submit'>
</form>";

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
<th class='header c1 centeralign' style='' scope='col'>ChuÆ¡ng trÃ¬nh</th>
<th class='header c1 centeralign' style='' scope='col'>MÃ£ lá»›p</th>
<th class='header c1 centeralign' style='' scope='col'>NgÃ y báº¯t Ä‘áº§u</th>
<th class='header c1 centeralign' style='' scope='col'>NgÃ y káº¿t thÃºc</th>
<th class='header c1 centeralign' style='' scope='col'>MÃ£ thi chá»¯</th>
<th class='header c1 centeralign' style='' scope='col'>Há» & tÃªn</th>
<th class='header c1 centeralign' style='' scope='col'>NgÃ y sinh</th>
<th class='header c1 centeralign' style='' scope='col'>Giá»›i tÃ­nh</th>
<th class='header c1 centeralign' style='' scope='col'>CMTND</th>
<th class='header c1 centeralign' style='' scope='col'>Online</th>
</tr>
</thead>
<tbody>";
if((empty($date)||empty($month)||empty($year)) && empty($malop) && empty($chuongtrinh)){
    echo "<tr class=''>
<td class='cell c0' style=''></td>
<td class='cell c1' style=''></td>
<td class='cell c2' style=''></td>
<td class='cell c3' style=''></td>
<td class='cell c4' style=''></td>
<td class='cell c5' style=''></td>
<td class='cell c6' style=''></td>
<td class='cell c7' style=''></td>
<td class='cell c8' style=''></td>
<td class='cell c9' style=''></td>
<td class='cell c10' style=''></td>
<td class='cell c11' style=''></td>
</tr>";
}

else {
    $sql ="SELECT s.idnumber chuongtrinh
	, c.name malop
	, c.datestart ngaybatdau
	, c.dateend ngayketthuc
	, c.idnumber mathichu
	, m.testcode makythi
	, m.datetest ngaythi
	, c.cancel_flag trackingtype
	, u.fullname hoten
	, u.ngaysinh
	, u.thangsinh
	, u.namsinh
	, u.gioitinh
	, u.cmnd
        
	, '' quanlytructiep
	, '' chucdanh
	, c.office vanphong
	, c.area mien
	, '' TD
	, '' AD
        
	, c_m.date_1, c_m.date_2, c_m.date_3, c_m.date_4, c_m.date_5, c_m.date_6, c_m.participate_condition dudieukien
	, '' as thithucte
	, m.score diemthi
	, '' as solanthidat
	, c.online
        
FROM vUser_Info u
INNER JOIN mdl232x0_cohort_members c_m ON u.id = c_m.userid
INNER JOIN mdl232x0_course_completions c_c ON c_c.userid = c_m.userid
INNER JOIN mdl232x0_cohort c ON c.id = c_m.cohortid
INNER JOIN mdl232x0_course s ON s.id = c_c.course
        
LEFT JOIN mdl232x0_mof_phl m ON m.idnumber = u.cmnd
        
WHERE u.deleted = 0 AND c.id NOT IN (326, 602)";
    if(!empty($date)&&!empty($month)&&!empty($year)){
        $sql .= " AND DATEADD(s, u.timecreated, '19700101') >= '$year-$month-$date'";
    }
    
    if(!empty($malop))
    {
        $sql .= " AND c.name = N'$malop'";
    }
    if(!empty($chuongtrinh))
    {
        $sql .= " AND s.idnumber = N'$chuongtrinh'";
    }
    $dt_array = $DB->get_recordset_sql($sql,array());
}
foreach ($dt_array as $dt) {
    if(empty($dt->ngaybatdau)){
        $ngaybatdau = '';
    }
    else{
        $ngaybatdau = date("d/m/Y",$dt->ngaybatdau);
    }
    if(empty($dt->ngayketthuc)){
        $ngayketthuc = '';
    }
    else{
        $ngayketthuc = date("d/m/Y",$dt->ngayketthuc);
    }
    if($dt->ngayketthuc==1)
    {
        $online = "CÃ³";
    }
    else {
        $online = "KhÃ´ng";
    }
    
    echo "<tr class=''>
<td class='cell c0' style=''>$dt->chuongtrinh</td>
<td class='cell c1' style=''>$dt->malop</td>
<td class='cell c2' style=''>$ngaybatdau</td>
<td class='cell c3' style=''>$ngayketthuc</td>
<td class='cell c4' style=''>$dt->mathichu</td>
<td class='cell c6' style=''>$dt->hoten</td>
<td class='cell c7' style=''>$dt->ngaysinh/$dt->thangsinh/$dt->namsinh</td>
<td class='cell c8' style=''>$dt->gioitinh</td>
<td class='cell c9' style=''>$dt->cmnd</td>
<td class='cell c10' style=''>$online</td>
</tr>";
}
if(!empty($date)||!empty($month)||!empty($year)){
    $dt_array->close();
}

echo "
</tbody>
</table>";

echo $OUTPUT->footer();



