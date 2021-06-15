<?php

use SmartBit\TemplateMaker\Http\Controllers\TemplateMakerController;
use SmartBit\TemplateMaker\Http\Controllers\TemplateController;
use SmartBit\TemplateMaker\Http\Controllers\PdfPreviewController;
use Illuminate\Support\Facades\Route;
use SmartBit\TemplateMaker\Models\TemplateMaker;
use SmartBit\TemplateMaker\Models\Template;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/preview/pdf/{documentTemplate}', [PdfPreviewController::class, 'show']);
// Route::post('document_template/{documentTemplate}/import', [TemplateController::class, 'import']);
// Route::post('document_template/{documentTemplate}/export', [TemplateController::class, 'export']);
Route::resource('template', TemplateController::class);