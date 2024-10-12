<?php
$perfilAtual = App\Models\Assinante::getIdPerfilAtual();
?>
@extends('layouts.index')


@section('main_content')

    <div class="main-content-container" style="padding-top:100px; padding-inline:20px;">

      <div class="favorites-contens">
      <ul class="videos-grid">
        @if(count($conteudo_pesquisa) >= 1)
            @foreach ($conteudo_pesquisa as $i => $conteudo)
            <?php
              $desc='';
              if(isset($conteudo->descricao)){
                $desc = str_replace('"','',$conteudo->descricao);
                $desc = str_replace("'","",$desc);
              }
            ?>
            <li class="slide-item">
                @php   $img_banner = \App\Models\ConteudoCanais::getImageById($conteudo->imagem_banner); @endphp
                @if ($img_banner)
                  <div class="block-images position-relative" onclick="abrirModal({{isset($conteudo->temporada)?$conteudo->temporada:0}},{{$conteudo->id}},'{{$desc}}','{{$img_banner}}','{{env('API_URL')}}')">
                @endif
                <div class="img-box">
                @php   $img = \App\Models\ConteudoCanais::getImageById($conteudo->imagem); @endphp
                @if ($img)
                  <img src="{{$img}}" class="img-fluid" alt="{{ $conteudo->titulo }}">
                @endif
                </div>
                </div>
                <div class="block-description">
                <div class="botoes-card">
                @if ($conteudo->tipo == 'serie' || $conteudo->tipo == 'desenho')
                <a href="{{ route('video.series', ['id' => $conteudo->id]) }}">
                @endif
                @if ($conteudo->tipo == 'filme')
                <a href="{{ route('video', ['id' => $conteudo->id]) }}">
                @endif
                @if ($conteudo->tipo == 'tv')
                <a href="{{ route('video_aovivo', ['id' => $conteudo->id]) }}">
                @endif
                @if(($conteudo->tipo) == 'filme')
                    <button class="btn-play" aria-label="Assistir" data-balloon-pos="down">
                    <span class="btn-play-hover">
                        <i class="fa fa-play mr-1" aria-hidden="true"></i>
                    </span>
                    </button>
                    @endif
                </a>

                @php   $img_banner = \App\Models\ConteudoCanais::getImageById($conteudo->imagem_banner); @endphp
                @if ($img_banner)

                @endif

                </button>
                </div>
                <div class="imdbInfo">
                                        @if($conteudo->imdb)
                                        <p class="imdbico">IMDb</p>
                                        @if($conteudo->imdb<5)
                                            <p class="imdbNota-baixa">{{$conteudo->imdb}}</p>
                                                @elseif($conteudo->imdb<7)
                                                <p class="imdbNota-media">{{$conteudo->imdb}}</p>
                                                    @else
                                                    <p class="imdbNota-alta">{{$conteudo->imdb}}</p>
                                            @endif
                                        @endif
                                        @if($conteudo->classificacao == '0')
                                        <p class="cl-livre">L</p>
                                            @elseif($conteudo->classificacao == '10')
                                            <p class="cl-10">{{$conteudo->classificacao}}</p>
                                                @elseif($conteudo->classificacao == '12')
                                                <p class="cl-12">{{$conteudo->classificacao}}</p>
                                                    @elseif($conteudo->classificacao == '14')
                                                    <p class="cl-14">{{$conteudo->classificacao}}</p>
                                                        @elseif($conteudo->classificacao == '16')
                                                        <p class="cl-16">{{$conteudo->classificacao}}</p>
                                                            @elseif($conteudo->classificacao == '18')
                                                            <p class="cl-18">{{$conteudo->classificacao}}</p>


                                        @endif
                                    </div>
                </div>
            </li>
            @endforeach
            @else
                <li class="slide-item">Sem resultado para a pesquisa</li>
            @endif
        </ul>
      </div>
  @include('home.partials.mais_info')

    </div>

@endsection



<!--


<section id="iq-favorites" class="topo-series">
    <div class="row">
      <div class="col-sm-12 overflow-hidden">
        <div class="iq-main-header d-flex align-items-center justify-content-between">
        </div>
        <div class="favorites-contens videos-grid">

          @foreach ($conteudo_pesquisa as $i => $conteudo)
            <li class="slide-item" style="width: 240px; float: left; list-style-type: none; margin-top:10px">
            @if ($conteudo->tipo == 'serie' || $conteudo->tipo == 'desenho')
              <a href="{{ route('video.series', ['id' => $conteudo->id]) }}">
            @endif
            @if ($conteudo->tipo == 'filme')
              <a href="{{ route('video', ['id' => $conteudo->id]) }}">
            @endif
            @if ($conteudo->tipo == 'tv')
            <a href="{{ route('video_aovivo', ['id' => $conteudo->id]) }}">
            @endif
                <div class="block-images position-relative">
                  <div class="img-box">
                  @php   $img = \App\Models\ConteudoCanais::getImageById($conteudo->imagem); @endphp
                    @if ($img)
                      <img src="{{$img}}"
                        class="img-fluid"
                        alt="{{ $conteudo->titulo }}" />
                    @endif
                  </div>
                  <div class="block-description">
                    <h6>{{ $conteudo->titulo }}</h6>
                    <span><small>{{ $conteudo->descricao }}</small></span>
                    <div class="hover-buttons">
                      <span class="btn btn-hover">
                        <i class="fa fa-play mr-1" aria-hidden="true"></i>Play
                      </span>
                    </div>
                  </div>
                </div>
              </a>
            </li>
          @endforeach

        </div>
      </div>
    </div>
</section> -->
