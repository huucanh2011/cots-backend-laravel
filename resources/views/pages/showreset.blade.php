<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
		<base href="{{asset('')}}">
	<title>Reset password</title>

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
</head>
<body class="bg-light">
	<div class="" id="password_modal">
		<div class="modal-dialog">
			<div class="modal-content">

				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">Reset Password</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<!-- Modal body -->
				<div class="modal-body">
					{{-- @include('layout.errors') --}}
					<form action="api/v1/auth/resetpassword/{{ $token }}" method="post">
						@csrf
						@method('PUT')
						<div class="form-group">
							<label for="current_password" >Email</label>
							<input class="form-control" disabled="true" type="email" name="email" value="{{ App\PasswordReset::where('token',$token)->firstorFail()->email }}">
						</div>
						<div class="form-group">
							<label for="new_password" >New Password</label>
							<input class="form-control @error('password') is-invalid @enderror" type="password" name="password">
							@error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>
						<div class="form-group">
							<label for="confirm_password">Confirm Password</label>
							<input class="form-control" type="password" name="password_confirmation">
						</div>
						<button type="submit" class="btn btn-info" data-dismiss="modal">Reset Password</button>  
					</form>
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					
				</div>

			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
	<script>
		
	</script>
</body>
</html>