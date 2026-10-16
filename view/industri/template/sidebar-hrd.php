<li class="nav-section">
	<span class="sidebar-mini-icon">
		<i class="fa fa-ellipsis-h"></i>
	</span>
	<h4 class="text-section">HRD Menu</h4>
</li>

<li class="nav-item <?php if ($route == 'industry_supervisor_management') {
						echo "active";
					} ?>">
	<a href="index.php?page=industry_supervisor_management" class="collapsed" aria-expanded="false">
		<i class="fas fa-user-secret"></i>
		<p>Supervisor Management</p>
	</a>
</li>


<li class="nav-section">
	<span class="sidebar-mini-icon">
		<i class="fa fa-ellipsis-h"></i>
	</span>
	<h4 class="text-section">Internship Menu</h4>
</li>


<li class="nav-item <?php checker($route, ['intern_candidate', 'validate_intern'], 'active') ?>">
	<a href="index.php?page=intern_candidate" class="collapsed" aria-expanded="false">
		<i class="fab fa-wpforms"></i>
		<p class='wrap'>Internship Candidate</p>
	</a>
</li>

<li class="nav-item <?php checker($route, ['internship_management', 'accepted_intern_management', 'rejected_intern_management', 'finished_intern_management'], 'active') ?>">
	<a href="index.php?page=internship_management" class="collapsed" aria-expanded="false">
		<i class="fas fa-users"></i>
		<p class='wrap'>Internship Management</p>
	</a>
</li>