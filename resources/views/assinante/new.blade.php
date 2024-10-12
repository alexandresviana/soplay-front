<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Controller;
$c = new Controller();
$current_app = $c->__app();
?>
@extends('layouts.index')


@section('main_content')


<br /><br /><br />


    <div class="container">


<div class="row justify-content-center align-items-center height-self-center">
         <div class="col-lg-5 col-md-12 align-self-center">
            <div class="sign-user_card ">
               <div class="sign-in-page-data">
                  <div class="sign-in-from w-100 m-auto">
                     <h3 class="mb-3 text-center">Novo cadastro</h3>

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


                     <form class="mt-4" action="{{route('assinante.create', ['app' => request()->get('app')])}}" method="POST" onsubmit="formSubmitted();">
                            @csrf
                        <div class="form-group">
                           <input type="text" name="nome" class="form-control mb-0" id="nome" placeholder="Informe seu nome" autocomplete="off">
                        </div>
                        <div class="form-group">
                           <input type="email" name="email" class="form-control mb-0" id="email" placeholder="Informe seu email" autocomplete="off">
                        </div>
                        @if($current_app->id == 2)
                        <div class="form-group">
                           <input type="text" name="razao_social" class="form-control mb-0" id="razao_social" placeholder="Informe a empresa" autocomplete="off">
                        </div>
                        <div class="form-group">
                           <input type="text" name="celular" class="form-control mb-0" id="celular" placeholder="Informe o celular" autocomplete="off">
                        </div>
                        @endif

                        <div class="form-group">
                           <input type="password" name="password" class="form-control mb-0" id="password" placeholder="Senha">
                        </div>
                        <div class="custom-control custom-checkbox mb-3">
                           <input type="checkbox" name="accept_terms" class="custom-control-input" id="accept">
                           <label class="custom-control-label" for="accept">Li e aceito <a href="{{route('assinante.terms')}}" class="text-primary" target="_blank"> Termos e condições</a></label>
                        </div>

                           <div class="sign-info">
                              <button type="submit" class="btn btn-hover" id="btn_submit">Efetuar cadastro</button>
                              <div class="custom-control custom-checkbox d-inline-block">
                                 <input type="checkbox" class="custom-control-input" id="remember">
                                 <label class="custom-control-label" for="remember">Lembrar</label>
                              </div>
                           </div>

                     </form>
                  </div>
               </div>
               <div class="mt-3">
                  <div class="d-flex justify-content-center links">
                     Já é assinante ? <a href="{{route('assinante.login')}}" class="text-primary ml-2">Login</a>
                  </div>
                  <div class="d-flex justify-content-center links">
                     <a href="#" class="f-link">Esqueceu a senha ?</a>
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

@section('javascript')
<script>
function formSubmitted()
{
   $('#btn_submit').attr('disabled', true);
   $('#btn_submit').html('Aguarde...');
}


var maskBehavior = function (val) {
 return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
},
options = {onKeyPress: function(val, e, field, options) {
 field.mask(maskBehavior.apply({}, arguments), options);
 }
};

$('#celular').mask(maskBehavior, options);

</script>
@endsection
