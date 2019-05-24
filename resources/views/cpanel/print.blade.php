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
        <title>Bill</title>
        
        <!-- Google Web Fonts 
		===========================-->        
        <link href="http://fonts.googleapis.com/earlyaccess/droidarabickufi.css" rel="stylesheet" type="text/css">
        
        <!-- Css Base And Vendor 
        ===================================-->
        <link rel="stylesheet" href="{{ url('admin-assets/print/vendor/bootstrap/bootstrap.min.rtl.css') }}">
        <!-- Site Css
        ====================================-->
        <link rel="stylesheet" href="{{ url('admin-assets/print/css/style.css') }}">
        
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="bill-wrap">
            <div class="bill-header">
                <div class="col-xs-5 ar-text slogon">
                    <span>تطبيق مذاق للمأكولات</span>
                    <span>الفيصلية - جدة - المملكة العربية السعودية</span>
                    <span>جوال : 0123456789</span>
                    <span>تلفاكس : 123456789</span>
                </div>
                <div class="col-xs-2">
                    <div class="logo" style="overflow: visible;">
                        <img src="{{ url('admin-assets/print/images/unnamed.png') }}">
                    </div>
                </div>
                <div class="col-xs-5 en-text slogon">
                    <span>Mathaq application</span>
                    <span>Al Faisaliah - Jeddah - Kingdom of Saudi Arabia</span>
                    <span>phone : 123456789</span>
                    <span>telefax : 123456789</span>
                </div>
            </div><!--End  bill header-->
            <div class="bill-info">
                <div class=" col-xs-4 quantity">
                    <span>{{ $value }}</span>
                </div>
                <div class="col-xs-4 bill-title">
                    <span>{{ $kind }}</span>
                    <!-- <span>support exchange</span> -->
                </div>
                <div class="col-xs-4 en-text">No :</div>
            </div><!--End bill info-->
            <div class="bill-content">
                <div class="ar-text">التاريخ : </div>
                <div class="user-sign"><?php echo date('Y-m-d'); ?></div>
                <div class="en-text">Date :</div>
            </div><!--End bill content-->
            <div class="bill-content">
                <div class="ar-text">{{ ($kind == "سند صرف")? "صرفنا إلى " : "قبضننا من" }} السيد / ة:  </div>
                <div class="user-sign">{{ $name }}</div>
                <div class="en-text">received from :</div>
            </div><!--End bill content-->
            <div class="bill-content">
                <div class="ar-text">الملبغ بالريال السعودى :</div>
                <div class="user-sign"> {{ $value }} </div>
                <div class="en-text">amount [SR] : </div>
            </div><!--End bill content-->
            <div class="bill-content">
                <div class="ar-text">نقدا  </div>
                <div class="user-sign">نقدا</div>
                <div class="en-text">Cash : </div>
            </div><!--End bill content-->
            <div class="bill-content">
                <div class="ar-text">وذلك عن : </div>
                <div class="user-sign tall-height">إستحقاق رصيد</div>
                <div class="en-text">for :</div>
            </div><!--End bill content-->
            <div class="bill-sign">
                <div class="col-xs-4 bill-sign-item">
                    <div class="bill-sign-title">
                        <div class="ar-text"> المحاسب</div>
                        <div class="en-text">receiver by </div>
                    </div><!--End bill-sign-title-->
                    <div class="user-sign">
                        <span>
                            ...........................................................
                        </span>
                    </div><!--End user-sign-->
                </div><!--End bill-sign-item-->
                <div class="col-xs-4 bill-sign-item">
                    <div class="bill-sign-title">
                        <div class="ar-text">المستلم</div>
                        <div class="en-text">receiver by </div>
                    </div><!--End bill-sign-title-->
                    <div class="user-sign">
                        <span>
                            ...........................................................
                        </span>
                    </div><!--End user-sign-->
                </div><!--End bill-sign-item-->
                <div class="col-xs-4 bill-sign-item">
                    <div class="bill-sign-title">
                        <div class="ar-text">المدير</div>
                        <div class="en-text">manager</div>
                    </div><!--End bill-sign-title-->
                    <div class="user-sign">
                        <span>
                            ...........................................................
                        </span>
                    </div><!--End user-sign-->
                </div><!--End bill-sign-item-->
            </div><!--End bill sign-->
        </div>
        <!-- JS Base And Vendor 
        ===================================-->
        <script src="{{ url('admin-assets/print/vendor/jquery/jquery-3.2.1.js') }}"></script>
        <script src="{{ url('admin-assets/print/vendor/bootstrap/bootstrap.min.js') }}"></script>
          
    </body>