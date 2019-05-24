@extends('cpanel.layout.master')
@section('content')
<div class="content">
    <div class="col-sm-12" id="container">
        <section class="page-heading">
            <div class="col-sm-6">
                <h2>Update provider account</h2>
            </div><!--End col-md-6-->
            <div class="col-sm-6">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li><a href="{{ route('provider.show') }}">مقدمى الخدمة</a></li>
                    <li class="active">تعديل بيانات مقدم الخدمة</li>
                </ul>
            </div><!--End col-md-6-->
        </section><!--End page-heading-->
        <div class="spacer-25"></div>
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-title">
                    تعديل بيانات مقدم الخدمة
                </div>
                <div class="widget-content">
                    <form class="ui form"  enctype="multipart/form-data" id="create-provider" method="POST" action="{{ route('provider.update') }}">
                        <input type="hidden" value="{{ $provider->provider_id }}" id="provider_id" name="provider_id" />
                        <div class="form-title">Please fill the information below .</div>
                        <div class="form-note">[ * ] required input</div>
                        <div class="ui error message"></div>
                        @if(!empty($errors->first()))
                            <div class="alert alert-danger">
                                <strong>Error!</strong> {{ $errors->first() }}
                            </div>
                        @endif
                        @if(!empty($msg))
                            <div class="alert alert-success">
                                <strong>Success!</strong> {{ $msg }}
                            </div>
                        @endif
                        <div class="widget-title">
                            Personal information
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>First Name :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="fname" id="fname" type="text" placeholder="First Name" value="{{ $provider->first_name }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>Second Name :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="sname" id="sname" type="text" placeholder="second Name" value="{{ $provider->second_name }}" />
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>Third Name :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="tname" id="tname" type="text" placeholder="Third Name" value="{{ $provider->third_name }}" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>Last Name :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="lname" id="lname" type="text" placeholder="Last Name" value="{{ $provider->last_name }}" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="ui field">
                                <label>Brand Name :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="bname" id="bname" type="text" placeholder="Brand Name" value="{{ $provider->brand_name }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            Contact and login information
                        </div>
                        <div>
                            <div class="ui field">
                                <label>Email : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="email" id="email" type="email" placeholder="Email Address" value="{{ $provider->email}}" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="ui field">
                                <label>Phone : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="phone" id="phone" type="text" placeholder="Phon number" value="{{ $provider->phone }}" />
                                </div>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>Password :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="upassword" id="upassword" type="password" placeholder="Password" value="" />
                                </div>
                            </div>
                            <div class="ui field">
                                <label>Repeate Password :<span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="upasswordcon" id="upasswordcon" type="password" placeholder="Repeate Password" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            Address information
                        </div>
                        <div class="two fields">
                            <div class="ui field">
                                <label>Countries :<span class="require">*</span></label>
                                <div class="ui input">
                                    <select class="ui dropdown" name="countries">
                                        <option value="">Select</option>
                                        @if($countries->count())
                                            @foreach($countries AS $country)
                                                @if($country->country_id == $country_id)
                                                    <option selected value="{{ $country->country_id }}">{{ $country->country_en_name }}</option>
                                                @else
                                                    <option value="{{ $country->country_id }}">{{ $country->country_en_name }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>Cities :<span class="require">*</span></label>
                                <select class="ui dropdown" name="cities">
                                    <option value="">Select</option>
                                    @if($cities->count())
                                            @foreach($cities AS $city)
                                                @if($city->city_id == $city_id)
                                                    <option selected value="{{ $city->city_id }}">{{ $city->city_en_name }}</option>
                                                @else
                                                    <option value="{{ $city->city_id }}">{{ $city->city_en_name }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="ui field">
                                <label>Address : <span class="require">*</span></label>
                                <div class="ui input">
                                    <input name="address" id="address" type="text" placeholder="Address" value="{{ $provider->address }}" />
                                </div>
                            </div>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="widget-title">
                            Provided Categories
                        </div>
                        <div class="form-title">Categories :<span class="require">*</span></div>
                        <div class="form-group">
                            <select multiple="" class="form-control" name="categories[]">
                                @if($categories->count())
                                    @foreach($categories AS $category)
                                        @if(in_array($category->cat_id, $selectedCats))
                                            <option selected value="{{ $category->cat_id }}">{{ $category->cat_en_name }}</option>
                                        @else
                                            <option value="{{ $category->cat_id }}">{{ $category->cat_en_name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="spacer-25"></div><!--End Spacer-->
                        <div class="ui right algined inline field">
                            <button type="submit" class="btn btn-primary creat-btn">
                                <i class="fa fa-plus"></i>
                                Update
                            </button>
                        </div>
                    </form>
                </div><!-- end widget-content -->
            </div><!-- end widget -->
        </div>
    </div><!-- end container -->
</div>
@stop