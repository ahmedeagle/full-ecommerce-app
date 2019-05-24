@extends('cpanel.layout.master')
@section('content')
   <div class="content">
        <div class="col-sm-12">
            <div class="widget">
                <div class="widget-content">
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($users != NULL)? $users : 0 }}" data-speed="2500">{{ ($users != NULL)? $users : 0 }}</div>
                                <span>المستخدمين</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($providers != NULL)? $providers : 0 }}" data-speed="2500">{{ ($providers != NULL)? $providers : 0 }}</div>
                                <span>مقدمين الخدمات</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($deliveries != NULL)? $deliveries : 0 }}" data-speed="2500">{{ ($deliveries != NULL)? $deliveries : 0 }}</div>
                                <span>الموصلين</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-cutlery"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($meals != NULL)? $meals : 0 }}" data-speed="2500">{{ ($meals != NULL)? $meals : 0 }}</div>
                                <span>الوجبات</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($sale != NULL)? $sale : 0 }}" data-speed="2500">{{ ($sale != NULL)? $sale : 0 }}</div>
                                <span>المدخلات</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($return != NULL)? $return : 0 }}" data-speed="2500">{{ ($return != NULL)? $return : 0 }}</div>
                                <span>المرجعات</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                    <div class="col-md-4">
                        <div class="counter">
                            <div class="counter-icon">
                                <i class="fa fa-comment-o"></i>
                            </div>
                            <div class="counter-content"> 
                                <div class="timer" data-to="{{ ($comments != NULL)? $comments : 0 }}" data-speed="2500">{{ ($comments != NULL)? $comments : 0 }}</div>
                                <span>التعليقات</span>
                            </div>
                        </div>
                    </div><!--End col-md-4-->
                </div>
            </div>
        </div>
        <div class="footer-copy-rights">جميع الحقوق محفوظة <a href="https://www.al-yasser.com.sa/en/">مجموعة الياسر</a> ©2017
        </div>
    </div>
    
@stop