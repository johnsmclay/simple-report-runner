<?php
setlocale(LC_MONETARY, 'en_US');
$money_format = '$%=#10.2n';
?>
<!DOCTYPE html>
<html lang="en">
<body>

<h1 style="font-weight:bold;font-family:Arial">Teacher Pay Checkup</h1>

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

<p style="font-family:Arial">Please review the following pay information and respond with any issues or your approval. An excel version of this report is attached.</p>

<h2 style="font-family:Arial">Summary</h2>

<table style="font-family:Arial;border-width:1px;border-spacing:0px;border-style:solid;border-color:black;border-collapse:separate;" border='1'>
	<thead style="font-weight:bold;font-family:Arial;border-width:1px;padding:0px;border-style:inset;border-color:gray;">
		<tr>
			<td>Month</td>
			<?php foreach ($SummaryResults as $SummaryColumn): ?>
				<td><?=$SummaryColumn['month_name']?></td>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Students Before</td>
			<?php foreach ($SummaryResults as $SummaryColumn): ?>
				<td><?=$SummaryColumn['qty_before']?></td>
			<?php endforeach; ?>
		</tr>
		<tr>
			<td>Students Added this Period</td>
			<?php foreach ($SummaryResults as $SummaryColumn): ?>
				<td><?=$SummaryColumn['qty_added']?></td>
			<?php endforeach; ?>
		</tr>
		<tr>
			<td>Students Dropped this Period</td>
			<?php foreach ($SummaryResults as $SummaryColumn): ?>
				<td><?=$SummaryColumn['qty_dropped']?></td>
			<?php endforeach; ?>
		</tr>
		<tr>
			<td>Est. Semester Payment</td>
			<?php foreach ($SummaryResults as $SummaryColumn): ?>
				<td><?=money_format($money_format,$SummaryColumn['proj_semester_pay'])?></td>
			<?php endforeach; ?>
		</tr>
		<tr>
			<td>Paid so far (cumulative)</td>
			<?php foreach ($SummaryResults as $SummaryColumn): ?>
				<td><?=money_format($money_format,$SummaryColumn['pay_so_far'])?></td>
			<?php endforeach; ?>
		</tr>
		<tr>
			<td>Periods Remaining</td>
			<?php foreach ($SummaryResults as $SummaryColumn): ?>
				<td><?=$SummaryColumn['periods_remaining']?></td>
			<?php endforeach; ?>
		</tr>
		<tr>
			<td>Pay This Month</td>
			<?php foreach ($SummaryResults as $SummaryColumn): ?>
				<td><?=money_format($money_format,$SummaryColumn['pay_this_month'])?></td>
			<?php endforeach; ?>
		</tr>
		<tr>
			<td>15th Paycheck Amount</td>
			<?php foreach ($SummaryResults as $SummaryColumn): ?>
				<td><?=money_format($money_format,($SummaryColumn['pay_this_month']/2))?></td>
			<?php endforeach; ?>
		</tr>
		<tr>
			<td>30th Paycheck Amount</td>
			<?php foreach ($SummaryResults as $SummaryColumn): ?>
				<td><?=money_format($money_format,($SummaryColumn['pay_this_month']/2))?></td>
			<?php endforeach; ?>
		</tr>
	</tbody>
</table>

<h2 style="font-family:Arial">Details</h2>

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
		<?php foreach ($DetailsResult as $DetailField): ?>
			<td><?=$DetailField?></td>
		<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

</body>
</html>