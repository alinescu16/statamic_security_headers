@extends('statamic::layout')

@section('title', 'Security Headers')

@section('content')
    <div id="security-headers-app" data-settings="{{ json_encode($settings) }}" data-grade={{ json_encode($grade) }} v-cloak></div>
@endsection
