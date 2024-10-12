@extends('layouts.index')

@section('main_content')
	@foreach ($destaques_home as $i => $conteudo_home)
        @include('home.partials.destaque_series')
    @endforeach

	<br />

    <div class="main-content-container">
        @foreach ($conteudo_by_categorias as $categoria => $conteudo_series)
            @include('home.partials.series_desenhos')
        @endforeach

        @foreach ($conteudos_series_by_category as $categoria => $conteudos_category)
            @include('home.partials.series_desenhos_by_category')
        @endforeach
    </div>
@endsection
