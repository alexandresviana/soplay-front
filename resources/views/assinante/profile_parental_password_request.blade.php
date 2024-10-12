@extends('layouts.index')


@section('main_content')


<br /><br /><br />


    <div class="container">


<div class="row justify-content-center align-items-center height-self-center">
         <div class="col-lg-5 col-md-12 align-self-center">
            <div class="sign-user_card ">
               <div class="sign-in-page-data">
                  <div class="sign-in-from w-100 m-auto">
                     <h3 class="mb-3 text-center">Perfil - Controle parental</h3>

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

                     <div class="">
                        Utilize a senha do controle parental para restringir acesso a determinados conteúdos.
                     </div>

                     <form class="mt-4" action="{{route('user_profile_parental_password_request')}}" method="POST">
                            @csrf

                        <div class="form-group">
                           <input type="password" name="password_parental" class="form-control mb-0" id="password_parental" placeholder="Senha do controle parental">
                           <small>Informe uma senha para acesso a conteúdos restritos</small><br />
                        </div>

                           <div class="sign-info">
                              <button type="submit" class="btn btn-hover">Verificar senha</button>
                               <small><a href="{{route('user_profile_parental_password')}}">Alterar senha parental</a></small><br />
                           </div>

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
