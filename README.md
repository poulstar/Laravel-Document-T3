# ساخت پروژه برای آموزش API و اجرای Spatie Permission بر روی آن

## برای اینکه بخواهیم پروژه سرور خود را شروع کنیم، ابتدا باید یک پروژه لاراول بسازیم. به همین منظور دستور زیر را اجرا می کنیم.
```bash
composer create-project laravel/laravel server
```
###### یکی از مهم ترین سیستم هایی که در یک نرم افزار مورد نیاز است، ACL یا Access Control List است که به وسیله آن می توانیم سطح دسترسی کاربر های خود را کنترل کنیم. به همین منظور اقدام به نصب پکیج معروف spatie می کنیم.
```bash
composer require spatie/laravel-permission
```
###### گام بعدی لازم است تا برای اینکه نرم افزار ما این پکیج را بشناسد، وارد پوشه config شده و در داخل فایل app.php آرایه ای وجود دارد به نام providers که داخل آن قطعه کد زیر را اضافه می کنیم.
```bash
Spatie\Permission\PermissionServiceProvider::class,
```
###### حال نوبت آن است که دستور دهیم تا فایل های config  و migration آن نیز ساخته شود، به همین منظور دستور زیر را اجرا می کنیم.
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

###### گام بعدی برای اینکه مطمئن شویم پروژه ما در حال اجرا spatie است و cache خاصی وجود ندارد، کد زیر را یک بار اجرا می کنیم.
```bash
php artisan config:clear
```
###### حال نوبت آن است که این پکیج را متصل به مدل User خود کنیم تا پروژه ما مسلح به Role شود و متناسب با آن دسترسی های مختلف تعیین شود. به همین منظور وارد پوشه app شده و سپس پوشه Models را ورود می کنیم و در داخل فایل User.php وارد شده و به کلاس User در بخش use کردن trait ها، HasRoles را اضافه می کنیم، برای اینکه HasRoles را بشناسد، در بخش import های آن، آدرس زیر را وارد می کنیم.
```bash
use Spatie\Permission\Traits\HasRoles;
```

###### حال سیستم ما آماده است تا بخواهیم از ACL استفاده کنیم. دقت کنید که پکیج spatie یک رابطه چند به چند دارد و به همین دلیل یک کاربر نیز می تواند چندین نقش داشته باشد.



