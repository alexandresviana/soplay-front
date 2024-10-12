@extends('layouts.layout_perfil')

@section('main_content')
<div class="main">
    <h3>Quem está assistindo?</h3>
    <div class="perfil_box">
        <ul>
            @foreach ($data as $item)
            <li>
                <a href="{{route('perfil.show', $item->id)}}">
                    <div class="lista_perfil" style="border-radius:50%; height:290px !important; width:290px !important; background-color:#FFF !important;">
                        @if ($item->arquivo)
                            <img class="perfil_img" src="{{$item->arquivo->getImagemFullUrl()}}" alt="">
                        @else
                            <svg width="138" height="155" viewBox="0 0 138 155" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M73 11C76.5079 9.03577 78.662 6.25544 80 3C82.245 6.58448 83.4911 10.6498 83 14C103.015 18.3323 109.73 32.8063 112 52C112.055 52.4985 111.98 57.3179 112 60C115.508 65.5936 115.909 73.6736 114 80C113.677 81.0699 112.462 81.9817 112 83C109.955 87.4868 107.262 90.3748 104 92C94.0827 123.996 44.797 123.542 35 92C33.9481 91.4663 32.8992 90.7614 32 90C26.8885 85.7226 24.2355 79.5358 24 73C23.8396 68.6877 24.6848 63.7091 27 60C27.0952 48.065 28.3879 35.8461 36 26C38.1557 23.1993 41.0492 19.9559 44 18C53.5514 11.7384 66.991 9.11823 71 0C72.6941 3.33934 73.5261 7.29628 73 11ZM69 127C56.5972 127 45.6755 121.136 41 113L19 122C10.6813 125.31 0 133.034 0 143V149C0.0105193 150.574 0.880961 151.888 2 153C3.11904 154.112 4.41813 154.991 6 155H132C133.581 154.99 134.882 154.112 136 153C137.118 151.888 137.99 150.573 138 149V143C138 133.029 127.304 125.31 119 122L97 113C92.3195 121.136 81.3928 127 69 127ZM40 87C40.3057 88.0868 40.6292 89.9331 41 91C49.489 115.493 86.8222 116.456 97 93C97.7101 91.2515 98.5186 88.824 99 87C102.849 85.7487 105.276 83.7989 107 80C107.343 79.2386 106.759 78.799 107 78C108.503 73.0944 108.758 65.5945 105 62C103.146 62.4985 103.273 66.2323 102 67C100.447 67.9323 99.431 66.2414 99 65C98.2734 62.8862 98.4761 58.2912 98 57C97.108 54.5871 94.446 51.9912 94 49C93.5239 45.7894 93.2378 45.7648 92 44C90.7622 42.2352 88.7183 40.7387 85 42C80.6352 43.4956 74.6978 45 69 45C63.3022 45 58.3748 43.4856 54 42C50.2817 40.7387 48.2177 42.2601 47 44C45.7823 45.7399 45.4761 45.7944 45 49C44.554 51.9912 41.897 54.5771 41 57C40.5289 58.2912 40.7316 62.8862 40 65C39.569 66.2414 38.5385 67.9323 37 67C35.7221 66.2273 34.8692 62.4587 33 62C30.4944 64.2933 29.8697 68.6648 30 72C30.1754 76.761 32.2766 81.8841 36 85C37.2929 86.0669 38.4766 86.5015 40 87Z" fill="white"/>
                            </svg>
                        @endif


                    </div>
                    <p>{{$item->nome}}</p>
                </a>

            </li>
            @endforeach
            <li>
                <a href="{{route('perfil.create')}}">
                    <div class="outline" style="border-radius:50%; height:290px !important; width:290px !important;">
                        <span>+</span>
                    </div>
                    <p>Adicionar perfil</p>
                </a>
            </li>
        </ul>
    </div>
    @if (count($data))
        <a class="my_btn" href="{{route('perfil.gerenciar')}}">
            Gerenciar perfis
        </a>
    @endif
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
<style>
.perfil_box ul {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.perfil_box ul li {
    margin-left: 20px
}

.outline {
    border: 5px solid #fff !important;
}

@media (max-width: 768px) {
    .perfil_box ul li {
        margin-left: 0px !important;
    }

    .perfil_box ul {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-wrap: wrap;
    }
}
</style>
@endsection
