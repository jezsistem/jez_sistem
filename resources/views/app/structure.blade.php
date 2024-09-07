<!DOCTYPE html>
<html lang="en">
@include('app._partials.head')
<!--begin::Body-->

<body id="kt_body"
      class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">

<style>


    #reader {
        width: 500px;

        border-radius: 60px;

        margin-bottom: 20px;
    }

    #result {

        text-align: center;

        font-size: 1.5rem;
    }
</style>
<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="d-flex flex-row flex-column-fluid page">
        @include('app._partials.sidebar')
        <!--begin::Wrapper-->
        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
            @include('app._partials.header')
            @yield('content')
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->
</div>
<!--end::Main-->
@include('app._partials.user_panel')
@include('app._partials.scroll_top')


</body>
<!--end::Body-->

</html>
