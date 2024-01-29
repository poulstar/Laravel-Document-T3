# ساخت فرآیند ورود به سرور و آموزش استفاده از SMS قاصدک در کنار تغییر روش passport در شناسایی کاربر و پیاده سازی آن توسط کار با yaml برای نمایش swagger

###### برای شروع ابتدا بهتر است برای ساخت ماژول SMS اقدام کنیم و سپس ادامه کار را پیش بگیریم. سایت <a href="https://ghasedak.me/docs">قاصدک</a> مستندی دارد جهت کار با ساختار آن که می توانیم از آن کمک بگیریم و کار خود را توسعه دهیم. به همین منظور ابتدا در app پوشه ای می سازیم به نام Actions و در آن فایلی ایجاد می کنیم به نام SMS.php و بعد آن ساختار کلاس مربوطه را در آن به صورت زیر به وجود می آوریم.
```bash
<?php

namespace App\Actions;

class SMS
{
    
}

```
###### حال نوبت آن است که برای کار خود الگو مناسب را پیاده سازی کنیم، برای ارسال کد تایید از OTP یا <a href="https://ghasedak.me/docs#VerificationBox">سرویس اعتبار سنجی قاصدک</a> استفاده نماییم. برای این کار می توانیم از نمونه کد php آن استفاده نماییم. فقط لازم است آن را باب میل خود بسازیم. دقت کنید که برای اینکار حتما لازم است تا در سایت قاصدک ثبت نام کنیم و یک قالب پیام اعتبار ماژول SMS ایجاد می کنیم.
```bash
protected const API_KEY = "debd0262b48d2aa9eafafef3e0ab44900ba13167f45a464dc51227ac48ad3799";
protected const TEMPLATE_NAME = 'poulstar';
public static function sendSMS($receptor, $param1, $param2)
{
    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => "https://api.ghasedak.me/v2/verification/send/simple",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPAUTH => CURLAUTH_ANY,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "receptor=" . $receptor . "&template=" . self::TEMPLATE_NAME . "&type=1&param1=" . $param1 . "&param2=" . $param2 . "",
            CURLOPT_HTTPHEADER => array(
                "apikey: " . self::API_KEY,
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
            )
        )
    );
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $response = json_decode($response, true);
        try {
            if ($response['result']['code'] === 200) {
                return true;
            }
        } catch (Exception $e) {
            Log::error(json_encode([$response, self::API_KEY]));
            return $response;
        }
    }
    return false;
}
```
###### دقت کنید در شروع دو property می سازیم، یکی برای نگه داری API Key ما و دیگری برای Template Name ما، به همین دلیل تلاش کنید از API Key و Template Name خود استفاده نمایید چون اعتبار این API Key محدود است و ممکن است کار نکند. دقت کنید که اگر از سرویس رایگان قاصدک استفاده می کنید، امکان استفاده از OTP برای شما وجود دارد و فقط هم به شماره سیم کارت خودتان پیام ارسال می کند و اگر برای شماره غیر شماره ثبت نام درخواست دهید، دچار مشکل می شوید.
###### در تابع sendSMS ما سه پارامتر از می خواهیم، اولی شماره تماس گیرنده، دومی نام گیرنده و سومی کدی که ساخته شده است. در بخش CURLOPT_POSTFIELDS پارامتر های خود را جای می دهیم تا وقتی درخواست اجرا شد و نتیجه بازگشت، بتوانیم ادامه روند را پیش ببریم، دقت کنید اگر از سمت سرور قاصدک جواب با کد 200 بازگشت، ما مطمئن می شویم که SMS به دست کاربر رسیده است و او را به صفحه بعد یا مرحله بعد منتقل می کنیم و در غیر این صورت خطا های مورد نظر هر بخش را چاپ می کنیم.
###### برای موارد فوق باید use هایی انجام دهیم که به صورت زیر آن را اضافه می کنیم.
```bash
use Exception;
use Illuminate\Support\Facades\Log;
```
###### حال نوبت آن رسیده تا برای کار کاربر های خود یک controller منحصر به فرد بسازیم، از همین رو دستور زیر را اجرا می کنیم.
```bash
php artisan make:controller UserController
```
###### حال که controller مورد نظر خود را ساختیم، نوبت آن است تا وارد پوشه routes بشویم و فایل api.php را باز کنیم تا route های مربوط به API ما جداگانه ساخته شوند، دقت کنید وقتی یک route در این فایل می سازید، برای دسترسی به آن باید در آدرس url، حتما قبل هر مورد یک api/ بگذارید. حال برای کار خود می رویم و یک route برای گرفتن کد تایید می سازیم.
```bash
Route::get('get-verify-code/{phone}', [UserController::class, 'getLoginCode']);
```
###### برای اینکه بتواند UserController را بشناسد، مسیر آن را use می کنیم.
```bash
use App\Http\Controllers\UserController;
```
###### حال نوبت آن است که به UserController برویم و تابع getLoginCode را بسازیم.
```bash
public function getLoginCode($phone)
{
    $user = User::where('phone', $phone)->first();
    if (!$user) {
        return $this->failResponse([
            'message' => 'User Not Found'
        ], 403);
    }

    $randomCode = Str::random(4);
    $user->verify_code = Hash::make($randomCode);
    $user->save();
    $state = SMS::sendSMS($user->phone, $user->name, $randomCode);
    if ($state) {
        return $this->successResponse([
            'message' => "Check Your Phone",
        ], 200);
    } elseif (!$state) {
        return $this->failResponse([
            'error' => "your request failed",
        ], 500);
    } else {
        return $this->failResponse([
            'error' => $state,
        ], 500);
    }
}
```
###### ابتدا در این تابع بررسی شماره ای که دریافت کرده ایم می پردازیم، اگر شماره تماس وجود نداشت می گوییم اصلا کاربری با این شماره تماس وجود ندارد، در غیر اینصورت کد ما ادامه پیدا می کند. حال نوبت آن است که یک کد تصادفی 4 کاراکتره بسازیم و بلافاصله hash شده آن را در ستون verify_code کاربر ذخیره می کنیم و اجراء ارسال SMS را می سازیم، اگر وضعیت بازگشتی مثبت بود، یعنی کد 200 بازگشت، ما یک پاسخ موفقیت باز می گردانیم و اگر موفق نبود پیام شکست را بازگشت می دهیم.



###### برای کدی که نوشتیم نیاز داریم تا موارد زیر را use کنیم.
```bash
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Actions\SMS;
```
###### برای اینکار و استفاده از کد هایی که نوشتیم، لازم است تا وارد فایل api.yaml شویم و شروع کنیم به ساختار مربوط به ارسال و دریافت کد تایید. از همین رو مرحله به مرحله به جلو پیش می رویم. ما در yaml ی که برای swagger می نویسیم لازم است برای هر نوع فرمی یا نوع نمایشی، یک ساختار یا schemas داشته باشیم. از همین رو در components بخش schemas را ایجاد می کنیم. اولین موردی که نیاز داریم، اینکه هر نوع درخواست با هر نوع method برای سرور ارسال شد، یک نوع مدل نمایش داشته باشد.
```bash
ApiResponse:
    type: object
    properties:
        code:
            type: integer
            format: int32
        type:
            type: string
        message:
            type: string
    xml:
        name: "##default"
```
###### تعیین می کنیم که نوع پاسخ یک object است و property های آن شامل code و type و message است و حالت xml آن را به صورت default در نظر می گیریم.

###### حال چون نیاز داریم تا یک مسیر یا path ارسال data بسازیم، از همین رو بخش paths را به وجود می آوریم و paths مانند components یک بخش مستقل است. در آن مسیر را به صورت زیر می نویسیم.
```bash
paths:
  /api/get-verify-code/{phone}:
    get:
      tags:
        - User
      summery: get login code
      description: get code for login
      parameters:
        - name: phone
          in: path
          description: Phone Number
          required: true
          schema:
            type: string
      responses:
        "200":
          description: Successful operation
          content:
            application/json:
              schema:
                $ref: "#/components/schemas/ApiResponse"
```
###### ابتدا در paths آدرسی که در api.php ساختیم را به عنوان سر فصل می نویسیم. بعد در آن نوع method را تعیین می کنیم که برابر با get گذاشتیم. دقت کنید که method ما باید با method در فایل api.php یکسان باشد. در داخل get ما اول دسته این مسیر را مشخص می کنیم تا به راحتی قبل دسترسی باشد و برای اینکه قابل فهم شود در موقع استفاده، برایش خلاصه  و توضیحات در نظر می گیریم. ما نیاز داریم بر روی path یک پارامتر ارسال شود، پس بخش parameters را باز می کنیم و یک پارامتر با نام پارامتری که در path نوشتیم می سازیم، می توانیم برایش توضیحات در نظر بگیریم که نمایش دهد و همچنین پر کردن پارامتر را برای اجرا اجباری می کنیم و نوع آن را در بخش schema مشخص می کنیم و وقتی هم پاسخ بازگشت، در حالت کد 200 چه توضیحاتی داشته باشد و محتوا آن از کدام نوع باشد را تعیین می کنیم. schema را هم از نوع نمی سازیم، بلکه ارجاع می دهیم به ApiResponse که ساخته بودیم. حال دریافت کد تایید  ما آماده است.

###### حال نوبت می رسد به ورود به سیستم یا همان login که می خواهیم آن را پیاده سازی کنیم. از آنجایی که ما از passport استفاده می کنیم، آدرس login ما همان /oauth/token است، پس نیاز نیست به فایل api.php برویم و این مسیر را بسازیم و برای آن تابع بسازیم. وارد فایل api.yaml شده و ابتدا schema آن را در components می سازیم.
```bash
Login:
    type: object
    properties:
        username:
            type: string
            description: phone or email
        password:
            type: password
            format: password
        grant_type:
            type: string
            enum: ["password"]
            default: "password"
        client_id:
            type: integer
            enum: [1]
            default: 1
        client_secret:
            type: string
            enum: ["PpYE2tplIoNkuwAU5T7mLcL7a1dBi6zdWt14iXjR"]
            default: "PpYE2tplIoNkuwAU5T7mLcL7a1dBi6zdWt14iXjR"
```
###### برای اینکه بخواهیم در passport عمل login را انجام دهیم، نیاز است پنج پارامتر را داشته باشیم. اول اینکه برای login خود type را از نوع object در نظر می گیریم. properties را باید با username و password و grant_type و client_id و client_secret پر کنیم. به همین منظور username را از نوع string در نظر گرفته و توضیحات آن را مطابق خواست خود می نویسیم. همین طور برای رمز عبور نیز همین کار را می کنیم. یک grant_type وجود دارد که باید آن را برابر با password بگذاریم، به همین دلیل برای آن enum و default در نظر می گیریم و در بخش client_id و client_secret نیز همین کار را می کنیم و داده های enum آن ها را از .env خود پر می کنیم.

###### حال نوبت آن است به سراغ ساخت path برویم تا مسیر آن را بسازیم. 
```bash
/oauth/token:
    post:
        tags:
            - User
        summery: Login user
        description: Login User Oauth
        requestBody:
            content:
                application/x-www-form-urlencoded:
                    schema:
                        $ref: "#/components/schemas/Login"
        responses:
            "200":
                description: Successful operation
                content:
                    application/json:
                        schema:
                            $ref: "#/components/schemas/ApiResponse"
```
###### مسیر /oauth/token را می سازیم و method آن را post می گذاریم، آن را جزء دسته User قرار می دهیم خلاصه و توضیحاتی هم برای آن در نظر می گیریم که اختیاری است. requestBody یا نوع ساختار ارسال اطلاعات را مشخص می کنیم، به همین منظور محتوا را با enctype مورد نظر ما یعنی application/x-www-form-urlencoded ارسال می کنیم و آن را به schema ساخته شده Login متصل می کنیم و responses را هم مثل گذشته به همان سبک می نویسیم. حال همه چی آماده است تا بخواهیم عمل login را انجام دهیم، به همین منظور باید دانست که passport با استفاده از email و password کاربر عمل احراز هویت را انجام می دهد، پس منظور از username همان email است و منظور از password همان رمزی که برای کاربر در نظر گرفته ایم. حال اگر بخواهیم در بخش شناسایی کاربر از طریق email مقدار phone هم ملاک باشد، باید از الگو <a href="https://laravel.com/docs/10.x/passport#customizing-the-username-field">Laravel Passport Customizing the Username Field</a> پیروی کنیم، به همین منظور وارد مدل User  می شویم و تابع زیر را می نویسیم.
```bash
public function findForPassport(string $username): User
{
    return $this
        ->where('phone', $username)
        ->orWhere('email', $username)
        ->first();
}
```
###### مقدار username را اول در phone جست و جو کرده، اگر نبود نیز آن را در email هم جست و جو می کنیم، طبیعتا اگر در هیچ شرایطی وجود نداشت، یعنی همچین کاربری وجود ندارد و اگر یکی از مورد ها رخ بدهد به کاربر می رسد و کاربر لازم نیست حتما فقط با email یا حتما phone برای login اقدام کند.

###### برای امر password هم می توانیم passport  را دستکاری کنیم و تایید درست بودن password را منوط به یکسان بودن با رمز عبور یا کد تایید کنیم. برای این کار می توانیم از الگو <a href="https://laravel.com/docs/10.x/passport#customizing-the-password-validation">Laravel Passport Customizing the Password Validation</a> پیروی کنیم و به صورت زیر در مدل User بنویسیم.
```bash
public function validateForPassportPasswordGrant(string $password): bool
{
    return (
        Hash::check($password, $this->password)
        ||
        Hash::check($password, $this->verify_code)
    );
}
```
###### برای قطعه کد بالا نیاز داریم تا Hash را use کنیم.
```bash
use Illuminate\Support\Facades\Hash;
```
###### بعد اینکه عمل login انجام شده، پاسخی از سمت سرور برای ما باز می گردد. برای اینکه login خود را نگه داریم و بتوانیم در مواردی که نیاز به احراز هویت است از آن استفاده نماییم، باید مقداری که در رشته access_token است را کپی کرده و در authorize قرار دهیم تا همواره login ما حفظ شود.

