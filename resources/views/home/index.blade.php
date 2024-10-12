<?php
$perfilAtual = App\Models\Assinante::getIdPerfilAtual();
?>

@extends('layouts.index')

@section('home_slider_content')
    @if ($message = Session::get('no_main_plan'))
        <div class="row">
            <div class="col-lg-4">
            </div>
            <div class="col-lg-4">
                <div class="alert alert-danger alert-block" style="margin-top:70px;">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            </div>
            <div class="col-lg-4">
            </div>
        </div>
    @endif

    @if ($message = Session::get('multilogin_max'))
        <div class="row">
            <div class="col-lg-4">
            </div>
            <div class="col-lg-4">
                <div class="alert alert-danger alert-block" style="margin-top:70px;">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            </div>
            <div class="col-lg-4">
            </div>
        </div>
    @endif

    @include('home.partials.home_slider')
@endsection


@section('main_content')
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


    <br />
    <div class="main-content-recomendados">

        @if (false)
            @include('home.partials.filmes')
        @endif
        @include('home.partials.tvaovivo_list_home')
        @if(sizeof($conteudo_recomendados))
        <section id="iq-favorites" class="home__slide-section">
            <div class="iq-main-header">
                <h4 class="main-title">
                    Recomendados para você
                </h4>
            </div>
            <div class="favorites-contens">
                <ul class="favorites-slider favorites-slider__video list-inline  slick-slider">

                    @foreach ($conteudo_recomendados as $recomendado)
                    <?php
                        $desc='';
                        if(isset($recomendado->descricao)){
                            $desc = str_replace('"','',$recomendado->descricao);
                            $desc = str_replace("'","",$desc);
                        }
                    ?>
                        <li class="slide-item" >
                            <div class="block-images position-relative"  onclick="abrirModal({{isset($recomendado->temporada)?$recomendado->temporada:0}},{{$recomendado->id}},'{{$desc}}','{{$recomendado->file ? $recomendado->file->imageUrl : ''}}','{{env('API_URL')}}')">

                                <div class="img-box" >
                                    <img src="{{ $recomendado->file ? $recomendado->file->imageUrl : '' }}" class="img-fluid" alt="{{ $recomendado->titulo }}">
                                </div>

                            </div>
                            <div class="block-description">
                                <div class="botoes-card">
                                    <a href="{{ route('video', isset($recomendado) ? $recomendado->id : '') }}">
                                        <button class="btn-play" aria-label="Assistir" data-balloon-pos="down">
                                            <span class="btn-play-hover">
                                                <i class="fa fa-play mr-1" aria-hidden="true"></i>
                                            </span>
                                        </button>
                                    </a>
                                        <button class="btn-fav" aria-label="Favoritar" data-balloon-pos="up" onclick="favorite('movie', {{$recomendado->id}});">
                                            <span class="btn-fav-hover">
                                                <i class="glyphicon {{$recomendado->liked($perfilAtual) ? 'glyphicon-ok' : 'glyphicon-plus'}} favorite_{{$recomendado->id}}" aria-hidden="true"></i>
                                            </span>
                                        </button>
                                        <button class="btn-mais" aria-label="Mais Informações" data-balloon-pos="up" onclick="abrirModal({{isset($recomendado->temporada)?$recomendado->temporada:0}},{{$recomendado->id}},'{{$desc}}','{{$recomendado->file ? $recomendado->file->imageUrl : ''}}','{{env('API_URL')}}')">
                                            <span class="btn-mais-hover">
                                                <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                            </span>
                                        </button>
                                </div>
                                <div class="imdbInfo">
                                    @if($recomendado->imdb)
                                        <p class="imdbico">IMDb</p>
                                        @if($recomendado->imdb<5)
                                        <p class="imdbNota-baixa">{{$recomendado->imdb}}</p>
                                            @elseif($recomendado->imdb<7)
                                            <p class="imdbNota-media">{{$recomendado->imdb}}</p>
                                                @else
                                                <p class="imdbNota-alta">{{$recomendado->imdb}}</p>
                                        @endif
                                    @endif
                                    @if($recomendado->classificacao == '0')
                                    <p class="cl-livre">L</p>
                                        @elseif($recomendado->classificacao == '10')
                                        <p class="cl-10">{{$recomendado->classificacao}}</p>
                                            @elseif($recomendado->classificacao == '12')
                                            <p class="cl-12">{{$recomendado->classificacao}}</p>
                                                @elseif($recomendado->classificacao == '14')
                                                <p class="cl-14">{{$recomendado->classificacao}}</p>
                                                    @elseif($recomendado->classificacao == '16')
                                                    <p class="cl-16">{{$recomendado->classificacao}}</p>
                                                        @elseif($recomendado->classificacao == '18')
                                                        <p class="cl-18">{{$recomendado->classificacao}}</p>


                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
        @endif

        @if(sizeof($conteudo_favorito))
        <section id="iq-favorites" class="home__slide-section">
            <div class="iq-main-header">
                <h4 class="main-title">
                    Favoritos
                </h4>
            </div>
            <div class="favorites-contens">
                <ul class="favorites-slider favorites-slider__video list-inline  slick-slider">

                    @foreach ($conteudo_favorito as $favorito)
                    <?php
                        $desc='';
                        if(isset($favorito->descricao)){
                            $desc = str_replace('"','',$favorito->descricao);
                            $desc = str_replace("'","",$desc);
                        }
                    ?>
                        <li class="slide-item" >
                            <div class="block-images position-relative"  onclick="abrirModal({{isset($favorito->temporada)?$favorito->temporada:0}},{{$favorito->id}},'{{$desc}}','{{ $favorito->file ? $favorito->file->imageUrl : '' }}','{{env('API_URL')}}')">

                                <div class="img-box" >
                                    <img src="{{ $favorito->file->imageUrl }}" class="img-fluid" alt="{{ $favorito->titulo }}">
                                </div>

                            </div>
                            <div class="block-description">
                                <div class="botoes-card">
                                    <a href="{{ route('video', isset($favorito) ? $favorito->id : '') }}">
                                        <button class="btn-play" aria-label="Assistir" data-balloon-pos="down">
                                            <span class="btn-play-hover">
                                                <i class="fa fa-play mr-1" aria-hidden="true"></i>
                                            </span>
                                        </button>
                                    </a>
                                        <button class="btn-fav" aria-label="Favoritar" data-balloon-pos="up" onclick="favorite('movie', {{$favorito->id}});">
                                            <span class="btn-fav-hover">
                                                <i class="glyphicon {{$favorito->liked($perfilAtual) ? 'glyphicon-ok' : 'glyphicon-plus'}} favorite_{{$favorito->id}}" aria-hidden="true"></i>
                                            </span>
                                        </button>
                                        <button class="btn-mais" aria-label="Mais Informações" data-balloon-pos="up" onclick="abrirModal({{isset($favorito->temporada)?$favorito->temporada:0}},{{$favorito->id}},'{{$desc}}','{{ $favorito->file ? $favorito->file->imageUrl : '' }}','{{env('API_URL')}}')">
                                            <span class="btn-mais-hover">
                                                <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                            </span>
                                        </button>
                                </div>
                                <div class="imdbInfo">
                                    @if($favorito->imdb)
                                        <p class="imdbico">IMDb</p>
                                        @if($favorito->imdb<5)
                                        <p class="imdbNota-baixa">{{$favorito->imdb}}</p>
                                            @elseif($favorito->imdb<7)
                                            <p class="imdbNota-media">{{$favorito->imdb}}</p>
                                                @else
                                                <p class="imdbNota-alta">{{$favorito->imdb}}</p>
                                        @endif
                                    @endif
                                    @if($favorito->classificacao == '0')
                                    <p class="cl-livre">L</p>
                                        @elseif($favorito->classificacao == '10')
                                        <p class="cl-10">{{$favorito->classificacao}}</p>
                                            @elseif($favorito->classificacao == '12')
                                            <p class="cl-12">{{$favorito->classificacao}}</p>
                                                @elseif($favorito->classificacao == '14')
                                                <p class="cl-14">{{$favorito->classificacao}}</p>
                                                    @elseif($favorito->classificacao == '16')
                                                    <p class="cl-16">{{$favorito->classificacao}}</p>
                                                        @elseif($favorito->classificacao == '18')
                                                        <p class="cl-18">{{$favorito->classificacao}}</p>


                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
        @endif

        {{-- @include('home.partials.trending') --}}
        {{-- @include('home.partials.tvaovivo_2')
            @include('home.partials.favoritos')
            @include('home.partials.futuros_lancamentos')
            @include('home.partials.top_10')
            @include('home.partials.sugestoes')
            @include('home.partials.paralax_1')
            @include('home.partials.trending_2')
            @include('home.partials.tvthrillers') --}}
    </div>

@endsection
