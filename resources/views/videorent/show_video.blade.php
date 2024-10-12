<?php
  use \App\Http\Controllers\Controller;
  $c = new Controller;
  $appProvedor = $c->__app();
  $user = $c->__subscriber();
?>
@extends('layouts.index')


@section('main_content')
<style>
.parallax-img img {
  box-shadow: 0px 20px 40px rgba(0, 0, 0, 0.6);
}

.parallax-window {
  height: 100vh;
  padding: 100px 0;
  position: relative;
  background-size: cover;
  background-attachment: fixed;
  display: flex;
  align-items: center;
}

.parallax-window::after {
  position: absolute;
  content: "";
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.4);
}

.parallaxt-details {
  z-index: 9;
  position: relative;
}

</style>
         <section id="parallex" class="parallax-window" style="background-position: left top; background-image: url({{$conteudo->getImagemBannerFullUrl()}});">
            <div class="main-content-container" style="overflow:hidden;">
               <div class="row justify-content-center h-100 parallaxt-details">
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block col-lg-10">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif

                  <div class="col-lg-6 r-mb-23">
                      <div class="col-lg-12 r-mb-23">
                         <div class="text-left">
                            <a href="{{route('video', ['id' => $conteudo->id])}}">
                               <h1 class="big-title title text-uppercase fadeInLeft animated" data-animation-in="fadeInLeft" data-delay-in="0.6" style="opacity: 1; animation-delay: 0.6s;">{{$conteudo->titulo}}</h1>
                            </a>
                         </div>
                      </div>
                      <div class="col-lg-12 r-mb-23">
                         <div class="text-left">
                            <div class="parallax-ratting d-flex align-items-center mt-3 mb-3">
                               <ul class="ratting-start p-0 m-0 list-inline text-primary d-flex align-items-center justify-content-left">
                                  <li><a href="javascript:void(0);" class="text-primary"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                                  <li><a href="javascript:void(0);" class="pl-2 text-primary"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                                  <li><a href="javascript:void(0);" class="pl-2 text-primary"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                                  <li><a href="javascript:void(0);" class="pl-2 text-primary"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                                  <li><a href="javascript:void(0);" class="pl-2 text-primary"><i class="fa fa-star-half-o" aria-hidden="true"></i></a></li>
                               </ul>
                               <span class="text-white ml-3">9.2 (lmdb)</span>
                            </div>
                            <div class="movie-time d-flex align-items-center mb-3">
                               <div class="badge badge-secondary mr-3">13+</div>
                               <h6 class="text-white">2h 30m</h6>
                            </div>
                            <p>{{$conteudo->descricao}}</p>
                            <div class="parallax-buttons">
                            </div>
                         </div>
                      </div>
                      <div class="col-lg-6">
                      </div>
                  </div>
                  @if($active_rent)
                  <div class="col-lg-6 align-top" style="background-color: #333;opacity:.8">
                      <div class="col-lg-12 r-mb-23">
                          <div class="text-left">
                              <h2 class="big-title title text"  style="opacity: 1;">Conteúdo disponível</h2>
                              <p>
                                Valor: <strong>R$ {{$active_rent->valor}}</strong><br />
                                Disponibilidade:
                                  de <strong>{{ (new \DateTime($active_rent->data_inicio))->format('d/m/Y, H:i') }} </strong>
                                  até <strong>{{ (new \DateTime($active_rent->data_fim))->format('d/m/Y, H:i') }} </strong>
                              </p>
                              <div class="parallax-buttons">
                                  @if($active_rent->tipo_conteudo == 'conteudo')
                                  <a href="{{route('video', ['id' => $active_rent->id_conteudo])}}" class="btn btn-hover">Assistir</a>
                                  @elseif($active_rent->tipo_conteudo == 'conteudo_series')
                                  <a href="{{route('video.series', ['id' => $active_rent->id_conteudo])}}" class="btn btn-hover">Assistir</a>
                                  @endif
                              </div>
                          </div>
                      </div>
                  </div>
                  @else
                      @if($invoice_info)
                          <div class="col-lg-6 align-top" style="background-color: #333;opacity:.8">
                              <div class="col-lg-12 r-mb-23">
                                  <div class="text-left">
                                      <h2 class="big-title title text"  style="opacity: 1;">Confirmação de pagamento</h2>
                                      <p>
                                        Valor: <strong>R$ {{$invoice_info['total_cents'] / 100}}</strong><br />
                                        Pagamento utilizando <strong>PIX</strong>
                                      </p>
                                      <div class="parallax-buttons">
                                        <div class="row">
                                          <div class="col-6">
                                              <h6>Aponte a câmera do seu celular para o qrcode utilizando o seu aplicativo de pagamentos</h6>
                                              <hr />
                                              <h6><a class="btn" href="javascript:return;" onclick="javascript:$('#txtpix').select();document.execCommand('copy')">Copiar código pix copia-cola</a></h6>
                                              <textarea class="col-12" width="100%" rows="4" id="txtpix">{{ $invoice_info['pix_qrcode_text'] }}</textarea>
                                              <hr />
                                              <p>Após o pagamento, o conteúdo estará liberado</p>

                                              @if($rent_info->tipo_conteudo == 'conteudo')
                                              <a href="{{route('video', ['id' => $rent_info->id_conteudo])}}" class="btn btn-sm btn-hover">Assistir</a>
                                              @elseif($rent_info->tipo_conteudo == 'conteudo_series')
                                              <a href="{{route('video.series', ['id' => $rent_info->id_conteudo])}}" class="btn btn-sm btn-hover">Assistir</a>
                                              @endif

                                          </div>
                                          <div class="col-6">
                                            <img src="{{$invoice_info['pix_qrcode_url']}}" class="bg-white" width="100%" />
                                          </div>

                                        </div>
                                        <br/>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @else
                          <div class="col-lg-6 align-top" style="background-color: #333;opacity:.8">
                              <div class="col-lg-12 r-mb-23">
                                  <div class="text-left">
                                      <h2 class="big-title title text"  style="opacity: 1;">Confirmar aluguel</h2>
                                      <p>
                                        Valor: <strong>R$ {{$rent_info->valor}}</strong><br />
                                        Diponível por: <strong>{{$rent_info->tempo_disponivel}} horas</strong>
                                      </p>
                                      <p>
                                        {{$rent_info->descricao}}
                                      </p>
                                      <div>
                                          <form class="mt-4" action="{{route('videorent.confirm', ['id' => $rent_info->id_conteudo, 'kind' => $rent_info->tipo_conteudo])}}" method="POST">
                                              @csrf
                                              @if(false)
                                              <div class="form-group">
                                                  <input type="password" name="password" class="form-control mb-0" id="password_parental" placeholder="Senha de acesso">
                                                  <small>Informe a senha para confirmar o aluguel</small><br />
                                              </div>
                                              @endif

                                              <div class="sign-info">
                                                  <button type="submit" class="btn btn-success">Confirmar</button>
                                              </div>
                                              <div>
                                                <br />
                                              </div>
                                          </form>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @endif
                  @endif
               </div>
            </div>
         </section>


<script>

    @if($rent_info->tipo_conteudo == 'conteudo')
    var redirectUrl = "{{route('video', ['id' => $rent_info->id_conteudo])}}";
    @elseif($rent_info->tipo_conteudo == 'conteudo_series')
    var redirectUrl = "{{route('video.series', ['id' => $rent_info->id_conteudo])}}";
    @endif

    function checkPaymentConfirmation(idRent)
    {
        var token = sessionStorage.getItem('current_token_login');

        var settings = {
            "url": "{{ config('app.osm_api_endpoint') }}/videorent/available/" + idRent,
            "method": "GET",
            "timeout": 0,
            "crossDomain": true,
            "dataType": 'json',
            "contentType": "application/json; charset=utf-8",
            "headers": {
                "Authorization": "Bearer " + token,
                "Access-Control-Allow-Origin": "*"

            },
        };

        $.ajax(settings).done(function(response) {
            if(!response.available) {
                return false;
            }
            clearInterval(interval);
            alert('Pagamento confirmado. Conteúdo disponível');
            window.location = redirectUrl;
        });
    }
</script>
@endsection

@section('javascript')
<script>

@if($invoice_info)
    var interval;
    $(document).ready(function() {
        interval = setInterval(function() {
          checkPaymentConfirmation({{ @$rented_content->id }});
        }, 5000);
    });
@endif

</script>
@endsection
