@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>الطلبات</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li class="active"> الطلبات</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                قائمة الطلبات
            </div>
            <div class="widget-content">
                <!-- <div class="col-sm-12">
                    <a href="" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        Add new order
                    </a>
                </div> -->
                <div class="spacer-25"></div><!--End Spacer-->
                @if(Session::has('error'))
                    <div class="alert alert-danger">
                        <strong>خطأ !</strong> {{ Session::get('error') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                <div class="table-responsive">  
                    @if($headers->count())        
                    <table id="datatable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>كود الطلب</th>
                                <th>نوع الطلب</th>
                                <th>طريقة التوصيل</th>
                                <th>المستخدم</th>
                                <th>رقم هاتف المستخدم</th>
                                <th>البريد الألكترونى</th>
                                <th>عنوان المستخدم</th>
                                <th>مقدم الخدمة</th>
                                <th>الموصل</th>
                                <th>الأجمالى</th>
                                <th>الكمية</th>
                                <th>التاريخ</th>
                                <th>الحالة </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($headers AS $header)
                            <tr>
                                <td>{{ $header->order_code }}</td>
                                <td>{{ ($header->in_future == 1)? 'Future order' : 'Current order' }}</td>
                                <td>{{ $header->method_en_name }}</td>
                                <td>{{ $header->user }}</td>
                                <td>{{ $header->user_phone }}</td>
                                <td>{{ $header->user_email }}</td>
                                <td>{{ $header->address }}</td>
                                <td>{{ $header->provider }}</td>
                                <td>{{ $header->delivery }}</td>
                                <td>{{ $header->total_value }}</td>
                                <td>{{ $header->total_qty }}</td>
                                <td>{{ date('Y-m-d', strtotime($header->created_at)) }}</td>
                                <td>{{ $header->sts }}</td>
                                <td>
                                    <a href="{{ route('orders.details', $header->order_id) }}" class="custom-btn green-bc">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <!-- <a href="" class="custom-btn blue-bc">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <button class="custom-btn red-bc">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                    <a href="" class="custom-btn blue-bc">
                                        <i class="fa fa-cog"></i>
                                    </a> -->
                                </td>
                            </tr>
                            @endforeach()
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div><!--End Widget-->
    </div>
</div>
@stop