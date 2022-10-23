@extends('layouts.login_app')

@section('content')
<div class="container"style="position: relative">
    <div class="row justify-content-center">
        <div class="col-md-8"style="background-color: #409EFF">
            <div class="card"style="border-color: #409EFF">
                <div class="card-header"style="background-color: #409EFF;color: white;text-align: center;font-size: 20px;border-color: #409EFF">{{ __('Login DIST Now!') }}</div>

                <div class="card-body"style="background-color: #409EFF;border-color: #409EFF">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3", style="text-align: center">
                            <label for="email" style="color: white">{{ __('Email Address') }}</label>

                            <div style="width: 50%; margin: auto">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-3", style="text-align: center">
                            <label for="password" style="color: white">{{ __('Password') }}</label>

                            <div style="margin: auto;width: 50%">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3" style="margin: auto">
                            <div class="col-md-6 offset-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-3" style="position: relative; horiz-align: center">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

{{--                                @if (Route::has('password.request'))--}}
{{--                                    <a class="btn btn-link" href="{{ route('password.request') }}" style="color: white">--}}
{{--                                        {{ __('Forget your password?') }}--}}
{{--                                    </a>--}}
{{--                                @endif--}}

                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
