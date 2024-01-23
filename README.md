# آموزش ساخت Enum و کامل کردن Seeder ها و اجرای آن و آموزش بحث CROS یا Cross-Origin Resource Sharing 

###### برای اینکه کار خود را پیش ببریم و سیستم را توسعه دهیم، لازم است که ابتدا چندین مرحله کار را پیش بگیریم، اول از همه به سراغ ساخت enum می رویم. در سیستم ها برای داده های حساس که تغییر آن ها منوط به تشخیص برنامه نویس سایت است. به همین منظور، برای شروع ابتدا در پوشه app یک پوشه برای کار خود می سازیم به نام Enum و در آن دو فایل می سازیم به نام Permissions و Roles و شروع می کنیم به کد نویسی در هر یک.

###### در فایل Roles.php می نویسیم.
```bash
<?php

namespace App\Enum;

final class Roles
{
    public const ADMIN = 'admin';
    public const USER = 'user';
}
```
###### اول اینکه کلاس آن را از نوع final می گذاریم تا دیگر نشود از آن ارث بری کرد و خود کلاس هم ثابت باشد و داخل آن دو property تعریف می کنیم برای admin و user که نقش های سیستم ما هستند.
###### در فایل Permissions.php می نویسیم.
```bash
<?php

namespace App\Enum;

final class Permissions
{
    public const VIEW_MY_PROFILE = 'view my profile';
    public const UPDATE_MY_ACCOUNT = 'update my account';

    public const VIEW_ANY_POST = 'view any post';
    public const LIKE_ANY_POST = 'like any post';

    public const CREATE_NEW_POST = 'create new post';
    public const READ_MY_POST = 'read my post';
    public const UPDATE_MY_POST = 'update my post';
    public const DELETE_MY_POST = 'delete my post';

    public const READ_ANY_POST = 'read any post';
    public const UPDATE_ANY_POST = 'update any post';
    public const DELETE_ANY_POST = 'delete any post';

    public const CREATE_ANY_ACCOUNT = 'create any account';
    public const READ_ANY_ACCOUNT = 'read any account';
    public const UPDATE_ANY_ACCOUNT = 'update any account';
    public const DELETE_ANY_ACCOUNT = 'delete any account';

    public const CREATE_ANY_COMMENT = 'create any comment';
    public const READ_ANY_COMMENT = 'read any comment';
    public const UPDATE_ANY_COMMENT = 'update any comment';
    public const DELETE_ANY_COMMENT = 'delete any comment';
}
```
###### در این فایل هم یک کلاس final می سازیم و تمام فعل هایی که در سیستم ما ممکن است اتفاق بی افتد را تحت عنوان یک property ثابت می سازیم تا بتوانیم به کاربر های خود آن دسترسی ها را بدهیم و مانع سو استفاده شویم.

###### حال نوبت آن است seeder های مورد نظر خود را بسازیم تا وسیله آن یک داده اولیه وارد پایگاه داده خود کنیم، تا سیستم به یک وضعیت مطلوب از نظر داده برسد. از همین رو ابتدا شروع می کنیم  به ساخت seeder ی که اطلاعات Oauth را توسط آن ثبت کنیم و ارتباط امن را به وسیله آن برقرار کنیم.
###### دستور زیر را میزنیم.
```bash
php artisan make:seeder OauthClientSeeder
```
###### حال در تابع run فایل OauthClientSeeder می نویسیم.
```bash
Client::create([
    'name' => 'Web Client',
    'id' => env('AUTH_WEB_CLIENT_ID', 1),
    'secret' => env('AUTH_WEB_CLIENT_SECRET'),
    'redirect' => 'localhost:8000',
    'provider' => 'users',
    'personal_access_client' => 0,
    'password_client' => 1,
    'revoked' => 0,
]);
```
###### برای این فایل لازم داریم تا در بالا use های زیر را انجام دهیم.
```bash
use Laravel\Passport\Client;
```
###### بعد آن نوبت می رسد به نوشتن کد جهت ثبت نقش ها و دسترسی هایی که هر کدام از نقش ها می توانند داشته باشند، به همین منظور ابتدا فایل فایل PermissionSeeder را از طریق دستور زیر می سازیم.
```bash
php artisan make:seeder PermissionSeeder
```
###### حال در تابع run فایل PermissionSeeder می نویسیم.
```bash
public function run(): void
{
    $admin = Role::create(['name' => Roles::ADMIN, 'guard_name' => 'api']);
    $user = Role::create(['name' => Roles::USER, 'guard_name' => 'api']);

    Permission::create(['name' => Permissions::VIEW_MY_PROFILE, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::UPDATE_MY_ACCOUNT, 'guard_name' => 'api']);

    Permission::create(['name' => Permissions::VIEW_ANY_POST, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::LIKE_ANY_POST, 'guard_name' => 'api']);

    Permission::create(['name' => Permissions::CREATE_NEW_POST, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::READ_MY_POST, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::UPDATE_MY_POST, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::DELETE_MY_POST, 'guard_name' => 'api']);

    Permission::create(['name' => Permissions::READ_ANY_POST, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::UPDATE_ANY_POST, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::DELETE_ANY_POST, 'guard_name' => 'api']);

    Permission::create(['name' => Permissions::CREATE_ANY_ACCOUNT, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::READ_ANY_ACCOUNT, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::UPDATE_ANY_ACCOUNT, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::DELETE_ANY_ACCOUNT, 'guard_name' => 'api']);

    Permission::create(['name' => Permissions::CREATE_ANY_COMMENT, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::READ_ANY_COMMENT, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::UPDATE_ANY_COMMENT, 'guard_name' => 'api']);
    Permission::create(['name' => Permissions::DELETE_ANY_COMMENT, 'guard_name' => 'api']);

    $admin->givePermissionTo(Permissions::VIEW_MY_PROFILE);
    $admin->givePermissionTo(Permissions::UPDATE_MY_ACCOUNT);

    $admin->givePermissionTo(Permissions::VIEW_ANY_POST);
    $admin->givePermissionTo(Permissions::LIKE_ANY_POST);

    $admin->givePermissionTo(Permissions::CREATE_NEW_POST);
    $admin->givePermissionTo(Permissions::READ_MY_POST);
    $admin->givePermissionTo(Permissions::UPDATE_MY_POST);
    $admin->givePermissionTo(Permissions::DELETE_MY_POST);

    $admin->givePermissionTo(Permissions::READ_ANY_POST);
    $admin->givePermissionTo(Permissions::UPDATE_ANY_POST);
    $admin->givePermissionTo(Permissions::DELETE_ANY_POST);

    $admin->givePermissionTo(Permissions::CREATE_ANY_ACCOUNT);
    $admin->givePermissionTo(Permissions::READ_ANY_ACCOUNT);
    $admin->givePermissionTo(Permissions::UPDATE_ANY_ACCOUNT);
    $admin->givePermissionTo(Permissions::DELETE_ANY_ACCOUNT);

    $admin->givePermissionTo(Permissions::CREATE_ANY_COMMENT);
    $admin->givePermissionTo(Permissions::READ_ANY_COMMENT);
    $admin->givePermissionTo(Permissions::UPDATE_ANY_COMMENT);
    $admin->givePermissionTo(Permissions::DELETE_ANY_COMMENT);

    $user->givePermissionTo(Permissions::VIEW_MY_PROFILE);
    $user->givePermissionTo(Permissions::UPDATE_MY_ACCOUNT);

    $user->givePermissionTo(Permissions::VIEW_ANY_POST);
    $user->givePermissionTo(Permissions::LIKE_ANY_POST);

    $user->givePermissionTo(Permissions::CREATE_NEW_POST);
    $user->givePermissionTo(Permissions::READ_MY_POST);
    $user->givePermissionTo(Permissions::UPDATE_MY_POST);
    $user->givePermissionTo(Permissions::DELETE_MY_POST);

    $user->givePermissionTo(Permissions::CREATE_ANY_COMMENT);
    $user->givePermissionTo(Permissions::READ_ANY_COMMENT);
}
```
###### برای این فایل لازم داریم تا use های زیر را در بالای class انجام دهیم.
```bash
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enum\Roles;
use App\Enum\Permissions;
```
###### گام بعدی نوبت می رسد به seeder های کاربران و پست ها و کامنت ها که برای سهولت می توانید کد ها را copy و paste نمایید. دقت کنید که در کاربران و  پست ها اشاره شده به یک پوشه و فایل، به همین منظور از طریق <a href="https://github.com/poulstar/Laravel-RESTFullAPI-Files/tree/SeederData">لینک</a> موجود فایل را دانلود کنید و پوشه default-image را در پوشه public لاراول قرار دهید. حال برای ساخت فایل UserSeeder دستور زیر را بزنید.
```bash
php artisan make:seeder UserSeeder
```
###### برای داشتن کاربر های اولیه، کد زیر را در تابع run فایل UserSeeder می نویسیم.
```bash
# First User
$user = User::create([
    'name' => 'root',
    'email' => 'root@root.com',
    'phone' => '09123456789',
    'password' => Hash::make('root')
]);
$user->assignRole(Role::findByName(Roles::ADMIN, 'api'));
$media = new Media([
    'size' => 298124,
    'mime_type' => 'image/png',
    'url' => 'default-image/Avatar.png'
]);
$media->user()->associate($user->id);
$media->save();

$user->media()->sync($media, [ 'create_at' => Carbon::now()]);
$user->save();
# Second User
$user = User::create([
    'name' => 'ali',
    'email' => 'ali@gmail.com',
    'phone' => '09223456789',
    'password' => Hash::make('123456')
]);
$user->assignRole(Role::findByName(Roles::USER, 'api'));
$media = new Media([
    'size' => 298124,
    'mime_type' => 'image/png',
    'url' => 'default-image/Avatar.png'
]);
$media->user()->associate($user->id);
$media->save();

$user->media()->sync($media, [ 'create_at' => Carbon::now()]);
$user->save();
# Third User
$user = User::create([
    'name' => 'hassan',
    'email' => 'hassan@gmail.com',
    'phone' => '09323456789',
    'password' => Hash::make('123456')
]);
$user->assignRole(Role::findByName(Roles::USER, 'api'));
$media = new Media([
    'size' => 298124,
    'mime_type' => 'image/png',
    'url' => 'default-image/Avatar.png'
]);
$media->user()->associate($user->id);
$media->save();

$user->media()->sync($media, [ 'create_at' => Carbon::now()]);
$user->save();
# Fourth User
$user = User::create([
    'name' => 'hossein',
    'email' => 'hossein@gmail.com',
    'phone' => '09423456789',
    'password' => Hash::make('123456')
]);
$user->assignRole(Role::findByName(Roles::USER, 'api'));
$media = new Media([
    'size' => 298124,
    'mime_type' => 'image/png',
    'url' => 'default-image/Avatar.png'
]);
$media->user()->associate($user->id);
$media->save();

$user->media()->sync($media, [ 'create_at' => Carbon::now()]);
$user->save();
# Fifth User
$user = User::create([
    'name' => 'sajjad',
    'email' => 'sajjad@gmail.com',
    'phone' => '09523456789',
    'password' => Hash::make('123456')
]);
$user->assignRole(Role::findByName(Roles::USER, 'api'));
$media = new Media([
    'size' => 298124,
    'mime_type' => 'image/png',
    'url' => 'default-image/Avatar.png'
]);
$media->user()->associate($user->id);
$media->save();

$user->media()->sync($media, [ 'create_at' => Carbon::now()]);
$user->save();
# Sixth User
$user = User::create([
    'name' => 'farhad',
    'email' => 'farhad@gmail.com',
    'phone' => '09623456789',
    'password' => Hash::make('123456')
]);
$user->assignRole(Role::findByName(Roles::USER, 'api'));
$media = new Media([
    'size' => 298124,
    'mime_type' => 'image/png',
    'url' => 'default-image/Avatar.png'
]);
$media->user()->associate($user->id);
$media->save();

$user->media()->sync($media, [ 'create_at' => Carbon::now()]);
$user->save();
# Seventh User
$user = User::create([
    'name' => 'naghme',
    'email' => 'naghme@gmail.com',
    'phone' => '09723456789',
    'password' => Hash::make('123456')
]);
$user->assignRole(Role::findByName(Roles::USER, 'api'));
$media = new Media([
    'size' => 298124,
    'mime_type' => 'image/png',
    'url' => 'default-image/Avatar.png'
]);
$media->user()->associate($user->id);
$media->save();

$user->media()->sync($media, [ 'create_at' => Carbon::now()]);
$user->save();
# Eighth User
$user = User::create([
    'name' => 'aryan',
    'email' => 'aryan@gmail.com',
    'phone' => '09823456789',
    'password' => Hash::make('123456')
]);
$user->assignRole(Role::findByName(Roles::USER, 'api'));
$media = new Media([
    'size' => 298124,
    'mime_type' => 'image/png',
    'url' => 'default-image/Avatar.png'
]);
$media->user()->associate($user->id);
$media->save();

$user->media()->sync($media, [ 'create_at' => Carbon::now()]);
$user->save();
# Ninth User
$user = User::create([
    'name' => 'vahid',
    'email' => 'vahid@gmail.com',
    'phone' => '09923456789',
    'password' => Hash::make('123456')
]);
$user->assignRole(Role::findByName(Roles::USER, 'api'));
$media = new Media([
    'size' => 298124,
    'mime_type' => 'image/png',
    'url' => 'default-image/Avatar.png'
]);
$media->user()->associate($user->id);
$media->save();

$user->media()->sync($media, [ 'create_at' => Carbon::now()]);
$user->save();
# Tenth User
$user = User::create([
    'name' => 'toba',
    'email' => 'toba@gmail.com',
    'phone' => '09133456789',
    'password' => Hash::make('123456')
]);
$user->assignRole(Role::findByName(Roles::USER, 'api'));
$media = new Media([
    'size' => 298124,
    'mime_type' => 'image/png',
    'url' => 'default-image/Avatar.png'
]);
$media->user()->associate($user->id);
$media->save();

$user->media()->sync($media, [ 'create_at' => Carbon::now()]);
$user->save();
```
###### برای قطعه کد بالا نیاز داریم تا در بالا class موارد زیر را use کنیم.
```bash
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Enum\Roles;
use App\Models\Media;
use Carbon\Carbon;
```
###### برای داشتن پست های اولیه لازم داریم تا در سیستم خود یک PostSeeder داشته باشیم، به همین منظور دستور زیر را جهت ساخت PostSeeder می نویسیم.
```bash
php artisan make:seeder PostSeeder
```
###### برای اینکه 100 پست اولیه داشته باشیم قطعه کد زیر را در تابع run کلاس PostSeeder می نویسیم.
```bash
# First User

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(1);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/01.jpg'
]);
$media->user()->associate(1);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(1);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/02.jpg'
]);
$media->user()->associate(1);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(1);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/03.jpg'
]);
$media->user()->associate(1);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(1);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/04.jpg'
]);
$media->user()->associate(1);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(1);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/05.jpg'
]);
$media->user()->associate(1);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(1);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/06.jpg'
]);
$media->user()->associate(1);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(1);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/07.jpg'
]);
$media->user()->associate(1);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(1);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/08.jpg'
]);
$media->user()->associate(1);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(1);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/09.jpg'
]);
$media->user()->associate(1);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(1);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/10.jpg'
]);
$media->user()->associate(1);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

# Second User

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(2);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/01.jpg'
]);
$media->user()->associate(2);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(2);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/02.jpg'
]);
$media->user()->associate(2);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(2);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/03.jpg'
]);
$media->user()->associate(2);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(2);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/04.jpg'
]);
$media->user()->associate(2);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(2);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/05.jpg'
]);
$media->user()->associate(2);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(2);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/06.jpg'
]);
$media->user()->associate(2);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(2);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/07.jpg'
]);
$media->user()->associate(2);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(2);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/08.jpg'
]);
$media->user()->associate(2);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(2);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/09.jpg'
]);
$media->user()->associate(2);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(2);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/10.jpg'
]);
$media->user()->associate(2);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

# Third User

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(3);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/01.jpg'
]);
$media->user()->associate(3);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(3);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/02.jpg'
]);
$media->user()->associate(3);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(3);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/03.jpg'
]);
$media->user()->associate(3);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(3);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/04.jpg'
]);
$media->user()->associate(3);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(3);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/05.jpg'
]);
$media->user()->associate(3);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(3);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/06.jpg'
]);
$media->user()->associate(3);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(3);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/07.jpg'
]);
$media->user()->associate(3);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(3);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/08.jpg'
]);
$media->user()->associate(3);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(3);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/09.jpg'
]);
$media->user()->associate(3);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(3);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/10.jpg'
]);
$media->user()->associate(3);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

# Fourth User

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(4);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/01.jpg'
]);
$media->user()->associate(4);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(4);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/02.jpg'
]);
$media->user()->associate(4);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(4);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/03.jpg'
]);
$media->user()->associate(4);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(4);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/04.jpg'
]);
$media->user()->associate(4);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(4);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/05.jpg'
]);
$media->user()->associate(4);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(4);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/06.jpg'
]);
$media->user()->associate(4);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(4);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/07.jpg'
]);
$media->user()->associate(4);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(4);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/08.jpg'
]);
$media->user()->associate(4);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(4);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/09.jpg'
]);
$media->user()->associate(4);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(4);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/10.jpg'
]);
$media->user()->associate(4);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

# Fifth User

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(5);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/01.jpg'
]);
$media->user()->associate(5);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(5);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/02.jpg'
]);
$media->user()->associate(5);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(5);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/03.jpg'
]);
$media->user()->associate(5);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(5);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/04.jpg'
]);
$media->user()->associate(5);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(5);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/05.jpg'
]);
$media->user()->associate(5);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(5);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/06.jpg'
]);
$media->user()->associate(5);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(5);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/07.jpg'
]);
$media->user()->associate(5);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(5);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/08.jpg'
]);
$media->user()->associate(5);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(5);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/09.jpg'
]);
$media->user()->associate(5);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(5);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/10.jpg'
]);
$media->user()->associate(5);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

# Sixth User

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(6);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/01.jpg'
]);
$media->user()->associate(6);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(6);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/02.jpg'
]);
$media->user()->associate(6);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(6);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/03.jpg'
]);
$media->user()->associate(6);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(6);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/04.jpg'
]);
$media->user()->associate(6);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(6);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/05.jpg'
]);
$media->user()->associate(6);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(6);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/06.jpg'
]);
$media->user()->associate(6);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(6);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/07.jpg'
]);
$media->user()->associate(6);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(6);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/08.jpg'
]);
$media->user()->associate(6);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(6);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/09.jpg'
]);
$media->user()->associate(6);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(6);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/10.jpg'
]);
$media->user()->associate(6);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

# Seventh User

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(7);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/01.jpg'
]);
$media->user()->associate(7);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(7);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/02.jpg'
]);
$media->user()->associate(7);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(7);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/03.jpg'
]);
$media->user()->associate(7);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(7);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/04.jpg'
]);
$media->user()->associate(7);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(7);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/05.jpg'
]);
$media->user()->associate(7);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(7);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/06.jpg'
]);
$media->user()->associate(7);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(7);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/07.jpg'
]);
$media->user()->associate(7);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(7);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/08.jpg'
]);
$media->user()->associate(7);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(7);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/09.jpg'
]);
$media->user()->associate(7);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(7);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/10.jpg'
]);
$media->user()->associate(7);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

# Eighth User

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(8);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/01.jpg'
]);
$media->user()->associate(8);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(8);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/02.jpg'
]);
$media->user()->associate(8);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(8);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/03.jpg'
]);
$media->user()->associate(8);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(8);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/04.jpg'
]);
$media->user()->associate(8);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(8);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/05.jpg'
]);
$media->user()->associate(8);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(8);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/06.jpg'
]);
$media->user()->associate(8);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(8);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/07.jpg'
]);
$media->user()->associate(8);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(8);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/08.jpg'
]);
$media->user()->associate(8);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(8);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/09.jpg'
]);
$media->user()->associate(8);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(8);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/10.jpg'
]);
$media->user()->associate(8);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

# Ninth User

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(9);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/01.jpg'
]);
$media->user()->associate(9);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(9);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/02.jpg'
]);
$media->user()->associate(9);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(9);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/03.jpg'
]);
$media->user()->associate(9);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(9);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/04.jpg'
]);
$media->user()->associate(9);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(9);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/05.jpg'
]);
$media->user()->associate(9);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(9);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/06.jpg'
]);
$media->user()->associate(9);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(9);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/07.jpg'
]);
$media->user()->associate(9);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(9);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/08.jpg'
]);
$media->user()->associate(9);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(9);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/09.jpg'
]);
$media->user()->associate(9);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(9);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/10.jpg'
]);
$media->user()->associate(9);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

# Tenth User

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(10);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/01.jpg'
]);
$media->user()->associate(10);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(10);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/02.jpg'
]);
$media->user()->associate(10);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(10);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/03.jpg'
]);
$media->user()->associate(10);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(10);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/04.jpg'
]);
$media->user()->associate(10);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(10);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/05.jpg'
]);
$media->user()->associate(10);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(10);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/06.jpg'
]);
$media->user()->associate(10);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(10);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/07.jpg'
]);
$media->user()->associate(10);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(10);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/08.jpg'
]);
$media->user()->associate(10);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(10);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/09.jpg'
]);
$media->user()->associate(10);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############

############### START ###############
$post = new Post([
    'title' => 'Lorem ipsum',
    'description' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available',
    'location' => '0101000020E610000076C2F162DCA2424068CCCCCC0FD54840'
]);
$post->user()->associate(10);
$post->save();

$media = new Media([
    'size' => 740000,
    'mime_type' => 'image/jpg',
    'url' => 'default-image/10.jpg'
]);
$media->user()->associate(10);
$media->save();

$post = Post::find($post->id);
$post->media()->sync($media, [ 'create_at' => Carbon::now() ]);
$post->save();
############### END ###############
```
###### حال برای اینکه بتوانیم کد های خود را ثبت کنیم، نیاز داریم تا بالای کلاس موارد زیر را use کنیم.
```bash
use App\Models\Post;
use App\Models\Media;
use Carbon\Carbon;
```
###### حال برای اینکه بخش comment های ما خالی نباشد، به همین منظور CommentSeeder را به وسیله دستور زیر می سازیم.
```bash
php artisan make:seeder CommentSeeder
```
###### برای اینکه حداقل سه comment فرضی داشته باشیم، قطعه کد زیر را در تابع run می نویسیم.
```bash
$comments = [
    [
        'user_id' => 1,
        'post_id' => 100,
        'parent_id' => null,
        'child' => false,
        'title' => 'root comment',
        'text' => 'root text',
    ],
    [
        'user_id' => 1,
        'post_id' => 100,
        'parent_id' => null,
        'child' => false,
        'title' => 'root comment',
        'text' => 'root text',
    ],
    [
        'user_id' => 1,
        'post_id' => 100,
        'parent_id' => null,
        'child' => false,
        'title' => 'root comment',
        'text' => 'root text',
    ],
];
Comment::insert($comments);
```
###### برای اینکه بتوانیم comment را ثبت کنیم، لازم داریم تا مدل comment را بالای کلاس use کنیم.
```bash
use App\Models\Comment;
```
###### حال که تمام seeder های خود را ساخته ایم، وقت آن رسیده تا آن ها را در DatabaseSeeder صدا کنیم تا موقع دستور ترمینالی  در پایگاه داده ثبت شود.
###### به همین منظور کد زیر را در تابع run کلاس DatabaseSeeder می نویسیم.
```bash
$this->call([
    OauthClientSeeder::class,
    PermissionSeeder::class,
    UserSeeder::class,
    PostSeeder::class,
    CommentSeeder::class,
]);
```
###### گام آخر نوبت آن است که تنظیمات مربوط به CROS را انجام دهیم، دقت کنید چون از سیستم بیرونی به آدرس های مختلفی از داده های ما درخواست ارسال می شود، لازم است CROS را فعال کنیم تا درخواست ها مسدود نشود. به همین منظور وارد پوشه config می شویم و فایل cros.php را ویرایش می کنیم.
```bash
'paths' => ['api/*', 'sanctum/csrf-cookie', 'oauth/token'],
'supports_credentials' => true,
```
###### در paths مقدار 'oauth/token' را اضافه می کنیم تا بتواند مسیر جدید را بشناسد و علاوه بر آن supports_credentials را true می کنیم.

###### حال همه کار ها تمام شده می توانیم دستور زیر را بزنیم تا تمام داده های ما در پایگاه داده ثبت شود.
```bash
php artisan migrate:fresh --seed
```
