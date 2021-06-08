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
 * List content in content bank.
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require('../config.php');

require_login();
$context = get_context_instance (CONTEXT_SYSTEM);
$roles = get_user_roles($context, $USER->id, false);
$role = key($roles);
$roleid = $roles[$role]->roleid;
if(!$roleid){
    echo "error";
}
require_once("$CFG->libdir/formslib.php");

class fileBlock extends moodleform {
    //Add elements to form
    function definition() {
        global $CFG;
        echo "<form action='/moodle/alpha/index.php' method='post' enctype='multipart/form-data'>
    Chá»�n file Ä‘á»ƒ upload:
    <input type='file' name='fileupload' id='fileupload'>
    <input type='submit' value='Ä�Äƒng áº£nh' name='submit'>
</form>";
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
$PAGE->set_title('<h1>Upload file</h1>');
$PAGE->set_heading('<h1>Upload file</h1>');
// Get all contents managed by active plugins where the user has permission to render them.
echo $OUTPUT->header();$mform = new fileBlock();
if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    // Dá»¯ liá»‡u gá»­i lÃªn server khÃ´ng báº±ng phÆ°Æ¡ng thá»©c post
    echo "Pháº£i Post dá»¯ liá»‡u";
    die;
}

// Kiá»ƒm tra cÃ³ dá»¯ liá»‡u fileupload trong $_FILES khÃ´ng
// Náº¿u khÃ´ng cÃ³ thÃ¬ dá»«ng
if (!isset($_FILES["fileupload"]))
{
    echo "Dá»¯ liá»‡u khÃ´ng Ä‘Ãºng cáº¥u trÃºc";
    die;
}

// Kiá»ƒm tra dá»¯ liá»‡u cÃ³ bá»‹ lá»—i khÃ´ng
if ($_FILES["fileupload"]['error'] != 0)
{
    echo "Dá»¯ liá»‡u upload bá»‹ lá»—i";
    die;
}

// Ä�Ã£ cÃ³ dá»¯ liá»‡u upload, thá»±c hiá»‡n xá»­ lÃ½ file upload

//ThÆ° má»¥c báº¡n sáº½ lÆ°u file upload
$target_dir    = "uploads/";
//Vá»‹ trÃ­ file lÆ°u táº¡m trong server (file sáº½ lÆ°u trong uploads, vá»›i tÃªn giá»‘ng tÃªn ban Ä‘áº§u)
$target_file   = $target_dir . basename($_FILES["fileupload"]["name"]);

$allowUpload   = true;

//Láº¥y pháº§n má»Ÿ rá»™ng cá»§a file (jpg, png, ...)
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Cá»¡ lá»›n nháº¥t Ä‘Æ°á»£c upload (bytes)
$maxfilesize   = 800000;

////Nhá»¯ng loáº¡i file Ä‘Æ°á»£c phÃ©p upload
$allowtypes    = array('xlsx');



// Kiá»ƒm tra náº¿u file Ä‘Ã£ tá»“n táº¡i thÃ¬ khÃ´ng cho phÃ©p ghi Ä‘Ã¨
// Báº¡n cÃ³ thá»ƒ phÃ¡t triá»ƒn code Ä‘á»ƒ lÆ°u thÃ nh má»™t tÃªn file khÃ¡c
if (file_exists($target_file))
{
    echo "TÃªn file Ä‘Ã£ tá»“n táº¡i trÃªn server, khÃ´ng Ä‘Æ°á»£c ghi Ä‘Ã¨";
    $allowUpload = false;
}
// Kiá»ƒm tra kÃ­ch thÆ°á»›c file upload cho vÆ°á»£t quÃ¡ giá»›i háº¡n cho phÃ©p
if ($_FILES["fileupload"]["size"] > $maxfilesize)
{
    echo "KhÃ´ng Ä‘Æ°á»£c upload áº£nh lá»›n hÆ¡n $maxfilesize (bytes).";
    $allowUpload = false;
}


// Kiá»ƒm tra kiá»ƒu file
if (!in_array($imageFileType,$allowtypes ))
{
    echo "Chá»‰ Ä‘Æ°á»£c upload cÃ¡c Ä‘á»‹nh dáº¡ng xlsx";
    $allowUpload = false;
}


if ($allowUpload)
{
    // Xá»­ lÃ½ di chuyá»ƒn file táº¡m ra thÆ° má»¥c cáº§n lÆ°u trá»¯, dÃ¹ng hÃ m move_uploaded_file
    if (move_uploaded_file($_FILES["fileupload"]["tmp_name"], $target_file))
    {
        echo "File ". basename( $_FILES["fileupload"]["name"]).
        " Ä�Ã£ upload thÃ nh cÃ´ng.";
        
        echo "File lÆ°u táº¡i " . $target_file;
        
    }
    else
    {
        echo "CÃ³ lá»—i xáº£y ra khi upload file.";
    }
}
else
{
    echo "KhÃ´ng upload Ä‘Æ°á»£c file, cÃ³ thá»ƒ do file lá»›n, kiá»ƒu file khÃ´ng Ä‘Ãºng ...";
}
require_once 'PHPLibrary/PHPExcel/Classes/PHPExcel.php';

//Ä�Æ°á»�ng dáº«n file
$file = 'uploads/test-thamdu.xlsx';
//Tiáº¿n hÃ nh xÃ¡c thá»±c file
$objFile = PHPExcel_IOFactory::identify($file);
$objData = PHPExcel_IOFactory::createReader($objFile);

//Chá»‰ Ä‘á»�c dá»¯ liá»‡u
$objData->setReadDataOnly(true);

// Load dá»¯ liá»‡u sang dáº¡ng Ä‘á»‘i tÆ°á»£ng
$objPHPExcel = $objData->load($file);

//Láº¥y ra sá»‘ trang sá»­ dá»¥ng phÆ°Æ¡ng thá»©c getSheetCount();
// Láº¥y Ra tÃªn trang sá»­ dá»¥ng getSheetNames();

//Chá»�n trang cáº§n truy xuáº¥t
$sheet = $objPHPExcel->setActiveSheetIndex(0);

//Láº¥y ra sá»‘ dÃ²ng cuá»‘i cÃ¹ng
$Totalrow = $sheet->getHighestRow();
//Láº¥y ra tÃªn cá»™t cuá»‘i cÃ¹ng
$LastColumn = $sheet->getHighestColumn();

//Chuyá»ƒn Ä‘á»•i tÃªn cá»™t Ä‘Ã³ vá»� vá»‹ trÃ­ thá»©, VD: C lÃ  3,D lÃ  4
$TotalCol = PHPExcel_Cell::columnIndexFromString($LastColumn);

//Táº¡o máº£ng chá»©a dá»¯ liá»‡u
$data = [];

//Tiáº¿n hÃ nh láº·p qua tá»«ng Ã´ dá»¯ liá»‡u
//----Láº·p dÃ²ng, VÃ¬ dÃ²ng Ä‘áº§u lÃ  tiÃªu Ä‘á»� cá»™t nÃªn chÃºng ta sáº½ láº·p giÃ¡ trá»‹ tá»« dÃ²ng 2
for ($i = 10; $i <= $Totalrow; $i++) { // vÃ­ dá»¥ náº¿u $i = 5 thÃ¬ báº¯t ngay cá»™t A5 - Kenny Sang
    //----Láº·p cá»™t
    for ($j = 1; $j < $TotalCol; $j++) { // náº¿u $j = 2 thÃ¬ báº¯t ngay cá»™t C
        // Tiáº¿n hÃ nh láº¥y giÃ¡ trá»‹ cá»§a tá»«ng Ã´ Ä‘á»• vÃ o máº£ng
        $data[$i - 2][$j] = $sheet->getCellByColumnAndRow($j, $i)->getValue();;
    }
}

echo "  TÃªn\t\t";
echo " Buá»•i 1\t\t";
echo "Buá»•i 2";

$totalHocVien = 4; // query total users of a cohort
$render_loop = 10 + $totalHocVien; // 10 is the start of row position to get data
for($i=10;$i<$render_loop;$i++)
{
    $hocvien = $sheet->getCellByColumnAndRow(1, $i)->getValue();;
    $buoi1 = $sheet->getCellByColumnAndRow(9,$i)->getValue();;
    $buoi2 = $sheet->getCellByColumnAndRow(10,$i)->getValue();;
    //var_dump($LeThiHanh);
    
    
    echo "\n";
    echo $hocvien."\t";
    if($buoi1 == "")
    {
        echo " x";
    }
    else{
        echo $buoi1; // <--- insert_records
    }
    echo "\t\t";
    if($buoi2 == "")
    {
        echo " x";
    }
    else{
        echo $buoi2;
    }
}
echo $OUTPUT->footer();
