@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>تعديل البيانات</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>مقدمى الخدمة</li>
                    <li class="active">تعديل البيانات</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                  نموذج تعديل البيانات
                </div>
                <div class="widget-content">
                    <form class="ui form"  enctype="multipart/form-data" id="create-provider" method="POST" action="{{ route('provider.update') }}">
                        <input type="hidden" value="{{ $provider->provider_id }}" id="provider_id" name="provider_id" />
                        <div class="form-title">من فضلك  إملئ الحقول التالية</div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>
                        @if(!empty($errors->first()))
                            <div class="alert alert-danger">
                                <strong>خطأ !</strong> {{ $errors->first() }}
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
                                <label>الأسم الأول :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="fname" id="fname" type="text" placeholder="الأسم الأول" value="{{ $provider->first_name }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>الأسم الأوسط :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="sname" id="sname" type="text" placeholder="الأسم الأوسط" value="{{ $provider->second_name }}" />
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الأسم الأخير :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="tname" id="tname" type="text" placeholder="الأسم الأخير" value="{{ $provider->third_name }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>إسم العائلة :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="lname" id="lname" type="text" placeholder="إسم العائلة" value="{{ $provider->last_name }}" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="ui field">
                                <label>إسم الماركة :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="bname" id="bname" type="text" placeholder="إسم الماركة" value="{{ $provider->brand_name }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                           تفاصيل العنوان
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الدول :<span class="require">*</span></label>
                                <div class="ui input">
                                    <select class="ui dropdown country" name="countries">
                                        <option value="">إختار</option>
                                        @if($countries->count())
                                            @foreach($countries AS $country)
                                                @if($country->country_id == $country_id)
                                                    <option selected value="{{ $country->country_id }}">{{ $country->country_en_name }}</option>
                                                @else
                                                    <option value="{{ $country->country_id }}">{{ $country->country_en_name }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="ui field cityDiv">
                                <label>المدن :<span class="require">*</span></label>
                                <select class="ui dropdown city" id="cities" name="cities">
                                    <option value="">إختار</option>
                                    @if($cities->count())
                                            @foreach($cities AS $city)
                                                @if($city->city_id == $city_id)
                                                    <option selected value="{{ $city->city_id }}">{{ $city->city_en_name }}</option>
                                                @else
                                                    <option value="{{ $city->city_id }}">{{ $city->city_en_name }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="ui field">
                                <label>العنوان : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="address" id="address" type="text" placeholder="العنوان" value="{{ $provider->address }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">صور للاوراق المطلوبة
                        </div>
                        <div class="two fields">
                            <div class=" ui field">
                                <label for="maroof_img">معروف</label>
                                <input type="file" id="maroof_img" name="maroof_img">
                                <!-- <div class="files-wr" data-label-text="معروف" data-count-files="1">
                                    <div class="one-file">
                                        <label for="maroof_img">معروف</label>
                                        <input name="maroof_img" id="maroof_img" type="file" />
                                        <div class="file-item hide-btn">
                                            <span class="file-name"></span>
                                            <span class="btn btn-del-file">x</span>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                            <div class=" ui field">
                                <div class="form-title">تاريخ إنتهاء معرووف :</div>
                                <div class="form-group">
                                    <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                        <input class="form-control" size="16" type="text" value="" readonly="" name="maroof_date" id="maroof_date">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class=" ui field">
                                <label for="health_certific">الشهادة الصحية</label>
                                <input name="health_certific" id="health_certific" type="file" />
                                <!-- <div class="files-wr" data-label-text="الشهادة الصحية" data-count-files="2">
                                    <div class="one-file">
                                        <label for="health_certific">الشهادة الصحية</label>
                                        <input name="health_certific" id="health_certific" type="file" />
                                        <div class="file-item hide-btn">
                                            <span class="file-name"></span>
                                            <span class="btn btn-del-file">x</span>
                                        </div>
                                    </div>
                                </div>-->
                            </div>
                            <div class=" ui field">
                                <div class="form-title">تاريخ إنتهاء الشهادة الصحية :</div>
                                <div class="form-group">
                                    <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                        <input class="form-control" size="16" type="text" value="" readonly="" name="health_certific_date" id="health_certific_date">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class=" ui field">
                                <label for="commercial_record">السجل التجارى</label>
                                <input name="commercial_record" id="commercial_record" type="file" />
                                <!-- <div class="files-wr" data-label-text="السجل التجارى" data-count-files="3">
                                    <div class="one-file">
                                        <label for="commercial_record">السجل التجارى</label>
                                        <input name="commercial_record" id="commercial_record" type="file" />
                                        <div class="file-item hide-btn">
                                            <span class="file-name"></span>
                                            <span class="btn btn-del-file">x</span>
                                        </div>
                                    </div>
                                </div>-->
                            </div>
                            <div class=" ui field">
                                <div class="form-title">تاريخ إنتهاء السجل التجارى :</div>
                                <div class="form-group">
                                    <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                        <input class="form-control" size="16" type="text" value="" readonly="" name="commercial_record_date" id="commercial_record_date">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                           معلومات التواصل والدخول
                        </div>
                        <div>
                            <div class="ui field">
                                <label>البريد الألكترونى : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="email" id="email" type="email" placeholder="البريد الألكترونى" value="{{ $provider->email}}" />
                                </div>
                            </div>
                        </div>
                        <!-- <div>
                            <div class="ui field">
                                <label>Phone : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="phone" id="phone" type="text" placeholder="Phon number" value="{{ $provider->phone }}" />
                                </div>
                            </div>
                        </div> -->
                        <div class="inline-form ui field">
                            <label class="col-md-2 col-sm-2">
                                رقم الهاتف: <span class="require">*</span>
                            </label>
                            <div class="ui input col-md-2 col-sm-3">
                                <input class="form-control country_code" id="country_code" value="{{ $provider->country_code  }}" placeholder="مثال : 0200" type="text" name="country_code">
                            </div>
                            <div class="ui input col-md-8 col-sm-7">
                                <input class="form-control phone" id="phone" value="{{ $provider->phone }}" placeholder="مثال : 01090353855" type="text" name="phone">
                            </div>
                        </div><!-- End inline-from -->
                        <span class="spacer-25"></span>
                        <div class="two fields">
                            <div class="ui field">
                                <label>كلمة المرور:<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="upassword" id="upassword" type="password" placeholder="كلمة المرور" value="" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>تأكيد كلمة المرور :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="upasswordcon" id="upasswordcon" type="password" placeholder="تأكيد كلمة المرور" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            تصنفيات مقدم الخدمة
                        </div>
                        <div class="form-title">التصنفيات :<span class="require">*</span></div>
                        <div class="form-group">
                            <select multiple="" class="form-control" name="categories[]">
                                @if($categories->count())
                                    @foreach($categories AS $category)
                                        @if(in_array($category->cat_id, $selectedCats))
                                            <option selected value="{{ $category->cat_id }}">{{ $category->cat_en_name }}</option>
                                        @else
                                            <option value="{{ $category->cat_id }}">{{ $category->cat_en_name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="ui right algined inline field">
                            <button type="submit" class="btn btn-primary creat-btn">
                                <i class="fa fa-plus"></i>
                               تحديث
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
<script type="text/javascript">
    $(document).ready(function(){
        $("body").on("change", ".country", function(){
            var country = $(this).val();
            getCountryCities("{{ route('country.cities') }}", country, 'en', $('#cities'), $('.country_code'), $(".phone"), 2);
        });
    });
</script>
@stop