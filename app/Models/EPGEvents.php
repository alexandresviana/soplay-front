<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EPGEvents
{
	/*
	* $prgSrcId int referencia do canal no gracenote
	* $dates array de dates no formato YYYY-mm-dd
	*/
	public static function getEventsGracenote($prgSrcId, $dates)
	{
		$canal = (int) $prgSrcId;
		$datas = implode(',', $dates);
		$events = DB::select(sprintf('
			select
			    gracenote_sources.prg_svc_id        as gn_channel_id,
			    gracenote_sources.name              as gn_channel_name,
			    gracenote_schedule.id               as gn_schedule_id,
			    gracenote_schedule.date             as gn_schedule_date,
			    gracenote_event.id                  as gn_event_id,
			    gracenote_event.tms_id              as gn_event_tms_id,
			    gracenote_event.dur_time            as gn_event_dur_time,
			    gracenote_event.time                as gn_event_time,
			    gracenote_event.time_end            as gn_event_time_end,
			    gracenote_event.dur_total_minutes   as gn_event_total_minutes,
			    gracenote_programs.tms_id           as gn_programs_tms_id,
			    gracenote_programs.title            as gn_programs_title,
			    gracenote_programs.descriptions     as gn_programs_descriptions
			from
			    gracenote_sources,
			    gracenote_schedule,
			    gracenote_event,
			    gracenote_programs
			where
			        gracenote_sources.prg_svc_id = %s -- canal
			    and gracenote_schedule.date in(%s) -- datas, string separada por virgulas
			    and gracenote_schedule.prg_svc_id = gracenote_sources.prg_svc_id
			    and gracenote_event.schedule_id = gracenote_schedule.id
			    and gracenote_programs.tms_id = gracenote_event.tms_id

			', $canal, $datas));


    	return $events;
	}


	public static function getEventsEPG($conteudo, $dates)
	{
		$canal = (int) $conteudo->id;
		$datas = implode(',', $dates);
		$events = sprintf('

select
	tbConteudoCanais.id 			as gn_channel_id,
	tbConteudoCanais.titulo 		as gn_channel_nome,
	"1" 							as gn_schedule_id,
	STR_TO_DATE(
		SUBSTR(tbEpgProgramacao.inicio, 1, 10),
		"%%Y/%%m/%%d"
	) 								as gn_schedule_date,
	tbEpgProgramacao.id				as gn_event_id,
	"event_tms_id" 					as gn_event_tms_id,
	TIME_FORMAT(
		TIMEDIFF(
			TIME_FORMAT(SUBSTR(tbEpgProgramacao.fim, 12, 5), "%%H:%%i"),
			TIME_FORMAT(SUBSTR(tbEpgProgramacao.inicio, 12, 5), "%%H:%%i")
		),
		"%%H:%%i"
	) 										as gn_event_dur_time2,
	TIME_FORMAT(
		TIMEDIFF(
			DATE_FORMAT(tbEpgProgramacao.fim, "%%Y/%%m/%%d %%H:%%i"),
			DATE_FORMAT(tbEpgProgramacao.inicio, "%%Y/%%m/%%d %%H:%%i")
		),
		"%%H:%%i"
	) 										as gn_event_dur_time,
	SUBSTR(tbEpgProgramacao.inicio, 12, 5) 	as gn_event_time,
	SUBSTR(tbEpgProgramacao.fim, 12, 5) 	as gn_event_time_end,
	"gn_event_total_minutes"		as gn_event_total_minutes,
	tbEpgProgramacao.id				as gn_programs_tms_id,
	tbEpgProgramacao.titulo			as gn_programs_title,
	tbEpgProgramacao.descricao 		as gn_programs_descriptions
from
	tbConteudoCanais,
	tbEpgProgramacao
where
		tbConteudoCanais.id = %d -- canal
	and tbConteudoCanais.epg_programacao_idxml = tbEpgProgramacao.idXml
	and STR_TO_DATE(
			SUBSTR(tbEpgProgramacao.inicio, 1, 10),
			"%%Y/%%m/%%d"
		) in("%s", "2021/01/01") -- datas, string separada por virgulas

			', $canal, $datas);
		$events = DB::select($events);


    	return $events;
	}



}
