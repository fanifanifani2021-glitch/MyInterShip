<div class="card-header">
    <div class="row row-nav-line">
        <ul class="nav nav-tabs nav-line nav-color-secondary w-100 pl-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'identity' ? 'active' : '' }}" href="{{ router('student.identity.index') }}">
                    <i class="fas fa-id-card mr-1"></i> Identity & Documents
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'avatar' ? 'active' : '' }}" href="{{ router('student.identity.avatar') }}">
                    <i class="fas fa-user-circle mr-1"></i> Profile Picture
                </a>
            </li>
            @if(empty($_SESSION['POLIBATAM_STUDENT']))
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'security' ? 'active' : '' }}" href="{{ router('student.identity.security') }}">
                        <i class="fas fa-lock mr-1"></i> Security & Password
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>