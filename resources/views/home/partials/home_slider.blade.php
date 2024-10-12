<section id="home" class="iq-main-slider p-0" style="display:none;">
    <div id="home-slider" class="slider m-0 p-0">

        @foreach ($destaques_home_aovivo as $i => $conteudo_home)
            <div class="slide slick-bg "
                style="background-image: url({{  $conteudo_home->file ?  $conteudo_home->file->imageUrl : '' }});background-size:cover;background-position: center;">
                <div class="slide-inner-container">
                    <div class="slider-inner h-100">
                        <div class="row align-items-center  h-100">
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="slider__text-content">
                                    <div class="slider__text-title">
                                        {{ $conteudo_home->titulo }}
                                    </div>
                                    <div class="slider__actions">
                                        <a href="{{ route('video_aovivo', ['id' => $conteudo_home->id]) }}" class="btn btn-hover">
                                            <i class="fa fa-play mr-2" aria-hidden="true"></i>
                                            Assista agora
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @foreach ($destaques_home as $i => $conteudo_home)
            <div class="slide slick-bg"
                style="background-image: url({{ $conteudo_home->file->imageUrl }});background-size:cover;background-position: center">
                <div class="slide-inner-container">
                    <div class="slider-inner h-100">
                        <div class="row align-items-center  h-100">
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="slider__text-content">
                                    <div class="slider__text-title">
                                        {{ $conteudo_home->titulo }}
                                    </div>
                                    <div class="slider__actions">
                                        <a href="{{ route('video', ['id' => $conteudo_home->id]) }}" class="btn btn-hover">
                                            <i class="fa fa-play mr-2" aria-hidden="true"></i>
                                            Assista agora
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44" width="44px" height="44px" id="circle" fill="none"
            stroke="currentColor">
            <circle r="20" cy="22" cx="22" id="test"></circle>
        </symbol>
    </svg>
</section>
