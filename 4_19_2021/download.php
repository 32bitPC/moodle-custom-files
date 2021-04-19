<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');
require('../../../config.php');
require($CFG->dirroot.'/cohort/lib.php');
require($CFG->dirroot.'/phlcohort/lib.php');
require_once($CFG->libdir.'/adminlib.php');


if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

$cohortid=optional_param('id',0, PARAM_INT);
$context=optional_param('context','', PARAM_RAW);
$trainer=optional_param('trainer','', PARAM_RAW);

require_login();


if($context=='members' && $cohortid>0)
{

	$listUserID='';
	if($SESSION->bulk_users_phl!=null)
	{
		$listUserID=implode(',',$SESSION->bulk_users_phl);
	}

	$sql = "SELECT u.id,CONCAT(u.firstname,' ',u.lastname) as uname,u.email,u.phone1,u.username,cm.timeadded,c.idnumber,FORMAT(dateadd(SECOND, ch.ngayhoc, '1/1/1970'),'dd/MM/yyyy') as ngayhoc,co.fullname as khoahoc,cohortmemberid,CONCAT(' ','') as sign,CONCAT(' ','') as code,CONCAT('$trainer','') as trainer,tenkhuvuc,tenquanhuyen  
             FROM {cohort} c
             JOIN {cohort_members} cm ON c.id=cm.cohortid
             JOIN {cohortphl} ch ON c.id=ch.cohortid
             JOIN {course} co ON ch.khoahoc = co.id              
             JOIN {user} u ON cm.userid=u.id
             JOIN {cohortphl_quanhuyen} qh ON ch.khuvuc = qh.id 
             JOIN {cohortphl_khuvuc} kv ON qh.khuvuc = kv.id              
             JOIN {cohortphl_thamdu} ck on cm.id=ck.cohortmemberid";
             if($cohortid>0)
             {	
             	if($listUserID!='')
                	$sql.=" WHERE c.visible=1 AND ch.cohortid=? AND u.id in (".$listUserID.")";
                else
                	$sql.=" WHERE c.visible=1 AND ch.cohortid=?";
             }
             else
             {
             	if($listUserID!='')
                	$sql.=" WHERE c.visible=1 AND u.id in (".$listUserID.")";
                else
                	$sql.=" WHERE c.visible=1";
             }
                
             
     $users=$DB->get_records_sql($sql,array('cohortid' =>$cohortid));

     if($cohortid!=2629){
         $objPHPExcel = PHPExcel_IOFactory::load($CFG->dirroot.'/phlcohort/csv/Form_2_col.xlsx');$i=10;
         foreach ($users as $user) {
                 $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A3','Lá»šP: '.$user->idnumber)
                 ->setCellValue('A4','Thá»�i gian há»�c: '.$user->ngayhoc)
                 ->setCellValue('A5','Ä�á»‹a Ä‘iá»ƒm huáº¥n luyá»‡n: '.$user->tenkhuvuc.' - '.$user->tenquanhuyen)
                 ->setCellValue('A6','ChuyÃªn viÃªn huáº¥n luyá»‡n:: '.$user->trainer)
                 ->setCellValue('A'.$i,$i-9)
                 ->setCellValue('B'.$i,$user->uname)
                 ->setCellValue('C'.$i,$cohortid)
                 ->setCellValue('D'.$i,'\''.$user->username)
                 ->setCellValue('F'.$i,'')
                 ->setCellValue('G'.$i,'');
                 $i++;
             }
         $objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('B10'),'A10:G'.$i);// Set active sheet index to the first sheet, so Excel opens this as the first sheet
         $objPHPExcel->setActiveSheetIndex(0);header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename="Members - '.$cohortid.'.xlsx"');
         header('Cache-Control: max-age=0');
         header('Cache-Control: max-age=1');
         header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
         header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
         header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
         header ('Pragma: public'); // HTTP/1.0
     }
     else{
         $objPHPExcel = PHPExcel_IOFactory::load($CFG->dirroot.'/phlcohort/csv/Form_4_col.xlsx');$i=10;
         foreach ($users as $user) {
             $objPHPExcel->setActiveSheetIndex(0)
             ->setCellValue('A3','Lá»šP: '.$user->idnumber)
             ->setCellValue('A4','Thá»�i gian há»�c: '.$user->ngayhoc)
             ->setCellValue('A5','Ä�á»‹a Ä‘iá»ƒm huáº¥n luyá»‡n: '.$user->tenkhuvuc.' - '.$user->tenquanhuyen)
             ->setCellValue('A6','ChuyÃªn viÃªn huáº¥n luyá»‡n:: '.$user->trainer)
             ->setCellValue('A'.$i,$i-9)
             ->setCellValue('B'.$i,$user->uname)
             ->setCellValue('C'.$i,$cohortid)
             ->setCellValue('D'.$i,'\''.$user->username)
             ->setCellValue('F'.$i,'')
             ->setCellValue('G'.$i,'')
             ->setCellValue('H'.$i,'')
             ->setCellValue('I'.$i,'');
             $i++;
         }
         $objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('B10'),'A10:I'.$i); $objPHPExcel->setActiveSheetIndex(0);
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename="Members - '.$cohortid.'.xlsx"');
         header('Cache-Control: max-age=0');
         header('Cache-Control: max-age=1');
         header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
         header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
         header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
         header ('Pragma: public'); // HTTP/1.0
     }
     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
     $objWriter->save('php://output');
     exit;
}
