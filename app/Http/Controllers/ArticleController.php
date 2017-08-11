<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Carbon\Carbon;
use App\ArticleModel;
use App\AuthorModel;
use Validator;

class ArticleController extends Controller {
    
    
    public function __construct(  ArticleModel $article_model, AuthorModel $author_model ) {        
        $this->article_model = new $article_model;  
        $this->author_model = new $author_model;
    }
    
    
    public function getAllArticle(  ) {         
        $final_data   = array();
        $article_data = $this->article_model->all();
        if($article_data) {
            $i = 0;
            foreach( $article_data as $val ) {
                $final_data[$i]["id"]        = $val->id;                
                $final_data[$i]["title"]     = $val->title;  
                $final_data[$i]["author"]    = $this->author_model->find($val->author_id)->name;  
                $final_data[$i]["summary"]   = $val->content;
                $final_data[$i]["url"]       = $val->url;
                $final_data[$i]["createdAt"] = Carbon::parse($val->createdAt)->format('Y-m-d');
                $i++;                
            }           
        }        
        return response($final_data, 200)->header('Content-Type', 'application/json');
    }   
    
    
    
    public function createArticle( Request $request ) {          
        $adata = array(); 
        $adata["author_id"] = $request->author_id; 
        $adata["title"]     = $request->title;
        $adata["url"]       = $request->url;
        $adata["content"]   = $request->content;
        
        $validator     = $this->validateAddArticle($adata);
        if( $validator->fails() ) {         
            $err = array();               
            $err["author_id"] = $validator->messages()->get('author_id');
            $err["content"]   = $validator->messages()->get('content');
            $err["title"]     = $validator->messages()->get('title');
            $err["url"]       = $validator->messages()->get('url');
            
            $data = array('status'=>401, 'data'=>array("error"=>$err));          
        } else {
            $this->article_model->author_id = $request->author_id;
            $this->article_model->content  = $request->content;
            $this->article_model->title     = $request->title;
            $this->article_model->url      = $request->url;
            
            $resp = $this->article_model->save();
             if($resp) {
                $data = array('status'=>200, 'data'=>array("message"=>"Successfull")); 
            } else {
                $data = array('status'=>401, 'data'=>array("message"=>"Failed")); 
            }  
        }          
        return response($data, 200)->header('Content-Type', 'application/json');
    }
    
        
   
    protected function validateAddArticle(array $data) {
        $messages = array();
        return Validator::make($data, [
            "content"    => 'max:5000',  
            'url'        => 'max:255|unique:article,url,'.$data["url"],
            "title"      => "max:255",
            "author_id"  => "required|exists:author,id",            
          ], $messages );
    }
    
    
    
    protected function validateEditArticle(array $data) {
        $messages = array();
        return Validator::make($data, [
            "content"    => 'max:5000', 
            "title"      => "max:255",
            "author_id"  => "required|exists:author,id",            
          ], $messages );
    }
    
    
    public function updateArticle( Request $request ) {  
        $adata = array();
        $err   = array();   
        ///$adata["article_id"] = $request->id; 
        $adata["author_id"] = $request->author_id; 
        $adata["title"]     = $request->title;
        $adata["url"]       = $request->url;
        $adata["content"]   = $request->content;
        
        if($this->article_model->find($request->id) ) {
            $validator     = $this->validateEditArticle($adata);
            if( $validator->fails() ) { 
                $err["author_id"] = $validator->messages()->get('author_id');
                $err["content"]   = $validator->messages()->get('content');
                $err["title"]     = $validator->messages()->get('title');
                $err["url"]       = $validator->messages()->get('url');
            }             
            $is_valid_url = $this->article_model->checkUrl($request->id, $adata["url"]);
            if($is_valid_url) 
                 $err["url"]  = "URL must be Unique";            
            
            if($err) {
                $data = array('status'=>401, 'data'=>array("error"=>$err)); 
            } else {                
                $resp = $this->article_model->where('id', $request->id)->update($adata);
                if($resp) 
                    $data = array('status'=>200, 'data'=>array("message"=>"Successfull")); 
                else 
                   $data = array('status'=>401, 'data'=>array("message"=>"Failed"));                  
            }
        } else {
           $data = array('status'=>401, 'data'=>array("message"=>"invalid Article Id")); 
        }
        return response($data, 200)->header('Content-Type', 'application/json');
    } 
    
    
    public function deleteArticle( $article_id) {
        $del = $this->article_model->find($article_id);
        if($del) {
            $del->delete(); 
            $data = array('status'=>200, 'data'=>array("message"=>"Successfull")); 
        } else {
            $data = array('status'=>401, 'data'=>array("message"=>"Invalid Id")); 
        }      
        
        return response($data, 200)->header('Content-Type', 'application/json'); 
    }
    
    
    
}
