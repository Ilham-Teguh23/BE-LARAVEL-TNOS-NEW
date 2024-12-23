<?php

namespace App\Http\Controllers\Api\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xendit\Xendit;

class ConfigXenditController extends Controller
{
    protected $serverKey;
    protected $callback_token;


    public function __construct()
    {
        $this->serverKey = config('xendit.xendit_development_key');
        $this->callback_token = config('xendit.xendit_development_callback_token');
    }

    public function apiKeyXendit()
    {
        Xendit::setApiKey($this->serverKey);
        return [
            "serverKey" =>  $this->serverKey,
            "callback_token" =>  $this->callback_token,
        ];
    }
}
