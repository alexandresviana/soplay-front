@extends('layouts.index')


@section('main_content')


<br /><br /><br />


    <div class="container">


<div class="row justify-content-center align-items-center height-self-center">
         <div class="col-lg-5 col-md-12 align-self-center">
            <div class="sign-user_card ">
               <div class="sign-in-page-data">
                  <div class="sign-in-from w-100 m-auto">
                     <h3 class="mb-3 text-center">Perfil</h3>

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

                        {{-- Removido temporariamente recurso ainda não disponível
                            <div class="custom-control custom-checkbox mb-3">
                           <a href="{{route('checkout.index')}}" class="text-primary"> Assinatura</a>

                        <div class="custom-control custom-checkbox mb-3">
                           <a href="{{route('videorent.rentals')}}" class="text-primary"> Locações</a>
                        </div>
                        </div>
                        --}}

                        <div class="custom-control custom-checkbox mb-3">
                           <a href="{{route('user_profile_new_password')}}" class="text-primary"> Alterar senha</a>
                        </div>

                        <div class="custom-control custom-checkbox mb-3">
                           <a href="{{route('user_profile_update_info')}}" class="text-primary"> Alterar dados pessoais</a>
                        </div>


                        <div class="custom-control custom-checkbox mb-3">
                           <a href="{{route('user_profile_parental_password')}}" class="text-primary" > Senha controle parental</a>
                        </div>

                        <div class="custom-control custom-checkbox mb-3">
                            <a href="{{route('perfil.index')}}" class="text-primary" > Trocar Perfil</a>
                        </div>

                        <div class="custom-control custom-checkbox mb-3">
                            <a href="{{route('user_devices')}}" class="text-primary" > Devices conectados</a>
                         </div>

                         <div class="custom-control custom-checkbox mb-3">
                            <a href="{{route('home.suporte')}}" class="text-primary" > Contato</a>
                         </div>

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


