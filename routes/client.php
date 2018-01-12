<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('client')->user();
    return view('subdomain.home');
})->name('home');

