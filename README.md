
I created API for calculate days and weeks from two diffreent datetime and timezone.
The author put the source code up for it https://github.com/sonalkeraliya/ITC515-assignment4/tree/master/ITC515-Debugging

**Step 1: Create the project by composer**
 
  
  composer create-project laravel/laravel testgreenButler
  
  cd testgreenButler
  
  php artisan serve

**Step2 : Set up CRUD routes and controllers**

  Add getDaysweeks  API routes:
  
  Route::post('GetCntDaysWeeks', [calculateController::class, 'getData']);
  
  First make controller by php artisan make:controller calculateController

  use Carbon library for calculate days and weeks.
  
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
          ); 
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
          
**Step3 :  Customize error and sucess response by making BaseController**

  First make controller by command : php artisan make:controller BaseController
  
  /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
     return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }


**Step4 :**  **Test our API with Postman**

  Install Postman
  
  Pass value from_date,to_date,from_timezone,to_timezone in Request Data

  Case1 : pass empty value validation fire
  
  Screenshot : 
  ![case1](https://user-images.githubusercontent.com/36131504/127260897-95f531f5-5822-4b26-b10c-e33e609ea96e.PNG)

  Case2 : check fromdate,todate,fromtimezone and totimezone format validation
  
  Screenshot:![case2](https://user-images.githubusercontent.com/36131504/127260945-409f6528-bdf5-454b-8ce7-ea831b3a134e.PNG)

  
   case3 : pass data valid format and calculate days and weeks
   
  Screenshot:
  
  ![cas3](https://user-images.githubusercontent.com/36131504/127260984-f3bceef2-db87-4303-8187-eeb3745e37ce.PNG)

  **Step4 : make script for Checking Diffrent test scenario**
  
  make testcase file using this command : php artisan make:test CalcDayWeekAPITest
  
  Below this Code in CalcDayWeekAPITest File
  
    /**
     * check required all data
     *
     * @return void
     */
    public function testAllRequestdataRequired()
    {
       $response = $this->call('POST', '/GetCntDaysWeeks', ['from_date' => '','to_date' => '','from_timezone' => '','to_timezone' => '']);
       $this->assertEquals(404, $response->status());

    }
    /**
     * check datetime proper format for fromdate param
     *
     * @return void
     */
    public function testcheckFromdateFormat()
    {
      $this->post('api/GetCntDaysWeeks',['from_date'=>'21/11/11','to_date' => '2021-01-01 00:00:00',
      'from_timezone' => 'Africa/Conakry','to_timezone' => 'Africa/Dakar'])
      ->assertStatus(404)
      ->assertExactJson(['message'=>'Validation Error.','success'=>false,
      'data' => ['from_date' => array ( 0 => 'The from date does not match the format Y-m-d H:i:s.')]]) ;

    }
  
      /**
     * check datetime proper format for to_date param
     *
     * @return void
     */
    public function testcheckTodateFormat()
    {
      $this->post('api/GetCntDaysWeeks',['from_date'=>'2021-01-01 00:00:00','to_date' => '21/11/11',
      'from_timezone' => 'Africa/Conakry','to_timezone' => 'Africa/Dakar'])
      ->assertStatus(404)
      ->assertExactJson(['message'=>'Validation Error.','success'=>false,
      'data' => ['to_date' =>
         array ( 0 => 'The to date does not match the format Y-m-d H:i:s.')]
        ]) ;

    }
    
      /**
     * check timezone proper format for from_timezone param
     *
     * @return void
     */
    public function testcheckFromTimezoneFormat()
    {
      $this->post('api/GetCntDaysWeeks',['from_date'=>'2021-01-01 00:00:00','to_date' => '2021-01-01 00:00:00','from_timezone' => 'Africa','to_timezone' => 'Africa/Conakry'])
      ->assertStatus(404)
      ->assertExactJson(['message'=>'Validation Error.','success'=>false,
      'data' => ['from_timezone' =>
         array ( 0 => 'The from timezone must be a valid timezone.')]
        ]) ;

    }
        /**
       * check  timezone proper format for to_timezone param
       *
       * @return void
       */
      public function testcheckToTimezoneFormat()
      {
        
        $this->post('api/GetCntDaysWeeks',['from_date'=>'2021-01-01 00:00:00','to_date' => '2021-01-01 00:00:00','from_timezone' => 'Africa/Conakry','to_timezone' => 'Africa'])
      ->assertStatus(404)
      ->assertExactJson(['message'=>'Validation Error.','success'=>false,
      'data' => ['to_timezone' =>
         array ( 0 => 'The to timezone must be a valid timezone.')]
        ]) ;

      }
        /**
       * check  proper return calculate days and weeks
       *
       * @return void
       */
      public function testcheckCalDaysWeeks()
      {
        // all data valid pass and check days and weeks proper
        $this->post('api/GetCntDaysWeeks',['from_date'=>'2020-01-01 00:00:00','to_date' => '2021-01-01 00:00:00',
        'from_timezone' => 'Africa/Conakry','to_timezone' => 'Africa/Dakar'])
        ->assertStatus(200)
        ->assertExactJson(['success'=>true,'message'=>'Successfully response',
        'data' => ['hours' => 366, 'weeks'=> 52   ]
          ]) ;

      }



**Step5:     Run the test case**

    Follow this command for run the all test cases.
    
    php artisan test
    
    Screenshot:
  
  ![runtest](https://user-images.githubusercontent.com/36131504/127261387-e035e81e-ab88-4f60-8e19-9d162e74a5b1.PNG)
  
  
  


**For FrontEndDeveloper :**
  Request parameter validation : 
  
  from_date and to_date must be format of Y-m-d H:i:s.
  
  from_timezone and to_timezone must be format of Timezone String Format such as Africa/Asmera




**Resources :**

  Basic Setup laravel :  https://laravel.com/docs/8.x/installation
  
  For unit testing : https://laravel.com/docs/8.x/testing
  
  For Calaculating days and Week use third party library Carbon :
  https://carbon.nesbot.com/docs/
   
   For Timezone String :   https://www.php.net/manual/en/timezones.others.php

