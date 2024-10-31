<!doctype html>

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('admin/assets') }}" data-template="vertical-menu-template-free" data-style="light">

<head>
    

    <!-- Select2 CSS -->
    
    @include('admin.components.styles')
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            @include('admin.components.asidemenu')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                @include('admin.components.navbar')

                <!-- / Navbar -->

                <!-- Content wrapper -->
                @yield('admincontent')

                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle">


            
        </div>
    </div>
    <!-- / Layout wrapper -->

 <!----   <div class="buy-now">
        <a href="#" target="_blank" class="btn btn-danger btn-buy-now">Upgrade to Pro</a>
    </div> --->

    
    <!-- Core JS -->
    @include('admin.components.scripts')
</body>





<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>




</html>
