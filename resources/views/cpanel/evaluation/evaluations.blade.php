@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>التقييمات</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li class="active">التقييمات</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                التقييمات
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
                        <strong>خطا !</strong> {{ Session::get('err') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
                <div class="col-sm-12">
                    <div class="ui form">
                        <div class="two fields">
                            <div class="ui field">
                                <label>من : </label>
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
                                <label>المستخدم</label>
                                <div>
                                    <select id="users" class="users-select2 form-control" placeholder="إسم المستخدم بالكامل">
                                        <option value="">إختار المستخدم</option>
                                        @if($users->count())
                                            @foreach($users AS $user)
                                        <option value="{{ $user->user_id }}">{{ $user->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>رقم الهاتف : </label>
                                <div class="ui input">
                                    <input class="form-control" placeholder="مثال : 01090353855" type="text" id="phone" name="phone">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button href="#" class="custom-btn blue-bc searchBu">
                            <i class="fa fa-search"></i> 
                            بحث
                        </button>
                    </div>
                </div>
                <span class="spacer-25"></span>
                @if($evaluations->count())
                <div class="table-responsive"> 
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="width-90">صورة المستخدم</th>
                                <th>إسم المستخدم</th>
                                <th>رقم الهاتف</th>
                                <th>كود الطلب</th>
                                <th>التقييم العام</th>
                                <th>التعليق</th>
                                <th>التاريخ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluations AS $evaluation)
                            <tr>
                                <td class="width-90">
                                    <a class="img-popup-link" href="{{ $evaluation->profile_pic }}">
                                        <img src="{{ $evaluation->profile_pic }}" class="table-img">
                                    </a>
                                </td>
                                <td>{{ $evaluation->full_name }}</td>
                                <td>{{ $evaluation->phone }}</td>
                                <td>{{ $evaluation->code }}</td>
                                <td>{{ $evaluation->rating }}</td>
                                <td>{{ $evaluation->comment }}</td>
                                <td>{{ $evaluation->created }}</td>
                                <td>
                                    <a href="{{ route('evaluations.details', $evaluation->id) }}" data-id="{{ $evaluation->id }}" class="custom-btn red-bc">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                <div class="col-sm-12">
                    {{ $evaluations->links() }}
                </div>
            </div><!--End Widget-content -->
        </div><!--End Widget-->
    </div>
</div>
@stop