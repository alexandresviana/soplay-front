                        <div class="navbar-right menu-right">


                           <ul class="d-flex align-items-center list-inline m-0">
                              <li class="nav-item nav-icon" style="display: none;">
                              <input type="text" size="1" id="input-pesquisar" placeholder="Efetuar busca" style="display: none;">
                                 <a href="#" class="search-toggle device-search">
                                 <i class="fa fa-search icon-pesquisa"></i>
                                 </a>
                              </li>
                                 @if(Auth::check() && false)
                              <li class="nav-item nav-icon">
                                 <a href="#" class="search-toggle" data-toggle="search-toggle">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" class="noti-svg">
                                       <path fill="none" d="M0 0h24v24H0z"></path>
                                       <path d="M18 10a6 6 0 1 0-12 0v8h12v-8zm2 8.667l.4.533a.5.5 0 0 1-.4.8H4a.5.5 0 0 1-.4-.8l.4-.533V10a8 8 0 1 1 16 0v8.667zM9.5 21h5a2.5 2.5 0 1 1-5 0z"></path>
                                    </svg>
                                    <span class="bg-danger dots"></span>
                                 </a>
                                 <div class="iq-sub-dropdown">
                                    <div class="iq-card shadow-none m-0">
                                       <div class="iq-card-body">
                                          <a href="#" class="iq-sub-card">
                                             <div class="media align-items-center">
                                                <img src="{{asset('geral/thumb-1.jpg')}}" class="img-fluid mr-3" alt="NXTV">
                                                <div class="media-body">
                                                   <h6 class="mb-0 ">Boot Bitty</h6>
                                                   <small class="font-size-12"> just now</small>
                                                </div>
                                             </div>
                                          </a>
                                          <a href="#" class="iq-sub-card">
                                             <div class="media align-items-center">
                                                <img src="{{asset('geral/thumb-2.jpg')}}" class="img-fluid mr-3" alt="NXTV">
                                                <div class="media-body">
                                                   <h6 class="mb-0 ">The Last Breath</h6>
                                                   <small class="font-size-12">15 minutes ago</small>
                                                </div>
                                             </div>
                                          </a>
                                          <a href="#" class="iq-sub-card">
                                             <div class="media align-items-center">
                                                <img src="{{asset('geral/thumb-3.jpg')}}" class="img-fluid mr-3" alt="NXTV">
                                                <div class="media-body">
                                                   <h6 class="mb-0 ">The Hero Camp</h6>
                                                   <small class="font-size-12">1 hour ago</small>
                                                </div>
                                             </div>
                                          </a>
                                       </div>
                                    </div>
                                 </div>
                              </li>
                                 @endif
                              @if(Auth::check())
                              <li class="nav-item nav-icon">
                                  <a href="#" class="iq-user-dropdown search-toggle p-0 d-flex align-items-center" data-toggle="search-toggle">
                                    <x-avatar></x-avatar>
                                  </a>

                                 <div class="iq-sub-dropdown iq-user-dropdown">
                                    <div class="iq-card shadow-none m-0">
                                       <div class="iq-card-body p-0 pl-3 pr-3">
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
                                          {{-- Removido temporariamente recurso ainda não disponível
                                            <a href="{{route('checkout.index')}}" class="iq-sub-card setting-dropdown">
                                             <div class="media align-items-center">
                                                <div class="right-icon">
                                                   <i class="ri-currency-fill text-primary"></i>
                                                </div>
                                                <div class="media-body ml-3">
                                                   <h6 class="mb-0 ">Assinatura</h6>
                                                </div>
                                             </div>
                                          </a> --}}
                                          <!-- <a href="{{route('user_settings')}}" class="iq-sub-card setting-dropdown">
                                             <div class="media align-items-center">
                                                <div class="right-icon">
                                                   <i class="ri-settings-4-line text-primary"></i>
                                                </div>
                                                <div class="media-body ml-3">
                                                   <h6 class="mb-0 ">Configurações</h6>
                                                </div>
                                             </div>
                                          </a> -->
                                          @if(false)
                                          <a href="pricing-plan.html" class="iq-sub-card setting-dropdown">
                                             <div class="media align-items-center">
                                                <div class="right-icon">
                                                   <i class="ri-settings-4-line text-primary"></i>
                                                </div>
                                                <div class="media-body ml-3">
                                                   <h6 class="mb-0 ">Pricing Plan</h6>
                                                </div>
                                             </div>
                                          </a>
                                          @endif
                                          <a href="{{route('assinante.logout')}}" class="iq-sub-card setting-dropdown">
                                             <div class="media align-items-center">
                                                <div class="right-icon">
                                                   <i class="ri-logout-circle-line text-primary"></i>
                                                </div>
                                                <div class="media-body ml-3">
                                                   <h6 class="mb-0 ">Sair</h6>
                                                </div>
                                             </div>
                                          </a>
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              @endif

                              <li class="nav-item nav-icon">
                                    @if(Auth::check())
                                    <a href="{{route('assinante.logout')}}" class="xbtn xbtn-light xbtn-sm">Sair</a>
                                    @else
                                    <a href="{{route('assinante.login')}}" class="btn btn-light btn-sm">Entrar</a>
                                    @endif
                              </li>
                           </ul>
                        </div>
