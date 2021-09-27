<?php
require_once dirname(__FILE__) . '/excel_reader/PHPExcel/Classes/PHPExcel.php';
require_once('../config.php');
function GetQuizByID($quizid)
{
    global $DB;
    $records = $DB->get_records_sql("select * from mdl232x0_grade_items where iteminstance=".$quizid);
    foreach ($records as $record){
        return $record;
    }
    
}
function custom_normal($chuongtrinh,$idnumber,$username)
{
    global $DB;
    if($chuongtrinh=='MOF2019')
    {
        $quiz_craved = 231;
    }
    if($chuongtrinh=='UL2019')
    {
        $quiz_craved = 183;
    }
    if($chuongtrinh=='PHTV')
    {
        $quiz_craved = 234;
    }
    if($chuongtrinh=='PHPL')
    {
        $quiz_craved = 235;
    }
    if($chuongtrinh=='UL')
    {
        $quiz_craved = 322;
    }
    if($chuongtrinh=='UL2021')
    {
        $quiz_craved = 324;
    }
    $quiz=GetQuizByID($quiz_craved);
    $gradepass=$quiz->gradepass;
    $nr_sql = "
    select u.id,u.firstname + ' ' + u.lastname as fullname, u.username,u.phone1, c.idnumber, q.timefinish,q.sumgrades,q.attempt
from mdl232x0_quiz_attempts q
left join mdl232x0_user u on u.id=q.userid
left join mdl232x0_cohort_members cm on cm.userid=u.id
left join mdl232x0_cohort c on c.id=cm.cohortid
where c.idnumber= N'$idnumber'
and [state]='finished'
and quiz=$quiz_craved
and u.username = N'$username'
order by sumgrades asc
";
    $listRecords=$DB->get_records_sql($nr_sql,array());
    foreach($listRecords as $lc)
    {
        if($lc->sumgrades >= $gradepass)
        {
            $lc1 = 1;
        }
        else {
            $lc1 = 0;
        }
    }
    return $lc1;
}

function custom_dt($idnumber,$username)
{
    global $DB;
    $lc_sql = "
       SELECT      CASE WHEN qa.sumgrades > 0 THEN qa.sumgrades ELSE 0 END AS grade1,
                         CASE WHEN
                             (SELECT        gradepass
                               FROM            mdl232x0_grade_items
                               WHERE        iteminstance = 305 AND courseid = 47) <= qa.sumgrades THEN 810 ELSE 69 END AS grade_status1,
                         CASE WHEN qa1.sumgrades > 0 THEN qa1.sumgrades ELSE 0 END AS grade2, CASE WHEN
                             (SELECT        gradepass
                               FROM            mdl232x0_grade_items
                               WHERE        iteminstance = 335 AND courseid = 47) <= qa1.sumgrades THEN 810 ELSE 69 END AS grade_status2
FROM            dbo.mdl232x0_cohort_members AS cm INNER JOIN
                         dbo.mdl232x0_user AS u ON u.id = cm.userid INNER JOIN
                         dbo.mdl232x0_cohort AS c ON c.id = cm.cohortid LEFT OUTER JOIN
                             (SELECT        userid, MAX(sumgrades) AS sumgrades
                               FROM            dbo.mdl232x0_quiz_attempts
                               WHERE        (quiz = 305)
                               GROUP BY userid) AS q ON q.userid = u.id LEFT OUTER JOIN
                         dbo.mdl232x0_quiz_attempts AS qa ON qa.sumgrades = q.sumgrades AND q.userid = qa.userid AND qa.quiz = 305 LEFT OUTER JOIN
                             (SELECT        userid, MAX(sumgrades) AS sumgrades
                               FROM            dbo.mdl232x0_quiz_attempts
                               WHERE        (quiz = 335)
                               GROUP BY userid) AS q1 ON q1.userid = u.id LEFT OUTER JOIN
                         dbo.mdl232x0_quiz_attempts AS qa1 ON qa1.sumgrades = q1.sumgrades AND q1.userid = qa1.userid AND qa1.quiz = 335
WHERE         (c.idnumber = N'$idnumber' and u.username = N'$username')
        ";
    $listRecords=$DB->get_records_sql($lc_sql,array());
    foreach($listRecords as $lc)
    {
        $lc1 = $lc->grade_status1. ' / '.$lc->grade_status2;
    }
    return $lc1;
}

$a1 = $_POST['day'];
$b1 = $_POST['month'];
$c1 = $_POST['year'];

$a2 = $_POST['day1'];
$b2 = $_POST['month1'];
$c2 = $_POST['year1'];

$a = $_POST['malop'];
$b = $_POST['chuongtrinh'];

$sql ="SELECT
		s.idnumber chuongtrinh
	, c.idnumber malop
	, c.datestart ngaybatdau
	, c.dateend ngayketthuc
	, c.testcode mathichu
	, mof.testcode makythi
	, mof.datetest ngaythi
	, c.cancel_flag trackingtype
	, u.fullname hoten
	, u.ngaysinh
	, u.thangsinh
	, u.namsinh
	, u.gioitinh
	, u.cmnd
    
	, ado.agent_code
	, c.trainer
	, ado.supervisor quanlytructiep
	, ado.grade chucdanh
	, ado.office vanphong
	, ado.area mien
	, ado.TD td_info
	, ado.RD rd_info
	, ado.AD ad_info
    
	, c_m.date_1, c_m.date_2, c_m.date_3, c_m.date_4, c_m.date_5, c_m.date_6, c_m.participate_condition dudieukien
	, [thithucte] = CASE WHEN mof.score > 0 THEN 1 ELSE NULL END
	, mof.score diemthi
	, [soluongthidat] = CASE WHEN mof.score >= 30 THEN 1 ELSE NULL END
	, c.online
    
FROM mdl232x0_mof_phl mof
	INNER JOIN vUser_Info u ON mof.idnumber = u.cmnd
	INNER JOIN mdl232x0_cohort c ON c.id = mof.cohortid
	INNER JOIN mdl232x0_course s ON s.id = c.courseid
	INNER JOIN mdl232x0_cohort_members c_m ON c_m.cohortid = c.id AND u.id = c_m.userid
	LEFT JOIN ADO_DATA ado ON ado.idnumber = mof.idnumber
    WHERE mof.idnumber IS NOT NULL
	";
if(!empty($a1)||!empty($b1)||!empty($c1)||!empty($a2)||!empty($b2)||!empty($c2)){
    $convert_first_date = strtotime($c1.'-'.$b1.'-'.$a1);
    $convert_second_date = strtotime($c2.'-'.$b2.'-'.$a2);
    $sql .= " AND c.datestart BETWEEN $convert_first_date AND $convert_second_date";
}

if(!empty($a))
{
    $sql .= " AND mof.testcode = N'$a'";
}
if(!empty($b))
{
    $check_cohort = $DB->get_records_sql('select * from {cohort} where name=?', array('name' =>$b));
    foreach ($check_cohort as $x) {
        $cohortid = $x->id;
    }
    if(empty($cohortid))
    {
        $cohortid = 0;
    }
    $sql .= " AND mof.cohortid = $cohortid";
}
//echo $sql;
$dt_array = $DB->get_recordset_sql($sql,array());


$i = 3;
// start the fun
$objPHPExcel = PHPExcel_IOFactory::load($CFG->dirroot.'/phlcohort/csv/DT_Report.xlsx');

foreach($dt_array as $dt){
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
    if(empty($dt->ngaythi)){
        $ngaythi = '';
    }
    else{
        $ngaythi = date("d/m/Y",$dt->ngaythi);
    }
    if($dt->online != 0)
    {
        $online_status = 1;
    }
    else {
        $online_status = '';
    }
    
    if($dt->chuongtrinh == 'PSS')
    {
        $lol = custom_dt($dt->malop,$dt->cmnd);
        if($lol == '810 / 810')
        {
            $lol = "1";
        }
        else {
            $lol = "0";
        }
    }
    else {
        $lol = custom_normal($dt->chuongtrinh,$dt->malop,$dt->cmnd);
    }
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i,''.$dt->chuongtrinh);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i,''.$dt->malop);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i,''.$ngaybatdau);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i,''.$ngayketthuc);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i,''.$dt->mathichu);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i,''.$dt->makythi);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i,''.$ngaythi);
    //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i,''.$lol);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i,''.$dt->hoten);
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i,''.$dt->ngaysinh);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i,''.$dt->thangsinh);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i,''.$dt->namsinh);
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$i,''.$dt->gioitinh);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$i,''.$dt->cmnd);
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$i,''.$dt->agent_code);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$i,''.$dt->trainer);
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$i,''.$dt->quanlytructiep);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.$i,''.$dt->chucdanh);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.$i,''.$dt->vanphong);
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T'.$i,''.$dt->mien);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.$i,''.$dt->td_info);
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('V'.$i,''.$dt->rd_info);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('W'.$i,''.$dt->ad_info);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('X'.$i,''.$lol);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y'.$i,''.$dt->date_1);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z'.$i,''.$dt->date_2);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA'.$i,''.$dt->date_3);
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB'.$i,''.$dt->date_4);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC'.$i,''.$dt->date_5);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD'.$i,''.$dt->date_6);
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE'.$i,''.$dt->dudieukien);
    
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF'.$i,''.$dt->thithucte);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG'.$i,''.$dt->diemthi);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AH'.$i,''.$dt->soluongthidat);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('AI'.$i,''.$online_status);
    
    $i++;
}
$objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('B3'),'A3:AH3');
$objPHPExcel->setActiveSheetIndex(0);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header('Content-Disposition: attachment;filename="PSS.xlsx"');
header('Content-Disposition: attachment;filename="baocaohuanluyen.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007')->save('php://output');
exit;

