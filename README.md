# Giới thiệu thông tin
- Package  hỗ trợ thưc thi các API theo tài liệu: https://docs.nextpay.vn
    + Thanh toán ngay
    + Lấy danh sách ngân hàng hỗ trợ thanh toán bằng QR-Code
    + Thanh toán bằng QR-Code
    + Lấy danh sách ngân hàng hỗ trợ trả góp
    + Thông tin phí trả góp
    + Tạo yêu cầu thanh toán trả góp
    + Kiểm tra giao dịch
# Cài đặt và loading
Cài bằng composer
```sh
composer require payon/paymentgateway
```
Include vào file PHP
```php
<?php
use Payon\PaymentGateway\PayonHelper;
//or require
require 'path/to/paymentgateway/src/PayonHelper.php';
```
# Code mẫu
- Các thanh số truyền vào hàm PayonHelper
    + $mc_id: MC_ID - ID Merchant để định danh khách hàng trên PayOn
    + $app_id: APP_ID - ID ứng dụng để định danh ứng dụng tích hợp
    + $secret_key: MC_SECRET_KEY - Khóa để thực hiện mã hóa tham số data trong các hàm nghiệp vụ
    + $url: URL_API - Đường dẫn API
    + $http_auth: MC_AUTH_USER - Tên Auth basic
    + $http_auth_pass: MC_AUTH_PASS - Mật khẩu Http Auth basic
- Thanh toán ngay
```php
<?php

use Payon\PaymentGateway\PayonHelper;

$payon = new PayonHelper($mc_id, $app_id, $secret_key, $url, $http_auth, $http_auth_pass);
$data = [
    "merchant_request_id" => $merchant_request_id  //Type String: Mã đơn hàng Merchant được tạo từ yêu cầu thanh toán
    "amount" => 10000, //Type Int: Giá trị đơn hàng. Đơn vị: VNĐ
    "description" => 'Thanh toán đơn hàng KH Tran Van A', //Type String: Mô tả thông tin đơn hàng
    "url_redirect" => 'https://payon.vn/', //Type String: Đường link chuyển tiếp sau khi thực hiện thanh toán thành công
    "url_notify" => 'https://payon.vn/notify', //Type String: Đường link thông báo kết quả đơn hàng
    "url_cancel" => 'https://payon.vn/cancel', //Type String: Đường link chuyển tiếp khi khách hàng hủy thanh toán
    "customer_fullname" => 'Tran Van A', //Type String: Họ và tên khách hàng
    "customer_email" => 'tranvana@payon.vn', //Type String: Địa chỉ email khách hàng
    "customer_mobile" => '0123456789', //Type String: Số điện thoại khách hàng
];
$response = $payon->CreateOrderPaynow($data);
if($response['error_code'] = "00"){
    // Call API thành công, tiếp tục xử lý
} else {
    //Có lỗi xảy ra check lỗi trả về
}
```
- Lấy danh sách ngân hàng hỗ trợ thanh toán bằng QR-Code
```php
<?php

use Payon\PaymentGateway\PayonHelper;

$payon = new PayonHelper($mc_id, $app_id, $secret_key, $url, $http_auth, $http_auth_pass);
$response = $payon->GetQrBankCode();
if($response['error_code'] = "00"){
    // Call API thành công, tiếp tục xử lý
} else {
    //Có lỗi xảy ra check lỗi trả về
}
```
- Tạo yêu cầu thanh toán bằng QR-Code
```php
<?php

use Payon\PaymentGateway\PayonHelper;

$payon = new PayonHelper($mc_id, $app_id, $secret_key, $url, $http_auth, $http_auth_pass);
$data = [
    "merchant_request_id" => $merchant_request_id  //Type String: Mã đơn hàng Merchant được tạo từ yêu cầu thanh toán
    "amount" => 10000, //Type Int: Giá trị đơn hàng. Đơn vị: VNĐ
    "description" => 'Thanh toán đơn hàng KH Tran Van A', //Type String: Mô tả thông tin đơn hàng
    "bank_code" => "TCB", //Type String: Mã ngân hàng thanh toán.
    "url_redirect" => 'https://payon.vn/', //Type String: Đường link chuyển tiếp sau khi thực hiện thanh toán thành công
    "url_notify" => 'https://payon.vn/notify', //Type String: Đường link thông báo kết quả đơn hàng
    "url_cancel" => 'https://payon.vn/cancel', //Type String: Đường link chuyển tiếp khi khách hàng hủy thanh toán
    "customer_fullname" => 'Tran Van A', //Type String: Họ và tên khách hàng
    "customer_email" => 'tranvana@payon.vn', //Type String: Địa chỉ email khách hàng
    "customer_mobile" => '0123456789', //Type String: Số điện thoại khách hàng
];
$response = $payon->CreateQRCode($data);
if($response['error_code'] = "00"){
    // Call API thành công, tiếp tục xử lý
} else {
    //Có lỗi xảy ra check lỗi trả về
}
```
- Lấy danh sách ngân hàng hỗ trợ trả góp
```php
<?php

use Payon\PaymentGateway\PayonHelper;

$payon = new PayonHelper($mc_id, $app_id, $secret_key, $url, $http_auth, $http_auth_pass);
$response = $payon->GetBankInstallment();
if($response['error_code'] = "00"){
    // Call API thành công, tiếp tục xử lý
} else {
    //Có lỗi xảy ra check lỗi trả về
}
```
- Thông tin phí trả góp
```php
<?php

use Payon\PaymentGateway\PayonHelper;

$payon = new PayonHelper($mc_id, $app_id, $secret_key, $url, $http_auth, $http_auth_pass);
$data = [
    "amount" => 10000, //Type Int: Giá trị đơn hàng. Đơn vị: VNĐ
    "bank_code" => "TCB", //Type String: Mã ngân hàng thanh toán.
    'cycles' => 3, // Type Int: Số kỳ (tháng) trả góp.
    'card_type' => "VISA" //Type String: Loại thẻ thanh toán:VISA, MASTERCARD, JCB.
];
$response = $payon->getFee($data);
if($response['error_code'] = "00"){
    // Call API thành công, tiếp tục xử lý
} else {
    //Có lỗi xảy ra check lỗi trả về
}
```
- Tạo yêu cầu thanh toán trả góp
```php
<?php

use Payon\PaymentGateway\PayonHelper;

$payon = new PayonHelper($mc_id, $app_id, $secret_key, $url, $http_auth, $http_auth_pass);
$data = [
    "merchant_request_id" => $merchant_request_id  //Type String: Mã đơn hàng Merchant được tạo từ yêu cầu thanh toán
    "amount" => 10000, //Type Int: Giá trị đơn hàng. Đơn vị: VNĐ
    "description" => 'Thanh toán đơn hàng KH Tran Van A', //Type String: Mô tả thông tin đơn hàng
    "bank_code" => "DAB", //Type String: Mã ngân hàng thanh toán.
    "cycle" => 3, // Type Int: Số kỳ (tháng) trả góp.
    "card_type" => "VISA" //Type String: Loại thẻ thanh toán:VISA, MASTERCARD, JCB.
    "userfee" => 1, //Type Int:	Chọn người chịu phí: 1. Người mua chịu phí thanh toán 2. Người bán chịu phí thanh toán.
    "url_redirect" => 'https://payon.vn/', //Type String: Đường link chuyển tiếp sau khi thực hiện thanh toán thành công
    "url_notify" => 'https://payon.vn/notify', //Type String: Đường link thông báo kết quả đơn hàng
    "url_cancel" => 'https://payon.vn/cancel', //Type String: Đường link chuyển tiếp khi khách hàng hủy thanh toán
    "customer_fullname" => 'Tran Van A', //Type String: Họ và tên khách hàng
    "customer_email" => 'tranvana@payon.vn', //Type String: Địa chỉ email khách hàng
    "customer_mobile" => '0123456789', //Type String: Số điện thoại khách hàng
];
$response = $payon->createOrderInstallment($data);
if($response['error_code'] = "00"){
    // Call API thành công, tiếp tục xử lý
} else {
    //Có lỗi xảy ra check lỗi trả về
}
```
- Kiểm tra giao dịch trước khi thực hiện cập nhật trạng thái cho đơn hàng
```php
<?php

use Payon\PaymentGateway\PayonHelper;

$payon = new PayonHelper($mc_id, $app_id, $secret_key, $url, $http_auth, $http_auth_pass);
$merchant_request_id = $merchant_request_id  //Type String: Mã đơn hàng Merchant được tạo từ yêu cầu thanh toán
$response = $payon->CheckPayment($merchant_request_id);
if($response['error_code'] = "00"){
    // Call API thành công, tiếp tục xử lý
} else {
    //Có lỗi xảy ra check lỗi trả về
}
```
- Bypass SSL_VERIFYPEER
```php
<?php

use Payon\PaymentGateway\PayonHelper;

$payon = new PayonHelper($mc_id, $app_id, $secret_key, $url, $http_auth, $http_auth_pass);
$payon->ssl_verifypeer = false;

```