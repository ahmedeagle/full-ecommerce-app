@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>تفاصيل التقييم</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li><a href="{{ route('evaluations.show') }}">التقييمات</a></li>
                    <li class="active">تفاصيل التقييم</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        @if($details != NULL)
        <div class="col-md-4">
            <div class="card">
                <div class="card-title">المستخدم</div>
                <div class="card-img">
                    <img src="{{ $details->profile_pic }}">
                </div>
                <div class="card-content">
                    <div class="card-content-info"><span>الأسم : </span> {{ $details->full_name }}</div>
                    <div class="card-content-info"><span>رقم الهاتف :  </span>{{ $details->phone }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-title">مقدم الخدمة</div>
                <div class="card-img">
                    <img src="{{ $details->provider_pic }}">
                </div>
                <div class="card-content">
                    <div class="card-content-info"><span>الأسم : </span> {{ $details->provider }}</div>
                    <div class="card-content-info"><span>رقم الهاتف :  </span>{{ $details->provider_phone }}</div>
                </div>
            </div>
        </div>
        @if($details->delivery != "")
        <div class="col-md-4">
            <div class="card">
                <div class="card-title">الموصل</div>
                <div class="card-img">
                    <img src="{{ $details->delivery_pic }}">
                </div>
                <div class="card-content">
                    <div class="card-content-info"><span>الأسم : </span> {{ $details->delivery }}</div>
                    <div class="card-content-info"><span>رقم الهاتف :  </span>{{ $details->delivery_phone }}</div>
                </div>
            </div>
        </div>
        @endif
        <span class="spacer-25"></span>
        <div class="widget" style="clear:both">
            @if(Session::has('success'))
                <div class="alert alert-success">
                    <strong>تم بنجاح !</strong> {{ Session::get('success') }}
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
            @endif
            @if(Session::has('err'))
                <div class="alert alert-danger">
                    <strong>خطأ !</strong> {{ Session::get('err') }}
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
            @endif
            <div class="widget-title">
               تقييم مقدم الخدمة
            </div>
            <div class="widget-content requests">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">جودة الخدمة</div>
                        <div class="panel-body">{{ $details->quality }}</div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">مطابقة الوجبة للصورة والتفاصيل</div>
                        <div class="panel-body">{{ $details->autotype }}</div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">التغلييف</div>
                        <div class="panel-body">{{ $details->packing }}</div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">النضج</div>
                        <div class="panel-body">{{ $details->maturity }}</div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">إمكانية الطلب مرة أخرى</div>
                        <div class="panel-body">{{ $details->ask_again }}</div>
                    </div>
                </div>
            </div><!--End Widget-content -->
        </div>
        <div class="widget">
            <div class="widget-title">
                تقييم  موصل الخدمةٍ
            </div>
            <div class="widget-content requests">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">الوصول فى المكان المحدد</div>
                        <div class="panel-body">{{ $details->delivery_arrival }}</div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">الوصول في الوقت المحدد</div>
                        <div class="panel-body">{{ $details->delivery_in_time }}</div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">السلوك</div>
                        <div class="panel-body">{{ $details->delivery_attitude }}</div>
                    </div>
                </div>
            </div>
        </div><!--End Widget-->
        <div class="widget">
            <div class="widget-title">
               التعليق
            </div>
            <div class="widget-content requests">
                <div class="col-sm-12">
                    <p>{{ $details->comment }}</p>
                </div>
                <span class="spacer-25"></span>
            </div>
        </div><!--End Widget-->
        @endif
    </div>
</div>
@stop