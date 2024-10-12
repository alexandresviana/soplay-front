@extends('layouts.index')


@section('main_content')

<script type="text/javascript" src="https://js.iugu.com/v2"></script>

<br /><br /><br />


<div class="container">

    @if ($message = Session::get('no_main_plan'))
        <div class="row">
            <div class="col-lg-4">
            </div>
            <div class="col-lg-4">
                <div class="alert alert-danger alert-block" style="margin-top:0px;">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            </div>
            <div class="col-lg-4">
            </div>
        </div>
    @endif



    <div class="row justify-content-center align-items-centers height-self-center align-top">
        <div class="col-lg-5 col-md-12 align-self-center">
            <div class="sign-user_card ">
                <div class="sign-in-page-data">
                    <div class="sign-in-from w-100 m-auto">
                        <h3 class="mb-3 text-center">Assinatura / Pagamento</h3>

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

                        @if ($message = Session::get('alert'))
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                        @endif

                        <div class="">
                            Informe os dados para pagamento da assinatura. <br /><br />

                            A assinatura é renovada automaticamente mensalmente.<br/>
                            @if($subscription_plan)
                            O valor do plano escolhido é <strong>R$ {{ $subscription_plan->valor }} </strong>
                            @else
                            O valor do plano escolhido é <strong>R$ </strong>
                            @endif
                        </div>

                        <form class="mt-4" id="payment-form" action="{{route('checkout.confirm')}}" method="POST">
                            @csrf

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-8">
                                        <input autocomplete="off" class="form-control mb-0" id="card_number" data-iugu="number" placeholder="Número do Cartão" type="text" value="" />
                                        <small>Informe o número do cartão</small>
                                    </div>
                                    <div class="col-4">
                                        <input autocomplete="off" class="form-control mb-0" id="card_expiration" data-iugu="expiration" placeholder="MM/AA" type="text" value="" />
                                        <small>Validade</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-8">
                                        <input class="form-control mb-0" data-iugu="full_name" placeholder="Titular do Cartão" type="text" value="" />
                                        <small>Titular do cartão</small>
                                    </div>
                                    <div class="col-4">
                                        <input autocomplete="off" class="form-control mb-0" id="card_verification_value" data-iugu="verification_value" placeholder="CVV" type="text"  pattern="[0-9]{3}" />
                                        <small>CVV</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <img src="https://s3-sa-east-1.amazonaws.com/storage.pupui.com.br/9CA0F40E971643D1B7C8DE46BBC18396/assets/cc-icons.e8f4c6b4db3cc0869fa93ad535acbfe7.png" alt="Visa, Master, Diners. Amex" border="0" />
                                <a class="iugu-btn" href="http://iugu.com" tabindex="-1"><img src="https://s3-sa-east-1.amazonaws.com/storage.pupui.com.br/9CA0F40E971643D1B7C8DE46BBC18396/assets/payments-by-iugu.1df7caaf6958f1b5774579fa807b5e7f.png" alt="Pagamentos por Iugu" border="0" /></a>
                            </div>

                            <div class="form-group hide">
                                <input type="text" name="token" id="token" style="text-align:center;display: none;" />
                            </div>

                            <div class="sign-info">
                                <button type="submit" class="btn btn-hover">Confirmar</button>
                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 col-md-12 align-top">
            <div class="sign-user_card align-top">
                <div class="sign-in-page-data">
                    <div class="sign-in-from w-100 m-auto">
                        <h3 class="mb-3 text-center">Extrato</h3>

                        <div class="">
                            <table class="table table-striped">
                                <thead>
                                    <th>Período</th>
                                    <th>Pagamento</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </thead>
                                @foreach($payments_list as $payment)
                                <tr>
                                    <td>{{ $payment['due_date']->format('d/m/Y') }} </td>
                                    <td>{{ $payment['paid_at']->format('d/m/Y, H:i') }} </td>
                                    <td>R${{$payment['total_paid']}}</td>
                                    <td>{{$payment['status']}}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>

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

@section('javascript')
<script>
Iugu.setAccountID("{{$iugu_id_conta}}");
Iugu.setTestMode(true);

jQuery(function($) {
  $('#payment-form').submit(function(evt) {
      var form = $(this);
      var tokenResponseHandler = function(data) {
          if (data.errors) {
              var err = data.errors;
              console.log(err);
              errors = [];
              if(err.number == 'is_invalid') {
                errors.push("- Número do cartão inválido");
              }
              if(err.verification_value == 'is_invalid') {
                errors.push("- CVV inválido");
              }
              if(err.expiration == 'is_invalid') {
                errors.push("- Data de validade inválida");
              }
              if(err.first_name == 'is_invalid' || err.last_name == 'is_invalid') {
                errors.push("- Nome inválido");
              }

              alert("Erro efetuando pagamento:\n\n" + errors.join("\n"));
          } else {
              $("#token").val( data.id );
              console.log(data.id);
              form.get(0).submit();
          }
      }

      Iugu.createPaymentToken(this, tokenResponseHandler);
      return false;
  });
});

var redirectUrl = "{{route('home')}}?{{time()}}";
function checkPlanAvailable()
{
    var token = sessionStorage.getItem('current_token_login');

    var settings = {
        "url": "{{ config('app.osm_api_endpoint') }}/checkplan",
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
    console.log('checkPlanAvailable');
    $.ajax(settings).done(function(response) {
        console.log(response);
        if(!response.available) {
            return false;
        }
        clearInterval(interval);
        alert('Plano liberdo. Conteúdo disponível');
        window.location = redirectUrl;
    });
}

var interval;
$(document).ready(function() {
    interval = setInterval(function() {
      checkPlanAvailable();
    }, 5000);
});


$(document).ready(function() {
    $("#card_number").mask("0000000000000000");
    $("#card_expiration").mask("00/00");
    $("#card_verification_value").mask("000");
});
</script>
@endsection
