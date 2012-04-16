<?php
setlocale(LC_MONETARY, 'en_US');
$money_format = '$%=#10.2n';
?>
<!DOCTYPE html>
<html lang="en">
<body>

<h1 style="font-weight:bold;font-family:Arial">Teacher Performance Checkup</h1>

<?php if ($single): ?>
<table style="font-family:Arial">
	<tbody>
		<tr>
			<td style="font-weight:bold">Teacher:&nbsp;</td>
			<td><?=$TeacherInfo['display_name']?></td>
		</tr>
		<tr>
			<td style="font-weight:bold">Language:&nbsp;</td>
			<td><?=$TeacherInfo['language']?></td>
		</tr>
		<tr>
			<td style="font-weight:bold">Email:&nbsp;</td>
			<td><?=$TeacherInfo['email_address']?></td>
		</tr>
	</tbody>
</table>
<?php endif; ?>

<p style="font-family:Arial">Please review the students who have not logged in for a week and/or have a grade that is 70% or lower.  Follow the guidelines in the MIL Teacher Handbook for contacting these students, their schools and the Director of Online Instruction.  Log your contacts according to the guidelines if necessary.</p>

<h2 style="font-family:Arial">Students</h2>

<table style="font-family:Arial;border-width:1px;border-spacing:0px;border-style:solid;border-color:black;border-collapse:separate;" border='1'>
	<thead style="font-weight:bold;font-family:Arial;border-width:1px;padding:0px;border-style:inset;border-color:gray;">
		<tr>
		<?php foreach (reset($DetailsResults) as $key => $value): ?>
			<td><?=$key?></td>
		<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($DetailsResults as $DetailsResult): ?>
		<tr>
		<?php foreach ($DetailsResult as $DetailField=>$DetailsValue): ?>
			<?php
			// add conditional formatting
			$td_properties = '';
			if($DetailField == 'Current Grade Percentage' && $DetailsValue <= 70)
			{
				$td_properties = 'style="font-weight:bold;background-color:yellow"';
			}
			if($DetailField == 'Last Login' && strtotime($DetailsValue) <= strtotime("-1 week"))
			{
				$td_properties = 'style="font-weight:bold;background-color:yellow"';
			}
			if($DetailField == 'Current Grade Points' || $DetailField == 'Final Grade Points' || $DetailField == 'Activities Completed/Course Total')
			{
				$DetailsValue = "'".$DetailsValue;
			}
			?>
			<td <?=$td_properties?>><?=$DetailsValue?></td>
		<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

</body>
</html>