@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>تعديل حساب الموصل</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li><a href="#">الموصلين</a></li>
                    <li class="active">تعديل حساب الموصل</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                    نموذج تعديل حساب الموصل 
                                    </div>
                <div class="widget-content">
                    @if($delivery != NULL)
                    <form class="ui form" enctype="multipart/form-data" id="create-delivery" method="post" action="{{ route('deliveries.update') }}">
                        <input type="hidden" name="id" value="{{ $delivery->delivery_id }}" />
                        <div class="form-title">من فضلك قم بملء كل البيانات المطلوبه.</div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>
                        @if(!empty($errors->first()))
                            <div class="alert alert-danger">
                                <strong>Error!</strong> {{ $errors->first() }}
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
                                    <input name="fname" id="fname" type="text" placeholder="الإسم الاول" value="{{ $delivery->firstname }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>الإسم الثانى :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="sname" id="sname" type="text" placeholder="الإسم الثانى" value="{{ $delivery->secondname }}" />
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الإسم الثالث :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="tname" id="tname" type="text" placeholder="الإسم الثالث" value="{{ $delivery->thirdname }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>الإسم الرابع :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="lname" id="lname" type="text" placeholder="الإسم الرابع" value="{{ $delivery->lastname }}" />
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
                                                <option {{ ($country->country_id == $delivery->country_id)? 'selected' : '' }} value="{{ $country->country_id }}">{{ $country->country_ar_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="ui field cityDiv">
                                <label>المدن :<span class="require">*</span></label>
                                <select id="cities" class="ui dropdown city" name="cities">
                                    <option value="">قم بإختيار مدينه</option>
                                    @if($cities->count())
                                        @foreach($cities AS $city)
                                            <option {{ ($city->city_id == $delivery->city_id)? 'selected' : '' }} value="{{ $city->city_id }}">{{ $city->city_ar_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            بيانات التواصل والداخول
                        </div>
                        <div>
                            <div class="ui field">
                                <label>البريد الألكترونى <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="email" id="email" type="email" placeholder="البريد الإلكترونى" value="{{ $delivery->email }}" />
                                </div>
                            </div>
                        </div>
                        <div class="ui field">
                            <label>رقم الجوال : <span class="require">*</span></label>
                            <div class="inline-form">
                                <div class="form-group col-md-1 col-sm-2">
                                    <input class="form-control country_code" value="{{ $delivery->country_code }}" placeholder="مثال : 202" maxlength="4" name="country_code" id="country_code" type="text">
                                </div>
                                <div class="form-group col-md-11 col-sm-10">
                                    <input class="form-control phone" value="{{ $delivery->phone }}" name="phone" id="phone" placeholder="مثال : 1234546789" maxlength="11" type="text">
                                </div>
                            </div>
                        </div>
                        <span class="spacer-25"></span>
                        <div class="two fields">
                            <div class="ui field">
                                <label>كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="pass" id="pass" type="text" placeholder="كلممة المرور" value="" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>تاكيد كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="passcon" id="passcon" type="text" placeholder="تاكيد كلمة المرور" value="" />
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
                                    <input name="car_type" id="car_type" type="text" placeholder="نوع السيارة" value="{{ $delivery->car_type }}" />
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
                        <div class="three fields">
                            <div class=" ui field">
                                <label class="custom-file">
                                    الوصره الاولى للسيارة : <span class="require">*</span></label>
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
                            <div class=" ui field">
                                <label class="custom-file">
                                    الصوره الثالثه للسيارة : <span class="require">*</span></label>
                                    <input type="file" name="car3" value="" id="car3" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <!-- <div class="widget-title">
                            Set Delivery location
                        </div>
                        <div id="map">
                            <input id="pac-input" type="text" placeholder="Search Box"/>
                        </div>
                        <div class="spacer-25"></div> -->
                        <div class="ui right algined inline field">
                            <button type="submit" class="btn btn-primary creat-btn">
                                <i class="fa fa-plus"></i>
                                تحديث
                            </button>
                        </div>
                    </form>
                    @endif
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