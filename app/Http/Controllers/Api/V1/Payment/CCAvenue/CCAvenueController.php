<?php

namespace App\Http\Controllers\Api\V1\Payment\CCAvenue;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\ApiController;
use App\Models\Payment\UserWalletHistory;
use App\Models\Payment\DriverWalletHistory;
use App\Transformers\Payment\WalletTransformer;
use App\Transformers\Payment\DriverWalletTransformer;
use App\Http\Requests\Payment\AddMoneyToWalletRequest;
use App\Transformers\Payment\UserWalletHistoryTransformer;
use App\Transformers\Payment\DriverWalletHistoryTransformer;
use App\Models\Payment\UserWallet;
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use App\Base\Constants\Setting\Settings;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Jobs\NotifyViaMqtt;
use App\Base\Constants\Masters\PushEnums;
use App\Models\Payment\OwnerWallet;
use App\Models\Payment\OwnerWalletHistory;
use App\Transformers\Payment\OwnerWalletTransformer;
use App\Models\Request\Request as RequestModel;
use Kreait\Firebase\Database;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Jobs\Notifications\SendPushNotification;
use Kishnio\CCAvenue\Payment as CCAvenueClient;
include 'Crypto.php';

/**
 * @group Paystack Payment Gateway
 *
 * Payment-Related Apis
 */
class CCAvenueController extends ApiController
{

     public function __construct(Database $database)
    {
        $this->database = $database;
    }
    /**
     * Initialize Payment
     * 
     * 
     * 
     * */
    public function initialize(Request $request){

$merchant_data = '';
$working_key = '679B1A4387D10902995FC11DE9DC7B6C';  //replace with your WORKING_KEY
$access_code = 'AVWH88JF34BF85HWFB  '; //REPLACE WITH YOUR ACCESS CODE
$merchant_id = '987718'; //REPLACE WITH YOUR MERCHANT_ID
$response_url = 'tagxi-server.ondemandappz.com/api/v1/ccavenue/webhook'; //Redirect URL or CANCEL URL
$order_id = "ORD" . rand(10000, 99999999) . time(); //GENERATE RANDOM ORDER ID
$amount = $request->amount;

foreach ($_POST as $key => $value) {
    $merchant_data .= $key . '=' . $value . '&';
}
// // randon and unique order id with time
// $merchant_data .= "order_id=" . $order_id;

$merchant_data .= 'merchant_id=' . $merchant_id . '&';
$merchant_data .= 'order_id=' . $order_id . '&';
$merchant_data .= 'redirect_url=' . $response_url . '&';
$merchant_data .= 'amount='.$amount.'&';
$merchant_data .= 'currency=INR&';

$encrypted_data = encryptCC($merchant_data, $working_key);

// create json of encrypted data and access code
$access_code = urlencode($access_code);
$encrypted_data = urlencode($encrypted_data);

$data = [
    'enc_val' => $encrypted_data,
    'access_code' => $access_code,
];

    
return response()->json($data);


    }


    public function webhook(Request $request){

        $response = $request->all();

        Log::info("webhook response");

        Log::info($response);

    }


    
}
