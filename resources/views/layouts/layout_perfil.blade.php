<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Controller;
$c = new Controller();
$app = $c->__app();
$title = $app->app_nome;
$logo = $app->settingsLogo();
$favicon = $app->settingsFavicon();
$sub = $c->__subscriber();
$c->checkUserStatus();
?>
<header>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css">
    @yield('styles')
</header>
@include('layouts.common.header_html')

<body class="route-{{ Route::currentRouteName() }}">

    <div id="loading" style="display: none;">
        <div id="loading-center">
        </div>
    </div>

    <header id="main-header" class="">
        <div class="main-header">
            <nav class="navbar navbar-expand-lg navbar-light p-0">
                  <img src="{{$logo ?? ''}}" class="img-fluid"
    style="{{ Route::currentRouteName() === 'assinante.login' ? 'max-height: 100px;' : 'max-height: 40px;'}}"
    alt="NXTV" />

            </nav>
            <div class="nav-overlay"></div>
        </div>
    </header>

    <!--@yield('home_slider_content')-->

    @if (Request::route()->getName() == 'home' || Request::route()->getName() == '')
    <?php
      $desc='';
      if(isset($destaques_home[$indice_banner]->descricao)){
        $desc = str_replace('"','',$destaques_home[$indice_banner]->descricao);
        $desc = str_replace("'","",$desc);
      }
      ?>
        @if(isset($destaques_home[$indice_banner]) && $destaques_home[$indice_banner] instanceof app\Models\BannerCustom)
        <div class="home_video">
            <video id="bgvid" muted autoplay
                poster="{{ isset($destaques_home_custom[0]) ? $destaques_home_custom[0]->getImagemFullUrl() : '' }}"
                style="pointer-events: none;">
            </video>
        </div>
        <div class="home_info_video">
            <button id="volume" style="opacity:0;">
                <span class="glyphicon glyphicon-volume-off"></span>
            </button>
        </div>
        @else
        <div class="home_video">
            <video id="bgvid" muted autoplay
                poster="{{ isset($destaques_home[$indice_banner]) ? $destaques_home[$indice_banner]->getImagemBannerFullUrl() : '' }}"
                style="pointer-events: none;">
                <source src="{{isset($destaques_home[$indice_banner]) ? $destaques_home[$indice_banner]->getTrailerUrl() : ''}}" type="video/mp4">
            </video>
        </div>
        <div class="home_info_video">
            <button id="volume" style="opacity:0;">
                <span class="glyphicon glyphicon-volume-off"></span>
            </button>
            <h1 id="titulo">{{ isset($destaques_home[$indice_banner]) ? $destaques_home[$indice_banner]->titulo : '' }}
            </h1>
            <p id="descricao">
                {{ isset($destaques_home[$indice_banner]) ? $destaques_home[$indice_banner]->subtitulo : '' }}</p>

                <a href="{{ route('video', isset($destaques_home[$indice_banner]) ? $destaques_home[$indice_banner]->id : '') }}">
                    <button id="btnplay">
                        <span class="glyphicon glyphicon-play"></span> Assistir
                    </button>
                </a>
                <button id="btnmf" class="openBtn" onclick="abrirModal({{isset( $destaques_home[$indice_banner]->temporada)? $destaques_home[$indice_banner]->temporada:0}},{{ $destaques_home[$indice_banner]->id}},'{{$desc}}','{{$destaques_home[$indice_banner]->getImagemBannerFullUrl()}}','{{env('API_URL')}}')"><span class="glyphicon glyphicon-info-sign"></span> Mais Informações
                </button>
                <div class="imdbInfo">

                                    @if($destaques_home[$indice_banner]->classificacao == '0')
                                    <p class="cl-livre">L</p>
                                        @elseif($destaques_home[$indice_banner]->classificacao == '10')
                                        <p class="cl-10">{{$destaques_home[$indice_banner]->classificacao}}</p>
                                            @elseif($destaques_home[$indice_banner]->classificacao == '12')
                                            <p class="cl-12">{{$destaques_home[$indice_banner]->classificacao}}</p>
                                                @elseif($destaques_home[$indice_banner]->classificacao == '14')
                                                <p class="cl-14">{{$destaques_home[$indice_banner]->classificacao}}</p>
                                                    @elseif($destaques_home[$indice_banner]->classificacao == '16')
                                                    <p class="cl-16">{{$destaques_home[$indice_banner]->classificacao}}</p>
                                                        @elseif($destaques_home[$indice_banner]->classificacao == '18')
                                                        <p class="cl-18">{{$destaques_home[$indice_banner]->classificacao}}</p>


                                    @endif

                                </div>
        </div>
        @endif
        @include('home.partials.mais_info')

    @endif
    <!-- Slider End -->
    <!-- MainContent -->

    <div class="main-content" style="display: none;">

        @yield('main_content')
    </div>

    @include('layouts.common.footer')
    @include('layouts.common.footer_js')

    @yield('javascript')

</body>

</html>
