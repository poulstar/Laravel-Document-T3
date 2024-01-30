# ساخت یک کاربر توسط admin و نوشتن قوانین کامل برای بررسی درخواست کاربر با احراز نقش آن

###### برای شروع ابتدا route را در api.php می نویسیم تا بتوانیم از طریق آن دسترسی جدیدی ایجاد نماییم.
```bash
Route::post('create-user-by-admin', [UserController::class, 'createUserByAdmin'])->middleware(['can:'.Permissions::CREATE_ANY_ACCOUNT]);
```
###### برای ساخت کاربر توسط مدیر سایت، route خود را از نوع post می گذاریم و آن را ارجاع می دهیم به UserController تا موقع فراخواندن این route تابع createUserByAdmin اجرا شود. دسترسی CREATE_ANY_ACCOUNT را می دهیم که مختص admin است.
###### حال می خواهیم برای  نوشتن تابع createUserByAdmin اقدام کنیم اما ابتدا به سراغ ساخت request خود می رویم و دستور زیر را می زنیم.
```bash
php artisan make:request AdminCreateUserRequest
```
###### ابتدا در بخش تابع authorize به بررسی وضعیت در خواست  دهنده می پردازیم و اگر مورد تایید شد به ادامه روند ادامه می دهیم. 
```bash
public function authorize(): bool
{
    if (Auth::user()->getRoleNames()[0] !== Roles::ADMIN) {
        return false;
    }else {
        return true;
    }
}
```
###### برای تابع authorize نیاز است Auth و Roles را use کنیم.
```bash
use Illuminate\Support\Facades\Auth;
use App\Enum\Roles;
```
###### دقت کنید که اگر کاربر درخواست دهنده از مدیر های ما نباشد، authorize آن false می شود و امکان ادامه روند وجود نخواهد داشت.
###### در بخش rules هم قوانین خود را می نویسیم و برای بخش بخش موارد تعیین و تکلیف می کنیم.
```bash
public function rules(): array
{
    return [
        'name' => 'required|min:3|max:100',
        'phone' => 'required|unique:users,phone|min:11|max:14',
        'email' => 'required|unique:users,email|email',
        'password' => ['required', 'max:100',
        Password::min(4)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        'avatar' => 'required|image|mimes:gif,ico,jpg,jpeg,tiff,jpeg,png,svg',
        'role' => 'required|in:admin,user',
    ];
}
```
###### برای rules نیاز داریم تا Password را use کنیم.
```bash
use Illuminate\Validation\Rules\Password;
```
###### حال نوبت آن است که تابع createUserByAdmin در UserController بنویسیم.
```bash
public function createUserByAdmin(AdminCreateUserRequest $request)
{
    $data = $request->safe(['name', 'phone', 'email', 'password']);
    $user = new User([
        'name' => $data['name'],
        'phone' => $data['phone'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);
    $user->save();
    if ($request->input('role') === 'admin') {
        $user->assignRole(Role::findByName(Roles::ADMIN, 'api'));
    } else {
        $user->assignRole(Role::findByName(Roles::USER, 'api'));
    }
    if ($request->file('avatar'))
        $this->storeUserAvatar($request->file('avatar'), $user->id);
    if ($user) {
        return $this->successResponse([
            'message' => 'User Created',
        ]);
    }
    return $this->failResponse();
}
```
###### ابتدا در شروع تابع AdminCreateUserRequest را اجرا می کنیم که درخواست ها را تحت بررسی قرار دهد و با قوانین و خواسته های ما بسنجد و اگر مجاز بود به ادامه روند به پردازیم، در غیر این صورت خطا را باز گرداند. وقتی مشکلی در درخواست کاربر نبود، name، phone، email و password که safe شده است را بر می داریم و به وسیله آن کاربر جدید خود را می سازیم. حال نوبت آن است متناسب با درخواست مدیر، نقش کاربر را به اون نسبت دهیم و بعد بررسی می کنیم که آیا فایل avatar ارسال شده است یا خیر، تا برای کاربر جدید خود، یک تصویر به ثبت رسانیم و سر انجام وقتی کاربر ما کامل شده، پیام موفقیت ثبت کاربر را باز می گردانیم، در غیر اینصورت با پیام شکست درخواست دهنده را آگاه می سازیم.
###### برای تابع createUserByAdmin نیاز داریم تا AdminCreateUserRequest را use کنیم.
```bash
use App\Http\Requests\AdminCreateUserRequest;
```
###### حال نوبت آن است که برای ظاهر و ساختاری که بتوانیم از کد هایی که نوشتیم، استفاده کنیم، برنامه ریزی کنیم. از همین رو وارد فایل api.yaml شده و ابتدا در بخش components برای ساخت یک کاربر توسط مدیر یک schema می سازیم.
```bash
CreateUser:
  type: object
  properties:
    name:
      type: string
    phone:
      type: string
    email:
      type: email
    password:
      type: password
      format: password
    avatar:
      type: string
      format: binary
    role:
      type: string
      enum: ["user", "admin"]
      default: "user"

```
###### برای اینکه فرم ما شکل گیرد، schema خود یعنی CreateUser را می سازیم و نوع آن را object می گذاریم و property های خود را به ترتیب می چینیم و نوع عر یک را مشخص می کنیم. بعد به سراغ ساخت path می رویم تا بتوانیم از schema ساخته شده استفاده نماییم.
```bash
/api/create-user-by-admin:
    post:
      tags:
        - User
      summery: Create a new user by admin
      description: admin can be add new user for app
      requestBody:
        content:
          multipart/form-data:
            schema:
              $ref: "#/components/schemas/CreateUser"
      responses:
        "200":
          description: Successful operation
          content:
            application/json:
              schema:
                $ref: "#/components/schemas/ApiResponse"
      security:
        - bearerAuth: []
```
###### در بخش paths وارد شده و path خود را مطابق سرور می نویسیم و method آن را post قرار می دهیم و در دسته بندی User آن را در نظر می گیریم. بعد برای آن خلاصه و توضیحاتی می نویسیم و در requestBody محتوا را از نوع multipart/form-data می گذاریم چون نیاز به ارسال فایل داریم و schema را به CreateUser ارجاع می دهیم. پاسخ را نیز مانند گذشته به همان صورت می نویسیم و از آنجایی که برای درخواست ساخت کاربر توسط admin، نیاز است تا کلید امنیتی login بودن همراه باشد، security را فعال می کنیم.
###### حال همه چی آماده است و می توانیم از آن استفاده نماییم.


