<!DOCTYPE html>
<html lang="en">
<head>
  <title>@yield('title')</title>
  <meta name="description" content="@yield('metdescp')">
 @include('partials.hscript')
 @yield('customCss')

</head>

<body id="body">
    <div id="pagesubmitloader" style="display:none;">
    <img src="{{ asset('admintheme/assets/img/ball-triangle.svg')}}" alt="processing..." />
    </div>
    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
        @include('partials.header')

        @include('partials.sidebar')
        <!-- Main Content -->
        <div class="main-content">

            @yield('main-content')

        </div>
        @include('partials.footer')
        </div>
    </div>
    @include('partials.fscript')

</body>



@yield('customJS')
</html>
