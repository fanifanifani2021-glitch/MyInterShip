<li class="nav-section">
	<span class="sidebar-mini-icon">
		<i class="fa fa-ellipsis-h"></i>
	</span>
	<h4 class="text-section">Internship Coordinator</h4>
</li>

<li class="nav-item <?php if ( $route == 'supervisor_management' ) { echo "active"; } ?>">
	<a href="index.php?page=supervisor_management" class="collapsed" aria-expanded="false">
		<i class="fas fa-users"></i>
		<p class="wrap">Lecturer Management</p>
	</a>
</li>


<li class="nav-item <?php if ( $route == 'student_intern_management' ) { echo "active"; } ?>">
	<a href="index.php?page=student_intern_management" class="collapsed" aria-expanded="false">
		<i class="fas fa-user"></i>
		<p class="wrap">Students Management</p>
	</a>
</li>

<li class="nav-item <?php if ( $route == 'internship-coordinator/internship_letter' ) { echo "active"; } ?>">
	<a href="index.php?page=internship-coordinator/internship_letter" class="collapsed" aria-expanded="false">
		<i class="fas fa-envelope"></i>
		<p class="wrap">Internship Letter</p>
	</a>
</li>

<li class="nav-item <?php if ( $route == 'jobdesk_management' ) { echo "active"; } ?>">
	<a href="index.php?page=jobdesk_management" class="collapsed" aria-expanded="false">
		<i class="fas fa-list"></i>
		<p class="wrap">Jobdesk Management</p>
	</a>
</li>

<?php if ( $_SESSION['prodi_koor'] == 'LPI' ) : ?>
<li class="nav-item <?php if ( $route == 'profile_jobdesc' ) { echo "active"; } ?>">
	<a href="index.php?page=profile_jobdesc" class="collapsed" aria-expanded="false">
		<i class="fas fa-graduation-cap"></i>
		<p class="wrap">Job Profile</p>
	</a>
</li>
<?php endif;?>

<li class="nav-item <?php checker($routes ?? $route, ['coordinator/internship_administration'], 'active') ?>">
	<a aria-label="Go to Administration" href="{{ router('coordinator/internship/administration.index') }}" class="collapsed" aria-expanded="false">
    	<i class="fas fa-file"></i>
        <p class="wrap">Student Administration</p>
    </a>
</li>