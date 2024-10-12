<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Controller;
$c = new Controller();
$c->setCurrentTokenLogin();
$appProvedor = $c->__app();
$title = $appProvedor->app_nome;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Required meta tags -->

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ $title ?? '' }} - TV ao vivo, filmes, séries e muito mais...</title>

  <link rel="icon" type="image/png" href="{{ $favicon ?? '' }}">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="{{ asset('/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css">
  <!-- Typography CSS -->
  <link rel="stylesheet" href="{{ asset('/css/typography.css') }}">
  <!-- Style -->
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
  <!-- Responsive -->
  <link rel="stylesheet" href="{{ asset('/css/responsive.css') }}">

  <!-- FontAwesome -->
  <script src="https://use.fontawesome.com/723a5525c9.js"></script>
  <script src="{{ asset('/js/jquery-3.4.1.min.js') }}"></script>

<!-- Google Tag Manager -->
<script>
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MPS67HZ4');
</script>



</head>

<script>
  @if (request()->session()->get('current_token_login'))
    sessionStorage.setItem('current_token_login', '{{ request()->session()->get('current_token_login') }}');
  @else
    sessionStorage.removeItem('current_token_login');
  @endif
</script>