@extends('cpanel.layout.master')
@section('content')
<style type="text/css">
    .table > thead > tr > th {
        border-bottom: 1px solid #ddd;
        font-size: 12px;
        font-weight: 600;
        line-height:25px;
        padding: 0 10px;
        text-transform: capitalize;
        text-align: center;
        vertical-align: middle;
    }
</style>
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>إيرادات التطبيق</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li class="active">إيرادات التطبيق</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                قائمة إيرادات التطبيق
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
                        <strong>خطأ !</strong> {{ Session::get('err') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                <div class="col-sm-12">
                    <div class="ui form">
                        <div class="two fields">
                            <div class="ui field">
                                <label>من :</label>
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
                    </div>
                    <div class="col-sm-3">
                        <button href="#" class="custom-btn blue-bc appIncomeSrchBut">
                            <i class="fa fa-search"></i> 
                            بحث
                        </button>
                    </div>
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
                <div class="toggle-container" id="accordion-3">
                    <div class="panel">
                        <a href="#accordion3_1" data-toggle="collapse" data-parent="#accordion-3" aria-expanded="false" class="">
                            <span id="total">{{ ($getTotalIncome != NULL)? ROUND($getTotalIncome->total,2) : '0' }}</span>
                        </a>
                        <div class="panel-collapse collapse in" id="accordion3_1" aria-expanded="true">
                            <div class="panel-content">
                                @if($getDetails->count())
                                <div class="table-responsive">          
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">كود الطلب</th>
                                                <th rowspan="2">القيمة الأجمالية</th>
                                                <th rowspan="2">نسبة مقدم الخدمة</th>
                                                <th rowspan="2">نسبة التطبيق من مقم الخدمة</th>
                                                <th rowspan="2">نسبة التوصيل</th>
                                                <th rowspan="2">نسبة التطبيق من   الموصل </th>
                                                <th colspan="2">قيم اخرى</th>
                                                <th rowspan="2">الحالة</th>
                                            </tr>
                                            <tr>
                                                <th>Provider marketer value</th>
                                                <th>Delivery marketer value</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody">
                                            @foreach($getDetails AS $row)
                                            <tr>
                                                <td>{{ $row->order_code }}</td>
                                                <td>{{ $row->total_value }}</td>
                                                <td>{{ $row->net_value }}</td>
                                                <td>{{ $row->app_value }}</td>
                                                <td>{{ $row->delivery_price }}</td>
                                                <td>{{ $row->delivery_app_value }}</td>
                                                <td>{{ $row->marketer_value }}</td>
                                                <td>{{ $row->marketer_delivery_value }}</td>
                                                <td>{{ ($row->balance_status == 1)? 'Pending' : 'Done' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div><!-- end content -->
                        </div><!--End panel-collapse-->
                    </div><!--End Panel-->
                </div>
            </div>
        </div><!--End Widget-->
    </div>
</div>
@stop

@section('customJs')
<script type="text/javascript">
$("body").on("click",".appIncomeSrchBut", function(){
    var indicator = $(this).find('i');
    var but       = $(this);
    indicator.removeClass('fa-search');
    indicator.addClass('fa-circle-o-notch fa-spin');
    but.attr('disabled',true);
    var from   = $(".from_date").val();
    var to     = $(".to_date").val();
    var posted = {'from':from, 'to':to};
    $.ajax({
        url: "{{ route('income.app.search') }}",
        type:"POST",
        data:posted,
        dataType:"JSON",
        scriptCharset:"application/x-www-form-urlencoded; charset=UTF-8",
        success: function(result){
            indicator.addClass('fa-search');
            indicator.removeClass('fa-circle-o-notch fa-spin');
            but.attr('disabled',false);
            $("#total").html(result.total);
            $("#tableBody").html(result.data);
        },
        error: function(){
            indicator.addClass('fa-search');
            indicator.removeClass('fa-circle-o-notch fa-spin');
            but.attr('disabled',false);
            alert('Something wrong, try again later');
        }
    });
});
</script>
@stop