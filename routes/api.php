<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistogramController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MaterielController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SortieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('posts' , PostController::class);
Route::apiResource('materiels' ,MaterielController::class);
Route::apiResource('receptions' ,ReceptionController::class);
Route::apiResource('sorties' ,SortieController::class);
Route::post('/register' , [AuthController::class , 'register']);
Route::post('/login' , [AuthController::class , 'login']);
Route::post('/logout' , [AuthController::class , 'logout'])->middleware('auth:sanctum');
Route::post('/generate-pdf/{CodeMateriel}', [PDFController::class, 'generatePDF']);
Route::get('search', [SearchController::class, 'search']);
Route::get('/years', [RapportController::class, 'getYears']);
Route::get('/material-types', [RapportController::class, 'getMaterialTypes']);
Route::post('/rapport', [RapportController::class, 'generatePDF']);
Route::get('/histogramme', [HistogramController::class, 'getStockHistogram']);
Route::get('/card', [HistogramController::class, 'getDashboardData']);