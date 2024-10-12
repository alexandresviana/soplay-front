@extends('layouts.index')

@section('main_content')

    @foreach ($destaques_home as $i => $conteudo_home)
        @include('home.partials.destaque_series')
    @endforeach

    <div class="main-content-container">
        <div class="seasons-and-episodes__container">
            @if ($conteudo->temporada)
                <ul class="nav nav-tabs seasons-and-episodes__seasons-container">
                    @for ($i = 0; $i != $conteudo->temporada; $i++)
                        <li>
                            <a href="#season-{{ $i + 1 }}"
                                class="seasons-and-episodes__season {{ $i === 0 ? 'active' : '' }}"
                                data-season="{{ $i + 1 }}" data-toggle="tab">Temporada {{ $i + 1 }}
                            </a>
                        </li>
                    @endfor
                </ul>

                <div id="content" class="tab-content seasons-and-episodes__episodes-container">

                    @for ($i = 0; $i != $conteudo->temporada; $i++)
                        <div class="tab-pane card fade seasons-and-episodes__season-episodes {{ $i === 0 ? 'show active' : '' }}"
                            id="season-{{ $i + 1 }}" data-season="{{ $i + 1 }}">
                            <a data-toggle="collapse" href="#collapse-{{ $i + 1 }}"
                                aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" class="card-header" role="tab">
                                <h5 class="mb-0">
                                    Temporada {{ $i + 1 }}
                                </h5>
                            </a>

                            <div id="collapse-{{ $i + 1 }}" class="collapse {{ $i === 0 ? 'show' : '' }}"
                                data-parent="#content">
                                <div class="card-body">
                                    @foreach ($conteudo_series_episodios as $episode => $conteudoep)
                                        @if ($i + 1 === $conteudoep->temporada)
                                            <a href="{{ route('video.series_play', ['id' => $conteudoep->id]) }}"
                                                class="seasons-and-episodes__episode">
                                                <div class="seasons-and-episodes__episode-thumb">
                                                    @if ($conteudoep->getImagemFullUrl())
                                                        <img alt="{{ $conteudoep->titulo }}"
                                                            src="{{ $conteudoep->getImagemFullUrl() }}">
                                                    @endif
                                                </div>
                                                <div class="seasons-and-episodes__episode-data">
                                                    <div class="seasons-and-episodes__episode-data-number">
                                                        Episódio {{ $conteudoep->episodio }}
                                                    </div>
                                                    <div class="seasons-and-episodes__episode-data-name">
                                                        {{ $conteudoep->titulo }}
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endfor

                </div>
            @else
                Não existem temporadas cadastradas
            @endif
        </div>
    </div>

@endsection
