@extends('layouts.index')


@section('main_content')


<br /><br /><br />


    <div class="container">


<div class="row justify-content-center align-items-center height-self-center">
         <div class="col-lg-12 col-md-12 align-self-center">
            <div class="sign-user_card ">
               <div class="sign-in-page-data">
                  <div class="sign-in-from w-100 m-auto">
                     <h3 class="mb-3 text-center">Minhas locações</h3>

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif

                        <table class="table table-striped">
                            @foreach($rentals as $rent)
                            <?php $conteudo = $rent->conteudo() ?>
                            <tr>
                                <td>{{ (new \DateTime($rent->created_at))->format('d/m/Y, H:i') }} </td>
                                <td>
                                    {{ $conteudo->titulo }}<br/>
                                </td>
                                <td>R${{$rent->valor}}</td>
                                <td> disponível
                                  de <strong>{{ (new \DateTime($rent->data_inicio))->format('d/m/Y, H:i') }} </strong><br />
                                  até <strong>{{ (new \DateTime($rent->data_fim))->format('d/m/Y, H:i') }} </strong>
                                </td>
                                <td>
                                  @if($rent->tipo_conteudo == 'conteudo' && $rent->available())
                                  <a href="{{route('video', ['id' => $rent->id_conteudo])}}" class="btn btn-hover">Assistir</a>
                                  @elseif($rent->tipo_conteudo == 'conteudo_series' && $rent->available())
                                  <a href="{{route('video.series', ['id' => $rent->id_conteudo])}}" class="btn btn-hover">Assistir</a>
                                  @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>

                  </div>
               </div>
            </div>
         </div>
      </div>

    </div>


<br />
<br />
<br />

@endsection
