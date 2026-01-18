<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class MIMSmsModel extends Model
{
    use HasFactory;

    
    public static function sendSms($mobileNumber, $message)
    {
        $data = [
            "UserName" => 'designcodeit@gmail.com',
            "Apikey" => 'RRWG446TJUI2CZZ',
            "MobileNumber" => '88'.$mobileNumber,
            "CampaignId" => 'null',
            "SenderName" => "8809617632515",
            "TransactionType" => "T",
            "Message" => $message
        ];
        
        $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get('https://api.mimsms.com/api/SmsSending/Send', $data);
    }

    public static function sendSmsMultipleUser($mobileNumber, $message)
    {
        $number = explode(", ", $mobileNumber);
       
        $transId = 'MANAbay' . time();
        $data = json_encode([
            "username" => 'manabaysms',
            "password" => '#2Ssv5G)8O9@3Y',
            "apicode" => "5",
            "msisdn" => $number,
            "countrycode" => "880",
            "cli" => "8801958033936",
            "messagetype" => "1",
            "message" => $message,
            "clienttransid" => $transId,
            "tran_type" => "T",
            "bill_msisdn" => "8801958033936",
            "request_type" => "S",
            "rn_code" => "91",
        ]);

        $curl = curl_init();
        $send_array = array(
            CURLOPT_URL => "https://corpsms.banglalink.net/bl/api/v1/smsapigw/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "content-type: application/json"
            ),
        );
        curl_setopt_array($curl, $send_array);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }
}
