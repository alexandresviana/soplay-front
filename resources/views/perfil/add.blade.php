@extends('layouts.index')

@section('main_content')
    <div class="main">
        <h3>Adicionar perfil</h3>
        <div class="perfil_box">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form class="mt-4"
                action="{{ isset($perfil) ? route('perfil.update', $perfil->id) : route('perfil.store') }}" method="POST">
                @if (isset($perfil))
                    @method('PUT')
                @endif
                @csrf

                <div class="form-group avatarSelecionado">
                    <div class="upload_box">
                        @if (isset($perfil) && $perfil->arquivo)
                            <img id="selected_avatar" class="avatar" src="{{ $perfil->arquivo->getImagemFullUrl() }}" alt="" />
                        @else
                            <img id="selected_avatar" class="avatar" alt="" />
                        @endif
                    </div>
                </div>

                <div class="form-group">

                    <div class="avatar2">
                        @foreach($avatarList as $avatar)
                        <div class="lds-dual-rings" style="display: inline;">
                            <button type="button" onclick="selectAvatar({{$avatar->id}}, '{{$avatar->getImagemFullUrl()}}')"><img class="avatar" src="{{ $avatar->getImagemFullUrl() }}" alt="" /></button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label for="Nome">Nome</label>
                    <input value="{{ isset($perfil) ? $perfil->nome : '' }}" type="text" name="nome" required
                        class="form-control mb-0" id="nome" placeholder="Informe o nome do perfil" autocomplete="off">
                </div>

                <input type="hidden" name="id_arquivo" id="id_arquivo">

                <div class="sign-info">
                    <button type="submit" class="btn btn-hover btn-sm">Salvar</button>&nbsp;
                </div>

            </form>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/upload.css') }}">
    <style>
        .main {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-direction: column;
        }

        .main h3 {
            font-size: 56px;
            font-style: normal;
            font-weight: 700;
            line-height: 66px;
            letter-spacing: 0em;
            text-align: center;
            margin-bottom: 60px;
        }

        .perfil_box {
            width: 100%;
            max-width: 400px;
        }

        .perfil_box ul {
            display: flex;
            justify-content: center;
        }

        .perfil_box ul,
        li {
            list-style: none;
            padding: 0px;
            margin: 0px;
        }

        .perfil_box li {
            margin-right: 20px;
        }

        .perfil_box li a {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .perfil_box li a div {
            width: 304px;
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #293E4A;
        }

        .perfil_box li a div.outline {
            background: transparent;
            border: 1px solid #fff;
            font-size: 100px;
            font-weight: 100;
        }

        .perfil_box li a p {
            font-size: 32px;
            font-style: normal;
            font-weight: 700;
            line-height: 38px;
            text-align: center;
            margin-top: 24px;
        }

        .upload_box {
            width: 80px;
            height: 80px;
            border: 2px solid #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            position: relative;
            border-radius: 50%;
        }

        .avatarSelecionado {
            display: flex !important;
            justify-content: center !important;
            margin-bottom: 50px !important;
        }

        .avatar2 {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .avatar2 button {
            width: 80px;
            height: 80px;
            background-color: #141414;
            border: 2px solid #141414;
            border-radius: 50%;
            padding: 0;
            margin-bottom: 20px;
            transition: all ease 0.2s
        }

        .avatar2 button:hover {
            background-color: #141414;
            border: 2px solid #fff;
        }

        .avatar2 button img {
            width: 74px;
            padding: 0;
        }

        .sign-info {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .sign-info button {
            width: 150px;
            height: 40px;
            font-size: 14px
        }

        .lds-dual-ring {
            display: inline-block;
            width: 80px;
            height: 80px;
        }

        .lds-dual-ring:after {
            content: " ";
            display: block;
            width: 64px;
            height: 64px;
            margin: 8px;
            border-radius: 50%;
            border: 6px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: lds-dual-ring 1.2s linear infinite;
        }

        @keyframes lds-dual-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .upload_box img {
            width: 80px;
            height: auto;
            object-fit: cover;
        }

        .btn_close {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 22px;
            display: flex;
            height: 20px;
            background-color: #fff;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            color: #000;
            font-weight: 900;
            cursor: pointer;
        }
        .main-content{
            height:100% !important;
        }

        @media screen and (max-width: 1400px){
            .avatar2 {
            flex-wrap: wrap !important;
        }}

    </style>
@endsection

@section('javascript')
    <script src="{{ asset('js/core.js') }}"></script>
    <script src="{{ asset('js/upload.js') }}"></script>

    <script>
        function selectAvatar(id, file)
        {
            $('#id_arquivo').val(id);
            $('#selected_avatar').attr('src', file);
        }


        $('.upload_box').on('click', '.btn_close', function() {
            $(this).remove();
            $('.upload').show();
            $('.avatar').remove();
            $('#id_arquivo').val('');
        });
    </script>
@endsection
