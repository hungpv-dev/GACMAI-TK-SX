<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Group extends Eloquent{
        protected $table = 'groups';

        protected $guarded = [];
        
        public $timestamps = false;
        
        public function users(){
            return $this->hasMany(User::class);
        }
        public function allocations(){
            return $this->hasMany(CostAllocation::class,'group_id');
        }
        
    }
