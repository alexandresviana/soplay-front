<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\{ConteudoCanais,EPGEvents,App};
use App\Http\Controllers\Controller;


class AoVivoController extends Controller
{
    public function test()
    {
        $canal = 136;
        $conteudo = ConteudoCanais::find($canal);
        $data = '2021/06/29 10:00:00';

        $events = DB::select(sprintf('
            select
                tbEpgProgramacao.titulo         as titulo,
                tbEpgProgramacao.descricao      as descricao,
                tbEpgProgramacao.capa           as capa,
                tbEpgProgramacao.inicio         as inicio,
                tbEpgProgramacao.fim            as fim
            from
                tbConteudoCanais,
                tbEpgProgramacao
            where
                    tbConteudoCanais.id = %d -- canal
                and tbConteudoCanais.epg_programacao_idxml = tbEpgProgramacao.idXml
                and tbEpgProgramacao.inicio >= "%s"
            order by
                tbEpgProgramacao.inicio ASC
            limit 1
            ', $canal, $data));


        return  response()->json($events, 200);
    }

    public function schedule(Request $request)
    {
        $sub = $this->__subscriber();

        $id = $request->get('id');
        $tipo = $request->get('tipo');

        if(!in_array($tipo, array('tv', 'radio'))) {
            $tipo = 'tv';
        }

        $conteudoSelected = ConteudoCanais::find($id);

        $ret = [];
        $conteudosPlanos = $sub->getConteudosPlanos($tipo);

        $app = App::get($sub->app);
        $currentOrder = $app->getSettings('canais_ordem');
        $conteudosPlanos = ConteudoCanais::sortConteudoCanaisListUsingArray($conteudosPlanos, $currentOrder);


        if($conteudoSelected) {
            $conteudoSelected->require_parental_password = false;
            array_unshift($conteudosPlanos, $conteudoSelected);
        }


        foreach($conteudosPlanos as $k => $conteudo) {
            if($k && $conteudo->id == $id) {
                continue;
            }
            $schedules = [];
            $dates   = array(date('Y-m-d'));
            //$evList  = EPGEvents::getEventsGracenote($conteudo->prg_svc_id, $dates);
            $evList = EPGEvents::getEventsEPG($conteudo, $dates);
            $events  = [];
            foreach($evList as $ev) {
                $pt = $ev->gn_programs_title;//current(explode('|', $ev->gn_programs_title));
                $ds = $ev->gn_programs_descriptions;//current(explode('|', $ev->gn_programs_descriptions));

                // ignora eventos passados, considerando que todos os eventos sao do dia atual
                $dEv = new \Datetime(sprintf('%s %s:00', date('Y-m-d'), $ev->gn_event_time));
                $dNow = new \Datetime(date('Y-m-d H:i:00'));
                // transforma o tempo do evento em intervalo de tempo
                list($hours, $mins) = explode(':', $ev->gn_event_dur_time);
                try {
                    $dEvtot = $dEv->add(new \Dateinterval(sprintf('PT%sH%sM', $hours, $mins)));
                }catch(\Exception $e) {
                    continue;
                    //dd($hours);
                }

                // verifica se evento ja passou
                if($dNow > $dEvtot) {
                    continue;
                }

                $events[] = array(
                    'programs' => array(
                        'title'         => $pt,
                        'descriptions'  => $ds,
                        'live'          => false, // FIX, confirmar
                        'tms_id'        => $ev->gn_programs_tms_id,
                    ),
                    'id'        => $ev->gn_event_id,
                    'dur_time'  => $ev->gn_event_dur_time,
                    'time'      => $ev->gn_event_time,
                    'time_end'  => $ev->gn_event_time_end,

                );
            }

            $restrictedUrl  = false;
            //$streamUrl      = $conteudo->getUrlWithCdn($app);
            $streamUrl      = $conteudo->getUrlWithToken($request, $app);

            if($conteudo->require_parental_password) {
                $restrictedUrl = route('video_aovivo', ['id' => $conteudo->id]);
                $streamUrl = false;
            }

            // caso seja canal de provedor utiliza nome e descrição no epg
            if(!sizeof($events) && $conteudo->app) {
                $events[] = [
                    'programs' => [
                        'title'         => $conteudo->titulo,
                        'descriptions'  => $conteudo->descricao . ' *',
                        'live'          => false,
                        'tms_id'        => 0,
                    ],
                    'id'        => 0,
                    'dur_time'  => '838:59',
                    'time'      => '00:00',
                    'time_end'  => '23:59',
                ];
            }

            $ret[] = array(
                'name'          => $conteudo->titulo,
                'prg_svc_id'    => $conteudo->prg_svc_id,
                'display'       => $conteudo->numero,
                'category_id'   => ConteudoCanais::CONTEUDO_CATEGORIA_AOVIVO,
                'poster'        => $conteudo->getImagemBannerFullUrl(),
                'thumbnail'     => $conteudo->getImagemFullUrl(),
                'slug'          => $conteudo->url,
                'stream_url'    => $streamUrl, // url_hls ?
                'stream_from'   => 'hls',
                'restricted_url'=> $restrictedUrl,
                'live_tv_id'    => $conteudo->id,
                /*
                'schedules' => array(
                    array(
                        'schedules_1' => 1,
                        'events' => array(),
                    ),
                ),
                */
                'schedules' => [array(
                    'schedules_id'  => 1, // FIX usando somente o primeiro, confirmar
                    'events'        => $events,
                )],
            );
        }

        $schedule = array(
            'user'          => $this->__subscriber()->email,
            'results'       => $ret,
            'total'         => sizeof($ret),
            'total_pages'   => 1,
        );

        return response()->json($schedule, 200);
    }
}
