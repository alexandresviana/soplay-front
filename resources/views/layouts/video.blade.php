<?php
use \App\Http\Controllers\HomeController;
use \App\Http\Controllers\Controller;
$c = new Controller;
$app = $c->__app();
$title = $app->app_nome;
$logo = $app->settingsLogo();
$favicon = $app->settingsFavicon();
$sub = $c->__subscriber();
?>

   @include('layouts.common.header_html')

   <body>
      <!-- loader Start -->
      <div id="loading" style="display: none;">
         <div id="loading-center">
         </div>
      </div>
      <!-- loader END -->
      <!-- Header -->
      <!-- <div class="internal-page__main-header">
           <a href="../home/filmes" class="movie-details__action-return">
               <svg width="27" height="19" fill="none" xmlns="http://www.w3.org/2000/svg">
                   <path
                       d="M25.532 8.635H2.69l6.834-7.16a.895.895 0 000-1.222.792.792 0 00-1.16 0L.236 8.801a.905.905 0 000 1.22l8.126 8.55c.321.337.84.337 1.16 0a.895.895 0 000-1.222L2.69 10.362h22.842c.453 0 .82-.387.82-.864 0-.476-.367-.863-.82-.863z"
                       fill="#E5E5E5" />
               </svg>
           </a>
       </div> -->

      <header id="main-header" class="">
         <div class="main-header">
            <nav class="navbar navbar-expand-lg navbar-light p-0">
            @include('layouts.common.menu_top')
            @include('layouts.common.menu_mobile')
            @include('layouts.common.menu_right')
            </nav>
            <div class="nav-overlay"></div>
         </div>
      </header>
      <!-- Header End -->
   <!-- Banner Start -->


   <div>
      @yield('before_video_container')
   </div>

   <div class="video-container iq-main-slider">
      @yield('video_container')
   </div>

   <div class="main-content" style="margin-top: 100px;">
      @yield('main_content')
   </div>

   @yield('video_js')

   @include('layouts.common.footer')
   @include('layouts.common.footer_js')

</body></html>
