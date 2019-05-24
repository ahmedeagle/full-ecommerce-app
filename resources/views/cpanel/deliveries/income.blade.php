@extends('cpanel.layout.master')
@section('content')
<style type="text/css">
    .table > thead > tr > th {
        border-bottom: 1px solid #ddd;
        font-size: 12px;
        font-weight: 600;
        line-height: 40px;
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
                <h2>قائمة إيرادات الموصلين</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                   <li>الموصلين</li>
                    <li class="active">قائمة إيرادات الموصلين</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
              قائمة إيرادات الموصلين
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
                                    <input class="form-control from_date" size="16" value="<?php echo date('Y-m-d') ?>" readonly="" type="text">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>إلى :</label>
                                <div class="form-group">
                                    <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                        <input class="form-control to_date" size="16" value="<?php echo date('Y-m-d') ?>" readonly="" type="text">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ui field">
                            <label>الموصلين<span class="require">*</span></label>
                            <div class="ui input">
                                <select class="js-example-basic-single select form-control">
                                    @if($deliveries->count())
                                        @foreach($deliveries AS $delivery)
                                            <option value="{{ $delivery->delivery_id }}">{{ $delivery->full_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                    <div class="col-sm-3">
                        <button href="#" class="custom-btn blue-bc incomeSrchBut">
                            <i class="fa fa-search"></i> 
                            بحث
                        </button>
                    </div>
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
                <div class="toggle-container" id="accordion-3">
                    <div class="panel"  style="display:none">
                        <a href="#accordion3_1" data-toggle="collapse" data-parent="#accordion-3" aria-expanded="false" class="">
                            <span id="total">0</span>
                        </a>
                        <div class="panel-collapse collapse in" id="accordion3_1" aria-expanded="true">
                            <div class="panel-content">
                                <div class="table-responsive">          
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>رقم الفاتورة</th>
                                                <th>رقم الطلب</th>
                                                <th>القيمة الأجمالية</th>
                                                <th>قيمة الموصل</th>
                                                <th>قيمة التطبيق</th>
                                                <th>قيمة المسوق</th>
                                                <th>الحالة</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody">

                                        </tbody>
                                    </table>
                                </div>
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
$("body").on("click",".incomeSrchBut", function(){
    var indicator = $(this).find('i');
    var but       = $(this);
    indicator.removeClass('fa-search');
    indicator.addClass('fa-circle-o-notch fa-spin');
    but.attr('disabled',true);
    var from   = $(".from_date").val();
    var to     = $(".to_date").val();
    var id     = $(".select").val();
    var posted = {'from':from, 'to':to, 'id':id, 'type':'delivery'};
    $.ajax({
        url: "{{ route('income.search') }}",
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
            $("#tableBody").parent().parent().parent().parent().parent().show();
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