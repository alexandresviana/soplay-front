<section id="iq-favorites">
    <div class="container-fluid" style="margin-top:-20px;">
        <div class="row">
            <div class="col-sm-12 overflow-hidden">
                <div class="iq-main-header d-flex align-items-center justify-content-between">
                    <h4 class="main-title">
                        <!--
                                    <a href="{{ route('home_ao_vivo') }}">TV ao vivo</a>
                                    -->
                    </h4>
                </div>
                <div class="favorites-contens">
                    <ul
                        class="favorites-slider favorites-slider__video list-inline align-items-center slick-slider row p-0 mb-0">
                        @foreach ($destaques_home as $i => $conteudo)
                            <li id="favorite_i_{{ $conteudo->id }}" class="slide-item" style="max-width: 130px;">
                                <a href="{{ route('video', ['id' => $conteudo->id]) }}">
                                    <div class="block-images position-relative">
                                        <div class="img-box">
                                            <img src="{{ $conteudo->getImagemFullUrl() }}" style="width: 130px;"
                                                class="img-fluid grayscale" alt="{{ $conteudo->titulo }}">
                                        </div>
                                        <div class="block-description">
                                            <h6>{{ $conteudo->titulo }}</h6>
                                            <span>{{ $conteudo->descricao }}</span>
                                            <div class="hover-buttons">
                                                <span class="btn btn-hover">
                                                    <i class="fa fa-play mr-1" aria-hidden="true"></i>Play
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
