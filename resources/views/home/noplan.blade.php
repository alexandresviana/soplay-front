@extends('layouts.index')


@section('main_content')

<br /><br /><br />


<div class="container">

    @if ($message = Session::get('no_main_plan'))
        <div class="row">
            <div class="col-lg-4">
            </div>
            <div class="col-lg-4">
                <div class="alert alert-danger alert-block" style="margin-top:0px;">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            </div>
            <div class="col-lg-4">
            </div>
        </div>
    @endif



@endsection
