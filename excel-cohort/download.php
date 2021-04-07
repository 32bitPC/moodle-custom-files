
     if($cohortid!=2629){
         $objPHPExcel = PHPExcel_IOFactory::load($CFG->dirroot.'/phlcohort/csv/Form_2_col.xlsx');
         
         
         $i=10;
         foreach ($users as $user) {
                 $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A3','Lá»šP: '.$user->idnumber)
                 ->setCellValue('A4','Thá»�i gian há»�c: '.$user->ngayhoc)
                 ->setCellValue('A5','Ä�á»‹a Ä‘iá»ƒm huáº¥n luyá»‡n: '.$user->tenkhuvuc.' - '.$user->tenquanhuyen)
                 ->setCellValue('A6','ChuyÃªn viÃªn huáº¥n luyá»‡n: '.$user->trainer)
                 ->setCellValue('A'.$i,$i-9)
                 ->setCellValue('B'.$i,$user->uname)
                 ->setCellValue('C'.$i,$cohortid)
                 ->setCellValue('D'.$i,'\''.$user->username)
                 ->setCellValue('F'.$i,'')
                 ->setCellValue('G'.$i,'');
                 $i++;
             }
         $objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('B10'),'A10:G'.$i);
         
         
         
         
         // Set active sheet index to the first sheet, so Excel opens this as the first sheet
         $objPHPExcel->setActiveSheetIndex(0);
         
         
         // Redirect output to a clientâ€™s web browser (Excel2007)
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename="Members - '.$cohortid.'.xlsx"');
         header('Cache-Control: max-age=0');
         // If you're serving to IE 9, then the following may be needed
         header('Cache-Control: max-age=1');
         
         // If you're serving to IE over SSL, then the following may be needed
         header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
         header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
         header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
         header ('Pragma: public'); // HTTP/1.0
         
         $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
         $objWriter->save('php://output');
         exit;
     }
     else{
         $objPHPExcel = PHPExcel_IOFactory::load($CFG->dirroot.'/phlcohort/csv/Form_4_col.xlsx');
         
         
         $i=10;
         foreach ($users as $user) {
             $objPHPExcel->setActiveSheetIndex(0)
             ->setCellValue('A3','Lá»šP: '.$user->idnumber)
             ->setCellValue('A4','Thá»�i gian há»�c: '.$user->ngayhoc)
             ->setCellValue('A5','Ä�á»‹a Ä‘iá»ƒm huáº¥n luyá»‡n: '.$user->tenkhuvuc.' - '.$user->tenquanhuyen)
             ->setCellValue('A6','ChuyÃªn viÃªn huáº¥n luyá»‡n: '.$user->trainer)
             ->setCellValue('A'.$i,$i-9)
             ->setCellValue('B'.$i,$user->uname)
             ->setCellValue('C'.$i,$cohortid)
             ->setCellValue('D'.$i,'\''.$user->username)
             ->setCellValue('F'.$i,'')
             ->setCellValue('G'.$i,'');
             $i++;
         }
         $objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('B10'),'A10:I'.$i);
         
         
         
         
         // Set active sheet index to the first sheet, so Excel opens this as the first sheet
         $objPHPExcel->setActiveSheetIndex(0);
         
         
         // Redirect output to a clientâ€™s web browser (Excel2007)
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename="Members - '.$cohortid.'.xlsx"');
         header('Cache-Control: max-age=0');
         // If you're serving to IE 9, then the following may be needed
         header('Cache-Control: max-age=1');
         
         // If you're serving to IE over SSL, then the following may be needed
         header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
         header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
         header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
         header ('Pragma: public'); // HTTP/1.0
         
         $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
         $objWriter->save('php://output');
         exit;
     }
