			<div id="sidenav">
				<ul>
					<!--<li>
						<a href="<? // =site_url("customers"); ?>">Customer Information</a>
					</li>
					<li>
						<a href="<? // =site_url("enrollments");?>">Enrollments</a>
					</li>
					<li>
						<a href="<? // =site_url("teachers");?>">Teachers</a>
					</li>
					<li>
						<a href="<? // =site_url("gale");?>">Gale</a>
					</li>-->
					<?php $this->load->library('UserAccess'); ?>
					<li>
						<a href="<?=site_url("customreport");?>">Custom Reports</a>
					</li>
					<?php if ($this->useraccess->HasRole(array('report admin','system admin'))): ?>
					<li>
						<a href="<?=site_url("reportbuilder");?>">Report Builder</a>
					</li>
					<?php endif; ?>
					<li>
						<a href="<?=site_url("useradmin");?>">User Profile</a>
					</li>
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
					<!-- 
						This runs via cron job, not necessary to have direct access<li>
						<a href="_mil_enrollments.php">Biweekly MIL Enrollments</a>
					</li> -->
				</ul>
			</div>
