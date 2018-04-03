<?php
/**
 * Config generated using Crm
 * Help: http://Crm.com
 * Crm is open-sourced software licensed under the MIT license.
 * Developed by: Zhovtyj IT Solutions
 * Developer Website: http://Zhovtyjitsolutions.com
 */

return [
    
	/*
    |--------------------------------------------------------------------------
    | General Configuration
    |--------------------------------------------------------------------------
    */
    
	'adminRoute' => 'admin',
    
    /*
    |--------------------------------------------------------------------------
    | Uploads Configuration
    |--------------------------------------------------------------------------
    |
    | private_uploads: Uploaded file remains private and can be seen by respective owners + Super Admin only
    | default_public: Will make default uploads public / private
	| allow_filename_change: allows user to modify filenames after upload. Changes will be only in Database not on actual files.
    | 
    */
    'uploads' => [
        'private_uploads' => false,
        'default_public' => false,
        'allow_filename_change' => true
    ],
];