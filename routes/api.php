<?php 
use Illuminate\Support\Facades\Route; 

Route::get('/test', fn() => ['name' => 'test api endpoint']);
