<?php

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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/start_import', 'HomeController@ViewImport')->name('import_process');
Route::get('/view_import', 'HomeController@ViewImportData')->name('view_import');
Route::get('/view_import_log', 'HomeController@ViewImportLog')->name('view_import_log');
Route::get('/log', 'HomeController@ViewImportLog')->name('log');
Route::get('/process_curl/{id}', 'HomeController@ProcessCurl')->name('process_curl');
Route::get('/view_curled', 'HomeController@ViewCurledData')->name('view_curled');
Route::get('/stop_curl', 'HomeController@StopCurl')->name('stop_curl');








// cron job route
Route::get('/start_cron', 'CronController@CronImport')->name('start_cron');
Route::get('/bava', 'CronController@ProcessCurlCron')->name('bava');


Route::post('/password/reset', 'Auth\PasswordController@reset');



Route::get('/export_excel', 'HomeController@ExportAsExcel')->name('export_excel');
// test route
Route::get('/test', 'HomeController@Test')->name('test');



// curl

// Route::get('csvcreateproduct/{id}', 'CsvController@CreateProduct')->name('csv.create.product');

Route::get('/import/{id}', 'HomeController@import')->name('import');
Route::get('/import_status', 'HomeController@ImportStatus')->name('import_status');
// Route::post('/import', 'HomeController@import')->name('import');



// Password reset routes...



Route::get('/changePassword','AdminController@showChangePasswordForm');
Route::post('/changePassword','AdminController@changePassword')->name('changePassword');





// email template

Route::get('/email', 'EmailController@dashboardView')->name('email');
Route::get('/createemail', 'EmailController@createEmailView')->name('create.email.template');
Route::post('/storeemail', 'EmailController@storeEmailTemplate')->name('store.email.template');
Route::get('/editemail/{id}', 'EmailController@editEmailTemplate')->name('edit.email.template');
Route::get('/deleteemail/{id}', 'EmailController@deleteEmailTemplate')->name('delete.email.template');
Route::post('/updateemail', 'EmailController@updateEmailTemplate')->name('update.email.template');
Route::get('/viewtemplates', 'EmailController@viewTemplates')->name('view.email.template');





Route::get('/sendemail', 'EmailController@sendEmailView')->name('send.email');
Route::get('/roc/get/{state}', 'EmailController@getRocEmail')->name('get.email.roc');
Route::get('/doi/get/{roc}', 'EmailController@getDoiEmail')->name('get.email.doi');
Route::get('/des/get/{obj}', 'EmailController@getActivityDescriptionEmail')->name('get.email.des');
Route::get('/cat/get/{des}', 'EmailController@getCategoryEmail')->name('get.email.cat');
Route::post('/sendemailadmin', 'EmailController@sendEmailAdmin')->name('send.email.admin');

Route::get('/configuration', 'EmailController@configEmail')->name('email.config');
Route::post('/storeconfiguration', 'EmailController@storeconfigEmail')->name('store.email.config');

Route::get('/emailcron', 'CronEmailController@sendEmailCron');

Route::get('/testemail', 'EmailController@testemail')->name('test.email');