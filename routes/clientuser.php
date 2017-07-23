<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('clientuser')->user();

    //dd($users);

    return view('clientuser.home');
})->name('home');

