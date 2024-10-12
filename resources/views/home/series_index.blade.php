@extends('layouts.index')


@section('main_content')
  @foreach ($destaques_home as $i => $conteudo_home)
    @include('home.partials.destaque_series')
  @endforeach

    <div class="main-content-container" style="padding-top:100px; padding-inline:20px;">
            @if($by_category > 0 || $by_category == 'favorito' || $by_category == 'aluguel')
                @include('home.partials.series')
            @else
                @foreach ($conteudo_by_categorias as $categoria => $conteudos_category)
                    @include('home.partials.series2')
                @endforeach
            @endif
    </div>

@endsection
