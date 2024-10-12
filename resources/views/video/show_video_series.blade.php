<script type="text/javascript" src="{{ asset('/js/kaltura-ovp-player.js') }}"></script>
<?php
use App\Http\Controllers\Controller;
$c = new Controller();
$perfilAtual = App\Models\Assinante::getIdPerfilAtual();
$appProvedor = $c->__app();
$user = $c->__subscriber();
?>
@extends('layouts.video')

@section('video_js')
  <script>
    var last = undefined;
    var episodioID = "{{ $conteudo_selecionado->id }}";
    var temporada = "{{ $conteudo_selecionado->temporada }}";
    var serieId = "{{ $conteudo_selecionado->idRefConteudoSerie }}";

    $(document).ready(function() {
      var appID = "{{ $appProvedor->id }}";
      var conteudoID = "{{ $conteudo_selecionado->id }}";
      const perfilAtual = "{{ $perfilAtual }}";

      try {
        var url = `https://analytics.nxplay.com.br/position/${appID}-${perfilAtual}/${conteudoID}/${temporada}`;
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
          console.log("FAIL1", erro);
        });

      } catch (error) {
        console.log("ERROR", error);
      }
    });
  </script>
@endsection


@section('main_content')
  <section class="movie-detail container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="trending-info season-info g-border">
          <h4 class="trending-text big-title text-uppercase mt-0" id="titulo_ep">{{ $conteudo_selecionado->titulo }}</h4>
          <!-- episodio -->

          <p class="trending-dec w-100 mb-0" id="descricao_ep">{{ $conteudo_selecionado->descricao }}</p>
          <ul class="list-inline p-0 mt-4 share-icons music-play-lists">
            <li><span><i class="fa fa-plus"></i></span></li>
            <li><span><i class="fa fa-heart"></i></span></li>
            <li class="share">
              <span><i class="fa fa-share"></i></span>
              <div class="share-box">
                <div class="d-flex align-items-center">
                  <a href="#" class="share-ico"><i class="fa fa-facebook"></i></a>
                  <a href="#" class="share-ico"><i class="fa fa-twitter"></i></a>
                  <a href="#" class="share-ico"><i class="fa fa-link"></i></a>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
@endsection

<script>
  var interval;
  var player;
  var ep_selecionado = '{{ $conteudo_selecionado->id }}'
  var img = '{{ $conteudo_selecionado->getImagemFullUrl() }}'
  playlist = <?= $playlist ?>

  // function sendBeatAnalytics() {

  //   //if (player.isPlaying()) {
  //     var settings = {
  //       "url": "https://d3gw6kqe2un7kj.cloudfront.net/beat",
  //       "method": "POST",
  //       "timeout": 0,
  //       "crossDomain": true,
  //       "dataType": 'json',
  //       "contentType": "application/json; charset=utf-8",
  //       "headers": {
  //         "Authorization": "Basic YXBpLWFuYWx5dGljczo0QGtfK2V3O3h1OTVWLg==",
  //         "Access-Control-Allow-Origin": "*"

  //       },
  //       "data": JSON.stringify({
  //         "appId": `{{ $appProvedor->id }}`,
  //         "dataType": "beat",
  //         "type": "vod",
  //         "user": {
  //           "id": `{{ $user->id }}`,
  //           "email": `{{ $user->email }}`
  //         },
  //         "asset": {
  //           "id": '{{ $conteudo_selecionado->id }}',
  //           "title": '{{ $conteudo_selecionado->titulo }}',
  //           "timeCurrent": null,
  //           "assetDuration": ""
  //         },
  //         "data": "beat",
  //         "payload": {
  //           "published": "2021-08-02T15:57:19.728Z",
  //           "collection": null
  //         }
  //       }),
  //     };

  //     $.ajax(settings).done(function(response) {
  //       console.log(response);
  //     });
  //  // }
  // }

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

  var source = {
    "title": '{{ $conteudo_selecionado->titulo }}',
    "description": '{{ $conteudo_selecionado->id }}',
    "hls": '{{ $conteudo_selecionado->getUrlWithCdnVod($appProvedor, 'url_video') }}',
    "dash": '{{ $conteudo_selecionado->url_dash != null ? $conteudo_selecionado->getUrlWithCdnVod($appProvedor, 'url_dash') : '' }}',
    thumbnailTrack: {
      url: '{{ $conteudo_selecionado->thumbnails_track }}'
    },
    labeling: {
      dash: {
        qualities: getQualityLabels,
        subtitles: getSubtitleLabels,
        tracks: getTrackLabels
      },
      hls: {
        qualities: getQualityLabels,
        subtitles: getSubtitleLabels,
        tracks: getTrackLabels
      }
    }
  }

  function setupPlayer(timeSeek) {
    var i = 0;

    if (ep_selecionado != undefined) {
      playlist.forEach(function(ep, key) {
        if (ep.id == ep_selecionado) {
          i = key + 1;
        }
      });
    }

    let listSources = []

    playlist.forEach(function(ep, key) {
      const obj = {
          sources: {
            poster:img,
            hls: [
              {
                id: ep.id,
                mimetype: "application/x-mpegURL",
                url: ep.hls
              }
            ]
          }
        }

        listSources.push(obj)
    });

    var config = {
      targetId: "player2",
      playback: {
        audioLanguage: "pt",
        autoplay: true,
        muted: false,
        pictureInPicture: false,
      },
      provider: {
        partnerId: 1,
      },
      playlist: {
        id: '1234',
        metadata: {
          name: 'my playlist name',
          description: 'my playlist desc'
        },
        items:listSources,
        countdown: {
          duration: 10,
          showing: true
        }
      }
    }

    try {
      var player = KalturaPlayer.setup(config);

      const playerRuntime = KalturaPlayer.getPlayer("player2")
      playerRuntime.addEventListener("error", () => {
        player.playlist.playNext();
        })

    } catch (e) {
      console.error(e.message)
    }

        interval = setInterval(function() {
          if (player.getCurrentTime()) {
            time = player.getCurrentTime();
            //enviarAnalytics(time);
            //sendBeatAnalytics();
          }
        }, 30000);

      // },

      // function(reason) {
        console.log('Error while creating Bitmovin Player instance');
        if (interval != undefined) {
          clearInterval(interval);
        }
        if (last != undefined) {
          clearInterval(last);
        }
      // }
    // );
    
  }
</script>

@section('video_container')
  <div id="player2" class="player"></div>
  <!--
          <video class="video d-block" controls="" loop="">
            <source src="{{ asset('geral/sample-video.mp4') }}" type="video/mp4">
            <source src="{{ $conteudo_selecionado->url_video }}" >
          </video>
        -->
@endsection
