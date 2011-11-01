<?php $this->load->view('dependencies/header',array(
	'title' => 'Reporting Dashboard',
	'header1' => 'The Reporting Dashboard',
	'header2' => 'Middlebury Interactive Reporting',
)); ?>
<?
echo $this->router->class;
echo date('Y-m-d',strtotime("first day of last month -0 months"));
?>
<ul id="reportList">
	<?php
	$category = '';
	foreach($reportList AS $category => $reportArray) {
		echo "<li class='categoryHeading'>{$category}";
		echo "<ul>";
		$counter = 1;
		foreach($reportArray AS $report) {
			
			if ($counter % 2 == 0)
			{
				echo "<li class='even reportItem'><a id='{$report['id']}'>{$report['display_name']}</a></li>";
			}
				else
					{
						echo "<li class='odd reportItem'><a id='{$report['id']}'>{$report['display_name']}</a></li>";
					}
			$counter ++;
		}
		echo "</ul></li>";
	}
	?>
</ul>
<? 
// The dynamicForm div is for the purpose of loading the dynamically built form
// via an ajax request by the jQuery $.load method.
?>
<div id="dynamicForm">
</div>
<div id="scheduleFormBlock">
	<?
	 // $this->load->view('customreports/schedule_report_view');
	?>
</div>
<?php $this->load->view('dependencies/footer'); ?>