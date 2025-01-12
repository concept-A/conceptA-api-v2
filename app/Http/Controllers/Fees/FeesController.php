<?php

namespace App\Http\Controllers\Fees;

use Exception;
use App\Models\Fee;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Notifications\Notification;

// for middleware in controller
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class FeesController extends Controller
{

   public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'admin', only: [ 'index']),
        ];
    }

    // show all paid fees
    public function index()
    {
        $allFees =  Fee::all();
        return response()->json(['Payments'=>$allFees],200);
        
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

    
 
    // PayStack Payment Integration
    public function payStack(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        if ($request->amount < 2000) {
            return response()->json(['message' => 'Invalid amount, please confirm'], 400);
        }

        try {
            $fields = [
                'email' => $user->email,
                'amount' => $request->amount, 
                'plan' => $request->plan,
                'metadata' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'user_id' => $user->id,
                    'payment_status' => $request->payment_status,
                ],
            ];

            // $response = Http::withHeaders([
            //     'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            //     'Cache-Control' => 'no-cache',
            // ])->post('https://api.paystack.co/transaction/initialize', $fields);
    
            $response = Http::withHeaders([
           
                        'Authorization' => 'Bearer ' .env('PAYSTACK_SECRET_KEY'),
                        // "Authorization" => "Bearer sk_test_d95812d6e2776b0d1460b5d18b3fed4d92501b6f",
                        "Cache-Control" => "no-cache",
                    ])->timeout(30)->withOptions([
                        'verify' => false,
                    ])->post('https://api.paystack.co/transaction/initialize', $fields);
                    
            $responseData = $response->json();
            $paymentReference = $responseData['data']['reference'];

            Fee::create([
                'user_id' => $user->id,
                'payment_id' => $paymentReference,
                'amount' => $request->amount,
                'payment_status' => 'pending', // Pending
            ]);

            return response()->json(['response' => $responseData, 'message' => 'Payment initialized'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Payment failed'], 422);
        }
    }

  
    // PayStack Webhook
    public function payStackWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('x-paystack-signature');
        $secret = env('PAYSTACK_SECRET_KEY');

        // Create an empty file in the public directory to confirm webhook call
        // file_put_contents(public_path('paystack_webhook_called.txt'), now());

        if ($this->isValidPaystackWebhook($payload, $signature, $secret)) {
            $eventData = $request->json('data');
            $eventType = $request->json('event');
            $metadata = $eventData['metadata'] ?? [];

            if ($eventType === 'charge.success') {
                $userId = $metadata['user_id'] ?? null;
                if ($userId) {
                    $fees = Fee::where('payment_id', $eventData['reference'])->first();
                    $profile = Profile::where('user_id', $userId)->first();
                    $user = User::find($userId);

                    if ($fees) {
                        $fees->payment_status = 'successful';
                        $fees->save();
                    }

                    if ($profile) {
                        $profile->subscription = 1;
                        $profile->save();
                    }

                    if ($user) {
                        Notification::send($user, new PaymentSuccessfulNotification($fees));
                    }

                    return response()->json(['status' => 'Payment successful'], 200);
                }
            } elseif ($eventType === 'subscription.expired') {
                $userId = $metadata['user_id'] ?? null;
                if ($userId) {
                    $profile = Profile::where('user_id', $userId)->first();

                    if ($profile) {
                        $profile->subscription = 0; // Set subscription to expired
                        $profile->save();

                        Log::info('Subscription expired for user ID: ' . $userId);
                    }
                }

                return response()->json(['status' => 'Subscription expired and profile updated'], 200);
            } elseif ($eventType === 'charge.failure') {
                return response()->json(['status' => 'Payment failed'], 400);
            }
        }

        return response()->json(['status' => 'Invalid webhook signature'], 400);
    }

    private function isValidPaystackWebhook($payload, $signature, $secret)
    {
        return hash_equals(hash_hmac('sha512', $payload, $secret), $signature);
    }


    // Delete Fee Record
    public function delete($id)
    {
        $fees =  Fee::find($id);
        $fees->delete();
        return response()->json('fees deleted successful');
    }

}
