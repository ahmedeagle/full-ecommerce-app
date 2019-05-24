@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>الأعدادات</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li class="active">الأعدادت</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                   الأعدادات
                </div>
                <div class="widget-content">
                    <form class="ui form" id="setting-form" method="post" action="{{ ($setting != NULL)? route('setting.update') : route('setting.save') }}">
                        @if(!empty($setting) && $setting != NULL)
                            <input type="hidden" value="{{ $setting->id }}" name="id">
                        @endif
                        <div class="form-title">رجاء إدخال جميع الحقول التى بالأسفل .</div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>
                        @if(!empty($errors->first()))
                            <div class="alert alert-danger">
                                <strong>Error!</strong> {{ $errors->first() }}
                            </div>
                        @endif
                        @if(Session::has('success'))
                            <div class="alert alert-success">
                                <strong>Success!</strong> {{ Session::get('success') }}
                            </div>
                        @endif
                        <div class="widget-title">
                           طلب وقت القبول
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الوقت بالساعات :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="hours" id="hours" type="text" placeholder="الوقت بالساعات" value="{{ (!empty($setting->time_in_hours))? $setting->time_in_hours : old('hours') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>الوقت بالدقايق:<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="minutes" id="minutes" type="text" placeholder="الوقت بالدقائق" value="{{ (!empty($setting->time_in_min))? $setting->time_in_min : old('minutes') }}" />
                                </div>
                            </div>
                        </div>
                        <!--start adding minimum time taken by provider to finish the order  -->
                        <div class="ui field">
                            <label>اقصى وقت مسموح للمقدم لتجهيز الطلب بالدقائق<span class="require">*</span></label>
                            <div class="ui input">
                                <input name="max_time_to_process_order" id="max_time_to_process_order" type="text" placeholder="اقصى وقت مسموح للمقدم لتجهيز الطلب" value="{{ (!empty($setting->max_time_to_process_order))? $setting->max_time_to_process_order  : old('max_time_to_process_order') }}" />
                            </div>
                        </div>
                        <!--end adding minimum time taken by provider to finish the order -->

                        <!--start adding minimum time taken by delivery to accept or reject the order  -->
                        <div class="ui field">
                            <label> اقصى وقت مسموح للموصل لقبول الطلب بالدقائق<span class="require">*</span></label>
                            <div class="ui input">
                                <input name="max_time_to_accept_order" id="max_time_to_accept_order" type="text" placeholder="اقصى وقت مسموح للموصل لقبول الطلب" value="{{ (!empty($setting->max_time_to_accept_order))? $setting->max_time_to_accept_order  : old('max_time_to_accept_order') }}" />
                            </div>
                        </div>
                        <!--end adding minimum time taken by provider to finish the order -->

                            <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            النسب المئوية
                        </div>
                        <div class="three fields">
                            <div class="ui field">
                                <label>نسبة مزود خدمة مذاق: <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="provider" id="provider" type="text" placeholder="نسبة مزود خدمة مذاق" value="{{ (!empty($setting->app_percentage))? $setting->app_percentage  : old('provider') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>نسبة التسليم <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="delivery" id="delivery" type="text" placeholder="نسبة التسليم" value="{{ (!empty($setting->delivery_percentage))? $setting->delivery_percentage  : old('delivery') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>نسبة المسوقين: <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="marketer" id="marketer" type="text" placeholder="نسبة المسقين:" value="{{ (!empty($setting->marketer_percentage))? $setting->marketer_percentage  : old('marketer') }}" />
                                </div>
                            </div>
                            <!--start adding minimum limit of requested balance to withdraw  -->
                            <div class="ui field">
                                <label>:الحد الادنى لسحب الرصيد <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="min_balance" id="min_balance" type="text" placeholder="الحد الادنى لسحب الرصيد" value="{{ (!empty($setting->min_balace_to_withdraw))? $setting->min_balace_to_withdraw  : old('min_balance') }}" />
                                </div>
                            </div>
                            <!--end adding minimum limit of requested balance to withdraw  -->
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            الأسعار
                        </div>
                        <div>
                            <div class="ui field">
                                <label>سعر الكيلو :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="kilo" id="kilo" type="text" placeholder="سعر الكيلو" value="{{ (!empty($setting->kilo_price))? $setting->kilo_price  : old('kilo') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>السعر المبدئى المضاف على سعر الطلب :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="initial_value_added_order_price" id="initial_value_added_order_price" type="text" placeholder="السعر المبدئى للطلب" value="{{ (!empty($setting->initial_value_added_order_price))? $setting->initial_value_added_order_price  : old('initial_value_added_order_price') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>سعر التوصيل خارج المدينة :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="delivery_price_outside" id="delivery_price_outside" type="text" placeholder="سعر التوصيل خارج المدينة" value="{{ (!empty($setting->delivery_price_outside))? $setting->delivery_price_outside  : old('delivery_price_outside') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            إعدادات الدعوة
                        </div>
                        <div class="form-title">نوع الدعوة :<span class="require">*</span></div>
                        <div class="form-group">
                            <select class="form-control" name="type">
                                <option value="">من فضلك إختار</option>
                                <option {{ (!empty($setting->invitation_type) && $setting->invitation_type == 1)? 'selected' : '' }} value="1">الكل</option>
                                <option {{ (!empty($setting->invitation_type) && $setting->invitation_type == 2)? 'selected' : '' }} value="2">دعوة الشخص فقط</option>
                                <option {{ (!empty($setting->invitation_type) && $setting->invitation_type == 3)? 'selected' : '' }} value="3">المدعو فقط</option>
                                <option {{ (!empty($setting->invitation_type) && $setting->invitation_type == 4)? 'selected' : '' }} value="4">لاشئ</option>
                            </select>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>نقاط المدعو <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="inPoints" id="inPoints" type="text" placeholder="Inviter points" value="{{ (!empty($setting->inviter_points))? $setting->inviter_points : old('inPoints') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>نقاط الدعوة <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="outPoints" id="outPoints" type="text" placeholder="Invited points" value="{{ (!empty($setting->invited_points))? $setting->invited_points : old('outPoints') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="ui right algined inline field">
                            <button type="submit" class="custom-btn">
                                <i class="fa {{ (!empty($setting) && $setting != NULL)? 'fa-pencil' : 'fa-plus' }}"></i>
                                {{ (!empty($setting) && $setting != NULL)? 'تحديث' :'إنشاء'}}
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
        $("body").on("keyup", "#hours", function(){
            var value = $(this).val();
            if(value != ""){
                var min = Math.round(value * 60);
            }else{
                var min = "";
            }

            $("#minutes").val(min);
        });

        $("body").on("keyup", "#minutes", function(){
            var value = $(this).val();
            if(value != ""){
                var h = (value / 60).toFixed(2);;
            }else{
                var h = "";
            }

            $("#hours").val(h);
        });
    });
</script>
@stop