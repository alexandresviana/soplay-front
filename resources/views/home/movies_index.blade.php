@extends('layouts.index')


@section('main_content')
    {{-- @ include('home.partials.movies_list_home') --}}

    @foreach ($destaques_home as $i => $conteudo_home)
        @include('home.partials.paralax_1')
    @endforeach

    <br /><br /><br />

    <div class="main-content-container add-margin">
        @foreach ($conteudo_by_categorias as $categoria => $conteudo_movies)
            @if($conteudo_movies)
                @include('home.partials.filmes')
           @endif
        @endforeach
    </div>
@endsection
