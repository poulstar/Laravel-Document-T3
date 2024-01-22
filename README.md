# تحلیل و ساخت Migration های مورد نیاز

###### برای اینکه بخواهیم برای پروژه خود پایگاه داده اصولی بسازیم، ابتدا باید نیاز های واقعی خود را بشناسیم. سپس به سراغ نوشتن کد و ساخت migration باشیم. از همین رو ما در سیستم خود نیاز داریم کاربری وجود داشته باشد که ورود و خروج و عمل ثبت شدن آن را رصد کنیم و بدانیم چه پستی گذاشته است، هر پست خود نیز دارای تصویری است که این تصویر نیز گاهی می تواند تصویر آواتار کاربر ما باشد. پر پست ممکن است توسط کاربری پسندیده شود و به آن اصطلاحا لایک تعلق گیرد. خواه و ناخواه ممکن است کاربر ما بخواهد برای پستی، نظر دلخواهش را بگذارد، پس باید این امر هم برایش برنامه ریزی شود. از همین رو شروع می کنیم به نوشتن ساختار جدولی که می خواهیم و بعد آن را تبدیل می کنیم به کد.

###### users
| id  | name | email | phone |password |
| - | - | - | - | - |
| id  | string | string-unique | string(14)-unique | string |

###### posts
| id  | user_id | title | description | location | up_vote_count |
| - | - | - | - | - | - |
| id  | unsignedBigInteger | string(250) | text-nullable | point-nullable | unsignedInteger-default(0) |


###### up_votes
| id  | user_id | post_id |
| - | - | - |
| id  | unsignedBigInteger | unsignedBigInteger |

###### media
| id  | user_id | size | mime_type | url |
| - | - | - | - | - |
| id  | unsignedBigInteger | unsignedInteger-default(0) | text-nullable | text-nullable |

###### model_has_media
| id  | model_id | model_type | media_id |
| - | - | - | - |
| id  | unsignedBigInteger | text | unsignedBigInteger |

###### comments
| id  | user_id | post_id | parent_id | child | title | text |
| - | - | - | - | - | - | - |
| id  | unsignedBigInteger | unsignedBigInteger | unsignedBigInteger-nullable | boolean-default(false) | string(100) | text |


###### حال نوبت آن رسیده برای جداولی که تعبیه دیده ایم، برنامه ریزی کنیم و کد نویسی آن را انجام دهیم. ابتدا از users شروع می کنیم. email_verified_at را نمی خواهیم، آن را حذف می کنیم، برای اینکه کاربر ما بتواند شماره تلفن داشته باشد، قطعه کد زیر را می نویسیم.
```bash
$table->string('phone',14)->unique();
```
###### حال نوبت posts هست، پس دستور ترمینالی زیر را می زنیم تا هم model و هم migration نیز با هم ساخته شود.
```bash
php artisan make:model Post -m
```
###### حال اطلاعات جدول را به صورت زیر می نویسیم.
```bash
$table->id();
$table->unsignedBigInteger('user_id');
$table->string('title', 250);
$table->text('description')->nullable();
$table->point('location')->nullable();
$table->unsignedInteger('up_vote_count')->default(0);
$table->timestamps();

$table->foreign('user_id')
->on('users')
->references('id')
->cascadeOnDelete()
->cascadeOnUpdate();
```
###### نوبت بعدی up_votes است که ابتدا model  و migration آن را با دستور زیر می نویسیم و بعد سراغ کد نویسی داخل آن می رویم.
```bash
php artisan make:model UpVote -m
```
###### حال کد های آن را به صورت زیر می نویسیم.
```bash
$table->id();
$table->unsignedBigInteger('user_id');
$table->unsignedBigInteger('post_id');
$table->timestamps();

$table->unique(['user_id', 'post_id']);

$table->foreign('user_id')
->on('users')
->references('id')
->cascadeOnDelete()
->cascadeOnUpdate();

$table->foreign('post_id')
->on('posts')
->references('id')
->cascadeOnDelete()
->cascadeOnUpdate();
```
###### گام بعدی نوبت media است که باید اول model و migration آن را بسازیم و بعد به ادامه کار برویم.
```bash
php artisan make:model Media -m
```
###### حال نوبت کد هایی است که می خواهیم برای media بنویسیم
```bash
$table->id();
$table->unsignedBigInteger('user_id');
$table->unsignedInteger('size')->default(0);
$table->text('mime_type')->nullable();
$table->text('url')->nullable();
$table->timestamps();

$table->foreign('user_id')
->on('users')
->references('id')
->cascadeOnDelete()
->cascadeOnUpdate();
```
###### نوبت بعدی جدول model_has_media است که یک جدول جهت نگه داری داده است برای بحث چند ریختی یا همان Polymorphism  که بعدا به آن می پردازیم. به همین منظور نیاز به ساخت model نیست و یکسره migration می سازیم.
```bash
php artisan make:migration create_model_has_media_table
```
###### کد های آن را به صورت زیر می نویسیم.
```bash
$table->id();
$table->unsignedBigInteger('model_id');
$table->text('model_type');
$table->unsignedBigInteger('media_id');
$table->timestamps();
```
###### مرحله بعدی نوبت نوشتن comments هست، از همین رو model و migration آن را ابتدا می سازیم.
```bash
php artisan make:model Comment -m
```
###### حال برای اجرا جدولی که طراحی کرده بودیم، کد های زیر را می نویسیم.
```bash
$table->id();
$table->unsignedBigInteger('user_id');
$table->unsignedBigInteger('post_id');
$table->unsignedBigInteger('parent_id')->nullable();
$table->boolean('child')->default(false);
$table->string('title',100);
$table->text('text');
$table->timestamps();

$table->foreign('user_id')
->on('users')
->references('id')
->cascadeOnDelete()
->cascadeOnUpdate();

$table->foreign('post_id')
->on('posts')
->references('id')
->cascadeOnDelete()
->cascadeOnUpdate();

$table->foreign('parent_id')
->on('comments')
->references('id')
->cascadeOnDelete()
->cascadeOnUpdate();
```




