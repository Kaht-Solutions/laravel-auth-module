@extends('auth::layouts.master') 
@section('title') {{ trans('auth::messages.register') }} 
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
    @endforeach @endif {!! Form::open(['url'=>['/auth/register'],'id'=>'register']) !!}

    <div class="form-group">
        {!! Form::text('mobile',Request::old('mobile'),array('class'=>'form-control
        ','placeholder'=>trans('auth::messages.mobile'))) !!}
        </div>
    
        <div class="form-group">
        <input class="form-control col-md-12" name="password" placeholder="{{trans('auth::messages.password')}}" type="password"></input>
        </div>

        <div class="form-group">
            <input class="form-control col-md-12" name="password_confirmation" placeholder="{{trans('auth::messages.password_confirmation')}}" type="password"></input>
            </div>

   
    {!! Form::submit(trans('auth::messages.register'),['class'=>'btn btn-info btn-circle']) !!} {!! Form::close()
    !!}
        </div>
</div>






@stop