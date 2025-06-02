<?php

use App\Http\Controllers\PartidaController;
use App\Http\Controllers\DenunciaController;
use App\Http\Controllers\CartaController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ola', [HomeController::class, 'index']);

// Rotas para PARTIDAS
// Route::controller(PartidaController::class)->group(function () {
//     Route::prefix('/partidas')->group(function () {
//         // READ
//         Route::get('/', 'index')->name('indexpartidas');
//         Route::get('/{codpartida}', 'show')->name('showpartidas');
//     });

//     Route::prefix('/partida')->group(function () {
//         // CREATE
//         Route::get('/', 'create')->name('criarpartidas');
//         Route::post('/', 'store');

//         // UPDATE
//         Route::get('/{codpartida}/edit', 'edit')->name('editpartidas');
//         Route::post('/{codpartida}/edit', 'update')->name('updatepartidas');

//         // DELETE
//         Route::get('/{codpartida}/delete', 'delete')->name('deletepartidas');
//         Route::post('/{codpartida}/remove', 'remove')->name('removepartidas');
//     });
// });

// // Rotas para DENÚNCIAS
// Route::controller(DenunciaController::class)->group(function () {
//     Route::prefix('/denuncias')->group(function () {
//         // READ
//         Route::get('/', 'index')->name('denuncia.index');
//         Route::get('/{id}', 'show')->name('denuncia.show');
//     });

//     Route::prefix('/denuncia')->group(function () {
//         // CREATE
//         Route::get('/', 'create')->name('denuncia.create');
//         Route::post('/', 'store')->name('denuncia.store');

//         // UPDATE
//         Route::get('/{id}/edit', 'edit')->name('denuncia.edit');
//         Route::post('/{id}/edit', 'update')->name('denuncia.update');

//         // DELETE
//         Route::get('/{id}/delete', 'delete')->name('denuncia.delete');
//         Route::post('/{id}/remove', 'remove')->name('denuncia.remove');
//     });
// });

