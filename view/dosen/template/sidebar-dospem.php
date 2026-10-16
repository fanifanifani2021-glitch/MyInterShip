<li class="nav-section">
    <span class="sidebar-mini-icon">
	    <i class="fa fa-ellipsis-h"></i>
	</span>
	<h4 class="text-section">Lecturer Menu</h4>
</li>
<li class="nav-item <?php if ( $route == 'intern_student_management' ) { echo "active"; } ?>">
    <a href="<?= router('lecturer/my/student.index') ?>" class="collapsed" aria-expanded="false">
	    <i class="fas fa-users"></i>
	    <p class="wrap">My Supervised Students</p>
	</a>
</li>