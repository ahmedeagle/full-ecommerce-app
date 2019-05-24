@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>الشكاوى</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>الشكاوى</li>
                    <li class="active">قائمة الشكاوى</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
                قائمة الشكاوى
            </div>
            <div class="widget-content requests">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> {{ Session::get('success') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
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
                                <label>المستخدم :</label>
                                <div>
                                    <select id="users" class="users-select2 form-control" placeholder="إسم المستخدم">
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
                                <label>مقدم الخدمة</label>
                                <div>
                                    <select id="provider" class="providers-select2 form-control" placeholder="إسم مقدم الخدمة">
                                        <option value="">إختار مقدم الخدمة</option>
                                        @if($providers->count())
                                            @foreach($providers AS $provider)
                                        <option value="{{ $provider->provider_id }}">{{ $provider->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>الموصل : </label>
                                <div>
                                    <select id="delivery" class="form-control" placeholder="إسم الموصل ">
                                        <option value="">إختار الموصل</option>
                                        @if($deliveries->count())
                                            @foreach($deliveries AS $delivery)
                                        <option value="{{ $delivery->delivery_id }}">{{ $delivery->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>التطبيق</label>
                                <div>
                                    <select id="app" class="form-control">
                                        <option value="">نوع التطبيق</option>
                                        <option value="user">تطبيق المستخدم</option>
                                        <option value="provider">تطبيق مقدم الخدمة</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button href="#" class="custom-btn blue-bc complainsSrchBu">
                            <i class="fa fa-search"></i> 
                            بحث
                        </button>
                    </div>
                </div>
                <span class="spacer-25"></span>
                @if($complains->count())
                    <?php $i = 0; ?>
                    @foreach($complains AS $complain)
                                   <div class="col-md-12">
                    <div class="complain-item">
                        <div class="compalin-info">
                            @if($complain->app == 'user')
                            <p><span>إسم صاحب الشكوى</span>{{ $complain->user }}</p>
                            @if($complain->provider != "")
                            <p><span>إلى صاحب الخدمة </span>{{ $complain->provider }}</p>
                            @else
                            <p><span>إلى الموصل : </span>{{ $complain->delivery }}</p>
                            @endif
                            @else
                            <p><span>الشكوى من مقدم الخدمة {{ $complain->provider }}</p>
                            <p><span> إلى الموصل : </span>{{ $complain->delivery }} </p>
                            @endif
                            <p><span>التاريخ :</span>{{ $complain->created }}</p>
                            <p><span>الشكوى :</span>{{ $complain->complain }}</p>

                        </div>
                        @if(!empty($attaches[$i]) && !is_null($attaches[$i]))
                        <div class="complain-img">
                            @foreach($attaches[$i] AS $file)
                            @if($file->type == "image")
                            <a class="img-popup-link" href="{{ $file->attach_path }}">
                                <img src="{{ $file->attach_path }}" class="table-img">
                            </a>
                            @else
                            <a href="{{ $file->attach_path }}" target="_blank">
                                <img src="{{ url('video_player.png') }}" class="table-img">
                            </a>
                            @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                        <?php $i++; ?>
                    @endforeach
                @endif
                <div class="col-sm-12">
                    {{ $complains->links() }}
                </div>
            </div><!--End Widget-content -->
        </div><!--End Widget-->
    </div>
</div>
@stop

@section('customJs')
<script type="text/javascript">
$(document).ready(function(){
    $('body').on('click', '.complainsSrchBu', function(){
        var from     = $('.from_date').val();
        var to       = $('.to_date').val();
        var user     = $('#users').val();
        var provider = $("#provider").val();
        var delivery = $('#delivery').val();
        var app      = $("#app").val();
        var url      = "{{ route('complains.search', ['from' => ':frm', 'to' => ':to', 'user' => ':user', 'provider' => ':provider', 'delivery' => ':delivery', 'app'=> ':app']) }}";
        if(from != null && from != ""){
            url = url.replace(':frm', from);
        }else{
            url = url.replace(':frm', null);
        }

        if(to != null && to != ""){
            url = url.replace(':to', to);
        }else{
            url = url.replace(':to', null);
        }

        if(user != null && user != ""){
            url = url.replace(':user', user);
        }else{
            url = url.replace(':user', null);
        }

        if(provider != null && provider != ""){
            url = url.replace(':provider', provider);
        }else{
            url = url.replace(':provider', null);
        }

        if(delivery != null && delivery != ""){
            url = url.replace(':delivery', delivery);
        }else{
            url = url.replace(':delivery', null);
        }

        if(app != null && app != ""){
            url = url.replace(':app', app);
        }else{
            url = url.replace(':app', null);
        }
        window.location.href = url;
    });
});
</script>
@stop