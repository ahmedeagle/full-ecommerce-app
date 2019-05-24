@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>الشكاوى اليومية</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li>الشكاوى</li>
                    <li class="active">الشكاوى اليومية</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div><!--End Spacer-->
        <div class="widget">
            <div class="widget-title">
             الشكاوى اليومية
             </div>
            <div class="widget-content requests">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>تم بنجاح !</strong> {{ Session::get('success') }}
                    </div>
                    <div class="spacer-25"></div><!--End Spacer-->
                @endif
        
                @if($complains->count())
                    <?php $i = 0; ?>
                    @foreach($complains AS $complain)
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                @if($complain->app == 'user')
                                    <p>Complain From user: {{ $complain->user }}</p>
                                    @if($complain->provider != "")
                                        <p>To provider: {{ $complain->provider }}</p>
                                    @else
                                        <p>To delivery: {{ $complain->delivery }}</p>
                                    @endif
                                @else
                                    <p> The complain is From provider: {{ $complain->provider }}</p>
                                    <p> To delivery: {{ $complain->delivery }} </p>
                                @endif
                                <p>Date: {{ $complain->created }}</p>
                            </div>
                            <div class="panel-body">{{ $complain->complain }}</div>
                            @if(!empty($attaches[$i]) && !is_null($attaches[$i]))
                            <div class="panel-footer overflow-hidden">
                                <div class="col-sm-12">
                                    @foreach($attaches[$i] AS $file)
                                        @if($file->type == "image")
                                            <div class="col-sm-1">
                                                <a class="img-popup-link" href="{{ $file->attach_path }}">
                                                    <img src="{{ $file->attach_path }}" class="table-img">
                                                </a>
                                            </div>
                                        @else
                                            <div class="col-sm-1">
                                                <a href="{{ $file->attach_path }}" target="_blank">
                                                    <img src="{{ url('video_player.png') }}" class="table-img">
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
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