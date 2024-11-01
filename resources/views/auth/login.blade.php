<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- ===============================================-->
    <!--   Document Title  -->
    <!-- ===============================================-->
    <title>Đăng nhập</title>
    <!--   Favicons  -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="{{ asset('css/theme.min.css') }}">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <div class="container">
            <div class="row flex-center min-vh-100 py-5">
                <div class="col-sm-10 col-md-8 col-lg-5 col-xl-5 col-xxl-3">
                    <a class="d-flex flex-center text-decoration-none mb-4" href="javascrift:">
                        <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                            <img src="{{ asset('img/logo-light.png') }}" alt="phoenix" width="120" />
                        </div>
                    </a>
                    <div class="text-center mb-7">
                        <h3 class="text-body-highlight">Đăng nhập</h3>
                        <p class="text-body-tertiary">Truy cập vào tài khoản của bạn</p>
                        @if (session(true)->has('error'))
                            <p class="text-danger"> {{ session(true)->get('error') }} </p>
                        @endif
                    </div>

                    <a href="{{ $url }}" class="btn btn-phoenix-secondary w-100 mb-3">
                        <span class="fab fa-google text-danger me-2 fs-9"></span>
                        Đăng nhập với Google
                    </a>

                    {{-- <div class="position-relative">
                        <hr class="bg-body-secondary mt-5 mb-4" />
                        <div class="divider-content-center">hoặc bằng tài khoản</div>
                    </div>
                    <form method="POST" action="/login">
                        @csrf
                        <div class="mb-3 text-start">
                            <label class="form-label" for="username">Tên Đăng nhập</label>
                            <div class="form-icon-container">
                                <input class="form-control form-icon-input" value="{{ old('username') }}" type="text" name="username" placeholder=" Tên đăng nhập" />
                                <span  class=" fas fa-user text-body fs-9 form-icon"></span>
                                @error('username')
                                    <span class="fs-9 fw-bold text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label" for="password">Mật khẩu</label>
                            <div class="form-icon-container">
                                <input class="form-control form-icon-input" value="{{ old('password') }}" type="password" name="password" placeholder="Mật khẩu" />
                                <span class=" fas fa-key text-body fs-9 form-icon"></span>
                                @error('password')
                                    <span class="fs-9 fw-bold text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 mb-3">Đăng Nhập</button>
                    </form> --}}
                </div>
            </div>
        </div>
    </main>
</body>
<script src="{{ asset('js/all.min.js') }}"></script>
</html>
