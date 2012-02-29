<?php
function styleReport_14($sheet)
{
	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set('memory_limit', '512M');
	
	$sheet->getColumnDimension()->setAutoSize(true);
	$objRichText = new PHPExcel_RichText();
	$objRichText->createText('Enrollment by Classroom');
	
	#***************************************
	#			Style Arrays
	#***************************************
				
	// These arrays hold styling information for the PHPExcel plugin
	// they can be used to add numerous styles to either one cell or a range of cells
    // and believe it or not, it greatly reduces and cleans up the amount of code
	// to apply these styles proceduarlly 
	
	$title_row2 = array(
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
	
	$totalRowStyles = array(
					'borders' => array(
						'top' => array(
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
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
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
	$sheet->getStyle('A1:H1')->applyFromArray($title_row2);
	$sheet->setCellValue('A2','=today()');
	$sheet->getStyle('A2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::toFormattedString('d-mmm-yyyy'));
	$sheet->getStyle('A2')->applyFromArray($boldFormatStyle);	
	$sheet->getStyle('A3:L3')->applyFromArray($headerRowStyles);
	$sheet->getColumnDimension('D')->setWidth(12);
	$sheet->getColumnDimension('E')->setWidth(12);
	$sheet->getColumnDimension('F')->setWidth(12);
	$sheet->getColumnDimension('B')->setWidth(22);
	
	#***************************************
	#			Sum of totals
	#***************************************
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objReader->setReadDataOnly(true);
	$highestRow = $sheet->getHighestRow(); // e.g. 10
	
	$totalRow = $highestRow+1;
	
	$sheet->setCellValue('A'.$totalRow,'Grand Total');
	$sheet->setCellValue('G'.$totalRow,'=Sum(G4:G'.$highestRow.')');
	$sheet->setCellValue('H'.$totalRow,'=Sum(H4:H'.$highestRow.')');
	$sheet->setCellValue('I'.$totalRow,'=Sum(I4:I'.$highestRow.')');
	$sheet->setCellValue('J'.$totalRow,'=Sum(J4:J'.$highestRow.')');
	$sheet->setCellValue('K'.$totalRow,'=Sum(K4:K'.$highestRow.')');
	$sheet->setCellValue('L'.$totalRow,'=Sum(L4:L'.$highestRow.')');

	
	
	$sheet->getStyle('A'.$totalRow.':L'.$totalRow)->applyFromArray($totalRowStyles);
	
}
?>