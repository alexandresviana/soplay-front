<?php
use App\Http\Controllers\Controller;
$perfilAtual = App\Models\Assinante::getIdPerfilAtual();
$c = new Controller();
$appProvedor = $c->__app();
$user = $c->__subscriber();
?>
@extends('layouts.video')

@section('video_js')
  <script>
    $(document).ready(function() {

      var appID = "{{ $appProvedor->id }}";
      var conteudoID = "{{ $conteudo->id }}";
      const perfilAtual = "{{ $perfilAtual }}";

      try {
        var url = `https://analytics.nxplay.com.br/position/${appID}-${perfilAtual}/${conteudoID}/0`;
        var settings = {
          "url": url,
          "method": "GET",
          "headers": {
            'Access-Control-Allow-Origin': '*'
          }
        };
        $.ajax(settings).done(function(response) {
          setupPlayer(response);
        }).fail(function(erro) {
          setupPlayer(0);
        });

      } catch (error) {

      }
    });

    function playVideo() {
      window.location = '{{ request()->url() }}#v';
      $('#main-header').hide();
      player.play();
      $('#btnVoltar').show();
    }

    function voltar() {
      window.location.href = "https://" + window.location.hostname + "/home/filmes/";
    }
  </script>
@endsection

@section('before_video_container')
  <section id="parallex" class="parallax-window"
    style="background-position: left top; background-image: url({{ $conteudo_home->getImagemBannerFullUrl() }});">
    <div class="main-content-container">
      <div class="row align-items-center justify-content-center h-100 parallaxt-details">
        <div class="col-lg-12 r-mb-23">
          <div class="text-left">
            <a href="{{ route('video', ['id' => $conteudo_home->id]) }}">
              <h1 class="big-title title text-uppercase fadeInLeft animated" data-animation-in="fadeInLeft"
                data-delay-in="0.6" style="opacity: 1; animation-delay: 0.6s;">
                {{ $conteudo_home->titulo }}
              </h1>
            </a>
          </div>
        </div>
        <div class="col-lg-6 r-mb-23">
          <div class="text-left">
            <div class="parallax-ratting d-flex align-items-center mt-3 mb-3">
              <ul class="ratting-start p-0 m-0 list-inline text-primary d-flex align-items-center justify-content-left">
                <li><a href="javascript:void(0);" class="text-primary"><i class="fa fa-star" aria-hidden="true"></i></a>
                </li>
                <li><a href="javascript:void(0);" class="pl-2 text-primary"><i class="fa fa-star"
                      aria-hidden="true"></i></a></li>
                <li><a href="javascript:void(0);" class="pl-2 text-primary"><i class="fa fa-star"
                      aria-hidden="true"></i></a></li>
                <li><a href="javascript:void(0);" class="pl-2 text-primary"><i class="fa fa-star"
                      aria-hidden="true"></i></a></li>
                <li><a href="javascript:void(0);" class="pl-2 text-primary"><i class="fa fa-star-half-o"
                      aria-hidden="true"></i></a></li>
              </ul>
              <span class="text-white ml-3">9.2 (lmdb)</span>
            </div>
            <div class="movie-time d-flex align-items-center mb-3">
              <div class="badge badge-secondary mr-3">13+</div>
              <h6 class="text-white">2h 30m</h6>
            </div>
            <p>{{ $conteudo_home->descricao }}</p>
            <div class="parallax-buttons">
              <a href="javascript:playVideo();" class="btn btn-hover">Assista Agora</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
        </div>
      </div>
    </div>
  </section>
@endsection


<script type="text/javascript" src="{{ asset('/js/kaltura-ovp-player.js') }}"></script>

<script>
  var interval;

  function sendBeatAnalytics() {

    if (player.isPlaying()) {
      var settings = {
        "url": "https://analytics.soplay.com.br/beat",
        "method": "POST",
        "timeout": 0,
        "crossDomain": true,
        "dataType": 'json',
        "contentType": "application/json; charset=utf-8",
        "headers": {
          "Authorization": "Basic YXBpLWFuYWx5dGljczo0QGtfK2V3O3h1OTVWLg==",
          "Access-Control-Allow-Origin": "*"
        },
        "data": JSON.stringify({
          "appId": `{{ $appProvedor->id }}`,
          "dataType": "beat",
          "type": "vod",
          "user": {
            "id": `{{ $user->id }}`,
            "email": `{{ $user->email }}`
          },
          "asset": {
            "id": '{{ $conteudo->id }}',
            "title": '{{ $conteudo->titulo }}',
            "timeCurrent": null,
            "assetDuration": ""
          },
          "data": "beat",
          "payload": {
            "published": "2021-08-02T15:57:19.728Z",
            "collection": null
          }
        }),
      };

      $.ajax(settings).done(function(response) {});
    }
  }

  function enviaContaudoAssistido() {
    const token = sessionStorage.getItem('current_token_login');
    if (player.isPlaying()) {
      var settings = {
        "url": "https://osm-play.nxtv.com.br/api/v1/assistindo/{{ $conteudo->id }}",
        "method": "POST",
        "timeout": 0,
        "crossDomain": true,
        "dataType": 'json',
        "contentType": "application/json; charset=utf-8",
        "headers": {
          "Authorization": "Bearer " + token,
          "Access-Control-Allow-Origin": "*"
        },
        "data": JSON.stringify({
          "id_app": `{{ $appProvedor->id }}`,
          "id_conteudo": `{{ $conteudo->id }}`,
          "id_usuario": "{{ $user->id }}",
          "origem": "site"
        }),
      };
      $.ajax(settings).done(function(response) {});
    }
  }

  function enviarAnalytics(time) {
    var appID = "{{ $appProvedor->id }}"
    var conteudoID = "{{ $conteudo->id }}";
    const perfilAtual = "{{ $perfilAtual }}";

    var url = `https://analytics.nxplay.com.br/position/${appID}-${perfilAtual}/${conteudoID}/0/${time}`
    if (player.isPlaying()) {
      var settings = {
        "url": url,
        "method": "POST",
        "headers": {
          'Access-Control-Allow-Origin': '*'
        }
      };
      $.ajax(settings).done(function(response) {
        console.log("Analytics", response);
      }).fail(function() {
        setupPlayer(0);
      });;
    }
  }

  function getTime(player) {
    var appID = "{{ $appProvedor->id }}"
    var conteudoID = "{{ $conteudo->id }}";
    const perfilAtual = "{{ $perfilAtual }}";

    try {
      var url = `https://analytics.nxplay.com.br/position/${appID}-${perfilAtual}/${conteudoID}/0`
      var settings = {
        "url": url,
        "method": "GET"
      };
      $.ajax(settings).done(function(response) {
        player.seek(200);
      });
    } catch (error) {
      console.log("Erro", error);
    }
  }

  var humanizeBitrate = function(bitrate) {
    var mbit = bitrate / 1000000;
    var rounded = mbit < 3 ? Math.round(mbit * 10) / 10 : Math.round(mbit);
    return rounded + 'Mbit';
  };

  var formatBitrate = function(bitrate) {
    return '(' + humanizeBitrate(bitrate) + ')';
  };

  var getQualityLabels = function(data) {
    var label;
    if (data.height <= 144) {
      label = '144p';
    } else if (data.height <= 240) {
      label = '240p';
    } else if (data.height <= 360) {
      label = '360p';
    } else if (data.height <= 480) {
      label = 'SD 480p';
    } else if (data.height <= 720) {
      label = 'HD 720p';
    } else if (data.height <= 1080) {
      label = 'HD 1080p';
    } else if (data.height <= 1440) {
      label = 'HD 1440p';
    } else if (data.height <= 2160) {
      label = '4K 2160p';
    } else {
      return '';
    }
    return label + ' ' + formatBitrate(data.bitrate);
  };


  var getSubtitleLabels = function(data) {
    if (!data.label) {
      return label += ' (sem legenda)';
    }
    var label = data.label.toUpperCase();
    if (data.label === 'en') {
      return label += ' (original)';

    }
    return label;
  };

  var getTrackLabels = function(data) {
    if (!data.mimeType) {
      return;
    }
    if (data.mimeType.indexOf('audio') >= 1) {
      return data.lang.toUpperCase();
    }
    return data.lang;
  };

  function setupPlayer(timePlayer) {

  //Config Kaltura Filmes

    // let player;
    var config = {
      targetId: "player2",
      playback: {
        audioLanguage: "pt",
        autoplay: false,
        muted: false,
        pictureInPicture: false,
      },
      provider: {
        partnerId: 1,
      }
    }

    const mediaConfig = {
      sources: {
        hls: [
          {
            mimetype: 'application/x-mpegurl',
            url: `{{ $conteudo->getUrlWithCdnVod($appProvedor, 'url_video') }}`
            }
        ],
        dash: [
          {
            mimetype: 'application/dash+xml',
            url: `{{ $conteudo->getUrlWithCdnVod($appProvedor, 'url_video') }}`
          }
        ],
        poster: '{{ $conteudo_home->getImagemBannerFullUrl() }}'
      },
      drm: {
        keySystem: 'https://widevine-dash.ezdrm.com/widevine-php/widevine-foreignkey.php?pX=41AD07'
      }
  };

    try {
      var player = KalturaPlayer.setup(config);
      player.setMedia(mediaConfig);

      player.load().then(

      function() {
        var time = 0;
        var enSubtitle = {
          id: "sub1",
          lang: "pt",
          label: "Português",
          url: '{{ $conteudo->legenda_pt }}',
          kind: "subtitle"
        };
        player.subtitles.add(enSubtitle);
        player.seek(timePlayer);
        assitido = false;

        //Localização para PT-BR do menu
        var locQualidade = document.getElementById('bmpui-id-130');
        var locVelocidade = document.getElementById('bmpui-id-133');
        var locAudio = document.getElementById('bmpui-id-136');
        var locQAudio = document.getElementById('bmpui-id-139');
        var locLegenda = document.getElementById('bmpui-id-175');

        if (locQualidade != null) {
          locQualidade.innerText = "Qualidade do vídeo";
        }
        if (locVelocidade != null) {
          locVelocidade.innerText = "Velocidade";
        }
        if (locAudio != null) {
          locAudio.innerText = "Áudio";
        }
        if (locQAudio != null) {
          locQAudio.innerText = "Qualidade do áudio";
        }
        if (locLegenda != null) {
          locLegenda.innerText = "Legenda";
        }
        // Fim Localização

        interval = setInterval(function() {
          sendBeatAnalytics();
          if (player.getCurrentTime() > 60.000000 && assitido == false) {
            assitido = true;
          }

          if (player.getCurrentTime()) {
            time = player.getCurrentTime();
            enviarAnalytics(time);
          }

        }, 30000);

      },
      function(reason) {
        if (interval != undefined) {
          clearInterval(interval);
        }
      }
);
    } catch (e) {
      console.error(e.message)
    }
    
  }
</script>

@section('video_container')
  <div class="internal-page__main-header" style="display: none;" id="btnVoltar">
    <a href="#" onClick="voltar()" class="movie-details__action-return">
      <svg width="27" height="19" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M25.532 8.635H2.69l6.834-7.16a.895.895 0 000-1.222.792.792 0 00-1.16 0L.236 8.801a.905.905 0 000 1.22l8.126 8.55c.321.337.84.337 1.16 0a.895.895 0 000-1.222L2.69 10.362h22.842c.453 0 .82-.387.82-.864 0-.476-.367-.863-.82-.863z"
          fill="#E5E5E5" />
      </svg>
    </a>
  </div>
  <a name="v" id="v"></a>
  <div id="player2" class="player">
  </div>
@endsection
