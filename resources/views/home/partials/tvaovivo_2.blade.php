          <section id="iq-trending" class="s-margin">
            <div class="container-fluid">
               <div class="row">
                  <div class="col-sm-12 overflow-hidden">
                     <div class="iq-main-header d-flex align-items-center justify-content-between">                      
                        <h4 class="main-title"><a href="show-category.html">TV ao vivo 2</a></h4>                        
                     </div>
                     <div class="trending-contens">
                        <ul id="trending-slider-nav" class="list-inline p-0 mb-0 row align-items-center slick-initialized slick-slider"><a href="#" class="slick-arrow slick-prev" style=""><i class="fa fa-chevron-left"></i></a>
                           <div class="slick-list draggable" style="padding: 0px;"><div class="slick-track" style="opacity: 1; width: 1894px; transform: translate3d( 0px, 0px);">


@foreach ($conteudos as $i => $conteudo)

                           <li class="slick-slide" data-slick-index="{{$i}}" aria-hidden="false" tabindex="0" style="width: 200px;">
                              <a href="/video/{{$conteudo->id}}" tabindex="0">
                                 <div class="movie-slick position-relative">
                                    <!-- <img src="geral/canais/canal_paramount.png" class="img-fluid" alt=""> -->
                                    <img src="{{$conteudo->getImagemFullUrl()}}" class="img-fluid" alt="">
                                 </div>
                              </a>
                           </li>

@endforeach

                        </div></div>
                        <a href="#" class="slick-arrow slick-next" style=""><i class="fa fa-chevron-right"></i></a></ul>
                     </div>
                  </div>
               </div>
            </div>
         </section>
