@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>خصائص مقدم الخدمة</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>مقدمى الخدمة</li>
                    <li class="active">خصائص مقدم الخدمة</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                خصائص مقدم الخدمة
                </div>
                <div class="widget-content">
                    <form class="ui form" id="provider-properties" method="post" action="{{ route('provider.setProperties') }}">
                        <input type="hidden" value="{{ $provider_id }}" name="provider_id" />
                        <div class="form-title">من فضلك إملئ البيانات التالية</div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>
                        @if(Session::has('errors'))
                            <div class="alert alert-danger">
                                <strong>خطأ !</strong> {{ Session::get('errors') }}
                            </div>
                            <div class="spacer-25"></div><!--End Spacer-->
                        @endif
                        <div class="widget-title">
                            إستلام مقدم الخدمة
                        </div>
                        <div class="col-sm-3">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="receive_orders" value="1" {{ ($getProviderData != NULL && $getProviderData->receive_orders == 1)? 'checked' : '' }} />
                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                    إستلام الطلبات
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="ui field">
                                <label>سعر التوصيل : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="price" id="price" type="text" placeholder="From time" value="{{ ($getProviderData != NULL)? $getProviderData->delivery_price : old('price') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            وقت إستلام الطلبات
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>من : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="from" id="from" type="text" placeholder="من" value="{{ ($getProviderData != NULL)? $getProviderData->allowed_from_time : old('from') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>إلى :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="to" id="to" type="text" placeholder="إلى" value="{{ ($getProviderData != NULL)? $getProviderData->allowed_to_time : old('to') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            وقت خروج الطلب
                        </div>
                       <div class="two fields">
                            <div class="ui field">
                                <label>من :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="fromexitTimes1" id="fromexitTimes1" type="text" placeholder="الخروج من الوقت الأول" value="{{ (!empty($getTimes[0]) && !empty($getTimes[0]->allowed_from_time))? $getTimes[0]->allowed_from_time : old('fromexitTimes1') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>إلى :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="toexitTimes1" id="toexitTimes1" type="text" placeholder="الخروج  إلى الوقت  الأول" value="{{ (!empty($getTimes[0]) && !empty($getTimes[0]->allowed_to_time))? $getTimes[0]->allowed_to_time : old('toexitTimes1') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>من<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="fromexitTimes2" id="fromexitTimes2" type="text" placeholder="الخروج من الوقت الثانى" value="{{ (!empty($getTimes[1]) && !empty($getTimes[1]->allowed_from_time))? $getTimes[1]->allowed_from_time : old('fromexitTimes2') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>إلى<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="toexitTimes2" id="toexitTimes2" type="text" placeholder="الخروج  إلى الوقت الثانى" value="{{ (!empty($getTimes[1]) && !empty($getTimes[1]->allowed_to_time))? $getTimes[1]->allowed_to_time : old('toexitTimes2') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>من<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="fromexitTimes3" id="fromexitTimes3" type="text" placeholder="الخروج من الوقت الثالث" value="{{ (!empty($getTimes[2]) && !empty($getTimes[2]->allowed_from_time))? $getTimes[2]->allowed_from_time : old('fromexitTimes3') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>إلى<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="toexitTimes3" id="toexitTimes3" type="text" placeholder="الخروج إلى الوقت الثالث" value="{{ (!empty($getTimes[2]) && !empty($getTimes[2]->allowed_to_time))? $getTimes[2]->allowed_to_time : old('toexitTimes3') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            طريقة التسليم
                        </div>
                        @if($deliveries->count())
                            @foreach($deliveries AS $delivery)
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="deliveries[]" value="{{ $delivery->method_id }}" {{ ($delivery->chosen == 1)? 'checked' : '' }}>
                                            <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                            {{ $delivery->method_en_name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <input type="hidden" value="" name="deliveries" />
                            <div class="col-sm-12">لايوجد أى توصيلات</div>
                        @endif
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">Orders types</div>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="current_orders" value="1" {{ ($getProviderData != NULL && $getProviderData->current_orders == 1)? 'checked' : '' }} />
                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                    الطلبات الحالية
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="future_orders" value="1" {{ ($getProviderData != NULL && $getProviderData->future_orders == 1)? 'checked' : '' }} />
                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                    الطلبات المستقبلية
                                </label>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                        إلى
                        </div>
                            <div class="ui field">
                                <label>إلى تاريخ ::<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="avail_date" id="avail_date" type="text" placeholder="إلى تاريخ" value="{{ ($avail_date != NULL)? $avail_date : old('avail_date') }}" />
                                </div>
                            </div>
                        <div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="ui right algined inline field">
                            <button type="submit" class="btn btn-primary creat-btn">
                                <i class="fa fa-plus"></i>
                                إنشاء
                            </button>
                        </div>
                    </form>
                </div><!-- end widget-content -->
            </div><!-- end widget -->
        </div>
    </div><!-- end container -->
</div>
@stop