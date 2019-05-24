@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>التعليقات اليومية</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                   <li>التعليقات</li>
                    <li class="active">التعليقات اليومية</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
             التعليقات اليومية
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
                
                @if($comments->count())
                <div class="table-responsive"> 
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="width-90">صورة الوجبة</th>
                                <th>إسم الوجبة</th>
                                <th>إسم المستخدم</th>
                                <th>رقم الهاتف</th>
                                <th>التلعيق</th>
                                <th>التاريخ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comments AS $comment)
                            <tr>
                                <td class="width-90">
                                    <a class="img-popup-link" href="{{ $comment->main_image }}">
                                        <img src="{{ $comment->main_image }}" class="table-img">
                                    </a>
                                </td>
                                <td>{{ $comment->meal_name }}</td>
                                <td>{{ $comment->full_name }}</td>
                                <td>{{ $comment->phone }}</td>
                                <td>{{ $comment->comment }}</td>
                                <td>{{ $comment->created }}</td>
                                <td>
                                    <button data-id="{{ $comment->id }}" class="custom-btn red-bc deleteMeal">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                <div class="col-sm-12">
                    {{ $comments->links() }}
                </div>
            </div><!--End Widget-content -->
        </div><!--End Widget-->
    </div>
</div>
@stop

@section('customJs')
<script type="text/javascript">
$(document).ready(function(){
    $('body').on('click', '.deleteMeal', function(){
        if(confirm("Are you sure?")){
            var id = $(this).attr('data-id');
            var url = "{{ route('comments.delete', ['id' => ':id']) }}";

            url = url.replace(':id', id);

            window.location.href = url;
        }
    });
});
</script>
@stop