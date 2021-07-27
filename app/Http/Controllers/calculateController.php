<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use Carbon\Carbon; 

class calculateController extends BaseController
{   
    /**
     * get diff days and week from two diffrent date
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function getData(Request $request){
        $input = $request->all();
		
		$rules = array(
		'from_date'=>['required', 'date_format:Y-m-d H:i:s'],
		'to_date'=>['required', 'date_format:Y-m-d H:i:s'],
		'from_timezone'=>['required','timezone'],
		'to_timezone'=>['required','timezone'],
		); // validation rule
		$validator = Validator::make($request->all(),$rules);
        if($validator->fails()){ // if data not valid
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $from_date = $request->input('from_date'); // get from date from request
    	$to_date = $request->input('to_date'); // get to date from request
		$from_timezone = $request->input('from_timezone'); // get from date timezone
    	$to_timezone = $request->input('to_timezone'); // get to date timezone
		$to = Carbon::createFromFormat('Y-m-d H:i:s', $from_date,$to_timezone); // create date carbon fromat with datetime and timezone
		$from = Carbon::createFromFormat('Y-m-d H:i:s', $to_date,$from_timezone);
		$diff_in_hours = $to->diffInDays($from); // calculate days
        $diff_in_weeks = $to->diffInWeeks($from);  // calculate weeks
        $response_array = array('hours' => $diff_in_hours, 'weeks'=> $diff_in_weeks); // set response

        return $this->sendResponse($response_array, 'Successfully response');
    }
}
