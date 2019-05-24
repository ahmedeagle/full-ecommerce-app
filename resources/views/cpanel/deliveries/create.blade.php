@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>إضافة موصل</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>الموصلين</li>
                    <li class="active">إضاف موصل</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                    نموذج إضافة موصل
                </div>
                <div class="widget-content">
                    <form class="ui form" enctype="multipart/form-data" id="create-delivery" method="post" action="{{ route('deliveries.store') }}">
                        <div class="form-title">من فضلك إملئ البيانات التالية</div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>
                        @if(!empty($errors->first()))
                            <div class="alert alert-danger">
                                <strong>خطا!</strong> {{ $errors->first() }}
                            </div>
                        @endif
                        @if(!empty($msg))
                            <div class="alert alert-success">
                                <strong>تم بنجاح !</strong> {{ $msg }}
                            </div>
                        @endif
                        <div class="widget-title">
                            البيانات الشخصية
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الإسم الاول :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="fname" id="fname" type="text" placeholder="الإسم الاول" value="{{ old('fname') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>الإسم الثانى :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="sname" id="sname" type="text" placeholder="الإسم الثانى" value="{{ old('sname') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الإسم الثالث :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="tname" id="tname" type="text" placeholder="الإسم الثالث" value="{{ old('tname') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>الإسم الرابع :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="lname" id="lname" type="text" placeholder="الإسم الرابع" value="{{ old('lname') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            بيانات العنوان
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الدول :<span class="require">*</span></label>
                                <div class="ui input">
                                    <select class="ui dropdown country" id="countries" name="countries">
                                        <option value="">قم بإختيار دوله</option>
                                        @if($countries->count())
                                            @foreach($countries AS $country)
                                                <option value="{{ $country->country_id }}">{{ $country->country_ar_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="ui field cityDiv">
                                <label>المدن :<span class="require">*</span></label>
                                <select id="cities" class="ui dropdown city" name="cities">
                                    <option value="">قم بإختيار مدينه</option>
                                </select>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                        بيانات التواصل والدخول
                        </div>
                        <div>
                            <div class="ui field">
                                <label>البريد الألكترونى <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="email" id="email" type="email" placeholder="البريد الإلكترونى" value="{{ old('email') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="ui field">
                            <label>رقم الجوال : <span class="require">*</span></label>
                            <div class="inline-form">
                                <div class="form-group col-md-1 col-sm-2">
                                    <input class="form-control country_code" value="{{ old('country_code') }}" placeholder="مثال : 202" maxlength="4" name="country_code" id="country_code" type="text">
                                </div>
                                <div class="form-group col-md-11 col-sm-10">
                                    <input class="form-control phone" value="{{ old('phone') }}" name="phone" id="phone" placeholder="مثال : 1234546789" maxlength="11" type="text">
                                </div>
                            </div>
                        </div>
                        <span class="spacer-25"></span>
                        <div class="two fields">
                            <div class="ui field">
                                <label>كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="password" id="password" type="text" placeholder="كلممة المرور" value="{{ old('password') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>تاكيد كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="passwordcon" id="passwordcon" type="text" placeholder="تاكيد كلمة المرور" value="{{ old('passwordcon') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            بيانات السيارة و الاوراق
                        </div>
                        <div>
                            <div class="ui field">
                                <label>نوع السيارة : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="car_type" id="car_type" type="text" placeholder="نوع السيارة" value="{{ old('car_type') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class=" ui field">
                                <label class="custom-file">
                                    الهوية : <span class="require">*</span></label>
                                    <input type="file" name="identity" value="" id="identity" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                            </div>
                            <div class=" ui field">
                                <label class="custom-file">
                                    الرخصه : <span class="require">*</span></label>
                                    <input type="file" name="license" value="" id="license" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class=" ui field">
                                <label class="custom-file">
                                    التامين : <span class="require">*</span></label>
                                    <input type="file" name="insurance" value="" id="insurance" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                            </div>
                            <div class=" ui field">
                                <label class="custom-file">
                                    التفويض : <span class="require">*</span></label>
                                    <input type="file" name="authorization" value="" id="authorization" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class=" ui field">
                                <label class="custom-file">
                                   الصورة الأولى للسيارة : <span class="require">*</span></label>
                                    <input type="file" name="car1" value="" id="car1" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                            </div>
                            <div class=" ui field">
                                <label class="custom-file">
                                    الصوره الثانية للسيارة : <span class="require">*</span></label>
                                    <input type="file" name="car2" value="" id="car2" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class=" ui field">
                                <label class="custom-file">
                                    الصوره الثالثه للسيارة : <span class="require">*</span></label>
                                    <input type="file" name="car3" value="" id="car3" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="ui right algined inline field">
                            <button type="submit" class="custom-btn">
                                <i class="fa fa-plus"></i>
                                إضافة
                            </button>
                        </div>
                    </form>
                </div><!-- end widget-content -->
            </div><!-- end widget -->
        </div>
    </div><!-- end container -->
</div>

@stop
@section('customJs')
<script>
    $(document).ready(function(){
        $("body").on("change", ".country", function(){
            var country = $(this).val();
            getCountryCities("{{ route('country.cities') }}", country, 'en', $('#cities'), $('.country_code'), $(".phone"), 2);
        });
    });
</script>
@stop