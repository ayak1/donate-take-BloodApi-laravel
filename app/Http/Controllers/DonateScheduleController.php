<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonateRequest;
use App\Http\Resources\DonateResource;
use App\Models\BloodTypes;
use App\Models\donate_schedule;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response ;

class DonateScheduleController extends Controller
{
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
        $donates_schedule = donate_schedule::with('user','blood_type')->get();
        return response()->json([
            "message"=>trans('response.test'),
            "data"=>DonateResource::collection($donates_schedule)
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function todayDonateScheduale()
    {
        //
        $donates_schedule = donate_schedule::whereDate("created_at",Carbon::now())->with('user','blood_type')->get();
        return response()->json([
            "message"=>trans('response.test'),
            "data"=>$donates_schedule,
        ], Response::HTTP_ACCEPTED);
    }

    public function store(StoreDonateRequest $request)
    {
            // return response()->json([
            //     "message"=>"you can't donate now you have to wait for one week btween donates",
            // ], Response::HTTP_ACCEPTED);
        
            $donate_schedule = donate_schedule::create([
                "user_id"=>$request->user_id,
                "amount"=>$request->amount,
                "blood_type_id"=>$request->blood_type_id,
                "verified"=>false,
            ]);
            return response()->json([
                "message"=>"created successfully",
                "data"=>$donate_schedule,
            ], Response::HTTP_ACCEPTED);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\donate_schedule  $donate_schedule
     * @return \Illuminate\Http\Response
     */
    public function show( $donate_schedule_id )
    {
        
        //get data for record in DB

        // i can get the record like this #1
        // $donate = donate_schedule::find($donate_schedule_id)->with('user','blood_type');

        // or like this #2
        $donate = donate_schedule::where('id',$donate_schedule_id)->with('user','blood_type')->get();

        if (is_null($donate)) {
            return response()->json([
                "message"=>"Data not found",
            ], Response::HTTP_NOT_FOUND); 
        }
            return response()->json([
                "message"=>"you got the record you want",
                "data"=>$donate,
            ], Response::HTTP_ACCEPTED);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\donate_schedule  $donate_schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $donate_schedule_id)
    {
        //update record
        $request->validate([
            "amount"=>"required|min:1|integer",
        ]);

        $donate=donate_schedule::find($donate_schedule_id);
        if (is_null($donate)) {
            return response()->json([
                "message"=>"Data not found",
            ], Response::HTTP_NOT_FOUND); 
        }
        else{
        donate_schedule::where('id',$donate_schedule_id)->update([
            "amount"=>$request->amount
        ]);
        return response()->json([
            'message' => "Donate_schedual Updated successfully!",
        ], Response::HTTP_OK);
         }
         
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\donate_schedule  $donate_schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy( $donate_schedule_id)
    {
        $donate=donate_schedule::where('id',$donate_schedule_id)->delete();
        return response()->json([
            'message' => "donate_schedual deleted successfully!",
            'data' => $donate,
        ], Response::HTTP_OK);
    }
    public function checkDonate( $donate_schedule_id)
    {
        $d=donate_schedule::select('blood_type_id')->where('id',$donate_schedule_id)->value('blood_type_id');
        $dA = donate_schedule::select('amount')->where('id',$donate_schedule_id)->value('amount');
        $this->destroy($donate_schedule_id);
        $prevAmount=BloodTypes::select('amount')->where('id',$d)->value('amount');
        $t=BloodTypes::select('amount')->where('id',$d)->update([
            'amount'=> $prevAmount+$dA
        ]);
        return response()->json([
            'message' => "this donate checked success",
        ], Response::HTTP_OK);
    }

}
