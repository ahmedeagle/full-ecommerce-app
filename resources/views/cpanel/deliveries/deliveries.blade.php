@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>قائمة الموصلين</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>الموصلين</li>
                    <li class="active">قائمة الموصلين</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
               قائمة الموصلين
            </div>
            <div class="widget-content">
                <div class="col-sm-12">
                    <a href="{{ route('deliveries.create') }}" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        إضافة موصل جديد
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
                                <th> الأسم بالكامل </th>
                                <th> الإسم التجارى </th>
                                <th> رقم الجوال </th>
                                <th> البريد الإلكترونى </th>
                                <th> الدولة </th>
                                <th> المدينة </th>
                                <th> العنوان </th>
                                <th> تفعيل رقم الهاتف </th>
                                <th> تفعيل الادارة </th>
                                <th>  </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($deliveries->count())
                                @foreach($deliveries AS $delivery)
                                    <tr>
                                        <td> {{ $delivery->full_name }} </td>
                                        <td> {{ $delivery->brand_name }} </td>
                                        <td> {{ $delivery->country_code.$delivery->phone }} </td>
                                        <td> {{ $delivery->email }} </td>
                                        <td> {{ $delivery->country }} </td>
                                        <td> {{ $delivery->city }} </td>
                                        <td> {{ $delivery->address }} </td>
                                        <td> {{ ($delivery->status == 0 || $delivery->status == 3)? 'غير مفعل' : 'مفعل' }} </td>
                                        <td> {{ ($delivery->status == 0 || $delivery->status == 2)? 'غير مفعل' : 'مفعل' }} </td>
                                        <td>
                                            <a href="{{ route('deliveries.edit', $delivery->delivery_id) }}" class="custom-btn blue-bc">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <!--
                                             0 => not sms activated
                                             2 => activate sms But not activated from manger
                                             1 => activated from the manger
                                            -->
                                            @if($delivery->status == 0 || $delivery->status == 2)
                                            <form action="{{ route('deliveries.activate', $delivery->delivery_id) }}" method="post" name="activate-delivery" id="activate-delivery">
                                                <button type="submit" name="activate-delivery-btn" class="btn btn-success">تفعيل</button>
                                            </form>
                                            @endif
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