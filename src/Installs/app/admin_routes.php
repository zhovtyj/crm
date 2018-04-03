<?php

use Zhovtyj\Crm\Helpers\LAHelper;

/* ================== Homepage ================== */
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::auth();

/* ================== Access Uploaded Files ================== */
Route::get('files/{hash}/{name}', 'LA\UploadsController@get_file');

/*
|--------------------------------------------------------------------------
| Admin Application Routes
|--------------------------------------------------------------------------
*/

$as = "";
if(LAHelper::laravel_ver() >= 5.3 || LAHelper::laravel_ver() == 5.4) {
	$as = config('Crm.adminRoute').'.';
	
	// Routes for Laravel 5.3
	Route::get('/logout', 'Auth\LoginController@logout');
}

Route::group(['as' => $as, 'middleware' => ['auth', 'permission:ADMIN_PANEL']], function () {
	
	/* ================== Dashboard ================== */
	
	Route::get(config('Crm.adminRoute'), 'LA\DashboardController@index');
	Route::get(config('Crm.adminRoute'). '/dashboard', 'LA\DashboardController@index');
	
	/* ================== Users ================== */
	Route::resource(config('Crm.adminRoute') . '/users', 'LA\UsersController');
	Route::get(config('Crm.adminRoute') . '/user_dt_ajax', 'LA\UsersController@dtajax');
	
	/* ================== Uploads ================== */
	Route::resource(config('Crm.adminRoute') . '/uploads', 'LA\UploadsController');
	Route::post(config('Crm.adminRoute') . '/upload_files', 'LA\UploadsController@upload_files');
	Route::get(config('Crm.adminRoute') . '/uploaded_files', 'LA\UploadsController@uploaded_files');
	Route::post(config('Crm.adminRoute') . '/uploads_update_caption', 'LA\UploadsController@update_caption');
	Route::post(config('Crm.adminRoute') . '/uploads_update_filename', 'LA\UploadsController@update_filename');
	Route::post(config('Crm.adminRoute') . '/uploads_update_public', 'LA\UploadsController@update_public');
	Route::post(config('Crm.adminRoute') . '/uploads_delete_file', 'LA\UploadsController@delete_file');
	
	/* ================== Roles ================== */
	Route::resource(config('Crm.adminRoute') . '/roles', 'LA\RolesController');
	Route::get(config('Crm.adminRoute') . '/role_dt_ajax', 'LA\RolesController@dtajax');
	Route::post(config('Crm.adminRoute') . '/save_module_role_permissions/{id}', 'LA\RolesController@save_module_role_permissions');
	
	/* ================== Permissions ================== */
	Route::resource(config('Crm.adminRoute') . '/permissions', 'LA\PermissionsController');
	Route::get(config('Crm.adminRoute') . '/permission_dt_ajax', 'LA\PermissionsController@dtajax');
	Route::post(config('Crm.adminRoute') . '/save_permissions/{id}', 'LA\PermissionsController@save_permissions');
	
	/* ================== Departments ================== */
	Route::resource(config('Crm.adminRoute') . '/departments', 'LA\DepartmentsController');
	Route::get(config('Crm.adminRoute') . '/department_dt_ajax', 'LA\DepartmentsController@dtajax');
	
	/* ================== Employees ================== */
	Route::resource(config('Crm.adminRoute') . '/employees', 'LA\EmployeesController');
	Route::get(config('Crm.adminRoute') . '/employee_dt_ajax', 'LA\EmployeesController@dtajax');
	Route::post(config('Crm.adminRoute') . '/change_password/{id}', 'LA\EmployeesController@change_password');
	
	/* ================== Organizations ================== */
	Route::resource(config('Crm.adminRoute') . '/organizations', 'LA\OrganizationsController');
	Route::get(config('Crm.adminRoute') . '/organization_dt_ajax', 'LA\OrganizationsController@dtajax');

	/* ================== Backups ================== */
	Route::resource(config('Crm.adminRoute') . '/backups', 'LA\BackupsController');
	Route::get(config('Crm.adminRoute') . '/backup_dt_ajax', 'LA\BackupsController@dtajax');
	Route::post(config('Crm.adminRoute') . '/create_backup_ajax', 'LA\BackupsController@create_backup_ajax');
	Route::get(config('Crm.adminRoute') . '/downloadBackup/{id}', 'LA\BackupsController@downloadBackup');
});
