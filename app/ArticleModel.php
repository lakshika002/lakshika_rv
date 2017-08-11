<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class ArticleModel extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table      = 'article';    
    protected $fillable  = [ "id",
                            "author_id", 
                             "title",
                             "url",
                             "content",
                             "createdAt",  
                             "updatedAt",
                                                                
                           ];  
    
    
    public function checkUrl($id, $url) {
        $data = DB::table('article')
                ->select('id')                
                ->where("url", $url ) 
                ->where("id",  "!=", $id )
                ->get();  
       return $data;
    }
            
            
}




