@extends('layouts.index')


@section('main_content')


    <br /><br /><br />


    <div class="container" style="{{ Route::currentRouteName() === 'assinante.login' ? 'margin-top: 80px' : '' }}">


        <div class="row justify-content-center align-items-center height-self-center">
            <div class="col-lg-5 col-md-12 align-self-center">
                <div class="sign-user_card ">
                    <div class="sign-in-page-data">
                        <div class="sign-in-from w-100 m-auto">
                            <h3 class="mb-3 text-center">Acesse sua conta</h3>

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


                            <form class="mt-4" action="{{ route('assinante.authenticate') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="email" class="form-control mb-0" id="email"
                                        placeholder="Informe seu email ou cpf" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control mb-0" id="password"
                                        placeholder="Senha">
                                </div>
                                @if ($current_app->tipo == 'multiprovedor')
                                    <div class="form-group" id="appdiv" style="display: none;">
                                        <select name="appx" id="idapp" class="form-control" size="1"
                                            onchange="changeApp()" style="display:anone;">
                                            <option value="" disabled="true">Selecione o provedor</option>
                                            <option value=""></option>
                                            @foreach ($app_list as $app)
                                                @if ($app->tipo == 'multiprovedor')
                                                    <option value="{{ $app->id }}">{{ $app->app_nome }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <br />
                                        @foreach ($app_list as $app)
                                            @if ($app->tipo == 'multiprovedor')
                                                <a href="#">
                                                    <img src="{{ $app->settingsLogo() }}" height=50"
                                                        style="margin-bottom: 30px;margin-right: 30px;"
                                                        onclick="changeProvedor({{ $app->id }})" />
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif


                                <div class="sign-info">
                                    <button type="submit" class="btn btn-hover btn-sm">Entrar</button>&nbsp;
                                    <div class="custom-control custom-checkbox d-inline-block">
                                        <input type="checkbox" class="custom-control-input" id="remember">
                                        <label class="custom-control-label" for="remember">Lembrar</label>
                                    </div>
                                    @if ($current_app->tipo == 'multiprovedor')
                                        <small><a href="#" class="btn btn-dark btn-sm"
                                                onclick="javascript: selectApp();"><small>Selecionar
                                                    provedor</small></a></small>&nbsp;
                                    @endif
                                </div>

                            </form>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-center links">
                            <a href="{{ route('forgot_password', ['app' => request()->get('app')]) }}"
                                class="f-link">Esqueceu a senha ?</a>
                        </div>
                        @if ($current_app->settingsSignupEnabled())
                            <div class="d-flex justify-content-center links">
                                Ainda não é assinante ? <a
                                    href="{{ route('assinante.new', ['app' => request()->get('app')]) }}"
                                    class="text-primary ml-2">Assinar</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>


    <br />
    <br />
    <br />

    <script>
        function selectApp() {
            $('#appdiv').show();
        }
    </script>

@endsection
