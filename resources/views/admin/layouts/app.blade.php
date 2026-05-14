<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.layouts.partials.head')
</head>

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr"
    data-pc-theme_contrast="" data-pc-theme="light">

    @include('admin.layouts.partials.loader')

    @include('admin.layouts.partials.sidebar')

    @include('admin.layouts.partials.topbar')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                                @yield('breadcrumb')
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h4 class="mb-0">@yield('page_title', 'Dashboard')</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            @yield('content')

        </div>
    </div>
    <!-- [ Main Content ] end -->

    @include('admin.layouts.partials.footer')

    @include('admin.layouts.partials.scripts')

</body>

</html>