<!DOCTYPE html>
<html>
    @if (Auth::guard('zad_admin')->guest())
        <script type="text/javascript">window.location.href="{{ route('loginView') }}";</script>
    @endif
    <head>
        <!-- Meta Tags
        ======================-->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="author" content="">
        
        <!-- Title Name
        ================================-->
        <title>مذاق - لوحة التحكم</title>

        <!-- Fave Icons
        ================================-->
        <link rel="shortcut icon" href="{{ url('admin-assets/images/fav.png') }}">
          
        <!-- Google Web Fonts 
		===================================-->
       
         <link href="http://fonts.googleapis.com/earlyaccess/droidarabickufi.css" rel="stylesheet" type="text/css">
        
        <!-- Css Base And Vendor 
        ===================================-->
        <link rel="stylesheet" href="{{ url('admin-assets/vendor/bootstrap/css/bootstrap-ar.css') }}">
        <link rel="stylesheet" href="{{ url('admin-assets/vendor/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ url('admin-assets/vendor/semantic/semantic.min.css') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
        <link href="{{ url('admin-assets/vendor/datepicker/jquery.datetimepicker.min.css') }}" rel="stylesheet">
        <link href="{{ url('admin-assets/vendor/colorpicker/jquery.minicolors.css') }}" rel="stylesheet">
        <link href="{{ url('admin-assets/vendor/magnific-popup/css/magnific-popup.css') }}" rel="stylesheet">
        <link href="{{ url('admin-assets/vendor/magnific-popup/css/custom.css') }}" rel="stylesheet">
        <!-- Site Css
        ====================================-->
        <link rel="stylesheet" href="{{ url('admin-assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ url('admin-assets/css/custom.css') }}">
        <link rel="stylesheet" href="{{ url('admin-assets/css/rtl.css') }}">
        <style>
            /* Always set the map height explicitly to define the size of the div
            * element that contains the map. */
            #map {
                position: relative;
                overflow: hidden;
                height: 500px;
                margin: 15px auto;
                width: 100%;
            }
            /* Optional: Makes the sample page fill the window. */
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
         </style>
        
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
    <div>

    </div>
        <div id="wrapper">
            <div class="main">
                <div class="side-menu">
                    <div class="logo">
                        لوحة تحكم مذاق
                    </div><!--End Logo-->
                    <aside class="sidebar">
                        <ul class="side-menu-links">
                            <li>
                                <a rel="nofollow" rel="noreferrer" href="{{ route('home') }}">الرئيسية</a>
                            </li>
                            <li>
                                <a rel="nofollow" rel="noreferrer" href="{{ route('setting.show') }}">الأعدادت</a>
                            </li>
                            <li>    
                                <a rel="nofollow" rel="noreferrer" href="{{ route('create_admin') }}">إضافة مدير</a>
                            </li>
                            <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                    المستخدمين
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('user.show') }}">قائمة المستخدمين</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('user.create') }}">إضافة مستخدم </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                    مقدمى الخدمة
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('provider.show') }}">قائمة مقدمى الخدمة</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('provider.create') }}">إضافة مقدم خدمة</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('provider.income.show') }}">إيرادات مقدمى الخدمة</a>
                                    </li>
                                    
                                </ul>
                            </li>
                            <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                    المسوقين
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('marketers.show') }}">قائمة المسوقين</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('marketer.create') }}">إضافة مسوق</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('marketer.income.show') }}">إيرادات المسوقين</a>
                                    </li>
                                    
                                </ul>
                            </li>
                            <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                    الموصلين
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('deliveries.show') }}">قائمة الموصلين</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('deliveries.create') }}">إضافة موصل</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('delivery.income.show') }}">قائمة إيرادات الموصلين</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                    التصنفيات
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('category.show') }}">قائمة التصنفيات</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('category.create') }}">إضافة تصنيف جديد</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a rel="nofollow" rel="noreferrer" href="{{ route('orders.filter') }}">الطلبات</a>
                            </li>
                            <!-- <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                    Marketers
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="#">Marketers filter</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="#">Add marketer</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="#">Marketer income</a>
                                    </li>
                                </ul>
                            </li> -->
                            <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                   سحب الرصيد
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('requests.today') }}">عمليات السحب اليومية</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('requests.show') }}">قائمة عمليات السحب</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                    الفواتير
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('invoices.filter') }}">قائمة الفواتير</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('invoices.create') }}">إنشاء فاتورة</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a rel="nofollow" rel="noreferrer" href="{{ route('balances.show') }}">الأرصدة</a>
                            </li>
                            <li>
                                <a rel="nofollow" rel="noreferrer" href="{{ route('income.app') }}">إيرادات التطبيق</a>
                            </li>
                            <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                    الشكاوى
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('complains.today') }}">الشكاوى اليومية</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('complains.show') }}">قائمة الشكاوى</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                   التعليقات
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('comments.today') }}">التعليقات اليومية</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('comments.show') }}">قائمة التعليقات</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="sub-menu">
                                <a rel="nofollow" rel="noreferrer" href="javascript:void(0);">
                                   التقييمات
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul>
                                    <li>
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('provider.evaluations.show') }}">تقييمات مقدمين الخدمة</a>
                                    </li>
                                    <li>    
                                        <a rel="nofollow" rel="noreferrer" href="{{ route('delivery.evaluations.show') }}">تقييمات الموصلين</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </aside>
                </div><!--End Side Menu-->
                <div class="page-content">
                     <div class="top-header">
                        <div class="toggle-icon"  data-toggle="tooltip" data-placement="left" title="إظهار/إخفاء القائمة">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        
                    </div>
                    @yield('content')
                    
                </div><!--End Page-Content-->
           </div><!--End Main-->
        </div>
           <!-- JS Base And Vendor 
        ===================================-->
        <script src="{{ url('admin-assets/vendor/jquery/jquery.js') }}"></script>
        <script src="{{ url('admin-assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>

        <script src="{{ url('admin-assets/vendor/datatables/datatables.js') }}"></script>        

        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
         <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.flash.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.print.min.js"></script>
        <script src="https://cdn.ckeditor.com/4.7.0/standard/ckeditor.js"></script>
    
        <script src="{{ url('admin-assets/vendor/semantic/semantic.min.js') }}"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        
        <script src="{{ url('admin-assets/vendor/datepicker/jquery.datetimepicker.full.min.js') }}"></script>
        
        <script src="{{ url('admin-assets/vendor/colorpicker/jquery.minicolors.min.js') }}"></script>

        <script src="{{ url('admin-assets/vendor/magnific-popup/js/magnific-popup.js') }}"></script>
        <script src="{{ url('admin-assets/vendor/count-to/jquery.countTo.js') }}"></script>

        <!-- Site JS
        ====================================-->
        <script src="{{ url('admin-assets/js/main.js') }}"></script>
        <script src="{{ url('admin-assets/js/custom.js') }}"></script>
        @yield('customJs')
        <!-- <script 
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCXBOJbpMWx-frwLP2BfsDdlzEG48mn9rc&callback=initMap" 
        async defer></script> -->
        <!-- 
            <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCXBOJbpMWx-frwLP2BfsDdlzEG48mn9rc&amp;sensor=false&amp;signed_in=true&amp;libraries=geometry,places"></script>
            <script src="https://google-maps-utility-library-v3.googlecode.com/svn-history/r287/trunk/markerclusterer/src/markerclusterer.js"></script>
            <script src="{{ url('admin-assets/js/maperizer/List.js') }}"></script>
            <script src="{{ url('admin-assets/js/maperizer/Maperizer.js') }}"></script>
            <script src="{{ url('admin-assets/js/maperizer/map-options.js') }}"></script>
            <script src="{{ url('admin-assets/js/maperizer/jqueryui.maperizer.js') }}"></script>
        -->
    </body>
</html>