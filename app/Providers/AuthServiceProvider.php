<?php

namespace App\Providers;
namespace App\Providers;




use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
//use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

    

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            // Extract the query parameters
            $spaUrl = env('FRONT_URL', 'https://concepta.com.ng') . '/verify?email_verify_url=' . urlencode($url);
        
            return (new MailMessage)
                ->subject('Verify Your Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $spaUrl);
        });
        

       
        ResetPassword::createUrlUsing(function (User $user, string $token) {
           return env('FRONT_URL').'/reset?token='.$token;
       });

         
      
    }
}
