<?php
function styleReport_11($sheet,$report_name,$report_vars)
{
	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set('memory_limit', '512M');
		
    $start_date = substr($report_vars['start_date'], 0, -9);
    $end_date   = substr($report_vars['end_date'],0,-9);	
	
	$sheet->getColumnDimension()->setAutoSize(true);
	$objRichText = new PHPExcel_RichText();
	$objRichText->createText('School Detail Report from: '.$start_date.' to '.$end_date);
	
	#***************************************
	#			Style Arrays
	#***************************************
				
	// These arrays hold styling information for the PHPExcel plugin
	// they can be used to add numerous styles to either one cell or a range of cells
    // and believe it or not, it greatly reduces and cleans up the amount of code
	// to apply these styles proceduarlly 
		
	$title_row = array(
					'borders' => array(
						'allborders' => array(
							'color' => array('argb' => '000000')
						)
					),
					'font' => array(
						'name' => 'Calibri',
						'size' => 20,
						'bold' => true
					),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
					)
				);
				
	$headerRowStyles = array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('argb' => '000000')
						)
					),
					'font' => array(
						'name' => 'Calibri',
						'size' => 8,
						'bold' => true
					),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
					)
				);
	$lastLogInColumnStyle = array(
						'font'=>array(
							'color'=> array('argb'=>'FF0000')
						)
				);
				
	$failingStudentStyle = array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('argb' => 'FFFF47')
					)
				);
	$boldFormatStyle = array(
					'font' => array(
						'bold' => true
					)
				);
	
				
	#***************************************
	#			Styling the Worksheet
	#***************************************
	$sheet->insertNewRowBefore(1,2);
	$sheet->mergeCells('A1:H1');
	$sheet->getCell('A1')->setValue($objRichText);			
	$sheet->getStyle('A1:H1')->applyFromArray($title_row);
	$sheet->setCellValue('A2','=today()');
	$sheet->getStyle('A2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::toFormattedString('d-mmm-yyyy'));
	$sheet->getStyle('A2')->applyFromArray($boldFormatStyle);
	$sheet->getStyle('A3:S3')->applyFromArray($headerRowStyles);
	$sheet->getColumnDimension('D')->setWidth(12);
	$sheet->getColumnDimension('I')->setWidth(12);
	$sheet->getColumnDimension('J')->setWidth(12);
	$sheet->getColumnDimension('K')->setWidth(12);
	$sheet->getColumnDimension('L')->setWidth(12);
	#***************************************
	#			Looping through rows
	#***************************************
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objReader->setReadDataOnly(true);
	$highestRow = $sheet->getHighestRow(); // e.g. 10
	
	for ($row = 4; $row <= $highestRow; ++$row) {
  	$curGrade = $sheet->getCellByColumnAndRow(12,$row)->getValue();
	$lastLogInDate = $sheet->getCellByColumnAndRow(9,$row)->getValue();
		//Checking that Current grade is less than 65
		if($curGrade < 65){
			$sheet->getStyle('A'.$row.':S'.$row)->applyFromArray($failingStudentStyle);
		}
		//Checking for last login greater than 1 wk.
		if(strtotime($lastLogInDate) < strtotime("7 days ago")){
			$sheet->getStyle('J'.$row)->applyFromArray($lastLogInColumnStyle);
		}
	}
}
?>