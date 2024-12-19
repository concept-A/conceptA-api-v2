<?php

namespace App\Http\Controllers\Fees;

use Exception;
use App\Models\Fee;
use App\Models\User;
use App\Models\Profile;
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
        $allFees =  Fee::all();
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
        ->where('user_id', $request->id)
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

    
    
    //  /*********
    //   * payStack payment
    //   for Auth access only
    //   **********************/
     public function payStack(Request $request)
        {   
            $user = User::findOrFail($request->user_id);
        if( $request->amount < 2000){
            return response()->json(['message'=>'invalid amount, pls comfirm '],400);
         }
            
         try {
          //  $student = Auth();
            $email = $request->email;
            $amount = $request->amount;
            $plan = $request->plan;
            $url = "https://api.paystack.co/transaction/initialize";

              // Additional data to pass along with the payment
            $metadata = [
                'custom_fields'=>[ 
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                'user_id' => $user->id,
                'payment_status' =>  $request->payment_status,
                ]
            ];

            $fields = [
                'email' => $email,
                'amount' => $amount,
                'plan'=> $plan,
                'metadata' => $metadata,
            ];

        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
        //     "Cache-Control" => "no-cache",
        // ])->timeout(30)->post($url, $fields);
        
        $response = Http::withHeaders([
           
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            // "Authorization" => "Bearer sk_test_d95812d6e2776b0d1460b5d18b3fed4d92501b6f",
            "Cache-Control" => "no-cache",
        ])->timeout(30)->withOptions([
            'verify' => false,
        ])->post($url, $fields);

        // Decode the response
        $responseData = $response->json();
        // Capture the payment reference from the response
        $paymentReference = $responseData['data']['reference']; // This is the unique reference for the payment

        // Store fee details in your database
        $fees = new Fee();
        $fees->user_id = $user->id;
        $fees->payment_id = $paymentReference; // You should use the reference here, not a static value
        $fees->amount = $request->amount;
        $fees->payment_status = 0; // pending or similar
        $fees->save();

    
        return response()->json( [ 'response'=>$responseData, 'message'=>'payment successful '],200);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'message' => 'payment failed.'], 422);
    }
    } 
     


    public function payStackWebhook(Request $request)
    {
        $payload = $request->getContent();
        // $paystackSecret = env('PAYSTACK_SECRET_KEY');
        $paystackSecret = 'sk_test_d95812d6e2776b0d1460b5d18b3fed4d92501b6f'; // Replace with your actual secret key
        $paystackHeader = $request->header('x-paystack-signature');
    
        // Verify the Paystack webhook signature
        if ($this->isValidPaystackWebhook($payload, $paystackHeader, $paystackSecret)) {
            $eventData = $request->json('data');
            $eventType = $request->json('event');
            $metadata = $eventData['metadata'] ?? [];
    
            if ($eventType === 'charge.success') {
                 // Extract user_id from metadata
                    $userId = $metadata['user_id'] ?? null;
                    if ($userId) {
                        // Handle successful payment
                        $fees = Fee::where('payment_id', $eventData['reference'])->first();
                        $profile = Profile::where('user_id', $userId)->first();
                        if ($fees) {
                            $fees->payment_status = 'successful'; // Update payment status
                            $fees->save();
                        }
                        if ($profile) {
                            $profile->subscription = 1; // Update subscription status
                            $profile->save();
                        }
        
                        return response()->json(['status' => 'Payment successful','profile'=>$profile], 200);
                    }
            }elseif($eventType === 'subscription.expired'){
                $email = $eventData['customer']['email']; // Notify user or mark subscription as expired in your database
                return response()->json(['status' => 'your subscription has expired'], 200);
                
            } elseif ($eventType === 'charge.failure') {
                // Handle failed payment
                return response()->json(['status' => 'Payment failed'], 400);
            }
        } else {
            return response()->json(['status' => 'Invalid webhook signature'], 400);
        }
    }
    
    private function isValidPaystackWebhook($payload, $signature, $secret)
    {
        $computedSignature = hash_hmac('sha512', $payload, $secret);
        return hash_equals($computedSignature, $signature);
    }

    


    public function delete($id)
    {
        $fees =  Fee::find($id);
        $fees->delete();
        return response()->json('fees deleted successful');
    }

}
