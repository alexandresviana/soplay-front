            <section id="iq-favorites">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12 overflow-hidden">
                            <div class="iq-main-header d-flex justify-content-between">
                                <h4 class="main-title">
                                    <a href="{{route('home_series')}}">{{$categoria}}</a>
                                </h4>
                            </div>
                            <div class="favorites-contens">
                                <ul class="favorites-slider favorites-slider__video list-inline  slick-slider row p-0 mb-0" >

@foreach ($conteudos_category as $i => $conteudo)
                                    <li class="slide-item" style="max-width: 250px;">
                                        <a href="{{route('video', ['id' => $conteudo->id])}}">
                                            <div class="block-images position-relative">
                                                <div class="img-box">
                                                    <img src="{{$conteudo->getImagemFullUrl()}}" style="height: 350px;" class="img-fluid" alt="{{$conteudo->titulo}}" >
                                                </div>
                                                <div class="block-description">
                                                    <h6>{{$conteudo->titulo}}</h6>
                                                    <span><small>{{$conteudo->subtitulo}}</small></span>
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
