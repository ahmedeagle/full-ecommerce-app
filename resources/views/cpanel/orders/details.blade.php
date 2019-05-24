@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>تفاصيل الطلب</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li><a href="{{ route('orders.filter') }}">الطلبات</a></li>
                    <li class="active">تفاصيل الطلب</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
              تفاصيل الطلب
            </div>
            <div class="widget-content">
                <!-- <div class="col-sm-12">
                    <a href="" class="custom-btn red-bc">
                        <i class="fa fa-plus"></i>
                        Add new order
                    </a>
                </div> -->
                <div class="spacer-25"></div><!--End Spacer-->
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>تم بنجاح  !</strong> {{ Session::get('success') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                <div class="table-responsive">  
                    @if($header != NULL)        
                    <table class="table table-bordered table-hover">
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
                            </tr>
                        </tbody>
                    </table>
                    @endif
                </div>
                <span class="spacer-25"></span>
                <div class="table-responsive">  
                    @if($details->count())        
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>الصورة</th>
                                <th>الوجبة</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>الأجمالى</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0; ?>
                            @foreach($details AS $row)
                                <?php $total += ($row->meal_price * $row->qty); ?>
                            <tr>
                                <td class="width-90">
                                    <a class="img-popup-link" href="{{ $row->main_image }}">
                                        <img src="{{ $row->main_image }}" class="table-img">
                                    </a>
                                </td>
                                <td>{{ $row->meal_name }}</td>
                                <td>{{ $row->qty }}</td>
                                <td>{{ $row->meal_price }}</td>
                                <td>{{ ($row->meal_price * $row->qty) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="4">الأجمالى</td>
                                <td>{{ $total }}</td>
                            </tr>
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div><!--End Widget-->
    </div>
</div>
@stop