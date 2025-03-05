@extends('layouts.auth')
@section('content')
    <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">

            <div class="card card-primary">
                <div class="card-header">
                    <h4>Reset Password</h4>
                </div>

                <div class="card-body">
                    <p class="text-muted">Reset your password here</p>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="text" value="{{ $request->token }}" hidden name="token">
                        <div class="form-group">
                            <input hidden id="email" type="email"
                                class="form-control @error('email')
                                is-invalid
                            @enderror"
                                name="email" tabindex="1" value="{{ $request->email }}">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input id="password" type="password"
                                class="form-control @error('password')
                                is-invalid
                            @enderror"
                                name="password" tabindex="2">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="d-block">Password Confirmation</label>
                            <input id="password_confirmation" type="password"
                                class="form-control @error('password_confirmation')
                                is-invalid
                            @enderror"
                                name="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection