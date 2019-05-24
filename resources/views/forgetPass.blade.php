<html>
<head>
	<title>Mathaq reset password</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<style type="text/css">
		.vcenter {
		    margin-top: 200px;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="col-sm-6 col-sm-offset-3 vcenter">
			<div class="panel panel-primary">
				<div class="panel-heading">إسترجاع كلمة المرور</div>
				<div class="panel-body">
					<form method="POST" class="form-horizontal" action="{{ route('forgetPassAction') }}">

						<input type="hidden" value="{{ $user_id }}" name="user_id" />
						<input type="hidden" value="{{ $api_email }}" name="api_email" />
						<input type="hidden" value="{{ $api_pass }}" name="api_password" />
						<!-- <div class="col-sm-6 col-sm-offset-3"><i class="fa fa-lock fa-5x"></i></div> -->
						@if($errors->first())
						<div class="alert alert-danger">
						 	<strong>خطا!</strong>
						 	<ul>
						 	@foreach($errors->all() AS $error)
						 		<li>{{ $error }}</li>
						 	@endforeach
						 	</ul>
						 	 
						</div>
						@endif
						@if(session()->has('success'))
						<div class="alert alert-success">
						 	<strong>تمت بنجاح !</strong>{{ session('success') }}
						</div>
						@endif
						@if(session()->has('err'))
						<div class="alert alert-danger">
						 	<strong>خطا!</strong> {{ session('err') }}
						</div>
						@endif
						<div class="form-group">
						    <label for="password" class="control-label col-sm-4">كلمة المرور الجديدة : </label>
						    <div class="col-sm-7 input-group">
						    	<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
						    	<input type="password" name="password" value="{{ old('password') }}" class="form-control" id="password">
						    </div>
						</div>
						<div class="form-group">
						    <label class="control-label col-sm-4" for="password_confirmation">تأكيد كلمة المرور : </label>
						    <div class="col-sm-7 input-group">
						    	<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
						    	<input type="password" class="form-control" value="{{ old('password_confirmation') }}" name="password_confirmation" id="password_confirmation">
						    </div>
						</div>
						<div class="form-group">        
					      <div class="col-sm-offset-4 col-sm-7">
					      		<button type="submit" class="btn btn-primary">تأكيد</button>
					      </div>
					    </div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>