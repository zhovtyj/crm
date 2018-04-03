<?php
/**
 * Controller generated using Crm
 * Help: http://Crm.com
 * Crm is open-sourced software licensed under the MIT license.
 * Developed by: Zhovtyj IT Solutions
 * Developer Website: http://Zhovtyjitsolutions.com
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Zhovtyj\Crm\Models\Module;
use Zhovtyj\Crm\Models\ModuleFields;
use Zhovtyj\Crm\Models\LAConfigs;
use Zhovtyj\Crm\Helpers\LAHelper;

use App\User;
use App\Models\Employee;
use App\Role;
use Mail;
use Log;

class EmployeesController extends Controller
{
	public $show_action = true;
	
	/**
	 * Display a listing of the Employees.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('Employees');
		
		if(Module::hasAccess($module->id)) {
			return View('la.employees.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => Module::getListingColumns('Employees'),
				'module' => $module
			]);
		} else {
            return redirect(config('Crm.adminRoute')."/");
        }
	}

	/**
	 * Show the form for creating a new employee.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created employee in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if(Module::hasAccess("Employees", "create")) {
		
			$rules = Module::validateRules("Employees", $request);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}
			
			// generate password
			$password = LAHelper::gen_password();
			
			// Create Employee
			$employee_id = Module::insert("Employees", $request);
			// Create User
			$user = User::create([
				'name' => $request->name,
				'email' => $request->email,
				'password' => bcrypt($password),
				'context_id' => $employee_id,
				'type' => "Employee",
			]);
	
			// update user role
			$user->detachRoles();
			$role = Role::find($request->role);
			$user->attachRole($role);
			
			if(env('MAIL_USERNAME') != null && env('MAIL_USERNAME') != "null" && env('MAIL_USERNAME') != "") {
				// Send mail to User his Password
				Mail::send('emails.send_login_cred', ['user' => $user, 'password' => $password], function ($m) use ($user) {
					$m->from('hello@Crm.com', 'Crm');
					$m->to($user->email, $user->name)->subject('Crm - Your Login Credentials');
				});
			} else {
				Log::info("User created: username: ".$user->email." Password: ".$password);
			}
			
			return redirect()->route(config('Crm.adminRoute') . '.employees.index');
			
		} else {
			return redirect(config('Crm.adminRoute')."/");
		}
	}

	/**
	 * Display the specified employee.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if(Module::hasAccess("Employees", "view")) {
			
			$employee = Employee::find($id);
			if(isset($employee->id)) {
				$module = Module::get('Employees');
				$module->row = $employee;
				
				// Get User Table Information
				$user = User::where('context_id', '=', $id)->firstOrFail();
				
				return view('la.employees.show', [
					'user' => $user,
					'module' => $module,
					'view_col' => $module->view_col,
					'no_header' => true,
					'no_padding' => "no-padding"
				])->with('employee', $employee);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("employee"),
				]);
			}
		} else {
			return redirect(config('Crm.adminRoute')."/");
		}
	}

	/**
	 * Show the form for editing the specified employee.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(Module::hasAccess("Employees", "edit")) {
			
			$employee = Employee::find($id);
			if(isset($employee->id)) {
				$module = Module::get('Employees');
				
				$module->row = $employee;
				
				// Get User Table Information
				$user = User::where('context_id', '=', $id)->firstOrFail();
				
				return view('la.employees.edit', [
					'module' => $module,
					'view_col' => $module->view_col,
					'user' => $user,
				])->with('employee', $employee);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("employee"),
				]);
			}
		} else {
			return redirect(config('Crm.adminRoute')."/");
		}
	}

	/**
	 * Update the specified employee in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if(Module::hasAccess("Employees", "edit")) {
			
			$rules = Module::validateRules("Employees", $request, true);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}
			
			$employee_id = Module::updateRow("Employees", $request, $id);
        	
			// Update User
			$user = User::where('context_id', $employee_id)->first();
			$user->name = $request->name;
			$user->save();
			
			// update user role
			$user->detachRoles();
			$role = Role::find($request->role);
			$user->attachRole($role);
			
			return redirect()->route(config('Crm.adminRoute') . '.employees.index');
			
		} else {
			return redirect(config('Crm.adminRoute')."/");
		}
	}

	/**
	 * Remove the specified employee from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if(Module::hasAccess("Employees", "delete")) {
			Employee::find($id)->delete();
			
			// Redirecting to index() method
			return redirect()->route(config('Crm.adminRoute') . '.employees.index');
		} else {
			return redirect(config('Crm.adminRoute')."/");
		}
	}
	
	/**
	 * Datatable Ajax fetch
	 *
	 * @return
	 */
	public function dtajax(Request $request)
	{
		$module = Module::get('Employees');
		$listing_cols = Module::getListingColumns('Employees');
		
		$values = DB::table('employees')->select($listing_cols)->whereNull('deleted_at');
		$out = Datatables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('Employees');
		
		for($i=0; $i < count($data->data); $i++) {
			for ($j=0; $j < count($listing_cols); $j++) { 
				$col = $listing_cols[$j];
				if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
				}
				if($col == $module->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('Crm.adminRoute') . '/employees/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
				}
				// else if($col == "author") {
				//    $data->data[$i][$j];
				// }
			}
			
			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("Employees", "edit")) {
					$output .= '<a href="'.url(config('Crm.adminRoute') . '/employees/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}
				
				if(Module::hasAccess("Employees", "delete")) {
					$output .= Form::open(['route' => [config('Crm.adminRoute') . '.employees.destroy', $data->data[$i][0]], 'method' => 'delete', 'style'=>'display:inline']);
					$output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
					$output .= Form::close();
				}
				$data->data[$i][] = (string)$output;
			}
		}
		$out->setData($data);
		return $out;
	}
	
	/**
     * Change Employee Password
     *
     * @return
     */
	public function change_password($id, Request $request) {
		
		$validator = Validator::make($request->all(), [
            'password' => 'required|min:6',
			'password_confirmation' => 'required|min:6|same:password'
        ]);
		
		if ($validator->fails()) {
			return \Redirect::to(config('Crm.adminRoute') . '/employees/'.$id)->withErrors($validator);
		}
		
		$employee = Employee::find($id);
		$user = User::where("context_id", $employee->id)->where('type', 'Employee')->first();
		$user->password = bcrypt($request->password);
		$user->save();
		
		\Session::flash('success_message', 'Password is successfully changed');
		
		// Send mail to User his new Password
		if(env('MAIL_USERNAME') != null && env('MAIL_USERNAME') != "null" && env('MAIL_USERNAME') != "") {
			// Send mail to User his new Password
			Mail::send('emails.send_login_cred_change', ['user' => $user, 'password' => $request->password], function ($m) use ($user) {
				$m->from(LAConfigs::getByKey('default_email'), LAConfigs::getByKey('sitename'));
				$m->to($user->email, $user->name)->subject('Crm - Login Credentials changed');
			});
		} else {
			Log::info("User change_password: username: ".$user->email." Password: ".$request->password);
		}
		
		return redirect(config('Crm.adminRoute') . '/employees/'.$id.'#tab-account-settings');
	}
}
