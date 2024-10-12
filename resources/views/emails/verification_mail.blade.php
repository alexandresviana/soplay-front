<p>
    <strong>{{ $app->app_nome }}</strong>
</p>

<p>Caro {{ $subscriber->nome }}.</p>

<p>
    Recebemos um pedido de confirmação de email. Para confirmar utilize o link abaixo:<br />
</p>

<p>
    <a href="{{ $link }}">Confirmar meu email</a>
<br /><br />
</p>

<p>
    Caso desconheça a origem dessa mensagem, pode ignorar esse e-mail.
</p>
