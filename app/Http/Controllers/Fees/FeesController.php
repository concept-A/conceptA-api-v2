<?php

namespace App\Http\Controllers\Fees;

use Exception;
use App\Models\Fees;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;

class FeesController extends Controller
{
   // public $amount= 50000;
     
    //  public function __construct()
    // {
    //  $this->middleware(['admin','staff'])->only([ 'filter','delete','show']);
   
    // }
 
    // show all paid fees
    public function allPayment()
    {
        $allFees =  Fees::all();
        return response()->json(['feess'=>$allFees],200);
    }


        /*********
         * TODO inplement the view/show payment status, 
         verifying the RRR through the remiter API (for student)
        i*******/


        /*********
        for admins access only
        filter for admin to find student fees
        ****************/

    public function filter(Request $request)
    {
        $request->validate([
            'matric_number'=>$request->matric_number,
            'payment_id'=>$request->payment_id,
        ]);
        $fees = DB::table('feess')
        ->where('user_id', $request->matric_number)
        ->where('payment_id', $request->payment_id)
        ->get();
        return response()->json(['fees'=>$fees],200);
    }


    
     // for admins access only
    public function remita(Request $request)
    {
       
        if( $request->amount >= 2000){

            $student_id =$request->student_id;
            $current_session = $request->current_session;
            $payment_id = $request->payment_id;
            $amount = $request->amount;
            $payment_type = $request->payment_type;
            $payment_status = $request->payment_status;
            $semester =$request->semester;  
            return response()->json(['message'=>'payment succeful '],200);
        }else{
            return response()->json(['message'=>'invalid amount '],400);
     
    }
    }

    
    
     /*********
      * payStack payment
      for Auth access only
      **********************/
     public function payStack(Request $request)
        {   
            $user = User::with('profiles')->find(1);
        if( $request->amount < 2000){
            return response()->json(['message'=>'invalid amount, pls comfirm '],400);
         }
            
         try {
          //  $student = Auth();
            $email = $user->profile->email;
            $amount = $request->amount*100;
            $url = "https://api.paystack.co/transaction/initialize";

              // Additional data to pass along with the payment
            $metadata = [
                'custom_fields'=>[ 
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                'user_id' => $user->id,
                'payment_status' =>  $request->payment_status,]
            ];

            $fields = [
                'email' => $email,
                'amount' => $amount,
                'metadata' => $metadata,
            ];

        $response = Http::withHeaders([
            "Authorization" => "Bearer ".config("services.paystack.secret_key"),
            "Cache-Control" => "no-cache",
        ])->timeout(30)->post($url, $fields);

        // Handle the response as needed (e.g., redirect to payment page)
        $response = $response->json();
                $fees = new Fees();
                $fees->user_id =$user->id;
                $fees->payment_id = $response['data']['reference'];
                $fees->amount = $request->amount;
                $fees->payment_status = 'pending';
                $fees->save();
    
        return response()->json( [ 'response'=>$response, 'message'=>'payment successful '],200);
         }catch(Exception $e){
        return response()->json(['error' => $e->getMessage(),'message'=>'payment failled.'], 422);
         }
    } 
     



    public function payStackWebhook(Request $request)
    {
        //Get Payload
        $payload = $request->getContent();
        // $payload = $request->all();

        // Verify the Paystack webhook signature
        $paystackSecret = config('services.paystack.secret_key');
        $paystackHeader = $request->header('x-paystack-signature');

        //
        if ($this->isValidPaystackWebhook($payload, $paystackHeader, $paystackSecret)) {
            // Handle the webhook event based on the event type
            $eventData = $request->json('data');
            $eventType = $request->json('event');

            if ($eventType === 'charge.success') {
    
                // Handle successful payment event
                // Example: Update order status or send confirmation email

                // $fees = new Fees();
                // $fees->student_id =$eventData['customer']['metadata']['student_id'];
                // $fees->current_session = $eventData['metadata'];
                // $fees->payment_id = $eventData['customer']['metadata']['reference'];
                // $fees->amount = $eventData['customer']['metadata']['amount'];
                // $fees->payment_type = $eventData['customer']['metadata']['payment_type'];
                // $fees->payment_status = 'Paid';
                // $fees->semester =$eventData['customer']['metadata']['semester'];
                // $fees->save();
    
                $userStatus = User::with('profile')->find(auth()->id());

                $userStatus->profile->subscription =1;
                $userStatus->save();
    
              return response()->json(['status' => 'payment succeessful '],200);
            } elseif ($eventType === 'charge.failure') {
                // Handle failed payment event
                // Example: Notify user about payment failure
                 return response()->json(['status' => 'payment failed '],400);
               //  Log::info('message', ['status' => 'payment failed ']);
            }
          //  Log::info('message', ['success' => 'Webhook received']);
        } else {
            Log::info('message', ['error' => 'Invalid webhook signature'],400);
        }

     
    }

    private function isValidPaystackWebhook($payload, $signature, $secret)
    {
        $computedSignature = hash_hmac('sha512', $payload, $secret);
        return $computedSignature === $signature;
        // return hash_equals($hash, $signature);
    }

    
    public function delete($id)
    {
        $fees =  Fees::find($id);
        $fees->delete();
        return response()->json('fees deleted successful');
    }

}
