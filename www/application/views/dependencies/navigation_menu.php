<div id="sidenav">
	<ul>
		<li>
			<a href="<?=site_url("customreport");?>">Custom Reports</a>
		</li>
		<?php if ($this->useraccess->HasRole(array('report admin','system admin'))): ?>
		<li>
			<a href="<?=site_url("reportbuilder");?>">Report Builder</a>
		</li>
		<?php endif; ?>
		<?php if ($this->useraccess->HasRole(array('user admin','system admin','internal','external'))): ?>
		<li>
			<a href="<?=site_url("useradmin");?>">Account Admin</a>
		</li>
		<?php endif; ?>
		<?php if ($this->useraccess->HasRole(array('system adminx'))): ?>
		<li>
			<a href="<?=site_url("sysadmin");?>">System Admin</a>
		</li>
		<?php endif; ?>
		<?php if ($this->useraccess->HasRole(array('mailer','system admin'))): ?>
		<li>
			<a href="<?=site_url("mass_emailer");?>">Notifications</a>
		</li>
		<?php endif; ?>
		<?php if ($this->useraccess->HasRole(array('system adminx','internalx'))): ?>
		<li>
			<a href="<?=site_url("pglmsweeklies");?>">PGLMS Weekly Reports</a>
		</li>
		<?php endif; ?>
		<li>
			<a href="<?=site_url("schedulereport");?>">Schedule Report</a>
		</li>
		<?php if ($this->useraccess->HasRole(array('system admin'))): ?>
		<!--<li>
			<a href="<?=site_url("periodic_reports");?>">Teacher Stuff</a>
		</li>-->
		<?php endif; ?>
	</ul>
</div>
