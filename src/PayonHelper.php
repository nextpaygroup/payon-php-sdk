<?php

namespace Payon\PaymentGateway;

use Payon\PaymentGateway\PayonEncrypto;

class PayonHelper
{
    private $mc_id;
    private $app_id;
    private $secret_key;
    private $url;
    private $http_auth;
    private $http_auth_pass;
    public $ssl_verifypeer;
    public $ref_code;
    public function __construct(
        string $mc_id, 
        string $app_id, 
        string $secret_key, 
        string $url, 
        string $http_auth, 
        string $http_auth_pass,
        bool $ssl_verifypeer = true)
    {
        $this->mc_id = $mc_id;
        $this->app_id = $app_id;
        $this->secret_key = $secret_key;
        $this->url = $url;
        $this->http_auth = $http_auth;
        $this->http_auth_pass = $http_auth_pass;
        $this->ssl_verifypeer = $ssl_verifypeer;
        $url_base = sprintf(
            '%s://%s',
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http',
            isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (gethostname() ?: 'localhost')
        );
        $this->ref_code = 'MCAPI-LV-'. $url_base;
    }

    /**
     * Thanh toán ngay
     * @param $data
     * @return mixed
     */
    function CreateOrderPaynow($data)
    {
        $data['merchant_id'] = (int)$this->mc_id;
        return $this->BuildPayment("createOrderPaynow", $data);
    }

    /**
     * Tạo yêu cầu thanh toán truyền thêm phương thức thanh toán
     * @param $data
     * @return mixed
     */
    function createOrderV2($data){
        $data['merchant_id'] = (int)$this->mc_id;
        $data['service_type_code'] = 'PAYNOW';
        $data['currency'] = 'VND';
        return $this->BuildPayment("createOrder", $data);
    }

    /**
     * Kiểm tra giao dịch
     * @param $merchant_request_id: merchant_request_id
     * @return mixed
     */
    function CheckPayment($merchant_request_id)
    {
        $data = array(
            'merchant_request_id' => $merchant_request_id,
        );
        return $this->BuildPayment("checkPayment", $data);
    }

    /**
     * Lấy danh sách ngân hàng hỗ trợ trả góp
     * @return mixed
     */
    function GetBankInstallment()
    {
        $data = array();
        return $this->BuildPayment("getBankInstallmentV2", $data);
    }

    /**
     * Thông tin phí trả góp
     * @param $data
     * @return mixed
     */
    function GetFee($data)
    {
        $data['merchant_id'] = (int)$this->mc_id;
        return $this->BuildPayment("getFeeInstallmentv2", $data);
    }

    /**
     * Tạo yêu cầu thanh toán trả góp
     * @param $data
     * @return mixed
     */
    function CreateOrderInstallment($data)
    {
        $data['merchant_id'] = (int)$this->mc_id;
        return $this->BuildPayment("createOrderInstallment", $data);
    }

    
    /**
     * Danh sách ngân hàng hỗ trợ QR-Code
     * @return mixed
     */
    function GetQrBankCode()
    {
        $data = array(
            "service_type_code" => 'PAYNOW',
            "service_code" => 'PAYNOW_QRLOCALBANK_DYNAMIC',
            "method_code" => 'LOCALBANK'
        );
        return $this->BuildPayment("getQrBankCode", $data);
    }

    /**
     * Tạo thanh toán QR-Code
     * @param $data
     */
    function CreateQRCode($data)
    {
        $data['merchant_id'] = (int)$this->mc_id;
        $data['service_type_code'] = 'PAYNOW';
        $data['service_code'] = 'PAYNOW_QRLOCALBANK_DYNAMIC';
        $data['method_code'] = 'LOCALBANK';
        $data['currency'] = 'VND';
        return $this->BuildPayment("createQRCode", $data);
    }

    /**
     * Validate Notify PayOn
     * @param $data
     * @param $checksum
     */
    function ValidateNotify($data, $checksum)
    {
        $checksum_en = md5($this->app_id . json_encode($data) . $this->secret_key);
        if($checksum_en === $checksum)
        {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    function getServices(){
        $data = [];
        return $this->BuildPayment("getServices", $data);
    }

    function BuildPayment($fnc, $param)
    {
        $data = json_encode($param);
        $crypto = new PayonEncrypto($this->secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($this->app_id . $data . $this->secret_key);
        $bodyPost = array(
            'app_id' => $this->app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, $fnc);
        return $result;
    }

    /**
     * Curl
     * @param $params
     * @param $fnc
     * @return mixed
     */
    function Call($params, $fnc)
    {
        if(substr( $this->url,-1) != '/'){
            $this->url = $this->url.'/';
        }
        $this->url = $this->url.$fnc;
        $agent = $_SERVER["HTTP_USER_AGENT"];
        if(empty($agent))
        {
            $agent = 'not user agent';
        }
        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl, CURLOPT_USERPWD, $this->http_auth . ':' . $this->http_auth_pass);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            )
        );
        $response = curl_exec($curl);
        $resultStatus = curl_getinfo($curl);
        curl_close($curl);
        if($resultStatus['http_code'] == 200 && isset($response) )
        {
            $response = json_decode($response, true);
            return $response;
        } else{
            return false;
        }
    }
}
