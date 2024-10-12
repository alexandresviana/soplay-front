@extends('layouts.index')


@section('main_content')

<br /><br /><br /><br /><br />
<center>
	<h2>Cadastre-se e tenha 7 dias grátis</h2>
	<br /><br />

    {!! QrCode::size(300)->margin(1)->generate($qrcode_text) !!}

</center>
<br /><br /><br />

@endsection
