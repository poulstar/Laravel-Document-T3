# پیاده سازی فرآیند ثبت نام و دریافت و ویرایش اطلاعات خود در سرور

###### برای اینکه بتوانیم عمل ثبت نام و دیدن پروفایل و ویرایش آن را بسازیم، ابتدا نیاز داریم تا route های آن را داشته باشیم.
```bash
Route::get('get-verify-code/{phone}', [UserController::class, 'getLoginCode']);
Route::post('register', [UserController::class, 'register']);

Route::Group([
    'middleware' => ['auth:api']
], function () {
    Route::get('user/profile', [UserController::class, 'profile'])->middleware(['can:' . Permissions::VIEW_MY_PROFILE]);
    Route::post('update-my-profile/{user}', [UserController::class, 'updateMyProfile'])->middleware(['can:' . Permissions::UPDATE_MY_ACCOUNT]);
});
```
###### برای این منظور یک route با method مورد نظر خود یعنی post می سازیم و به UserController ارجاع می دهیم. حال برای دو عمل بعدی کاربر ما باید login باشد، به همین منظور برای آن و تمام اعمال دیگری که قرار است اتفاق بیافتد و نیاز به login دارد، یک route group می سازیم و بر روی آن middleware مورد نیاز ما یعنی auth را پیاده سازی می کنیم تا اگر کاربر login نبود نتواند به ادامه کار برود. برای دیدن پروفایل فقط کافی است یک route از نوع get بسازیم و ارجاع دهیم به UserController و بر روی آن middleware دسترسی VIEW_MY_PROFILE را چک کنیم که اگر کاربر این دسترسی را داشت، بعد بتواند به داده ها دست یابد. همچنین برای بخش ویرایش هم یک route از نوع post می سازیم و ارجاع می دهیم به UserController و دسترسی UPDATE_MY_ACCOUNT را روی آن بررسی می کنیم.
###### برای قطعه کد فوق نیاز داریم تا Permissions را use کنیم.
```bash
use App\Enum\Permissions;
```
###### قبل اینکه وارد UserController بشویم، ابتدا یک request برای ذخیره کاربر با دستور زیر می سازیم.
```bash
php artisan make:request RegisterRequest
```
###### در فایل RegisterRequest ابتدا در تابع authorize وضعیتی که باید باز گرداند را به true تبدیل می کنیم.
```bash
public function authorize(): bool
{
    return true;
}
```
###### در مرحله بعدی به سراغ تابع rules می رویم و قوانینی که مورد نظر ماست را اعمال می کنیم.
```bash
public function rules(): array
{
    return [
        'name' => 'required|min:3|max:100',
        'phone' => 'required|unique:users,phone|min:11|max:14',
        'email' => 'required|email',
        'password' => ['required', 'max:100',
        Password::min(4)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        'avatar' => 'required',
    ];
}
```
###### برای فایل RegisterRequest نیاز داریم تا Password را use کنیم.
```bash
use Illuminate\Validation\Rules\Password;
```
###### وارد UserController می شویم و تابع register را می نویسیم.
```bash
public function register(RegisterRequest $request)
{
    $check = User::where('email', $request->input('email'))->first();
    if ($check)
        return $this->failResponse([
            'errors' => ['error' => ['This E-Mail Already Exist']],
        ]);
    $data = $request->safe(['name', 'phone', 'email', 'password']);
    $user = new User([
        'name' => $data['name'],
        'phone' => $data['phone'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);
    $user->save();
    $user->assignRole(Role::findByName(Roles::USER, 'api'));
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
###### در ابتدا RegisterRequest را اعمال می کنیم تا درخواستی که ارسال شد، مورد ارزیابی قرار بگیرد که اگر قوانین ما را رعایت نکرده بود وارد تابع نشود و خطا هایی که مرتکب شده است به او اعلام شود. بعد، از طریق email ی که کاربر پر کرده است بررسی می کنیم که آیا کاربری با این email وجود دارد یا خیر، اگر وجو داشت پاسخ شکست دهیم و بگوییم کاربری با این email وجود دارد و اگر نه امکان ادامه روند شکل بگیرد.
###### مرحله بعدی، چهار داده ارسالی یعنی name و email و password و phone را با اجرای تابع safe امن می کنیم و این موارد را برای ادامه کار بر می گزینیم. کاربر جدید را می سازیم و نقش user را به اون نسبت می دهیم و بررسی می کنیم اگر فایل avatar وجود داشت، تصویر را ذخیره می کنیم و وقتی کار ذخیره سازی و ثبت نام به اتمام رسید، پیام ساخت کاربر را می دهیم، در غیر اینصورت پیام شکست می دهیم.
###### برای تابع register نیاز است تا موارد زیر را use کنیم.
```bash
use App\Http\Requests\RegisterRequest;
use Spatie\Permission\Models\Role;
use App\Enum\Roles;
```
###### برای اینکه بتوانیم از کدی که نوشتیم استفاده نماییم، به فایل api.yaml می رویم و ظاهر آن را می سازیم تا بتوان به این مکانیزم دسترسی پیدا کنیم. وارد components می شویم و در schema برای ثبت نام کد زیر را می نویسیم.
```bash
Register:
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
```
###### برای ثبت نام، schema خود یعنی Register را می سازیم و نوع آن را object می گذاریم و property های آن را با name و phone و email و password و avatar پر می کنیم که name و phone از نوع string است و password از نوع password و avatar را از نوع string در نظر می گیریم و format آن را binary در نظر می گیریم تا بتوان یک فایل را از طریق آن ارسال نماییم.
###### برای اینکه بتوانیم فرم را داشته باشیم در قسمت paths مسیر ثبت نام را می سازیم.
```bash
/api/register:
    post:
        tags:
            - User
        summery: Create a new user
        description: create a normal user for client
        requestBody:
            content:
                multipart/form-data:
                    schema:
                        $ref: "#/components/schemas/Register"
        responses:
            "200":
                description: Successful operation
                content:
                    application/json:
                        schema:
                            $ref: "#/components/schemas/ApiResponse"
```
###### برای باز کردن مسیر ثبت نام آدرس route آن را می نویسیم و سپس method آن که post است را می نویسیم. tag آن را زیر مجموعه User قرار می دهیم و برای آن خلاصه و توضیحاتی  در نظر می گیریم. نوع ساختار فرم را در requestBody مشخص می کنیم. چون در فرم خود در حال ارسال فایل هستیم، مجبوریم از نوع multipart/form-data استفاده کنیم و schema آن را به Register که ساختیم متصل می کنیم. برای حالت پاسخ هم مثل دفعات قبل وضعیت 200 را می نویسیم.

###### حال نوبت آن است که تابع profile را در UserController بسازیم تا بتوان به اطلاعات پروفایل کاربر login شده دسترسی پیدا کنیم.
```bash
public function profile()
{
    $user = User::where('id', Auth::id())->with('roles')->with('media')->first();
    if ($user) {
        return $this->successResponse([
            'user' => $user,
            'message' => 'profile',
        ]);
    } else
        return $this->failResponse();
}
```
###### تابع را می نویسیم و در آن یک جست و جو می زنیم و کاربر login شده را می یابیم و همراه آن  نقش ها و media او را نیز می گیریم به جواب را باز می گردانیم، اگر کاربر به هر دلیلی وجود نداشت، پاسخ عدم موفقیت درخواست را باز می گردانیم.
###### برای تابع profile نیاز است تا موارد زیر را use کنیم.
```bash
use Illuminate\Support\Facades\Auth;
```
###### برای نمایش این بخش نیز به فایل api.yaml می رویم و ساختار ظاهر شدن آن را می نویسیم، از آنجایی که فرمی نیاز ندارد تا داده ارسال کند به سمت سرور، فقط path آن را می سازیم.
```bash
/api/user/profile:
    get:
        tags:
            - User
        summery: get profile
        description: get user profile after login
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
###### مسیر خود را می سازیم و method آن را get می گذاریم و آن را در دسته بندی tag مورد نظر ما یعنی User می گذاریم، برای آن خلاصه و توضیحات می نویسیم و پاسخ از سمت سرور را مطابق گذشته تکرار می کنیم. در اینجا چون login بودن کاربر برای ما مهم است بخش security را فعال می کنیم و می گوییم در لحظه درخواست با خود مقدار bearerAuth ببر تا سرور تشخیص دهد ما کاربر login شده هستیم.
###### مرحله بعد سراغ updateMyProfile می رویم اما قبل آن فایل request آن را می سازیم.
```bash
php artisan make:request UpdateProfileRequest
```
###### در فایل UpdateProfileRequest ابتدا به سراغ تابع authorize می رویم و مقدار return آن را true می کنیم.
```bash
public function authorize(): bool
{
    return true;
}
```
###### حال به سراغ تابع rules می رویم و قوانینی که مورد نظر ماست برای ویرایش پروفایل کاربر، می نویسیم.
```bash
public function rules(): array
{
    return [
        'name' => 'nullable|min:3|max:100',
        'phone' => 'nullable|min:11|max:14',
        'email' => 'nullable|email',
        'password' => ['nullable', 'max:100',
        Password::min(4)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        'avatar' => 'nullable|image',
    ];
}
```
###### برای فایل UpdateProfileRequest نیاز داریم تا Password را use کنیم.
```bash
use Illuminate\Validation\Rules\Password;
```
###### حال وارد UserController می شویم و تابع updateMyProfile را می نویسیم.
```bash
public function updateMyProfile(UpdateProfileRequest $request, User $user)
{
    if ($user->id !== Auth::id()) {
        return $this->failResponse([], 403);
    }
    $data = $request->safe(['name', 'phone', 'email', 'password']);

    if ($request->input('name'))
        $user->name = $data['name'];

    if ($request->input('phone') && $request->input('phone') != Auth::user()->phone) {
        if (User::where('phone', $request->input('phone'))->first()) {
            return $this->failResponse([
                'errors' => ['error' => ['This Phone Already Exist']],
            ]);
        } else {
            $user->phone = $data['phone'];
        }
    }
    if ($request->input('email') && $request->input('email') != Auth::user()->email) {
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

    if ($request->file('avatar'))
        $this->storeUserAvatar($request->file('avatar'), $user->id);
    return $this->successResponse([
        'message' => 'User Updated',
    ]);
}
```
###### در این تابع ما ابتدا UpdateProfileRequest می نویسیم که آیا قوانین ما در صورت پر شدن داده ها رعایت شده یا خیر و بعد مدل User را صدا می کنیم تا برای ما کاربر مورد نظر را پیدا کند. وقتی این مرحله رد شد و وارد تابع شدیم، بررسی می کنیم این کاربری که درخواست داده با اطلاعات کاربری که می خواهد مطابقت دارد یا خیر. اگر مطابقت نداشت، پیام شکست با کد 403 صادر می کنیم، بعد از آن چهار مقدار name، phone، email و password را safe می کنیم. بعد آن شروع می کنیم به بررسی مقدار name، اگر پر شده بود آن را جای گذاری می کنیم.
###### بعد به سراغ phone می رویم، اگر پر شده بود، بررسی می کنیم آیا همان مقدار قبلی است یا خیر و اگر مقدار جدیدی هست، آیا با شماره تماس کاربر های دیگر تداخل ندارد، اگر وجود داشت، پیام شکست با متن این شماره تماس وجود دارد را صادر می کنیم و در غیر این صورت شماره تماس را ذخیره می کنیم. همین کار را متقابلا برای email هم انجام می دهیم و بعد به سراغ password می رویم، اگر پر شده بود، مقدار جدید را hash می کنیم و در user قرار می دهیم و کاربر را به روز می کنیم، اگر برای ویرایش avatar هم ارسال شده بود، دستور ذخیره سازی avatar را هم می دهیم و سر انجام پیام موفقیت با متن کاربر به روز شد را باز می گردانیم.

###### برای تابع updateMyProfile نیاز است موارد زیر را use کنیم.
```bash
use App\Http\Requests\UpdateProfileRequest;
```
###### حال وارد فایل api.yaml می شویم و ساختار نمایش فرم و ارسال داده را می سازیم. به همین منظور ابتدا در بخش components وارد می شویم و schema مورد نیاز برای ویرایش پروفایل را می سازیم.
```bash
UpdateProfile:
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
```
###### در UpdateProfile نوع را object می گذاریم و مقادیر property ها را مانند register می سازیم تا بتوانیم از آن ها استفاده کنیم. مرحله بعد به سراغ paths می رویم تا بتوان مسیر را ساخت و این schema را به کار گرفت.
```bash
/api/update-my-profile/{user}:
    post:
      tags:
        - User
      summery: Update Profile
      description: Update Your Profile
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
              $ref: "#/components/schemas/UpdateProfile"
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
###### در این مسیر برای ویرایش پروفایل کاربر خود ما یک پارامتر داریم و در کنار آن فرم هم باید اجرا شود. پس مسیر خود را می نویسیم و پارامتر را در آن تعیین می کنیم. وارد path می شویم و method مورد نظر خود یعنی post را برای آن قرار می دهیم. این مسیر را هم در زیر مجموعه tag کاربر یعنی User قرار می دهیم و برای آن هم خلاصه و توضیحاتی لحاظ می کنیم. پارامتر خود را هم نام پارامتر در path می نویسیم و توضیحات و الزامی بودن پر شدن را بر روی آن اعمال می کنیم و schema مورد نظر خود را به آن نسبت می دهیم.
###### در requestBody محتوا را از نوع multipart/form-data می گذاریم، چون همراه فرم یک فایل عکس هم ارسال می کنیم و ساختار فرم را ارجاع می دهیم به UpdateProfile که تازه ساخته ایم. حالت بازگشت هم مانند گذشته می نویسیم. برای ویرایش پروفایل هم security را فعال می کنیم تا login بودن ما برای سرور محرز شود.

###### حال همه چی برای ثبت نام، مشاهده و ویرایش پروفایل آماده است و می توان از آن استفاده نمود.
