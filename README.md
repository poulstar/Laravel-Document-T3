# آموزش YAML و Swagger و نحوه پیاده سازی Swagger-UI و پیاده سازی الگو مناسب جهت اجرا Oauth و bearer

###### برای اینکه بخواهیم برای پروژه خود از <a href="https://swagger.io/">swagger</a> استفاده کنیم باید کمی به مقدمات کار آشنا شویم. دقت کنید که swagger یک پلتفرم است برای زدن API تا بتوان یک عمل از CRUD را انجام داد یا کار هایی مانند login را بررسی نمود. از همین رو swagger را به چند روش می توان اجرا نمود. یکی از این روش ها اجرا <a href="https://github.com/DarkaOnLine/L5-Swagger">Laravel Swagger</a> است که می توان از آن الگو برای ساخت و استفاده از swagger استفاده نمود که مبتنی بر controller های شماست و مدل دیگر <a href="https://swagger.io/tools/swagger-ui/">Swagger UI</a> است که لازم است شما YAML یا Yet Another Markup Language است که ساختار سلسله مراتبی دارد و بسیار به زبان انسان شبیه است و به عنوان یکی دیگر از زبان های نشانه گذاری  شناخته می شود. به همین ما سعی می کنیم به صورت مرحله به مرحله YAML را یاد بگیریم. برای اینکه ما بخواهیم Swagger UI را اجرا کنیم، لازم داریم تا فایل های مورد نیاز آن را یعنی css و js را داشته باشیم به همین منظور به <a href="https://github.com/poulstar/Laravel-RESTFullAPI-Files/tree/Swagger">لینک</a> موجود مراجعه می کنیم و فایل مورد نظر را دانلود می کنیم. پوشه swagger که دانلود شده است، حاوی فایل css و js است که پوشه swagger را همان طور برداشته و به داخل پوشه public می بریم.

###### پوشه swagger ی که داخل public گذاشته ایم، نیاز دارد تا یک فایل api.yaml داشته باشد، پس آن را می سازیم.

###### حال نوبت آن است که بخواهیم از فایل های ساخته شده خود استفاده نماییم. دقت کنید، ابتدا باید چند کار انجام دهیم و سپس به سراغ کد نویسی برویم، به همین منظور ابتدا در پوشه resources، در بخش views، پوشه ای می سازیم به نام swagger و کد های زیر را در آن می نویسیم. دقت کنید اگر بخواهید  فایل welcome را پاک کنید، به اختیار شماست، چون استفاده ای از آن نمی شود.
```bash
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="SwaggerUI" />
    <title>SwaggerUI</title>
    <link rel="stylesheet" href="{{ asset('swagger/swagger-ui.css') }}" />
    <script src="{{ asset('swagger/swagger-ui-bundle.js') }}" defer></script>
    <script defer>
        window.onload = () => {
            window.ui = SwaggerUIBundle({
                url: '{{ asset('swagger/api.yaml') }}',
                dom_id: '#swagger-ui',
                persistAuthorization: true
            });
        };
    </script>
</head>

<body>
    <div id="swagger-ui"></div>
</body>

</html>
```
###### برای css آدرس به فایل css موجود در پوشه swagger واقع در public می دهیم و همین کار را برای js هم انجام می دهیم و در بخش script دوم هم آدرس url را به فایل api.yaml خود بر می گردانیم.

###### حال نوبت آن است تا کار های لاراولی خود را انجام دهیم. به همین منظور وارد فایل web.php شده و اسم فایلی که قرار است در ازائه route اصلی یعنی root درخواست شود را به فایل swagger ما باز گرداند.
```bash
Route::get('/', function () {
    return view('swagger.swagger');
});
```

###### برای آنکه وارد api.yaml شویم و کد نویسی کنیم، باید کمی از اصول و چارچوب yaml آگاه شویم به همین منظور می توانید <a href="https://editor.swagger.io/">Swagger Editor</a> را مشاهده کنید و از نحوه نوشتن swagger آگاه شوید.

###### برای شروع ما ابتدا نسخه openapi را مشخص می کنیم و سپس سراغ نوشتن info می رویم و اطلاعاتی از کاری که می خواهیم انجام دهیم ارائه می کنیم و برای مرتب نویسی و منظم بودن کار در آینده، tag هایی به وجود می آوریم و از آنها برای دسته بندی کردن کد های خود بهره می بریم.

```bash
openapi: 3.0.3
info:
  title: Swagger Poulstar Social Media - OpenAPI 3.0
  description: for learn swagger and yaml
  version: 1.0.0
tags:
  - name: User
    description: Everything about your Users
  - name: Post
    description: Everything about your Posts
  - name: Comment
    description: Everything about your Comments
```
###### برای اینکه امنیت سایت خود را برقرار کنیم، به عبارتی متناسب با نیاز ما که Oauth است، این عمل را در components انجام می دهیم و بخش را باز می کنیم به نام securitySchemes که مدل امنیتی نیز در آن تعیین می شود. جهت مطالعه بیشتر می توان <a href="https://swagger.io/docs/specification/authentication/">Swagger Authentication</a> را مطالعه نمود. ما برای کار خود از Bearer استفاده می کنیم تا بتوانیم کلید امنیتی را حفظ کنیم و بحث Oauth را دستی بسازیم.
```bash
components:
  securitySchemes:
    bearerAuth:
      type: http
      description: "Authorization: Bearer <token>"
      Authorization: Bearer <token>
      scheme: bearer
      bearerFormat: JWT
```
###### حال همه چی برای شروع کد نویسی و تست آن به صورت API آماده است و می توانیم نرم افزار خود را توسعه دهیم.


