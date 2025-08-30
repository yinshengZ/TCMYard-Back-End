<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


use App\Services\InventoryService;
use App\Services\TreatmentService;



Route::get('/inventory/test/stock/{id}', [InventoryService::class, 'getStocksByInventoryId']);
Route::post('/treatment/test/add', [TreatmentService::class, 'addTreatment']);
Route::get('/treatment/test/treatment/{id}', [TreatmentService::class, 'getPatientTreatments']);
