                        <div class="mobile-more-menu">
                           <a href="javascript:void(0);" class="more-toggle" id="dropdownMenuButton" data-toggle="more-toggle" aria-haspopup="true" aria-expanded="false">
                           <i class="fa fa-bars"></i>
                           </a>
                           <div class="more-menu" aria-labelledby="dropdownMenuButton">
                              <div class="navbar-right position-relative">
                                 <ul class="d-flex align-items-center justify-content-end list-inline m-0">
                                    <li>
                                       <a href="#" class="search-toggle">
                                       <i class="ri-search-line"></i>
                                       </a>
                                       <div class="search-box iq-search-bar">
                                          <form action="#" class="searchbox">
                                             <div class="form-group position-relative">
                                                <input type="text" class="text search-input font-size-12" placeholder="Pesquisar">
                                                <i class="search-link ri-search-line"></i>
                                             </div>
                                          </form>
                                       </div>
                                    </li>
                                    @if(false)
                                    <li class="nav-item nav-icon">
                                       <a href="#" class="search-toggle position-relative">
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
                                                         <h6 class="mb-0 ">Boop Bitty</h6>
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
                                    <li>
                                       <a href="#" class="iq-user-dropdown search-toggle d-flex align-items-center">
                                       <img src="/geral/user.jpg" class="img-fluid avatar-40 rounded-circle" alt="user">
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
                                                         <h6 class="mb-0">Logout</h6>
                                                      </div>
                                                   </div>
                                                </a>
                                             </div>
                                          </div>
                                       </div>
                                    </li>
                                 </ul>
                              </div>
                           </div>
                        </div>
