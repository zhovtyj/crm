<?php
/**
 * Model generated using Crm
 * Help: http://Crm.com
 * Crm is open-sourced software licensed under the MIT license.
 * Developed by: Zhovtyj IT Solutions
 * Developer Website: http://Zhovtyjitsolutions.com
 */

namespace App;

use Zizaco\Entrust\EntrustPermission;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends EntrustPermission
{
    use SoftDeletes;
	
	protected $table = 'permissions';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
}
