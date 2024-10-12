<?php
use App\Http\Controllers\Controller;
$c = new Controller();
$appProvedor = $c->__app();
?>

@extends('layouts.video')

@section('video_js')
    <script>
        $(document).ready(function() {
            $('#main-header').hide();
            //$('#main-header').collapse('hide');
            setupPlayer();
        });
        var media = '{{ $conteudo->id }}';
    </script>
@endsection


@section('main_content')
    <section class="movie-detail container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="trending-info season-info g-border">
                    <h4 class="trending-text big-title text-uppercase mt-0">{{ $conteudo->titulo }}</h4>
                    <!-- episodio -->
                    <p class="trending-dec w-100 mb-0">{{ $conteudo->descricao }}</p>
                    <ul class="list-inline p-0 mt-4 share-icons music-play-lists">
                        <li><span><i class="fa fa-plus"></i></span></li>
                        <li><span><i class="fa fa-heart"></i></span></li>
                        <li class="share">
                            <span><i class="fa fa-share"></i></span>
                            <div class="share-box">
                                <div class="d-flex align-items-center">
                                    <a href="#" class="share-ico"><i class="fa fa-facebook"></i></a>
                                    <a href="#" class="share-ico"><i class="fa fa-twitter"></i></a>
                                    <a href="#" class="share-ico"><i class="fa fa-link"></i></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @include('home.partials.tvaovivo')
@endsection


<script type="text/javascript" src="{{ asset('/js/kaltura-ovp-player.js') }}"></script>

<script>
    var source = {
        "title": '{{ $conteudo->titulo }}',
        "description": '{{ $conteudo->id }}',
        "hls": '{{ $conteudo->getUrlWithCdn($appProvedor) }}?sjwt={{ $token }}',
    }

    function setupPlayer() {
        var config = {
            "key": "6f2b9962-a240-493f-8cc6-6993645645f9"
        }

        var container = document.getElementById('player2');
        var player = new bitmovin.player.Player(container, config);

        player.load(source).then(
            function() {
                console.log('Successfully created Bitmovin Player instance');
            },
            function(reason) {
                console.log('Error while creating Bitmovin Player instance');
            }
        );
    }
</script>

@section('video_container')
    <div id="player2" class="player"></div>
@endsection
