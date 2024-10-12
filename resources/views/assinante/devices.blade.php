@extends('layouts.index')


@section('main_content')
    <div class="container">


        <div class="row justify-content-center align-items-center height-self-center" style="margin-top: 80px">
            <div class="col-lg-5 col-md-12 align-self-center">
                <div class="sign-user_card ">
                    <div class="sign-in-page-data">
                        <div class="sign-in-from w-100 m-auto">
                            <h3 class="mb-3 text-center">Dispositivos conectados</h3>

                            @if (session()->has('limit_error'))
                                <div class="error">
                                    {{ session()->get('limit_error') }}
                                </div>
                            @endif

                            @foreach ($devices as $d)
                                <div class="custom-control mb-3" style="">
                                    <div>
                                        @if ($d->auth_session_limite == $sessionAtual)
                                            <div class="session_atual">Sessão atual</div>
                                        @endif
                                        <p>
                                            IP: {{ $d->ip }}
                                        </p>
                                        <p>
                                            Device: {{ $d->agent }}
                                        </p>
                                        <p>
                                            Logado desde: {{ date('d/m/Y H:s', strtotime($d->created_at)) }}
                                        </p>
                                    </div>
                                    <div>
                                        <form action="{{ route('user_devices_delete', $d->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button_action danger" type="submit">Desconectar</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
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

@section('styles')
    <style>
        .button_action {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 25px;
            background-color: #F2994A;
            font-size: 14px;
            padding-left: 10px;
            padding-right: 10px;
            margin-right: 10px;
            color: #FFF;
            border: 0px;
            outline: 0px;
        }

        .perfil_actions .button_action.danger {
            background-color: red;
        }

        .error {
            padding: 10px;
            color: #FFF;
            background-color: red;
        }

        .custom-control {
            flex-direction: row;
            align-items: flex-end;
            border-bottom: 1px solid #FFF;
            width: 100%;
        }

        .session_atual {
            position: absolute;
            right: 0px;
            top: 0px;
            padding: 4px 8px;
            border-radius: 50px;
            background-color: #ad3104;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
    </style>
@endsection

@section('javascript')
    <script>
        $('.danger').click(function(e) {
            e.preventDefault() // Don't post the form, unless confirmed
            if (confirm('Tem certeza que deseja remover essa sessão?')) {
                // Post the form
                $(e.target).closest('form').submit() // Post the surrounding form
            }
        });
    </script>
@endsection