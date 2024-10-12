@if(sizeof($conteudo_aovivo))
<section id="iq-favorites" class="home__slide-section slide-aovivo-home" style="display: none;">
    {{-- <div class="iq-main-header d-flex align-items-center justify-content-between">
        <h4 class="main-title slide-aovivo-title" style="margin-bottom:-35px; margin-left:-25px;">
            Canais
        </h4>
    </div>
    <div class="favorites-contens favorites-slider__aovivo-container">
        <ul class="favorites-slider favorites-slider__aovivo slick-slider" style="padding:0;">

            @foreach ($conteudo_aovivo as $i => $conteudo)
                <div style="width:120px;height:120px; margin-bottom:50px;" id="favorite_i_{{ $conteudo->id }}" class="slide-item aovivo-home" onmouseover="$('#img_{{$conteudo->id}}').removeClass('grayscale');" onmouseout="$('#img_{{$conteudo->id}}').addClass('grayscale');">
                    <a href="{{ route('video_aovivo', ['id' => $conteudo->id]) }}">
                        <div class="block-images position-relative aovivo-home-block">
                        <div style="object-fit:fill; width:100px;height:100px; border-radius:5px;" class="img-box aovivo-home-block-img">
                            <img style="border-radius:5px;" src="{{ $conteudo->getImagemFullUrl() }}" id="img_{{$conteudo->id}}" class="img-fluid grayscale"
                            alt="{{ $conteudo->titulo }}">
                        </div>
                        </div>
                    </a>
                </div>
            @endforeach

        </ul>
    </div> --}}
</section>
@endif

@if (Auth::check() && sizeof($conteudo_aovivo_favoritos))
  <section id="iq-favorites" class="home__slide-section">
    <div class="iq-main-header d-flex align-items-center justify-content-between">
        <h4 class="main-title">
            @if (sizeof($conteudo_aovivo_favoritos))
            Favoritos
            @endif
        </h4>
        </div>
        <div class="favorites-contens favorites-slider__aovivo-container">
        <ul id="favorites_ul"
            class="favorites-slider favorites-slider__aovivo slick-slider">
            @foreach ($conteudo_aovivo_favoritos as $i => $conteudo)
            <div id="favorite_i_{{ $conteudo->id }}" class="slide-item">
                <a href="{{ route('video_aovivo', ['id' => $conteudo->id]) }}">
                <div class="block-images position-relative">
                    <div class="img-box">
                    <img src="{{ $conteudo->file->imageUrl }}" class="img-fluid grayscale"
                        alt="{{ $conteudo->titulo }}">
                    </div>
                    <div class="block-description">
                        <div class="hover-buttons">
                            <span class="btn btn-hover">
                            <i class="fa fa-play mr-1" aria-hidden="true"></i>Play
                            </span>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            @endforeach

        </ul>
    </div>
  </section>
@endif
