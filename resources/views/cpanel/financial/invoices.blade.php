@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة الفواتير</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>الفواتير</li>
                    <li class="active">قائمة الفواتير</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة الفواتير
            </div>
            <div class="spacer-25"></div><!--End Spacer-->
            @if(Session::has('success'))
                <div class="alert alert-success">
                    <strong>تم بنجاح !</strong> {{ Session::get('success') }}
                </div>
            @endif

            @if(Session::has('err'))
                <div class="alert alert-danger">
                    <strong>خطأ !</strong> {{ Session::get('err') }}
                </div>
            @endif
            <div class="widget-content requests">
                <div class="col-sm-12">
                    <a href="{{ route('invoices.create') }}" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة   فاتورة
                    </a>
                </div>
                @if($invoices->count())
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
                        <div class="two fields">
                            <div class="ui field">
                                <label>بحث بواسطة الأسم</label>
                                <div class="ui input">
                                    <input class="form-control" name="name" id="name" placeholder="بحث بواسطة الأسم" type="text">
                                </div>
                            </div>
                            <div class="ui field">
                                <label>بحث بواسطة رقم الهاتف</label>
                                <div class="ui input">
                                    <input class="form-control" name="phone" id="phone" placeholder="بحث بواسطة رقم الهاتف" type="text">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="ui field">
                                <label>نوع الفاتورة</label>
                                <div>
                                    <select id="type" class="form-control" placeholder="Invoice type">
                                        <option value="">الكل</option>
                                        <option value="1">بيع</option>
                                        <option value="2">مسترجع</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="spacer-25"></span>
                    <div class="col-sm-3">
                        <button href="#" class="custom-btn blue-bc invoiceSearchBu">
                            <i class="fa fa-search"></i> 
                            بحث
                        </button>
                    </div>
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
                <div class="table-responsive">          
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>الأسم</th>
                                <th>رقم الهاتف</th>
                                <th>القيمة</th>
                                <th>النوع</th>
                                <th>الوصف</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach($invoices AS $invoice)
                            <tr>
                                <td>{{ $invoice->invo_id }}</td>
                                <td>{{ $invoice->name }}</td>
                                <td>{{ $invoice->phone }}</td>
                                <td>{{ $invoice->value }}</td>
                                <td>{{ ($invoice->type == 1)? 'Sale' : 'Return' }}</td>
                                <td>{{ $invoice->invo_desc }}</td>
                                <td>{{ date('Y-m-d', strtotime($invoice->created_at)) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div><!--End Widget-->
    </div>
</div>
@stop

@section('customJs')
<script type="text/javascript">
$("body").on("click",".invoiceSearchBu", function(){
    var indicator = $(this).find('i');
    var but       = $(this);
    indicator.removeClass('fa-search');
    indicator.addClass('fa-circle-o-notch fa-spin');
    but.attr('disabled',true);
    var from   = $(".from_date").val();
    var to     = $(".to_date").val();
    var name   = $("#name").val();
    var phone  = $("#phone").val();
    var type   = $("#type").val();
    var posted = {'from':from, 'to':to, 'name':name, 'phone':phone, 'type':type};
    $.ajax({
        url: "{{ route('invoices.search') }}",
        type:"POST",
        data:posted,
        scriptCharset:"application/x-www-form-urlencoded; charset=UTF-8",
        success: function(result){
            indicator.addClass('fa-search');
            indicator.removeClass('fa-circle-o-notch fa-spin');
            but.attr('disabled',false);
            if(result != ''){
                $("#tableBody").html(result);
            }else{
                alert('empty result');
            }
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