<!-- TODO: AJUSTAR POSICIONAMENTO DAS INFORMAÇOES IMDB NO LAYOUT  -->

<div id="myModal" class="modal fade" tabindex="-1">
   <div class="modal-dialog modal-xl">
      <div class="modal-content">
         <div class="modal-header">

               <button type="button" class="close">
               <span class="glyphicon glyphicon-remove" aria-hidden="true" data-dismiss="modal" aria-label="Close"></span>
               </button>
               <!-- imagem do banner -->
               <div class="im-box">

               </div>

               <img id="img-modal"src=""/>
         </div>
         <div class="modal-body">
            <div class="botoes">
            @if(!Route::is('*series'))
               <a id="link-play"href="">
            @else
               <a id="link-play-serie"href="" style="display:none;">
            @endif

               <button class="btnplay"><span class="glyphicon glyphicon-play"></span> Assistir
               </button>
               </a>
               <button class="btnAddFav" aria-label="Favoritar" style="display:none;">
               <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
               </button>
            </div>
            <div id="imdbInfo-modal">
               <p class="imdbNota" id="imdbNota"></p>
               <p class="ano" id="ano"></p>
               <p class="duracao" id="duracao" style="display:none"></p>
            </div>

            <div id="text-desc" class="desc">
            </div>
         </div>
         <div class="modal-footer" id="mod-footer">
            <p class="eps" style="display:none;">Episódios</p>
            <select name="" id="temporadas" class="temporadas" style="display:none; margin-bottom:20px;">
               <option class="optionGroup" value="1"></option>
            </select>
            <div class="episodios" style="display:none"></div>
         </div>
      </div>
   </div>
</div>
