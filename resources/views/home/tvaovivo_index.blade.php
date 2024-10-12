@extends('layouts.index')

@section('main_content')
    @include('home.partials.tvaovivo')

    @foreach ($conteudos_ao_vivo_by_category as $categoria => $conteudos_category)
        @include('home.partials.tvaovivo_by_category')
    @endforeach
@endsection
