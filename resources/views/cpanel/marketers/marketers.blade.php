@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة المسوقين</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>المسوقين</li>
                    <li class="active">قائمة المسوقين</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة المسوقين
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                    <a href="{{ route('marketer.create') }}" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة مسوق
                    </a>
                </div>
                <div class="spacer-25"></div><!--End Spacer-->
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> {{ Session::get('success') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                <div class="table-responsive">          
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>الأسم بالكامل</th>
                                <th> رقم الهاتف</th>
                                <th> البريد الألكترونى </th>
                                <th> الدولة</th>
                                <th> المدينة </th>
                                <th> العنوان </th>
                                <th> الحالة </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($marketers->count())
                                @foreach($marketers AS $marketer)
                                    <tr>
                                        <td> {{ $marketer->full_name }} </td>
                                        <td> {{ $marketer->country_code.$marketer->phone }} </td>
                                        <td> {{ $marketer->email }} </td>
                                        <td> {{ $marketer->country }} </td>
                                        <td> {{ $marketer->city }} </td>
                                        <td> {{ $marketer->address }} </td>
                                        <td> {{ ($marketer->status == 0)? 'not activated' : 'activated' }} </td>
                                        <td>
                                            <!-- <button class="custom-btn green-bc">
                                                <i class="fa fa-eye"></i>
                                            </button> -->
                                            <a href="{{ route('marketer.edit', $marketer->marketer_id)}}" class="custom-btn blue-bc">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <!-- <button class="custom-btn red-bc">
                                                <i class="fa fa-trash-o"></i>
                                            </button> -->
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!--End Widget-->
    </div>
</div>
@stop