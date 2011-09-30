<?php
class Gale extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->database('gale');
		$this->load->model('Gale_model');
	}
	
	public function index() {
		// Allows you to name an individual JavaScript file to be loaded for this page.
		// Just provide the name of the file, without the .js extension. Then create the 
		// file in the 'assets/javascript' folder located in the root of the codeIgniter folder
		$view_data['javascript'] = 'gale';
		
		$this->load->view('gale_view',$view_data);
	}
	
	// Gale Report Generator ----
	// Developed by Adam Haymond and Ryan May
	// August 3, 2011
	// 
	// The queries in this file were created by Ken Deller and slightly
	// tweaked by Ryan. They are a work in progress. If you have any questions 
	// please see Adam or Ryan.
	public function startGale() {
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			error_reporting(E_ALL);
			ini_set('display_errors',1);
		}
		set_time_limit(450);
		// if($_POST['get_gale'] == true) 
		$this->get_gale_reports();
	}
		
	
	
	private function get_gale_reports() {
		//TODO: Move this to the model
		// Set date variables used by queries below
		$setVarQuery = $this->setVarsQuery();
		$this->db->query($setVarQuery);
		
		//TODO: Create a cron job that will update the irs.sessions_language table at the beginning of each month!
		
		// Check the sessions_language table to see if the previous months data is available
			$beginTime = $this->Gale_model->checkLanguagesTable();

			$monthFromTable = date('m',strtotime($beginTime));
			$previousMonth = date('m', time() - date('j') *24*60*60);
	
		// If the month in the sessions_langauge table is not the previous months numeric value
		// we must TRUNCATE the table and update it with the previous months data 
		if ($monthFromTable != $previousMonth) {
			$this->Gale_model->updateSessionsLanguageTable();
		}		

		// Obtain all providers for the previous month that will get reports generated for them
		$providers = $this->Gale_model->getProviders();
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// First call the query to generate the Gale Summary Report
		
		$queryResults = $this->Gale_model->getGaleSummary();
	 	
		$headers = array_keys($queryResults[0]); // Create an array of values to use for the column headers row
		$this->generateGaleReport($queryResults,'gale_DailySummary',$headers);
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		
	// ======================================================================================
			
		// obtain month summary data
		$monthSummaryData = $this->Gale_model->getMonthSummary();
		
		$subheaders = array_keys($monthSummaryData[0]); // This headers array is for the individual reports
		
		// Loop through providers and query results to generate each individual report
		foreach ($providers AS $provider) {
	
			foreach($queryResults AS $key => $val) {
				if($val['provider'] == $provider) {
					$providerDailyData[] = $val;
				}
			}
			
			foreach($monthSummaryData as $key => $val) {
				if($val['provider'] == $provider) {
					$monthSummary = $val;
				}
			}
			
			$this->generateGaleReport($providerDailyData,$provider,$headers,true,$monthSummary,$provider,$subheaders);
			unset($providerDailyData);
		}
		
		
		// Set path variable depending on whether it is on localhost or live server
		if ($_SERVER['HTTP_HOST'] != 'localhost') {
			$path = readlink('/var/www/database/dash') . 'gale_reports';
		}
			else{
				$path = readlink('/Users/ode/Sites/dash') . DIRECTORY_SEPARATOR . 'gale_reports';
			}
		
		
		// Zip entire directory
		if ($this->Zip($path,$path . '/gale_reports.zip',true) === true) {
				
			// Create an array of all the files in the array (for cleaning them up)
			$dir = opendir($path);
			while ($file = readdir($dir)) {
				if ($file != '.' || $file != '..') {
					$filearray[] = $file;
				}
			}
			
			// Delete all the excel files leaving only the zip file
			foreach ($filearray as $filename) {
				if ($filename != 'gale_reports.zip' && !is_dir($filename)) {
					unlink($path . DIRECTORY_SEPARATOR . $filename);
				}
			}
		}
		
		$status = json_encode(array('status' => true));
		echo $status;
		exit();
	}
	 
	// ======================================================================================
	

	private function generateGaleReport($data,$filename,$headers,$individualReport=false,$monthSummaryData=false,$providers=false,$subheaders=false) {
		// Get PHPExcel plugin
		require_once 'PHPExcel.php';
		
		// This array contains the needed alphabet letters for excel columns for up to 52 columns
		$columns = array (
			'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AT','AU','AV','AW','AX','AY','AZ'
		);
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
		$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0); // Set active sheet for writing data to
	
		
		$month_name = date('F', time() - date('j') *24*60*60); // Calculates for the previous months name
		$month_number = date('m', time() - date('j') *24*60*60); // Calculates for the previous months number
		$year = date('Y');
		
		// Set up top of worksheet
		$objPHPExcel->getActiveSheet() // Get the currently active sheet for writing data to
					->setCellValue('A1','Usage Report for ' . $month_name . ' ' . $year . ' - By Language') // Write data to cells
					->setCellValue('E2','Session:')
					->setCellValue('F2','any access of the product by a user (until session ends or times out)')
					->setCellValue('E3','Minutes:')
					->setCellValue('F3','minutes of use of the product')
					->setCellValue('E4','Accessed:')
					->setCellValue('F4','# of times each language was selected')
					->setCellValue('E5','[note:]')
					->setCellValue('F5','it is possible for patrons to access the product but not select a language.')
					->setCellValue('A6','Monthly Totals:')
					->setCellValue('A9','Breakdown by Date:');
					
		// Style the "legend" of the worksheet
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
		$objPHPExcel->getActiveSheet()->getStyle('E2:E5')->getFont()->setBold(true)->setSize(8);
		$objPHPExcel->getActiveSheet()->getStyle('F2:F5')->getFont()->setSize(8);
		$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true)->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A9')->getFont()->setBold(true)->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A7:'. $columns[count($data[0])-1] . '7')->getAlignment()->setWrapText(true); // Header row -- wrap text
		$objPHPExcel->getActiveSheet()->getStyle('A10:'. $columns[count($data[0])-1] . '10')->getAlignment()->setWrapText(true); // Header row -- wrap text
		
		// Set column widths
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
	
		
		// Booleans for setting header row and legend
		$indReportHeadersSet = false;
		$headersSet = false;
		
		$rowCounter = 10; // For placing cell data in correct row
		$columnCounter = 0; // For placing cell data in correct column (used in conjuction with $columns Array)
		
		// Loop through and add Data into cells
		foreach ($data AS $key => $val) {
			
			foreach ($val AS $data) {
				
				// ---------------------------------------------------------------------------
				// Set column headers row
				
				if (!$headersSet) {
					$preparedHeaders = $this->prepareHeaders($headers);
					
					// Add extra data and header row if this is an individual report
					if ($individualReport) {
						
						if (!$indReportHeadersSet) {
							$preparedSubHeads = $this->prepareHeaders($subheaders);
							foreach ($preparedSubHeads AS $subhead) {
								$objPHPExcel->getActiveSheet() // Retrieve the current active sheet
											->setCellValue($columns[$columnCounter] . '7', $subhead); // Write monthly summary headers to cells
								$columnCounter += 1; // Advance column
								
								if ($columnCounter >= count($preparedHeaders)) {
									$columnCounter = 0;
								}
							}
	
							foreach ($monthSummaryData AS $monthData) {
								$objPHPExcel->getActiveSheet() // Retrieve the current active sheet
											->setCellValue($columns[$columnCounter] . '8', $monthData); // Write month summary to cells
								$columnCounter += 1; // Advance column
								
								if($columnCounter >= count($monthSummaryData)) {
									$columnCounter = 0;
								}
							}
							
							// Style border for monthly data (NOTE: only if this is an individual report)
							$objPHPExcel->getActiveSheet()->getStyle('A7:' . $columns[count($preparedSubHeads)-1] . '7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$indReportHeadersSet = true;
							
						}

					}
					
					foreach ($preparedHeaders AS $header) {
						$objPHPExcel->getActiveSheet() // Retrieve the current active sheet
									->setCellValue($columns[$columnCounter] . $rowCounter, $header); // Write data to cell
						$columnCounter += 1; // Advance column
						
						if ($columnCounter >= count($headers)) {
							$columnCounter = 0; // Reset column counter
							$headersSet = true; // All headers have been set
							$rowCounter += 1; // Advance row pointer
						}
					}
				}
	
				// ---------------------------------------------------------------------------
				
				// Add report data into cells
				$objPHPExcel->getActiveSheet() // Retrieve the current active sheet
							->setCellValue($columns[$columnCounter] . $rowCounter, $data); // Writes the data to the next cell
				
				$columnCounter += 1; // Advance column pointer
				
				if ($columnCounter >= count($val)) { // Reaches the end of the array (aka row);
					$columnCounter = 0; // Reset the column counter
					$rowCounter += 1; // Advance the row pointer
				}
			}
		}
	
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Gale Report ' . $month_name . ' ' . $year);
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		if (!$filename) {
			$filename = 'gale_report';
		}
			else {
				$filename = $filename . '_' . $month_number . '_' . $year;
			}
		
		// show(readlink('/var/www/database/dash') . 'gale_reports' . DIRECTORY_SEPARATOR . $filename . '.xlsx');
		
		$rowCounter = 10;
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->setPreCalculateFormulas(false);
		
		if ($_SERVER['HTTP_HOST'] != 'localhost') {
			$objWriter->save(readlink('/var/www/database/dash') . 'gale_reports' . DIRECTORY_SEPARATOR . $filename . '.xlsx');
		}
			else {
				$objWriter->save(readlink('/Users/ode/Sites/dash') . DIRECTORY_SEPARATOR . 'gale_reports' . DIRECTORY_SEPARATOR . $filename . '.xlsx');
			}
			
		// Delete the worksheet so a new one can be created
		$objPHPExcel->disconnectWorksheets();
		unset($objPHPExcel);
	}
	
	/**
	 * setVarsQuery accepts a number (1-31) which will limit the number of days searched for.
	 * If no number is provided it will use the number of days in the previous month.
	 */
	private function setVarsQuery($number=false) {
		$month = date('m', time() - date('j')*24*60*60);
		$year = date('Y');
		
		// This is to make sure that December will get the previous years data not Dec 2012
		// when it should be Dec 2011. It will of course have to be changed to 2013 eventaully.
		if($month == 12 && date('Y') == 2012) {
			$year = 2011;
		}
		if (is_numeric($number)) {
			if ($number < 10) {
				$days = '0' . $number;
			}
				else {
					$days = $number;
				}
		}
			else {
				$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			}
		$query =
		"
			SET 
				@beg := ' {$year}-{$month}-01 00:00:00',
				@endd := '{$year}-{$month}-$days 23:59:59'
		";

		return $query;
	}
	
	# Prepare header column titles
	private function prepareHeaders($headers) {
		foreach ($headers AS $header) {
			$spacedHeader = preg_replace('/_/', ' ', $header); // Remove hyphens from headers
			$capitalizedHeader = ucwords(strtolower($spacedHeader)); // Proper case headers
			
			if (preg_match('/(esl)?/i', $capitalizedHeader)) {
				$properHeader = preg_replace('/esl/i', 'ESL', $capitalizedHeader); // re-capitalize ESL after it is lowercased just before this
			}
				else {
					$properHeader = $capitalizedHeader;
				}
			$preparedHeaders[] = $properHeader;
		}
		
		return $preparedHeaders;
	}
	
	
	//++++++++++++++++ ZIP FUNCTIONS +++++++++++++++++++++++++
	
	/* creates a compressed zip file */
	function Zip($source, $destination,$overwrite=false) {
		if (file_exists($destination)) {
			unlink($destination);
		}
	    if (extension_loaded('zip') === true) {
	        if (file_exists($source) === true) {
	                $zip = new ZipArchive();
					// $overwrite = $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE;
	                if ($zip->open($destination,ZIPARCHIVE::CREATE) === true) {
	                        $source = realpath($source);
	
	                        if (is_dir($source) === true) {
	                                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
	
	                                foreach ($files as $file) {
	                                        $file = realpath($file);
	
	                                        if (is_dir($file) === true) {
	                                                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
	                                        }
	
	                                        else if (is_file($file) === true) {
	                                                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
	                                        }
	                                }
	                        }
	
	                        else if (is_file($source) === true) {
	                                $zip->addFromString(basename($source), file_get_contents($source));
	                        }
	                }
	
	                return $zip->close();
	        }
	    }
	
	    return false;
	}

	// iFrame targets this function to download the zip file via "AJAX" as it were.
	public function downloadGale() {
		if ($_SERVER['HTTP_HOST'] != 'localhost') {
			$path = readlink('/var/www/database/dash') . 'gale_reports/';
		}
			else{
				$path = readlink('/Users/ode/Sites/dash') . DIRECTORY_SEPARATOR . 'gale_reports/';
			}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/zip");
		header("Content-Disposition: attachment; filename=\"gale_reports.zip\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($path.'gale_reports.zip'));
		readfile($path.'gale_reports.zip');
		
		unlink($path . 'gale_reports.zip');
		exit;
	}
}
?>