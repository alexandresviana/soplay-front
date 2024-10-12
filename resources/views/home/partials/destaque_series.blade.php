<section
    class="destaque-serie-container"
    style="background-image: url({{ $conteudo_home->file ? $conteudo_home->file->imageUrl : '' }});">

    <div class="destaque-serie-content">

        <div class="destaque-cover">
            @if ($conteudo_home->file)
                <img src="{{ $conteudo_home->file ? $conteudo_home->file->imageUrl : '' }}"
                    class="img-fluid"
                    alt="{{ $conteudo_home->titulo }}">
            @endif
        </div>
        <div class="series-details">
            <a href="{{ route('video.series', ['id' => $conteudo_home->id]) }}">
                <h1 class="big-title title text-uppercase"
                    data-animation-in="fadeInLeft"
                    data-delay-in="0.6"
                    style="opacity: 1; animation-delay: 0.6s;">
                    {{ $conteudo_home->titulo }}
                </h1>
            </a>
            <div class="parallax-ratting d-flex align-items-center mt-3 mb-3">
                <ul class="ratting-start p-0 m-0 list-inline d-flex align-items-center justify-content-left">
                    <li>
                        <a href="javascript:void(0);">
                            <i class="fa fa-star" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="pl-2">
                            <i class="fa fa-star" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="pl-2">
                            <i class="fa fa-star" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="pl-2">
                            <i class="fa fa-star" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="pl-2">
                            <i class="fa fa-star-half-o" aria-hidden="true"></i>
                        </a>
                    </li>
                </ul>
                <span class="text-white ml-3">9.2 (lmdb)</span>
            </div>
            <div class="movie-time d-flex align-items-center mb-3">
                <div class="badge badge-secondary mr-3">13+</div>
                <h6 class="text-white">2h 30m</h6>
            </div>
            <p>{{ $conteudo_home->descricao }}</p>
            <div class="destaque-home-action">
                <a href="{{ route('video.series', ['id' => $conteudo_home->id]) }}" class="btn btn-hover">Ver detalhes</a>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <!--
            <div class="parallax-img">
            <a href="movie-details.html">
                {{ $conteudo_home->titulo }}
                <img src="/geral/p1.jpg" class="img-fluid w-100" alt="bailey">
                }
            </a>
            </div>
        -->
    </div>

</section>
