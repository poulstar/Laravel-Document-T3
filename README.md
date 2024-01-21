# نصب تکنولوژی Passport و انجام تنظیمات مورد نیاز بر روی آن

###### ابتدا برای اینکه بخواهیم passport را نصب کنیم، باید به فایل php.ini برویم و extension مورد نظر یعنی sodium را فعال کنیم.
###### برای اینکه بخواهید در مورد passport بیشتر بدانید، می توانید <a href="https://laravel.com/docs/9.x/passport">Laravel Passport</a> را مطالعه کنید. حال دستوری که لازم است تا در ترمینال بزنید تا نصب آغاز شود، دستور زیر است.
```bash
composer require laravel/passport
```
###### بعد اینکه نصب انجام شد، ما نیاز داریم تا migration های خود را migrate کنیم، از همین رو برای سیستم خود postgresql و pgadmin نصب کنید، دقت کنید که extension مورد نیاز برای این دوره postgis است، پس آن را هم بر روی postgresql خود نصب کنید. بعد این مرحله وارد pgadmin شده و یک database بسازید و حتما extension postgis3 را به آن اضافه کنید. بعد وارد پروژه شوید و در فایل .env مقادیر زیر را برای database تنظیم کنید.
```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=postgres
DB_PASSWORD=root
```
###### حال که این تنظیمات را انجام داده اید، دستور زیر را بزنید.
```bash
php artisan migrate
```

###### حال نوبت آن است تا در ترمینال دستور دهیم تا کلید های امنیتی خود را تولید کنیم و در سیستم ذخیره کنیم. برای این کار دستور زیر را می زنیم.
```bash
php artisan passport:install
```
###### بعد این خروجی ای به شکل زیر به شما نشان می دهد.
```bash
Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client secret: PpYE2tplIoNkuwAU5T7mLcL7a1dBi6zdWt14iXjR
Password grant client created successfully.
Client ID: 2
Client secret: yPXtH2ZjPUe5fCeErg2bqXb89ODBLkPVaINAPoZD
```
###### یکی از Client ID ها و Client secret را می توانید جهت ارتباط امن نگه دارید. برای این کار وارد .env شده و در انتها فایل دو متغیر زیر را تعریف می کنیم.
```bash
AUTH_WEB_CLIENT_ID=1
AUTH_WEB_CLIENT_SECRET=PpYE2tplIoNkuwAU5T7mLcL7a1dBi6zdWt14iXjR
```
###### معمولا در مدل User یکی از مواردی use شده است HasApiTokens است، اگر نبود شما اضافه کنید. مرحله بعدی بجای 
```bash
use Laravel\Sanctum\HasApiTokens;
```
###### مقدار مربوط به passport را قرار دهید.
```bash
use Laravel\Passport\HasApiTokens;
```
###### حال نوبت ان است که تنظیمات آن را نیز انجام دهیم، به همین منظور وارد پوشه config شده و در فایل auth.php در بخش guards، مقدار زیر را نیز اضافه می کنیم.
```bash
'api' => [
    'driver' => 'passport',
    'provider' => 'users',
],
```
###### حال به سراغ ساخت کلید می رویم، جهت این کار ابتدا وارد پوشه app شده و در Providers فایل AuthServiceProvider.php را باز می کنیم. در تابع boot آن مقدار زیر را می نویسیم.
```bash
$this->registerPolicies();

Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
```
###### و برای اینکه passport را بشناسد، آن را بالا use می کنیم.
```bash
use Laravel\Passport\Passport;
```
###### برای اینکه کلید های خصوصی و عمومی تولید شود، در پوشه app یک پوشه می سازیم به نام secrets و در آن هم یک پوشه می سازیم به نام oauth و بعد دستور زیر را در ترمینال می زنیم.
```bash
php artisan passport:keys
```
###### برای اینکه فایل config مربوط به passport ساخته شود دستور زیر را می زنیم.
```bash
php artisan vendor:publish --tag=passport-config
```
###### برای اینکه migration های مربوط به passport را بسازیم هم دستور زیر را می زنیم.
```bash
php artisan vendor:publish --tag=passport-migrations
```
###### حال همه چی آماده است تا ما از laravel passport استفاده کنیم.
