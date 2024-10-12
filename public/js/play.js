$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#iq-favorites').show();

});


jQuery(document).ready(function() {
    $('#home').show();
});


function changeApp()
{
    var app = $('#idapp').val();
    window.location = '/user/login/?app=' + app;
}

function changeProvedor(id)
{
    window.location = '/user/login/?app=' + id;
}

function favorite(type, id)
{

    var c  = $('.favorite_' + id);
    var cf = $('#favorite_f_' + id);
    var ci = $('#favorite_i_' + id);
    var fa = $('#favorites_ul > .slick-list > .slick-track');
    $.get('/home/favorite?id='+ id + '&type='+ type, function(data){
        var status = data.status;
        if(!status) {
            alert(data.msg);
            return;
        }
        var act = data.act;
        if(act == 'like') {
            c.addClass('glyphicon-ok');
            c.removeClass('glyphicon-plus');
            //cf.addClass('text-success');
            //cf.removeClass('text-default');
            //fa.append(ci[0].outerHTML);
            //console.log(ci[0].outerHTML);
        } else if(act == 'unlike'){
            //var favListItem = $('#favorites_ul > .slick-list > .slick-track > #favorite_i_' + id);
            //favListItem.remove();
            //favListItem.hide();
            c.removeClass('glyphicon-ok');
            c.addClass('glyphicon-plus');
            //cf.removeClass('text-success');
            //cf.addClass('text-default');
        }
        //$('.favorites-slider').slick('resize');

        return true;
    }).fail(function(){
        alert('Erro ao adicionar, tente novamente');
    }).always(function(){
        //$('html').css('cursor', 'default');
    });

}

var pingInt;
function pingStatus()
{
    var token = sessionStorage.getItem('current_token_login');
    if(!token) {
        return;
    }
    var settings = {
        "url": "/api/v1/ping/status",
        "method": "GET",
        "timeout": 0,
        "dataType": 'json',
        "contentType": "application/json; charset=utf-8",
    };
console.log('pingStatus');
console.log(token);
    $.ajax(settings).done(function(response) {
        if(!response.active) {
            clearInterval(pingInt);
            window.location = '/';
            alert('Assinante desconectado, efetue nova autenticação');
            return false;
        }
    });
}
// ping desabilitado na plataforma
// $(document).ready(function() {
//     pingInt = setInterval(function() {
//       pingStatus();
//     },30000);
// });
