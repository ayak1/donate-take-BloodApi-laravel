<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonateRequest;
use App\Models\BloodTypes;
use App\Models\taken_request;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// use Symfony\Component\HttpFoundation\Response;

class TakenRequestController extends Controller
{
  
    public function __construct()
    {
        $this->middleware('blood_compare')->only('store');
    }
 
    public function index()
    {
        $taken_requests = taken_request::with('user','blood_type')->get();
        return response()->json([
            "message"=>"these all taken request",
            "data"=>$taken_requests
        ], Response::HTTP_ACCEPTED);
    }

    public function store(StoreDonateRequest $request)
    {
       
        $amountOfThisType=BloodTypes::select('amount')->where('id',$request->blood_type_id)->value('amount');
        if($amountOfThisType>=$request->amount){
        $taken_request= taken_request::create([
            "user_id"=>$request->user_id,
            "amount"=>$request->amount,
            "blood_type_id"=>$request->blood_type_id,
            "verified"=>false,
        ]);
        $amountOfThisType=BloodTypes::select('amount')->where('id',$request->blood_type_id)->update([
            'amount'=>$amountOfThisType-$request->amount
        ]);

        return response()->json([
            "message"=>"created successfully",
            "data"=>$taken_request,
        ], Response::HTTP_ACCEPTED);}
        else{
            return response()->json([
                "message"=>"there is no enough amount",
                "data"=>$amountOfThisType
            ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }
       
    }

    public function show( $taken_request_id)
    {
        //get data for record in DB
        $taken = taken_request::where('id',$taken_request_id)->with('user','blood_type')->get();
        if (is_null($taken)) {
            return response()->json([
                "message"=>"Data not found",
            ], Response::HTTP_NOT_FOUND); 
        }
            return response()->json([
                "message"=>"you got the record you want",
                "data"=>$taken,
            ], Response::HTTP_ACCEPTED);
    }

    public function update(Request $request,  $taken_request_id)
    {
        //update record
        $request->validate([
            "amount"=>"required|min:1",           
        ]);
        $taken=taken_request::find($taken_request_id);
        if (is_null($taken)) {
            return response()->json([
                "message"=>"Data not found",
            ], Response::HTTP_NOT_FOUND); 
        }
        else{
        $t=BloodTypes::select('amount')->where('id',$taken->blood_type_id)->value('amount');
        if($t>=$request->amount){
        $taken->update([
            "amount"=>$request->amount,
        ]);
        return response()->json([
            "message"=>"taken_schedual Updated successfully!",
        ], Response::HTTP_ACCEPTED);}
        else{
            return response()->json([
                "message"=>"there is no enough amount",
                "data"=>$t
            ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }
        
         }
         
    }
    public function destroy( $taken_request_id)
    {
        $taken=taken_request::find($taken_request_id)->delete();
        return response()->json([
            'message' => "donate_schedual deleted successfully!",
            'data' => $taken,
        ], Response::HTTP_OK);
    }
    public function checkTakenRequest( $taken_request_id)
    {
        $blood=taken_request::select('blood_type_id')->where('id',$taken_request_id)->value('blood_type_id');
        $takenBloodAmount = taken_request::select('amount')->where('id',$taken_request_id)->value('amount');
        $prevAmount=BloodTypes::select('amount')->where('id',$blood)->value('amount');
        BloodTypes::select('amount')->where('id',$blood)->update([
            'amount'=> $prevAmount-$takenBloodAmount
        ]);
        $this->destroy($taken_request_id);
        return response()->json([
            'message' => "this taken checked success",
        ], Response::HTTP_OK);
    }
}
