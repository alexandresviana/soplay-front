@extends('layouts.index')


@section('main_content')


<br /><br /><br />


<div class="container">


	<div class="row justify-content-center align-items-center height-self-center">
		<div class="col-lg-5 col-md-12 align-self-center">
			<div class="sign-user_card ">
				<div class="sign-in-page-data">
					<div class="sign-in-from w-100 m-auto">
						@if (count($errors) > 0)
						<div class="alert alert-danger">
							<ul>
								@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
						@endif

						@if ($message = Session::get('success'))
						<div class="alert alert-success alert-block">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>{{ $message }}</strong>
						</div>
						@endif

						@if ($message = Session::get('alert'))
						<div class="alert alert-danger alert-block">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>{{ $message }}</strong>
						</div>
						@endif

						<h3 class="mb-3 text-center">Verificar email</h3>
						<p>
							Verifique seu email clicando no link que nós acabamos de enviar para seu endereço eletrônico.
						</p>

						<br /><br />

						<form action="{{ route('verification.request') }}" method="post">
							<button class="btn btn-default" type="submit">Solicitar novo link</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>


<br />
<br />
<br />

@endsection
