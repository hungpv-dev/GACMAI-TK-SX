<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class OrderFile extends Eloquent{
        protected $table = 'order_files';

        protected $guarded = [];
        public $timestamps = false;

        protected $appends = ['url'];

        public function getUrlAttribute(){
            return public_path('upload.php' . $this->image);
        }
    }
