<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.layouts.partials.head')
</head>

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
    
    @include('admin.layouts.partials.loader')

    <div class="auth-main">
        <div class="auth-wrapper v1">
            <div class="auth-form">
                @yield('content')
            </div>
        </div>
    </div>

    @include('admin.layouts.partials.scripts')

</body>
</html>
