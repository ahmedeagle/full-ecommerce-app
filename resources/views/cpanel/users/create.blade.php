@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>إضافة مستخدم</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>المستخدمين</li> 
                    <li class="active">إضافة مستخدم</li> 
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
    </div>
    <div class="spacer-25"></div>
    <div class="col-md-3">
        <div class="profile-card">
            <div class="profile-img">
                <img src="{{ url('admin-assets/images/avatar_big_ic.png') }}" alt="">
            </div><!--End profile-img-->
            <div class="profile-name">
                
            </div><!--End profile-name-->
            <div class="spacer-20"></div>
            <div class="profile-menu">
                <ul>
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            بيانات الحساب 
                        </a>
                    </li>
                </ul>
            </div><!--End profile-menu-->
        </div><!--End profile-card-->
    </div><!--End col-md-3-->
    <div class="col-md-9">
        <div class="profile-content profile-account-setting">
            <div class="profile-widget-title pattern-bg">
               نموذج إضافة مستخدم جديد
            </div><!--End profile-widget-title-->
            <div class="profile-widget-content">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> {{ Session::get('success') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif

                @if(Session::has('errors'))
                    <div class="alert alert-danger">
                        <strong>خطأ !</strong> {{ Session::get('errors') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                <form id="user-form" class="form ui" method="post" action="{{ route('user.store') }}">
                    <div class="form-title">من فضلك إملى الحقول التالية</div>
                    <div class="form-note">[ <span class="require">*</span> ] حقل مطلوب</div>
                    <div class="ui error message"></div>
                    <!-- <div class="form-group"> -->
                    <div class="ui field">
                        <label>
                            الأسم بالكامل <span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="text" value="{{ old('full_name') }}" name="full_name">
                        </div>
                    </div><!--End form-group-->
                    <div class="ui field">
                        <label>
                           البريد الألكترونى <span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="email" value="{{ old('email') }}" name="email">
                        </div>
                    </div><!--End form-group-->
                    <div class="spacer-20"></div>
                    <div class="ui field">
                        <label>
                            الدولة :<span class="require">*</span>
                        </label>
                        <div class="ui input">
                            <select class="country" name="country">
                                <option value="">إختار الدولة</option>
                                @if($countries->count())
                                    @foreach($countries as $country)
                                        <option value="{{ $country->country_id }}">{{ $country->country_en_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div><!--End form-widget-->
                    <div class="ui field">
                        <label>
                           المدينة :  <span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input cityDiv">
                            <select id="cities" class="city" name="city">
                                <option value="">إختار المدينة</option>
                            </select>
                        </div>
                    </div><!--End form-group-->
                    <div class="inline-form ui field">
                        <label class="col-sm-12">
                           رقم الهاتف :  <span class="require">*</span>
                        </label>
                        <div class="clearfix"></div>
                        <div class="ui input c-code">
                            <input class="form-control country_code" placeholder="مثال : 002" type="text" name="country_code">
                        </div>
                        <div class="ui input c-phone">
                            <input class="form-control phone" placeholder="مثال : 01090353855" type="text" name="phone">
                        </div>
                    </div><!-- End inline-from -->
                    <hr>
                    <div class="spacer-20"></div>
                    <div class="ui field">
                        <label>
                            كلمة المرور :  <span class="require">*</span>
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="text" value="{{ old('password') }}" name="password">
                        </div>
                    </div><!--End form-group-->
                    <div class="ui field">
                        <label>
                            تاكيد كلمة المرور
                        </label><!--End label-->
                        <div class="ui input">
                            <input class="form-control" type="text" value="{{ old('passwordcon') }}" name="passwordcon">
                        </div>
                    </div><!--End form-group-->
                    <hr>
                    <div class="spacer-20"></div>
                    <div class="ui form">
                        <button type="submit" class="custom-btn">
                            <i class="fa fa-plus"></i>
                            إضافة
                        </button>
                    </div>
                </form><!-- End form-->
            </div><!--profile-->                                                            
        </div>
    </div><!--End col-md-9-->
</div><!--End Content-->
@stop
@section('customJs')
<script type="text/javascript">
    /*Select With Search
    ============================*/
    $(document).ready(function () {
        "use strict";
        $(".country").select2();
        $("body").on("change", ".country", function(){
            var country = $(this).val();
            getCountryCities("{{ route('country.cities') }}", country, 'en', $('#cities'), $('.country_code'), $(".phone"), 1);
        });
     });
</script>
@stop