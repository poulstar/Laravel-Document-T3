# ویرایش کاربر های ثبت شده در سیستم توسط مدیر سایت با احراز هویت درخواست دهنده و بررسی داده های ورودی

###### برای شروع ابتدا وارد فایل api.php شده و route ویرایش کردن هر کاربری توسط admin را می سازیم.
```bash
Route::post('update-user-by-admin/{user}', [UserController::class, 'updateUserByAdmin'])->middleware(['can:'.Permissions::UPDATE_ANY_ACCOUNT]);
```
###### برای ویرایش کاربر، route خود را از نوع post می سازیم و ارجاع می دهیم به تابع updateUserByAdmin در UserController و middleware مورد نظر خود یعنی UPDATE_ANY_ACCOUNT را که بر روی آن قرار می دهیم تا غیر admin نتواند به آن دسترسی پیدا کند.
###### برای اینکه وارد UserController بشویم و تابع updateUserByAdmin را بسازیم، قبل آن request خود برای ویرایش کاربر توسط admin را با دستور زیر می سازیم.
```bash
php artisan make:request AdminUpdateUserRequest
```
###### بعد اینکه فایل AdminUpdateUserRequest را ساختیم، وارد تابع  authorize می شویم و شرط می کنیم اگر کاربر admin است می تواند به ادامه مسیر برود.
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
###### برای تابع authorize موارد زیر را use می کنیم.
```bash
use Illuminate\Support\Facades\Auth;
use App\Enum\Roles;
```
###### در تابع rules تمام قوانینی که می خواهیم را می نویسیم، فقط دقت کنید که nullable در نظر می گیریم که ممکن است پر نشود.
```bash
public function rules(): array
{
    return [
        'name' => 'nullable|min:3|max:100',
        'phone' => 'nullable|min:11|max:14',
        'email' => 'nullable|email|min:3|max:100',
        'password' => ['nullable', 'max:100',
        Password::min(4)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        'avatar' => 'nullable|image|mimes:gif,ico,jpg,jpeg,tiff,jpeg,png,svg',
        'role' => 'nullable|in:admin,user',
    ];
}
```
###### برای تابع rules لازم است تا password را use کنیم.
```bash
use Illuminate\Validation\Rules\Password;
```
###### وارد UserController می شویم و تابع updateUserByAdmin را می نویسیم.
```bash
public function updateUserByAdmin(AdminUpdateUserRequest $request, User $user)
{
    $data = $request->safe(['name', 'phone', 'email', 'password']);
    if ($request->input('name'))
        $user->name = $data['name'];
    if ($request->input('phone') && $request->input('phone') != $user->phone) {
        if (User::where('phone', $request->input('phone'))->first()) {
            return $this->failResponse([
                'errors' => ['error' => ['This phone Already Exist']],
            ]);
        } else {
            $user->phone = $data['phone'];
        }
    }
    if ($request->input('email') && $request->input('email') != $user->email) {
        if (User::where('email', $request->input('email'))->first()) {
            return $this->failResponse([
                'errors' => ['error' => ['This E-Mail Already Exist']],
            ]);
        } else {
            $user->email = $data['email'];
        }
    }
    if ($request->input('password'))
        $user->name = Hash::make($data['password']);
    $user->update();
    if ($request->input('role')) {
        DB::table('model_has_roles')->where('model_id', $user->id)->delete();
        if ($request->input('role') === 'admin') {
            $user->assignRole(Role::findByName(Roles::ADMIN, 'api'));
        } else {
            $user->assignRole(Role::findByName(Roles::USER, 'api'));
        }
    }
    if ($request->file('avatar'))
        $this->storeUserAvatar($request->file('avatar'), $user->id);
    if ($user) {
        return $this->successResponse([
            'message' => 'User Updated',
        ]);
    }
    return $this->failResponse();
}
```
###### در پارامتر تابع updateUserByAdmin نیاز است که AdminUpdateUserRequest و مدل User را بنویسیم، اگر در مرحله بررسی احراز هویت و داده های ارسالی مشکلی نباشد به داخل تابع می آییم. از مقادیر safe شده، چهار مقدار یعنی name، phone، email و password را بر می داریم و با بررسی شرط اول که اگر مقدار name پر شده بود جای گذاری می کنیم.
###### در شرط دوم بررسی می کنیم که آیا phone پر شده است و همچنین مقدار آن با قبلی خود برابر هست یا خیر، اگر phone ارسال شده بود و مقدار آن پر شده بود و با شماره تماس قبلی که ثبت کرده برابر نبود، وارد شرط شده و بررسی می شود که مقدار جدید با شماره کاربران دیگر یکسان است یا خیر، اگر بود، خطا باز می گردانیم، در غیر اینصورت مقدار جدید را مقدار دهی می کنیم. دقت کنید همین کار را هم برای email می کنیم.
###### در شرط بعدی بررسی می کنیم آیا رمز عبور پر شده است یا خیر، اگر پر شده بود، مقدار جدید را hash می کنیم و در password کاربر خود قرار می دهیم.
###### بعد از این کاربر خود را به روز می کنیم و وارد فرآیند بررسی وضعیت نقش آن می شویم، اگر نقش ارسال شده بود، چون نمی خواهیم کاربر ما بیش از یک نقش داشته باشد، نقش قبلی را پاک می کنیم و نقش جدید را به او نسبت می دهیم.
###### بعد از این گام بررسی می کنیم آیا فایلی برای avatar ارسال شده است یا خیر، اگر ارسال شده بود، فایل جدید را به عنوان avatar جدید کاربر ذخیره می کنیم و وقتی همه کار های کاربر با موفقیت به اتمام رسید، این وضعیت را به کاربر درخواست دهنده اعلام می کنیم، در غیر اینصورت با ارسال پیام خطا، کاربر را از روند درخواستش آگاه می سازیم.
###### مواردی که لازم است برای تابع updateUserByAdmin نیز use کنیم.
```bash
use App\Http\Requests\AdminUpdateUserRequest;
```
###### حال برای اینکه بتوانیم ظاهر را بسازیم تا از طریق آن عمل ویرایش کاربر برای admin را فعال سازیم، وارد فایل api.yaml می شویم. ابتدا به بخش components می رویم و schema ویرایش کاربر را می سازیم.
```bash
UpdateUser:
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
```
###### برای اینکه schema کاربر را بسازیم، نوع آن را object می گذاریم. property های مورد نیاز خود را با توجه به الگو افزودن کاربر توسط admin می سازیم، با این تفاوت که هیچ کدام را مقدار default نمی دهیم و اجباری نمی گذاریم. حال نوبت آن است تا وارد بخش paths شده و path ویرایش کاربر توسط admin را بسازیم.
```bash
/api/update-user-by-admin/{user}:
    post:
      tags:
        - User
      summery: Update User By Admin
      description: Update Your any user by admin
      parameters:
        - name: user
          in: path
          description: User ID
          required: true
          schema:
            type: integer
            format: int64
      requestBody:
        content:
          multipart/form-data:
            schema:
              $ref: "#/components/schemas/UpdateUser"
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
###### برای ویرایش path را نوشته و یک پارامتر user در آن می گذاریم تا بتوانیم ID کاربر مورد نظر را دریافت کنیم. سپس در داخل path نوع method را برابر با post می گذاریم و tag را user در نظر می گیریم تا در لیست user قرار بگیرد. خلاصه و توضیحات مورد نظر خود را درج می کنیم و ابتدا وضعیت پارامتر را مشخص می کنیم. برای پارامتر توضیحی در نظر می گیریم. پر کردن پارامتر خود را اجباری می کنیم و schema آن را تعیین می کنیم. سپس سراغ requestBody رفته و نوع content را multipart/form-data می گذاریم، چون در فرم خود یک فایل داریم و در انتها برای schema فرم خود، آن را ارجاع می دهیم به UpdateUser و به سراغ نوشتن responses می رویم با همان اصول قبلی ای که انجام می دادیم. برای درخواست نیاز داریم login بودن و هویت خود را برای سرور مشخص کنیم، به همین دلیل security را فعال می کنیم.
###### حال همه چی آماده است برای ویرایش هر کاربری که می خواهیم، به شرط اینکه با حساب مدیر درخواست دهیم.


