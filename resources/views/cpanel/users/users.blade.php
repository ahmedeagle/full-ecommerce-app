@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة المستخدمين</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
            <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li> 
                    <li>المستخدمين</li>
                    <li class="active">قائمة المستخدمين</li>
                </ul>
             
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                قائمة المستخدمين 
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                    <a href="{{ route('user.create') }}" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة مستخدم جديد
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
                    @if($users->count())        
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                                <th> صورة المستخدم</th>
                                <th> الأسم بالكامل</th>
                                <th> البريد الألكترونى</th>
                                <th> رقم الهاتف</th>
                                <th> الرصيد </th>
                                <th> المدينة </th>
                                <th> الحالة </th>
                                <th>  </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users AS $user)
                            <tr>
                                <td>
                                    <a class="img-popup-link" href="{{ $user->profile_pic }}">
                                        <img src="{{ $user->profile_pic }}" class="table-img">
                                    </a>
                                </td>
                                <td> {{ $user->full_name }} </td>
                                <td> {{ $user->email }} </td>
                                <td> {{ $user->country_code.$user->phone }} </td>
                                <td> {{ $user->points }} </td>
                                <td> {{ $user->city_en_name }} </td>
                                <td> {{ ($user->status == 1)? 'activated' : 'not activated' }}</td>
                                <td>
                                    <!-- <button class="custom-btn green-bc">
                                        <i class="fa fa-eye"></i>
                                    </button> -->
                                    <a href="{{ route('user.edit', $user->user_id) }}" class="custom-btn blue-bc" title="تعديل مستخدم">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <!-- <button class="custom-btn red-bc">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                    <a href="#" class="custom-btn blue-bc">
                                        <i class="fa fa-cog"></i>
                                    </a> -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div><!--End Widget-->
    </div>
</div>
@stop