{{-- <br>
<section class="module" id="home">
    <div class="container" style="padding: 0">
        <div class="row" style="margin: 0">
            <div class="col-sm-8 col-sm-offset-2 xs-12">
            <h2 class="module-title-termos font-alt termos-titulo">Suporte Técnico</h2>
            <hr align="left" class="verdinho">
            <br>
            <div class="termos">
                <p align="justify">Contatos Para Suporte</span></p>

                <p align="justify">Telefone : 0800 4848 000</span></p>

                <p align="justify"><a href="https://api.whatsapp.com/send?phone=551535004800&text=" target="_blank">WhatsApp (15) 3500-4800</a></span></p>


                </div>
            </div>
        </div>
    </div>
</section> --}}

@extends('layouts.index')


@section('main_content')


    <br /><br /><br />


    <div class="container">


        <div class="row justify-content-center align-items-center height-self-center">
            <div class="col-lg-5 col-md-12 align-self-center">
                <div class="sign-user_card">
                    <div class="sign-in-page-data">
                        <div class="sign-in-from w-100 m-auto">
                            <h3 class="mb-3 text-center">Contato</h3>

                            <section class="module" id="home">
                                <div class="container" style="padding: 0">
                                    <div class="row" style="margin: 0">
                                        <div class="col-sm-12 col-sm-offset-2 xs-12">
                                            {{-- <h2 class="module-title-termos font-alt termos-titulo">Suporte Técnico</h2>
                                            <hr align="left" class="verdinho col-lg-8 col-md-12 align-self-center"> --}}
                                            <br>
                                            <div class="termos">
                                                <p>Contatos:</p>

                                                <div>
                                                <p>E-mail: </p><a href="mailto:{{ $appEmail == NULL ? 'suporte@naxostelecom.com.br' : $appEmail }}" target="_blank"> {{ $appEmail == NULL ? 'suporte@naxostelecom.com.br' : $appEmail }}</a>
                                            </div>

                                            <div>
                                                <p>Telefone: <span>{{ $appTelefone == NULL ? '(11) 5197-8777' : $appTelefone }}</span></p>
                                            </div>
                                            <div>
                                                <p>WhatsApp: </p><a
                                                        href="https://api.whatsapp.com/send?phone=55{{preg_replace('/[^[:digit:]]/', '',  $appWhatsapp == NULL ? '(11) 5197-8777' : $appWhatsapp )}}&text="
                                                        target="_blank"> {{ $appWhatsapp == NULL ? '(11) 5197-8777' : $appWhatsapp }}</a>
                                            </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <br />
    <br />
    <br />

@endsection

@section('styles')
<style>
    p {
        display: inline-block;
        font-weight: bold;
    }

    span {
        font-weight: normal;
    }

</style>
@endsection
