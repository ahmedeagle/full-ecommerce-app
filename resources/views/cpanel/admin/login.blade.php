<!DOCTYPE html>
<html>

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
       
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,600,800">
        
        <!-- Css Base And Vendor 
        ===================================-->
        <link rel="stylesheet" href="{{ url('admin-assets/vendor/bootstrap/css/bootstrap-en.css') }}">
        <link rel="stylesheet" href="{{ url('admin-assets/vendor/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ url('admin-assets/vendor/magnific-popup/css/magnific-popup.css') }}">
        
        <!-- Site Css
        ====================================-->
        <link rel="stylesheet" href="{{ url('admin-assets/css/pages.css') }}">
        
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div id="password-recover-dialog" class="mfp-with-anim mfp-hide mfp-dialog">
            <div class="dialog-title">
                إسترجاع كلمة المرور
            </div>    
            <div class="form-box">
                <div class="form-title">
                    <i class="fa fa-smile-o"></i>
                    إسترجاع كلمة المرور
                </div>    
                <form class="login-form">
                    <div class="form-group">
                        <input class="form-control" placeholder="البريد الألكترونى" type="email">
                    </div><!--End form-group-->
                </form><!--End dialog-form-->
                <div class="form-footer">
                    <button type="submit" class="custom-btn col-sm-12">إسترجاع</button>
                </div>
            </div><!--End login-dialog-->
        </div><!--End login-dialog-->
        <div id="wrapper" class="theme-2">
            <div class="main">
                <div class="page-content">
                    <div id="welcome-home">
                        <div class="container">
                            <div class="row">
                                <div class="center-height text-center col-md-12">
                                    <div class="logo">
                                        لوحة تحكم مذاق
                                    </div>
                                    <div class="form-box">
                                        <div class="form-title">
                                            <i class="fa fa-lock"></i>
                                            تسجيل الدخول
                                        </div>    
                                        <form class="login-form" method="post" action="{{ route('admin.login') }}">
                                            @if(!empty($errors->first()))
                                                <div class="alert alert-danger">
                                                    <strong>Error!</strong> {{ $errors->first() }}
                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <input class="form-control" name="email" id="email" value="{{ old('name') }}" placeholder="البريد الألكترونى" type="email">
                                            </div><!--End form-group-->
                                            <div class="form-group">
                                                <input class="form-control" name="password" id="password" placeholder="الرقم السرى" value="{{ old('name') }}" type="password">
                                            </div><!--End form-group-->
                                            <!-- <div class="form-group">
                                                <div class="remmeber">
                                                    <input id="remmeber" type="checkbox">
                                                    <label for="remmeber">
                                                      remember
                                                    </label>
                                                </div>
                                                <a class="popup-text forget" href="#password-recover-dialog" data-effect="mfp-zoom-out">
                                                    Forget Password ?
                                                </a>
                                            </div> -->
                                            <div class="form-footer">
                                             <button type="submit" class="custom-btn">تسجيل الدخول</button>
                                        </div>
                                        </form><!--End dialog-form-->
                                        
                                      
                                    </div><!--End login-dialog-->
                                    <div class="copy-rights">2017 © جميع الحقوق محفوظة - مذاق</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--End Page-Content-->
           </div><!--End Main-->
        </div>
        <!-- JS Base And Vendor 
        ===================================-->
        <script src="{{ url('admin-assets/vendor/jquery/jquery.js') }}"></script>
        <script src="{{ url('admin-assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ url('admin-assets/vendor/magnific-popup/js/magnific-popup.js') }}"></script>
        <script src="{{ url('admin-assets/js/pages.js') }}"></script>
     
    </body>