<?php

namespace App\Http\Controllers;

use App\Models\BloodTypes;
use App\Models\taken_request;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// use Symfony\Component\HttpFoundation\Response;

class TakenRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('blood_compare')->only('store');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $taken_requests = taken_request::with('user','blood_type')->get();
        return response()->json([
            "message"=>"these all taken request",
            "data"=>$taken_requests
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
            "user_id"=>"required|exists:users,id",
            "amount"=>"required|min:1",
            "blood_type_id"=>"required|exists:blood_types,id",
            "verified"=>"nullable",
        ]);
        $t=BloodTypes::select('amount')->where('id',$request->blood_type_id)->value('amount');
        if($t>=$request->amount){
        $taken_request= taken_request::create([
            "user_id"=>$request->user_id,
            "amount"=>$request->amount,
            "blood_type_id"=>$request->blood_type_id,
            "verified"=>false,
        ]);
        $t=BloodTypes::select('amount')->where('id',$request->blood_type_id)->update([
            'amount'=>$t-$request->amount
        ]);

        return response()->json([
            "message"=>"created successfully",
            "data"=>$taken_request,
        ], Response::HTTP_ACCEPTED);}
        else{
            return response()->json([
                "message"=>"there is no enough amount",
                "data"=>$t
            ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\taken_request $donate_schedule
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\taken_request $donate_schedule
     * @return \Illuminate\Http\Response
     */
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
        // taken_request::where('id',$taken_request_id)->update([
        //     "amount"=>$request->amount
        // ]);
        $t=BloodTypes::select('amount')->where('id',$taken->blood_type_id)->value('amount');
        if($t>=$request->amount){
        $taken->update([
            "amount"=>$request->amount,
        ]);
        $t=BloodTypes::select('amount')->where('id',$request->blood_type_id)->update([
            'amount'=>$t-$request->amount
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\taken_request $donate_schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy( $taken_request_id)
    {
        // $donate = taken_request::find($donate_schedule->id);
        $taken=taken_request::find($taken_request_id)->delete();
        return response()->json([
            'message' => "donate_schedual deleted successfully!",
            'data' => $taken,
        ], Response::HTTP_OK);
    }
}
