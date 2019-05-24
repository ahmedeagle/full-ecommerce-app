@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة عمليات السحب</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>سحب الرصيد</li>
                    <li class="active">قائمة عمليات السحب</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة عمليات السحب
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
                        <div class="two fields">
                            <div class="ui field">
                                <label>حالات الطلب</label>
                                <div>
                                    <select id="status" class="form-control" placeholder="Request status">
                                        <option value="">الكل</option>
                                        <option value="1">معلق</option>
                                        <option value="2">تم التنفيذ</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>الوظيفة</label>
                                <div>
                                    <select id="job" class="form-control">
                                        <option value="">الكل</option>
                                        <option value="provider">مقدم خدمة</option>
                                        <option value="delivery">موصل </option>
                                        <option value="marketer">مسوق</option>
                                        <option value="user">مستخدم</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button href="#" class="custom-btn blue-bc requestSearchBu">
                            <i class="fa fa-search"></i> 
                            بحث
                        </button>
                    </div>
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
                @if($requests->count())
                <div class="table-responsive">          
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>الأسم بالكامل</th>
                                <th>رقم الهاتف</th>
                                <th>الرصيد المستحق</th>
                                <th>الرصيد الحالى</th>
                                <th>الرصيد المحجوز</th>
                                <th>الأجمالى</th>
                                <th>الإسم</th>
                                <th>البنك</th>
                                <th>رقم الحساب</th>
                                <th>الهاتف</th>
                                <th>النوع</th>
                                <th>الحالة</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach($requests AS $request)
                            <tr>
                                <td>{{ $request->full_name }}</td>
                                <td>{{ $request->country_code.$request->person_phone }}</td>
                                <td>{{ $request->due_balance }}</td>
                                <td>{{ $request->current_balance }}</td>
                                <td>{{ $request->forbidden }}</td>
                                <td>{{ $request->current_balance - $request->due_balance - $request->forbidden }}</td>
                                <td>{{ (is_null($request->name))? '' : $request->name }}</td>
                                <td>{{ (is_null($request->bank_name))? '' : $request->bank_name }}</td>
                                <td>{{ (is_null($request->account_num))? '' : $request->account_num }}</td>
                                <td>{{ (is_null($request->phone))? '' : $request->phone }}</td>
                                <td>{{ $request->type }}</td>
                                <td>{{ ($request->status == 1)? 'Pending' : 'Done' }}</td>
                                <td>
                                    <!-- <button class="custom-btn green-bc">
                                        <i class="fa fa-eye"></i>
                                    </button> -->
                                    <!-- <a href="" class="custom-btn blue-bc">
                                        <i class="fa fa-pencil"></i>
                                    </a> -->
                                    <!-- <button class="custom-btn red-bc">
                                        <i class="fa fa-trash-o"></i>
                                    </button> -->
                                    @if($request->status == 1)
                                        <a href="{{ route('requests.execute', $request->id) }}" class="custom-btn blue-bc">
                                            <i class="fa fa-return"></i>
                                            تنفيذ
                                        </a>
                                    @else
                                        تم
                                    @endif
                                </td>
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
$("body").on("click",".requestSearchBu", function(){
    var indicator = $(this).find('i');
    var but       = $(this);
    indicator.removeClass('fa-search');
    indicator.addClass('fa-circle-o-notch fa-spin');
    but.attr('disabled',true);
    var from   = $(".from_date").val();
    var to     = $(".to_date").val();
    var name   = $("#name").val();
    var phone  = $("#phone").val();
    var status = $("#status").val();
    var job    = $("#job").val();
    var posted = {'from':from, 'to':to, 'name':name, 'phone':phone, 'status':status, 'job':job};
    $.ajax({
        url: "{{ route('requests.search') }}",
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