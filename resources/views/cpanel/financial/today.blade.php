@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>عمليات السحب اليومية</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>سحب الرصيد</li>
                    
                    <li class="active">عمليات السحب اليومية</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                قائمة عمليات السحب اليومية
            </div>
            <div class="widget-content requests">
                <div class="spacer-25"></div><!--End Spacer-->
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
                @if($requests->count())
                <div class="table-responsive">          
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>الأسم بالكامل</th>
                                <th>رقم الهاتف</th>
                                <th>الرصيد المستحق</th>
                                <th>الرصيد الحالى</th>
                                <th>الأجمالى</th>
                                <th>النوع</th>
                                <th>الحالة</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach($requests AS $request)
                            <tr>
                                <td>{{ $request->full_name }}</td>
                                <td>{{ $request->country_code.$request->phone }}</td>
                                <td>{{ $request->due_balance }}</td>
                                <td>{{ $request->current_balance }}</td>
                                <td>{{ $request->current_balance - $request->due_balance }}</td>
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
            if(result != ''){
                $("#tableBody").html(result);
            }else{
                alert('empty result');
            }
        },
        error: function(){
            alert('Something wrong, try again later');
        }
    });
});
</script>
@stop