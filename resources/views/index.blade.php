@extends('statamic::layout')

@section('title', 'Security Headers')

@section('content')
    <div id="security-headers-app" data-settings="{{ json_encode($settings) }}" v-cloak></div>
@endsection
