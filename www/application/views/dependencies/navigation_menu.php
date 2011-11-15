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
		<?php if ($this->useraccess->HasRole(array('system admin'))): ?>
		<li>
			<a href="<?=site_url("sysadmin");?>">System Admin</a>
		</li>
		<?php endif; ?>
		<?php if ($this->useraccess->HasRole(array('system admin','internal'))): ?>
		<li>
			<a href="<?=site_url("pglmsweeklies");?>">PGLMS Weekly Reports</a>
		</li>
		<?php endif; ?>
		<li>
			<a href="<?=site_url("schedulereport");?>">Schedule Report</a>
		</li>
	</ul>
</div>

