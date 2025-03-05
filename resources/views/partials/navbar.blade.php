<nav class="navbar navbar-expand-lg navbar-light px-3 bg-light">
    <div class="container-fluid">
        <a href="/dashboard" class="d-flex align-items-center me-md-auto text-black text-decoration-none">
            <img src="{{ asset('assets/logo.svg') }}" alt="Dashboard" style="width: 25px; height: 25px; margin-right: 10px;">
            <span class="fs-4">Admin Panel</span>
            </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href=""></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href=""></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href=""></a>
                </li>
            </ul>
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                {{ app()->getLocale() === 'en' ? 'Language' : 'Bahasa' }}
                </button>
                <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                    <li><a class="dropdown-item" href="{{ route('set.language', 'en') }}">English</a></li>
                    <li><a class="dropdown-item" href="{{ route('set.language', 'id') }}">Indonesia</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
