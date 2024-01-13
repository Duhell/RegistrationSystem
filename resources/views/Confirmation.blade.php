@extends('layout.main')
@section('title','Registered')
@section('content')
    <div id="confirmContainer">
        <div id="msg">
            <p>{{ $response['msg'] }}</p>
        </div>
    </div>
@endsection
