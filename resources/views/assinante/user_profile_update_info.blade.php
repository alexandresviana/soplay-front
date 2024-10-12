@extends('layouts.index')


@section('main_content')


<br /><br /><br />


    <div class="container">


<div class="row justify-content-center align-items-center height-self-center">
         <div class="col-lg-5 col-md-12 align-self-center">
            <div class="sign-user_card ">
               <div class="sign-in-page-data">
                  <div class="sign-in-from w-100 m-auto">
                     <h3 class="mb-3 text-center">Perfil - Alterar dados pessoais</h3>

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


                     <form class="mt-4" action="{{route('user_profile_update_info')}}" method="POST">
                        @csrf
                        <div class="form-group">
                           <label for="documentacao_cpf">Informe o CPF</label>
                           <input type="text" name="documentacao_cpf" value="{{old('documentacao_cpf', $subscriber->documentacao_cpf)}}" class="form-control mb-0" id="documentacao_cpf" placeholder="CPF">
                        </div>

                        <div class="form-group">
                           <label for="email">Informe o Email</label>
                           <input type="text" name="email" value="{{old('email', $subscriber->email)}}" class="form-control mb-0" id="email" placeholder="Email">
                        </div>

                        <div class="sign-info">
                           <button type="submit" class="btn btn-hover">Atualizar</button>
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

@section('javascript')
<script>
   $(document).ready(function () {
      $("#documentacao_cpf").mask("999.999.999-99");
   });
</script>
@endsection

