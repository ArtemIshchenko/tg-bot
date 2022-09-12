<?php

namespace App\Http\Controllers;

//use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
//use GuzzleHttp\Client;
//use GuzzleHttp\Pool;
//use GuzzleHttp\Psr7\Request as ghRequest;
//use GuzzleHttp\RequestOptions;
//use Psr\Http\Message\ResponseInterface;
//use GuzzleHttp\Exception\ClientException;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public $apiUrl = "https://api.telegram.org/bot889673981:AAEr8c6SXUan0-apuglTuFVt-M37QbBSvlM/";

    public function index(Request $request) {
        return utf8_encode("U+1F634");
    }
}
