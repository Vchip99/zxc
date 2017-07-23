@extends('layouts.master')
@section('content')
    <div class="container">
        <div class="login-form">
            
            <h1 class="text-center">PHP Quiz Application</h1>
            <div class="form-header">
                <i class="fa fa-user"></i>
            </div>
            <form method="post" action="{{ url('register')}}" class="form-register" role="form" id="register-form">
                {{ csrf_field() }}
                <div>
                    <input name="name" id="name" type="text" class="form-control" placeholder="Name"> 
                    <span class="help-block"></span>
                </div>
                <div>
                    <input name="email" id="email" type="email" class="form-control" placeholder="Email address" > 
                    <span class="help-block"></span>
                </div>
                <div>
                    <input name="password" id="password" type="password" class="form-control" placeholder="Password"> 
                    <span class="help-block"></span>
                </div>
                <div>
                    <input name="confirm_password" id="confirm_password" type="password" class="form-control" placeholder="Confirm Password"> 
                    <span class="help-block"></span>
                </div>
                
                <div>
                    <select class="form-control" name="category_id" id="technology" required>
                        <option value="">Select Technolgoy</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" > {{ $category->name }}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
                
                <button class="btn btn-block bt-login" type="submit">Sign Up</button>
            </form>
            
            <div class="form-footer">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <i class="fa fa-lock"></i>
                        <a href=""> Forgot password? </a>
                    </div>                    
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <i class="fa fa-check"></i>
                        <a href="{{ url('login') }}"> Sign In </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /container -->
@endsection