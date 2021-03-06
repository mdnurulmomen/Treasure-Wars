<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BundlePack extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
    
    public static function boot()
	{
		parent::boot();

		static::created (
			function ($obj) {
		        $obj->custom_id = 'BndlPck_'.$obj->id;
		        $obj->save();
			}
		);

		static::saving (
			function ($obj) {
		        $obj->custom_id = 'BndlPck_'.$obj->id;
			}
		);
	}

    public function bundleComponents()
    {
    	return $this->hasMany('App\Models\BundleComponent', 'bundle_packs_id', 'id');
    }
}
