<p>
    <strong>{{ $app->app_nome }}</strong>
</p>

<p>Caro {{ $subscriber->nome }}.</p>

<p>
    Recebemos um pedido de mudança de senha. Para confirmar utilize o link abaixo:<br />
</p>

<p>
    <a href="{{ $link }}">Alterar minha senha</a>
<br /><br />
</p>

<p>
    Caso não deseje mudar a sua senha, pode ignorar este e-mail.
</p>
