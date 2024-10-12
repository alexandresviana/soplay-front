<a href="#" class="navbar-toggler c-toggler collapsed" data-toggle="collapse" data-target="#navbarSupportedContent"
  aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
  <div class="navbar-toggler-icon" data-toggle="collapse"></div>
</a>

<a class="navbar-brand" href="{{route('home')}}">
  <!-- <img class="img-fluid _logo" src="{{asset('/geral/logo_nxtv.png')}}" style="max-height: 47px;" alt="NXTV"> -->
  <img src="{{$logo ?? ''}}" class="img-fluid"
    style="{{ Route::currentRouteName() === 'assinante.login' ? 'max-height: 100px;' : 'max-height: 40px;'}}"
    alt="NXTV" />
</a>
<div class="collapse navbar-collapse" id="navbarSupportedContent"
  style="{{ Route::currentRouteName() === 'assinante.login' ? 'margin-top: -40px;' : '' }}">
  <div class="menu-main-menu-container">

    <ul id="top-menu" class="navbar-nav ml-auto">

      <li class="menu-item" id="foto-perfil" style="display: none;flex-direction:column;">
        <a href="{{route('user_profile')}}" class="iq-sub-card align-items-center" data-toggle="search-toggle">
          <x-avatar></x-avatar>
          <p style="margin-top: 10px;">Perfil</p>
        </a>
      </li>

      @if (!$app->settingsAovivoDisabled())
        <li class="menu-item {{ Route::currentRouteName() === 'home_ao_vivo' ? 'menu-item--active' : '' }}">
          <a href="{{route('home_ao_vivo')}}">
              Ao vivo
          </a>
      </li>
      @endif

      @if (!$app->settingsAovivoDisabled())
    {{--Removido temporariamente a pedidos de Atrismar--}}
      {{-- <li class="menu-item {{ Route::currentRouteName() === 'home_ao_vivo_radios' ? 'menu-item--active' : '' }}">
          <a href="{{route('home_ao_vivo_radios')}}">Rádios</a>
      </li> --}}
      @endif

      @if($sub->getConteudosVODPlanos())
      <li class="menu-item {{ Route::currentRouteName() === 'home_filmes' ? 'menu-item--active' : '' }}">
        <a href="{{route('home_filmes')}}">{{$app->id !== 4050 ? 'Filmes' : 'Cursos'}}</a>
      </li>
      @endif

      @if($sub->getConteudosDesenhosVODPlanos())
      <li class="menu-item  {{ Route::currentRouteName() === 'home_desenhos' ? 'menu-item--active' : '' }}" style="display:none;">
        <a href="{{route('home_desenhos')}}">Desenhos</a>
      </li>
      @endif

      @if($sub->getConteudosSeriesVODPlanos())
      <li class="menu-item  {{ Route::currentRouteName() === 'home_series' ? 'menu-item--active' : '' }}">
        <a href="{{route('home_series')}}">Séries</a>
      </li>
      @endif

      @if(false)
      <li class="menu-item">
        <a href="{{route('home_marketplace')}}">Marketplace</a>
      </li>
      @endif

      @if ($app->settingsLojaEnabled())
        @if($sub->getConteudosVODPlanos() || $sub->getConteudosDesenhosVODPlanos() || $sub->getConteudosSeriesVODPlanos())
        <li class="nav-item">
          <a class="btn-default btn-lg" style="background-color: #ccc;color: #333;" href="{{route('home_loja')}}">Loja </a>
        </li>
        @endif
      @endif

      @if(false)
      <li class="menu-item">
        <a href="{{route('user_profile')}}" class="iq-sub-card setting-dropdown">
            <div class="media align-items-center">
              <div class="right-icon">
                  <i class="ri-file-user-line text-primary"></i>
              </div>
              <div class="media-body ml-3">
                  <h6 class="mb-0 ">Perfil</h6>
              </div>
            </div>
        </a>
      </li>
      @endif

      @if(Auth::check())
      <li class="menu-item sair-mobile">
        <a href="{{route('assinante.logout')}}">Sair</a>
      </li>
      @else
      <li class="menu-item sair-mobile">
        <a href="{{route('login')}}">Entrar</a>
      </li>
      @endif

    </ul>
  </div>
</div>
