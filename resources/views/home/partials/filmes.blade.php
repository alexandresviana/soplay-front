<?php
$perfilAtual = App\Models\Assinante::getIdPerfilAtual();
?>
<section id="iq-favorites" class="video__slide-section">
    <div class="iq-main-header d-flex justify-content-between">
        <h4 class="main-title">
            <a
                href="{{ route('home_filmes_categoria', ['category' => $categoria]) }}">{{ $categorias_indexadas[$categoria]->descricao }}</a>
        </h4>
    </div>
    <div class="favorites-contens">
        <ul class="favorites-slider favorites-slider__video list-inline  slick-slider">

            @foreach ($conteudo_movies as $i => $conteudo)
                <?php
                $desc = '';
                if (isset($conteudo->descricao)) {
                    $desc = str_replace('"', '', $conteudo->descricao);
                    $desc = str_replace("'", '', $desc);
                }
                ?>
                <li class="slide-item">
                    <div class="block-images position-relative"
                        onclick="abrirModal({{ isset($conteudo->temporada) ? $conteudo->temporada : 0 }},{{ $conteudo->id }},'{{ $desc }}','{{ $conteudo->file ? $conteudo->file->imageUrl : '' }}','{{ env('API_URL') }}')">
                        <div class="img-box">
                            <img data-original="{{ $conteudo->file ? $conteudo->file->imageUrl : '' }}"
                                src="{{ asset('/geral/lazy.png') }}" class="img-fluid"
                                alt="{{ $conteudo->titulo }}">
                        </div>
                    </div>
                    <div class="block-description">
                        <div class="botoes-card">
                            <a href="{{ route('video', isset($conteudo) ? $conteudo->id : '') }}">
                                <button class="btn-play" aria-label="Assistir" data-balloon-pos="down">
                                    <span class="btn-play-hover">
                                        <i class="fa fa-play mr-1" aria-hidden="true"></i>
                                    </span>
                                </button>
                            </a>
                            <button class="btn-fav" aria-label="Favoritar" data-balloon-pos="up"
                                onclick="favorite('movie', {{ $conteudo->id }});">
                                <span class="btn-fav-hover">
                                    <i class="glyphicon {{ $conteudo->liked($perfilAtual) ? 'glyphicon-ok' : 'glyphicon-plus' }} favorite_{{ $conteudo->id }}"
                                        aria-hidden="true"></i>
                                </span>
                            </button>
                            <button class="btn-mais" aria-label="Mais Informações" data-balloon-pos="up"
                                onclick="abrirModal({{ isset($conteudo->temporada) ? $conteudo->temporada : 0 }},{{ $conteudo->id }},'{{ $desc }}','{{ $conteudo->file ? $conteudo->file->imageUrl : '' }}','{{ env('API_URL') }}')">
                                <span class="btn-mais-hover">
                                    <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                </span>
                            </button>
                        </div>
                        <div class="imdbInfo">
                            @if ($conteudo->imdb)
                                <p class="imdbico">IMDb</p>
                                @if ($conteudo->imdb < 5)
                                    <p class="imdbNota-baixa">{{ $conteudo->imdb }}</p>
                                @elseif($conteudo->imdb < 7)
                                    <p class="imdbNota-media">{{ $conteudo->imdb }}</p>
                                @else
                                    <p class="imdbNota-alta">{{ $conteudo->imdb }}</p>
                                @endif
                            @endif
                            @if ($conteudo->classificacao == '0')
                                <p class="cl-livre">L</p>
                            @elseif($conteudo->classificacao == '10')
                                <p class="cl-10">{{ $conteudo->classificacao }}</p>
                            @elseif($conteudo->classificacao == '12')
                                <p class="cl-12">{{ $conteudo->classificacao }}</p>
                            @elseif($conteudo->classificacao == '14')
                                <p class="cl-14">{{ $conteudo->classificacao }}</p>
                            @elseif($conteudo->classificacao == '16')
                                <p class="cl-16">{{ $conteudo->classificacao }}</p>
                            @elseif($conteudo->classificacao == '18')
                                <p class="cl-18">{{ $conteudo->classificacao }}</p>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach

            <li class="slide-item">
                <a href="{{ route('home_filmes_categoria', ['category' => $categoria]) }}">
                    <div class="block-images position-relative">
                        <div class="img-box mais-filmes">
                            <img src="/geral/exibir-todos.jpg" alt="" class="mais">
                            <div class="description">
                                <h6>Exibir todos</h6>
                            </div>
                        </div>
                    </div>
                </a>
            </li>

        </ul>
    </div>
    @include('home.partials.mais_info')
</section>
