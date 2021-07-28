<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CalcDayWeekAPITest extends TestCase
{
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
}
