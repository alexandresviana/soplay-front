@extends('layouts.index')


@section('main_content')

    <div class="main-content-container" style="padding-top:100px; padding-inline:20px;">
        @foreach ($conteudo_by_categorias as $categoria => $conteudos_category)
            @if($categoria == 'aluguel_movies')
                @include('home.partials.filmes')
            @elseif($categoria == 'aluguel_series')
                @include('home.partials.series2')
            @endif
        @endforeach

    </div>

@endsection
