@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>إضافة مدير</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="#">الرئيسية</a></li>
                    <li class="active">إضافة مدير</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                نموذج إضافة مدير 
                </div>
                <div class="widget-content">
                    <form class="ui form" id="create-admin" method="post" action="{{ route('admin.store') }}">
                        <div class="form-title">من فضلك إملئ الحقول التالية</div>
                        <div class="form-note">[ * ] حقل مطلوب</div>
                        <div class="ui error message"></div>

                        @if(!empty($errors->first()))
                            <div class="alert alert-danger">
                                <strong>خطأ!</strong> {{ $errors->first() }}
                            </div>
                        @endif
                        @if(!empty($msg))
                            <div class="alert alert-success">
                                <strong>نجاح !</strong> {{ $msg }}
                            </div>
                        @endif
                        <div>
                            <div class="ui field">
                                <label>الأسم : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="name" id="name" type="text" placeholder="إسم المدير بالكامل" value="{{ old('name') }}" />
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="ui field">
                                <label>البريد الألكترونى :  <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="email" id="email" type="text" placeholder="البريد الألكترونى" value="{{ old('email') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>كلمة المرور : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="password" id="password" type="text" placeholder="كلمة المرور" value="{{ old('password') }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>تأكيد كلمة المرور : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="passwordcon" id="passwordcon" type="text" placeholder="تأكيد كلمة المرور" value="{{ old('passwordcon') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="ui right algined inline field">
                            <button type="submit" class="custom-btn">
                                <i class="fa fa-plus"></i>
                               إضافة
                            </button>
                        </div>
                    </form>
                </div><!-- end widget-content -->
            </div><!-- end widget -->
        </div>
    </div><!-- end container -->
</div>
@stop