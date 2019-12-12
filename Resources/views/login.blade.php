@extends('auth::layouts.master')
@section('title') {{ trans('auth::messages.login') }}
@stop
@section('content')
<div class="d-flex justify-content-center">
    <div class="col-md-4">
        @if(isset($errors)) @foreach($errors->all() as $error)
        <div class="alert alert-warning">
            {{ $error }}
            <a class="close" data-dismiss="alert" href="#">
                ×
            </a>
        </div>
        @endforeach @endif
        <form action="/auth/login" method="POST">
            @csrf
            <div class="form-group">
                <input name="mobile" value="09367034765" class="form-control"
                    placeholder="trans('auth::messages.mobile')">

            </div>


            <div class="form-group">
                <input class="form-control col-md-12" name="password" placeholder="{{trans('auth::messages.password')}}"
                    type="password" value="123456789"></input>
            </div>
            <button class="btn btn-info" type="submit">
                {{trans('auth::messages.login')}}
            </button>
        </form>
    </div>
</div>

@stop