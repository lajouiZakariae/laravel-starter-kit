<?php

namespace App\Services;

use App\Data\Mail\UserMailData;
use App\Mail\UserRegisteredMail;
use Illuminate\Support\Facades\Mail;

class UserMailerService {
    public function __construct() {}

    public function sendUserRegisteredEmailForUser(UserMailData $userMailData): void {
        Mail::to($userMailData->email)->send(new UserRegisteredMail($userMailData));
    }
}
