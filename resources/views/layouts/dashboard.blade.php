<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="generator" content="">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('stylesheet')

    <script>
        var settings = {
            baseUrl: '{{ url('/') }}',
            cdnUrl: '{{ url('/storage') }}',
        }
    </script>

    <!-- Custom styles for this template -->
    <link href="{{ asset('templates/dashboard/css/dashboard.css') }}" rel="stylesheet">
</head>

<body>

    <header class="border-bottom">
        <nav class="position-relative">
            <div class="logo ms-4">
                <img src="/images/cake-shop.webp" alt="..." class="img-fluid">
            </div>
            <div class="profile">
                <div class="user">
                    <h4>{{ Auth::user()->name }}</h4>
                    <p>{{ Auth::user()->email }}</p>
                </div>
                <div class="img-box">
                    <img src="{{ Auth::user()->avatar && Storage::disk('public')->exists(Auth::user()->avatar) ? Storage::disk('public')->url(Auth::user()->avatar) : '/images/user-profile-icon.webp' }}"
                        alt="some user image">
                </div>
            </div>
            <div class="menu">
                <ul class="ps-2">
                    <li><a href="{{ route('profile.edit') }}"><i class="ph-bold ph-user"></i>&nbsp;Profile</a></li>
                    {{-- <li><a href="#"><i class="ph-bold ph-envelope-simple"></i>&nbsp;Inbox</a></li>
                    <li><a href="#"><i class="ph-bold ph-gear-six"></i>&nbsp;Settings</a></li>
                    <li><a href="#"><i class="ph-bold ph-question"></i>&nbsp;Help</a></li> --}}
                    <li><a href="javascript:void(0);"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="ph-bold ph-sign-out"></i>&nbsp;Sign Out</a></li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        {{ csrf_field() }}
                    </form>
                </ul>
            </div>
        </nav>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block overflow-auto collapse min-vh-100 border-end">
                <div class="position-sticky mt-3">
                    <ul class="nav flex-column gap-2">
                        <li class="nav-item">
                            <a class="nav-link {{ url()->current() == route('admin') ? 'active' : '' }}"
                                aria-current="page" href="/admin">
                                <i class="fa-solid fa-sliders"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ strpos(url()->current(), 'users') !== false ? 'active' : '' }}"
                                href="{{ route('users.index') }}">
                                <i class="fa-solid fa-user-group"></i> Users
                            </a>
                        </li>
                        <li class="nav-item text-nowrap">
                            {{-- @if (Auth::check())
                                <a class="btn btn-light" href="javascript:void(0);"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-arrow-right-square-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M0 14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v12zm4.5-6.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5a.5.5 0 0 1 0-1z" />
                                    </svg>
                                    Logout
                                </a>
                            @endif

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                {{ csrf_field() }}
                            </form> --}}
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-3">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/admin.js?v=' . config('versions.js')) }}"></script>
    <script>
        $(document).ready(function() {
            $(".profile").click(function() {
                var isActive = $(".menu").hasClass("active");
                $(".menu").toggleClass("active", !isActive);
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
