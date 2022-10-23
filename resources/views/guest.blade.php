@extends('layouts.guest_app')

@section('content-title', 'DummyTitle')

@section('content')
{{--    <example-component></example-component>--}}
    <guest-container></guest-container>
{{--<div class="row">--}}
{{--  <div class="col-md-8 col-md-offset-2">--}}
{{--    @component(config('generator.view_component').'components.panel')--}}
{{--      @slot('title')--}}
{{--        Title--}}
{{--      @endslot--}}

{{--      @if (session('status'))--}}
{{--      <div class="alert alert-success">--}}
{{--        {{ session('status') }}--}}
{{--      </div>--}}
{{--      @endif--}}
{{--      Content goes here--}}

{{--    @endcomponent--}}
{{--  </div>--}}
{{--</div>--}}
@endsection
