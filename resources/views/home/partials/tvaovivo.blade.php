<?php
use App\Http\Controllers\Controller;
$c = new Controller();
$appProvedor = $c->__app();
$user = $c->__subscriber();
$routeName = \Route::currentRouteName();

$tipo = 'tv';

if ($routeName == 'home_ao_vivo_radios') {
    $tipo = 'radio';
}
$marginTopRadio = $routeName == 'home_ao_vivo_radios' ? 'margin-top: 75px;' : '';
$scheduleUrl = sprintf('%s/api/v1/schedule?id=%d&tipo=%s', URL::to('/'), $conteudo->id, $tipo);
?>
<div class="message--empty-message">Carregando canais...</div>

<div class="tv__player-container" style="<?= $marginTopRadio ?>">
  <div class="tv__player tv__player--hidden" id="not_display_mobile">
    <div defer id="myplayer" class="movie-player">
      <div class="player--loading"></div>
    </div>
  </div>

  <div class="tv__player-channels"></div>
</div>

<script>
  var interval_start = null;
  var interval_click = null;
  var routeName = "<?= $routeName ?>";

  var interval_assistir;

  function enviaContaudoAssistido(canal_id) {
    const token = sessionStorage.getItem('current_token_login');
    const id = canal_id === undefined ? `{{ $conteudo->id }}` : canal_id;

    if (player.isPlaying()) {
      var settings = {
        "url": "{{ config('app.osm_api_endpoint') }}/assistindo/" + id,
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
          "id_conteudo": id,
          "id_usuario": "{{ $user->id }}",
          "origem": "site",
          "time": '0',
          "tipo": "LIVE"
        }),
      };
      $.ajax(settings).done(function(response) {
        console.log(response);
      });
    }
  }

  var mobile = false;
  @if (request()->cookie('OS'))
    if (/Android|Mobile|iPhone|iPad|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Kindle|Silk|Opera Mini/i.test(
        navigator.userAgent)) {
      mobile = false;
      {{-- removido a pedido do Sergio --}}
      {{-- document.getElementById( 'not_display_mobile' ).style.display = 'none'; --}}
    }
  @endif

  function renderChannelsNowProgram(channelsArray) {
    return channelsArray.map((channel, index) => {
      const channelHasProgram = channel.schedules.length && channel.schedules.some(ch => ch.events
        .length > 0)
      const channelHasStreamUrl = channel.stream_url;

      let program, fullDuration, startTime, endTime;

      if (channelHasProgram) {
        program = channel.schedules[0].events[0];
        fullDuration = moment(`${program.dur_time}`, 'HH:mm');
        startTime = moment(program.time, 'HH:mm');
        endTime = startTime.clone().add(fullDuration.format('HH'), 'hours').add(fullDuration.format(
          'mm'), 'minutes');
      }

      return `
                <a
                    ${!mobile ? `href="${channel.stream_url}" target="_blank"` : `href='#'`}
                    class="tv__player-channel ${index == 0 && !mobile ? 'tv__player-channel--active' : 'tv__player-channel'}"
                    ${channel.slug ? `data-channel="${channel.slug}"` : ''}
                    ${channel.name ? `data-channelname="${channel.name}"` : ''}
                    ${channel.live_tv_id ? `data-channelid="${channel.live_tv_id}"` : ''}
                    ${channel.poster ? `data-poster="${channel.poster}"` : ''}
                    ${channel.stream_url ? `data-streamurl="${channel.stream_url}"` : ''}
                    ${channel.stream_from ? `data-streamfrom="${channel.stream_from}"` : ''}
                    ${channel.restricted_url ? `data-restrictedurl="${channel.restricted_url}"` : ''}
                >
                    <div class="tv__player-channel-number ${channel.display ? '' : 'tv__player-channel-number-missing' }">
                        ${channel.display || '---'}
                    </div>
                    <div class="tv__player-channel-thumb">
                        ${channel.thumbnail ? `<img src="${channel.thumbnail}" alt="" />` : ''}
                    </div>
                    <div class="tv__player-channel-details">
                        ${
                            channelHasProgram ? `
                                <div class="tv__player-channel-details-time">
                                    ${program ? `${startTime.format('HH')}:${startTime.format('mm')} - ${endTime.format('HH')}:${endTime.format('mm')}` : '--:--'}
                                </div>
                                <div class="tv__player-channel-details-program">
                                    ${program ? program.programs.title.split('|')[0] : '-----'}
                                    </div>
                                <div class="tv__player-channel-details-description">
                                    ${program ? program.programs.descriptions.split('|')[0] : '-----'}
                                </div>
                            ` : `
                                <div class="tv__player-channel-details-noinfo">Sem informações</div>
                            `
                        }
                    </div>
                </a>
            `;
    }).join('');
  }

  function sendBeatPlaying(idConteudo) {
    return true; // tmp
    var idCanal = idConteudo;
    var token = sessionStorage.getItem('current_token_login');
    if (idConteudo == undefined) {
      idCanal = `{{ $conteudo->id }}`;
    }

    if (player.isPlaying()) {
      var settings = {
        "url": "{{ config('app.osm_api_endpoint') }}/playing?tipo=live&id_canal=" + idCanal,
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
        console.log(response);
      });
    }
  }

  function sendBeatAnalytics(idConteudo, nomeConteudo) {
    var idCanal;
    var nomeCanal;
    if (idConteudo == undefined) {
      idCanal = `{{ $conteudo->id }}`;
      nomeCanal = `{{ $conteudo->titulo }}`;
    } else {
      idCanal = idConteudo;
      nomeCanal = nomeConteudo;
    }

    if (player.isPlaying()) {
      console.log(player.getVideoQuality());
      var settings = {
        "url": "https://d3gw6kqe2un7kj.cloudfront.net/beat",
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
          "type": "live",
          "user": {
            "id": `{{ $user->id }}`,
            "email": `{{ $user->email }}`
          },
          "asset": {
            "id": idCanal,
            "title": nomeCanal,
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

      $.ajax(settings).done(function(response) {
        console.log(response);
      });
    }
  }

  function updatePlayer($element) {
    // Se player não existe não faz o update
    if (typeof player === 'undefined') {
      return;
    }

    if (mobile) {
      return;
    }

    //
    var source_click;
    const channelData = {
      poster: $element.data('poster'),
      streamUrl: $element.data('streamurl'),
      restrictedUrl: $element.data('restrictedurl'),
      id: $element.data('channelid'),
      name: $element.data('channelname')
    };

    if (typeof channelData.restrictedUrl !== 'undefined' && channelData.restrictedUrl != false) {
      window.location = channelData.restrictedUrl
      return true;
    }

    player.load(source_click).then(player => {
      $('.player--loading').removeClass('player--loading-active');
      clearInterval(interval_start);
      interval_assistir = setInterval(function() {
        //enviaContaudoAssistido();
      }, 30000);

      if (interval_click !== undefined) {
        clearInterval(interval_assistir);
        clearInterval(interval_click);
      }

      interval_click = setInterval(function() {
        //enviaContaudoAssistido(channelData.id);
        sendBeatAnalytics(channelData.id, channelData.name);
        sendBeatPlaying(channelData.id);
      }, 30000);

    }).catch((e) => {
      clearInterval(interval_assistir);
      clearInterval(interval_click);
      $('.player--loading').removeClass('player--loading-active');
    })
  }

  function sortChannels(a, b) {
    if (a.display === null || a.display === '') {
      return 1;
    }
    if (b.display === null || b.display === '') {
      return -1;
    }
    if (a.display < b.display) {
      return -1;
    }
    if (a.display > b.display) {
      return 1;
    }
    return 0;
  }

  //Kaltura Player
  $(document).ready(() => {

    var source;
    var config = {
      targetId: "myplayer",
      playback: {
        autoplay: true,
        muted: false,
        allowMutedAutoPlay: false,
        pictureInPicture: false,
      },
      provider: {
        partnerId: 1,
      }
    }

    const mediaConfig = {
      sources: {
        hls: [{mimetype: 'application/x-mpegurl', url: `{{ $conteudo->getUrlWithCdn($appProvedor) != null ? $conteudo->getUrlWithCdn($appProvedor) : '' }}`}],
        dash: [{mimetype: 'application/x-mpegurl', url: `{{ $conteudo->getUrlWithCdn($appProvedor) != null ? $conteudo->getUrlWithCdn($appProvedor) : '' }}`}], 
      }
  };

    try {
      var player = KalturaPlayer.setup(config);
      player.setMedia(mediaConfig);
    } catch (e) {
      console.error(e.message)
    }
  
    $('body').on('click', '.tv__player-channel', function(e) {
      e.preventDefault();
      const isActive = $(this).hasClass('tv__player-channel--active');
    
      if (window.isIos) {
        var element = document.createElement('a');
        element.setAttribute('href', $(this).data('streamurl'));
        element.setAttribute('download', $(this).data('streamurl'));

        element.style.display = 'none';
        document.body.appendChild(element);

        console.log(element);
        element.click();

        document.body.removeChild(element);

        return;
      }

      if (!isActive) {
        $('.tv__player-channel').removeClass('tv__player-channel--active');
        $(this).addClass('tv__player-channel--active');
        if (mobile) {
          window.location = $(this).data('streamurl');
        } else {
          const lastPlayer = KalturaPlayer.getPlayer("myplayer")
      lastPlayer.destroy()

      var playerChannel = document.querySelector('.tv__player-channel--active')

      var config = {
      targetId: "myplayer",
      playback: {
        autoplay: true,
        muted: true,
        allowMutedAutoPlay: true,
        pictureInPicture: false,
      },
      provider: {
        partnerId: 1,
      },
      sources: {
                hls: [
                    {
                      mimetype: 'application/x-mpegurl',
                      url: playerChannel.getAttribute('href')
                    }
                ],
                dash: [
                  {
                    mimetype: 'application/x-mpegurl',
                    url: playerChannel.getAttribute('href')
                  }
                ]
            },
      }

      var player = KalturaPlayer.setup(config);
        }
      }
    });

    $.ajax({
      type: 'GET',
      {{-- url: 'http://localhost/rest-api/v100/schedule', --}}
      {{-- url: 'https://proxy.nxplay.com.br/vcplay-stag.tvnanuvem.com/rest-api/v100/schedule', --}}
      url: '{!! $scheduleUrl !!}',
      data: {
        now: true, // Quando passa now=true entrega todos os canais
      },
      dataType: 'json',
      headers: {
        'API-KEY': '881946f60e8e9a1'
      },
      //xhrFields: {
      //    withCredentials: true
      //},
      success: function(response) {
        document.querySelector('.tv__player').classList.remove('tv__player--hidden');
        $('.message--empty-message').hide();

        // Ordena canais por número do canal, caso "null" fica por último
        //const sortedChannels = response.results.sort(sortChannels);

        // ordenacao é feita em backend...
        const sortedChannels = response.results; //.sort(sortChannels);
        const channelsNow = renderChannelsNowProgram(sortedChannels);
        if (channelsNow.length) {
          document.querySelector('.tv__player-channels').innerHTML = channelsNow;

          // Tenta atualizar o player para o primeiro canal da lista
          const firstChannel = $('.tv__player-channels .tv__player-channel').first();
          updatePlayer(firstChannel);
        } else {
          document.querySelector('.tv__player-container').innerHTML = `
                    <div class="message--empty-message">
                        Não existe nenhum resultado no momento
                    </div>
                `
        }
        $('html').scrollTop(3);
      },
      error: function() {
        $('.message--empty-message').hide();
        document.querySelector('.tv__player-container').innerHTML = `
                    <div class="message--empty-message">
                        Ocorreu um erro nos servidores, tente mais tarde
                    </div>
                `
      }
    });
  });
</script>
<style>
  html,
  body {
    overscroll-behavior-y: contain;
    touch-action: pan-down;
  }
</style>
