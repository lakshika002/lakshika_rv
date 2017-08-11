<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Carbon\Carbon;
use App\AuthorModel;
use Validator;

class AuthorController extends Controller {
    
    public function __construct(  AuthorModel $author_model ) {        
        $this->author_model = new $author_model;         
    }
    
    
    public function createAuthor( Request $request ) {
        $adata  = array(); 
        $adata["name"] = $request->name; 
        $validator     = $this->validateAuthor($adata);
         
        if( $validator->fails() ) {         
            $err = array();               
            $err["name"] = $validator->messages()->get('name');
            $data = array('status'=>401, 'data'=>array("error"=>$err));
        } else {
            $this->author_model->name = $request->name;
            $resp = $this->author_model->save();
            if($resp) {
                $data = array('status'=>200, 'data'=>array("message"=>"Successfull")); 
            } else {
                $data = array('status'=>401, 'data'=>array("message"=>"Failed")); 
            }            
        }
        return response($data, 200)->header('Content-Type', 'application/json');
    }
    
    
    
    
    
    
    
   
    protected function validateAuthor(array $data) {
        
  
        
        $messages = array();
        return Validator::make($data, [
             "name"  => 'required|max:255',   
            
          ], $messages );
    }
    
    
    
    
    
    
    public function addPlan( Request $request ) {
        $plan_data = array(); 
        $stuData   = $this->getStudentDataByTocken($request->token);  
        
        if($stuData && isset($stuData["student_id"]) ) {
            $stuid     = $stuData["student_id"];
            $data      = array();
           // $plan_data["repeat_item"]    = $request->repeat_item;        
            $plan_data["title"]          = $request->title;        
            $plan_data["start_date"]     = $request->start_date;
            $plan_data["end_date"]       = $request->end_date;
            $plan_data["start_time"]     = $request->start_time;        
            $plan_data["end_time"]       = $request->end_time;
            $plan_data["event_color"]    = $request->event_color;
            $plan_data["remind_date"]    = $request->remind_date;        
            $plan_data["remind_time"]    = $request->remind_time;
            //$plan_data["repeat_type_id"] = $request->repeat_type_id;
            $plan_data["note"]           = $request->note;
            $plan_data["customise_dates"]= (array)json_decode($request->customise_dates);
            
            $validator  = $this->validatePlanner($plan_data); 
            if( $validator->fails() ) {         
                $err = array();
                $err["note"]         = $validator->messages()->get('note');
                $err["title"]        = $validator->messages()->get('title');
                $err["start_date"]   = $validator->messages()->get('start_date');
                $err["end_date"]     = $validator->messages()->get('end_date');
                $err["remind_date"]  = $validator->messages()->get('remind_date');

                $data = array('status'=>200,
                            'data' => array("Fail"=>"Fail", "error"=>$err));
            } else {               
                $c_date = Carbon::now()->toDateTimeString();            
                $rslt = DB::statement(('CALL spInsertPlanner("'.$stuid.'","'.$plan_data["title"].'",
                          "'.$plan_data["start_date"].'","'.$plan_data["end_date"].'","'.$plan_data["start_time"].'",
                          "'.$plan_data["end_time"].'","","'.$c_date.'","'.$stuid.'",
                          "'.$stuid.'","'.$plan_data["event_color"].'","'.$plan_data["note"].'", @planner_id, @status )')                    
                );              
                $status     =  DB::select("select @status as status");
                $planner    =  DB::select("select @planner_id as planner_id");  
                $planner_id = $planner[0]->planner_id;
                /*
                if( ( $status[0]->status == 'T') && $planner_id  ) {
                    $rmd_date = $plan_data['remind_date'];
                    $rmd_time = $plan_data['remind_time'];
                    $relt2 = DB::select("SELECT fnInsertPlannerReminder('$planner_id','$rmd_date','$rmd_time')");

                    if( isset($plan_data["customise_dates"]) && !empty($plan_data["customise_dates"]) ) {
                        foreach( $plan_data["customise_dates"] as $cust_date ) {
                            $relt3 = DB::select("SELECT fnInsertPlannerReminder('$planner_id','$cust_date','$rmd_time')");           
                        } 
                    }
                } 
                */
                if( $status[0]->status == 'T' ) {  
                    $data = array('status'=>200, 'data'=>array("Success"=>"Success")); 
                } else {
                    $data = array('status'=>200, 'data'=>array("Fail"=>fail));  
                }
            } 
        } else {
            $data = array('status'=>401, 'data'=>array("Invalid"=>"Invalid")); 
        }
                     
        return response($data, 200)->header('Content-Type', 'application/json');
    }
}
