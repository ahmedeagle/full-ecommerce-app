@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>تقييمات الموصلين</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li><a href="#">التقييمات</a></li>
                    <li class="active">تقييمات الموصلين</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               تقييمات الموصلين
            </div>
            <div class="widget-content requests">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> {{ Session::get('success') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                @if(Session::has('err'))
                    <div class="alert alert-danger">
                        <strong>خطا !</strong> {{ Session::get('err') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                <div class="col-sm-12">
                    <div class="ui form">
                        <div class="two fields">
                            <div class="ui field">
                                <label>من : </label>
                                <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                    <input class="form-control from_date" size="16" value="" readonly="" type="text">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>إلى :</label>
                                <div class="form-group">
                                    <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                        <input class="form-control to_date" size="16" value="" readonly="" type="text">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>المستخدم</label>
                                <div>
                                    <select id="users" class="users-select2 form-control">
                                        <option value="">إختار المستخدم</option>
                                        @if($users->count())
                                            @foreach($users AS $user)
                                        <option value="{{ $user->user_id }}">{{ $user->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>رقم هاتف المستخدم : </label>
                                <div class="ui input">
                                    <input class="form-control" placeholder="مثال : 01090353855" type="text" id="phone" name="phone">
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الموصلين</label>
                                <div>
                                    <select id="deliveries" class="users-select2 form-control">
                                        <option value="">إختار الموصل</option>
                                        @if($deliveries->count())
                                            @foreach($deliveries AS $delivery)
                                                <option value="{{ $delivery->delivery_id }}">{{ $delivery->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>رقم هاتف الموصل : </label>
                                <div class="ui input">
                                    <input class="form-control" placeholder="مثال : 01090353855" type="text" id="delivery_phone" name="delivery_phone">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button href="#" class="custom-btn blue-bc searchBu">
                            <i class="fa fa-search"></i> 
                            بحث
                        </button>
                    </div>
                </div>
                <span class="spacer-25"></span>
                @if($evaluations->count())
                <div class="table-responsive"> 
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="width-90">صورة المستخدم</th>
                                <th>إسم المستخدم</th>
                                <th>رقم الهاتف</th>
                                <th class="width-90">صورة الموصل</th>
                                <th>إسم الموصل</th>
                                <th>رقم الموصل</th>
                                <th>كود الطلب</th>
                                <th>التقييم العام</th>
                                <th>التعليق</th>
                                <th>التاريخ</th>
                                <!-- <th></th> -->
                            </tr>
                        </thead>
                        <tbody id="result">
                            @foreach($evaluations AS $evaluation)
                            <tr>
                                <td class="width-90">
                                    <a class="img-popup-link" href="{{ $evaluation->profile_pic }}">
                                        <img src="{{ $evaluation->profile_pic }}" class="table-img">
                                    </a>
                                </td>
                                <td>{{ $evaluation->full_name }}</td>
                                <td>{{ $evaluation->phone }}</td>
                                <td class="width-90">
                                    <a class="img-popup-link" href="{{ $evaluation->delivery_pic }}">
                                        <img src="{{ $evaluation->delivery_pic }}" class="table-img">
                                    </a>
                                </td>
                                <td>{{ $evaluation->delivery_name }}</td>
                                <td>{{ $evaluation->delivery_phone }}</td>
                                <td>{{ $evaluation->code }}</td>
                                <td>{{ $evaluation->rating }}</td>
                                <td>{{ $evaluation->comment }}</td>
                                <td>{{ $evaluation->created }}</td>
                                <!-- remember -->
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="table-responsive"> 
                    <table class="table table-bordered table-hover" style="display:none">
                        <thead>
                            <tr>
                                <th class="width-90">صورة المستخدم</th>
                                <th>إسم المستخدم</th>
                                <th>رقم الهاتف</th>
                                <th class="width-90">صورة الموصل</th>
                                <th>إسم الموصل</th>
                                <th>رقم الموصل</th>
                                <th>كود الطلب</th>
                                <th>التقييم العام</th>
                                <th>التعليق</th>
                                <th>التاريخ</th>
                                <!-- <th></th> -->
                            </tr>
                        </thead>
                        <tbody id="result">
                        </tbody>
                    </tabl>
                </div>
                @endif
                <div class="col-sm-12" id="pagi">
                    {{ $evaluations->links() }}
                </div>
            </div><!--End Widget-content -->
        </div><!--End Widget-->
    </div>
</div>
@stop

@section('customJs')
<script type="text/javascript">

    $("body").on("click", ".searchBu", function(){
        var indicator = $(this).find('i');
        var but       = $(this);
        indicator.removeClass('fa-search');
        indicator.addClass('fa-circle-o-notch fa-spin');
        but.attr('disabled',true);
        var user           = $("#users").val();
        var user_phone     = $("#phone").val();
        var delivery       = $("#deliveries").val();
        var delivery_phone = $("#delivery_phone").val();
        var from_date      = $(".from_date").val();
        var to_date        = $(".to_date").val();
        var type           = 'delivery';

        var data = {'user':user, 'user_phone':user_phone, 'subject':delivery, 'subject_phone':delivery_phone, 'from':from_date, 'to':to_date, 'type':type};
        $.ajax({
            url:"{{ route('evaluations.search') }}",
            type:"POST",
            data:data,
            scriptCharset:"application/x-www-form-urlencoded; charset=UTF-8",
            success: function(result){
                $("#result").html(result);
                $("#pagi").html("");
                $(".table").css('display', 'table');
                indicator.addClass('fa-search');
                indicator.removeClass('fa-circle-o-notch fa-spin');
                but.attr('disabled',false);
            },
            error: function(){
                indicator.addClass('fa-search');
                indicator.removeClass('fa-circle-o-notch fa-spin');
                but.attr('disabled',false);
                alert('حدث خطأ ما من فضلك حاول مره اخرى');
            }
        });
    });

</script>
@stop