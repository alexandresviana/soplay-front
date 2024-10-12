@extends('layouts.index')


@section('main_content')


<br /><br /><br />


<div class="container" style="{{ Route::currentRouteName() === 'assinante.login' ? 'margin-top: 80px' : '' }}">


  <div class="row justify-content-center align-items-center height-self-center">
    <div class="col-lg-5 col-md-12 align-self-center">
      <div class="sign-user_card ">
        <div class="sign-in-page-data">
          <div class="sign-in-from w-100 m-auto">
            <h3 class="mb-3 text-center">Recuperar senha</h3>

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


            <form class="mt-4" action="{{route('forgot_password', ['app' => request()->get('app')])}}" method="POST">
              @csrf
              <div class="form-group">
                <p>Um email será enviado com instruções para mudança de senha</p>
              </div>
              <div class="form-group">
                <input type="email" name="email" class="form-control mb-0" id="email" placeholder="Informe seu email" autocomplete="off">
              </div>

              <div class="sign-info">
                <button type="submit" class="btn btn-hover btn-sm">Enviar</button>&nbsp;
              </div>

            </form>
          </div>
        </div>
        <div class="mt-3">
          <div class="d-flex justify-content-center links">
            <a href="{{route('login', ['app' => request()->get('app')])}}" class="f-link">Login</a>
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