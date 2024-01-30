# گرفتن تمام کاربر های سایت توسط مدیر با احراز نقش آن و آموزش Pagination

###### برای اینکه بخواهیم دسترسی باه کاربران را بدهیم ابتدا باید یک route بسازیم و از آن استفاده نماییم.
```bash
Route::get('all-users', [UserController::class, 'allUser'])->middleware(['can:'.Permissions::READ_ANY_ACCOUNT]);
```
###### یک route از نوع get می سازیم و آن را ارجاع می دهیم به تابع allUser و بر روی آن دسترسی READ_ANY_ACCOUNT را می گذاریم که فقط admin امکان دسترسی را دارد. حال به سراغ UserController می رویم و تابع allUser را می سازیم.
```bash
public function allUser()
{
    $perPage = request()->input('perPage') ?
        request()->input('perPage') : 2;
    $filter = request()->input('filter');
    if (Auth::user()->getRoleNames()[0] !== Roles::ADMIN) {
        return $this->failResponse([], 403);
    }
    $query = User::query()
        ->select([
            'id',
            'name',
            'phone',
            'email'
        ])
        ->when($filter, function (Builder $limit, string $filter) {
            $limit->where(DB::raw('lower(name)'), 'like', '%' . strtolower($filter) . '%');
        })
        ->with('roles')
        ->with('media')
        ->orderBy('id', 'desc');
    $users = $query->paginate($perPage);
    return $this->paginatedSuccessResponse($users, 'users');
}
```
###### قصد ما این است که داده های دریافتی را به صورت صفحه بندی یا paginate شده تحویل دهیم. برای مطالعه بیشتر می توانید به <a href="https://laravel.com/docs/10.x/pagination">Laravel Pagination</a> مراجعه نمایید. وقتی ما یک route می سازیم و در آن مکانیزم صفحه بندی را رصد می کنیم، لاراول در route به دنبال  مقدار page می گردد تا بتواند صفحه ای که شما می خواهید را به شما باز گرداند، اگر وجو نداشت، پیشفرض صفحه نخست را تحویل می دهد. وارد تابع که می شویم، یک متغیر perPage در نظر می گیریم تا کاربر ما بتواند تعیین کند در هر صفحه چه تعداد کاربر وجود داشته باشد، به عبارتی 5 تا 5 تا می خواهد کاربر را تحویل بگیرد یا تعدادی دیگر. perPage را نیز به گونه ای نوشتیم که به آن ternary می گویند و تعیین کردیم که اگر مقدار perPage ارسال نشد یا خالی بود، در هر صفحه 2 تا 2 تا کاربر بازگردان. یک متغیر filter هم می سازیم تا اگر کاربر خواست بر روی نام کاربر های جست و جو کند، بتواند.
###### در مرحله بعد بررسی می کنیم آیا درخواست دهنده از admin های ما هست یا خیر، اگر نبود با خطا 403 پاسخ شکست باز می گردانیم.
###### سپس یک متغیر query می سازیم و بر روی User جست و جو می کنیم و ستون هایی که می خواهیم را select می کنیم و بر روی جست و جو خود یک شرط می گذاریم، اگر مقدار filter خالی نبود وارد فرآیند تفکیک کردن داده شویم. وقتی وارد آن شویم دو متغیر وجود دارد، یکی limit که نماینده هر ردیف رکورد query است و filter هم نماینده مقداری که می خواهیم از طریق آن جست و جو را تفکیک کنیم. در تفکیک خود یک شرط می گذاریم که اگر برقرار بود انتخاب شود، ابتدا مدل DB را صدا می کنیم تا بتوانیم به دستور خام MySQL بنویسیم، در شروع name را به حروف کوچک تبدیل می کنیم که بزرگ و کوچک بودن کاراکتر ها موجب تمایز نشود و بعد دستور می دهیم like که همان شبیه بودن است اجرا شود، یعنی اگر کاراکتر هایی که داده ایم در name وجود داشت، مورد تایید ما نیز است و در ادامه مقدار filter را نیز کوچک می کنیم و قبل و بعد آن از % استفاده می کنیم که یعنی اگر اول یا آخر آن به کلماتی که ما گفته ایم شروع یا تمام شد، مورد تایید است.
###### در گام بعدی نقش و تصویر هر کاربر را از طریق ORM پیدا می کنیم و با orderBy نحوه مرتب سازی جست و جو را نزولی می کنیم. کل جست و جو را paginate می کنیم به تعداد perPage و آن را در users قرار می دهیم. حال از paginatedSuccessResponse استفاده می کنیم تا داده های های ما را به صورت json که از قبل تعریف کرده ایم، به درخواست دهنده ارسال کند.
###### برای تابع allUser نیاز داریم تا موارد زیر را use کنیم.
```bash
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
```

###### حال وارد api.yaml می شویم و بخش ظاهر آن را می سازیم.
```bash
/api/all-users?page={page}&perPage={perPage}&filter={filter}:
    get:
      tags:
        - User
      summery: get all user
      description: All User for admin
      parameters:
        - name: page
          in: path
          description: page number for paginate
          schema:
            type: integer
          allowEmptyValue: true
        - name: perPage
          in: path
          description: how many user in a page
          schema:
            type: string
          allowEmptyValue: true
        - name: filter
          in: path
          description: write your username to find it
          schema:
            type: string
          allowEmptyValue: true
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
###### ابتدا path خود را می سازیم و سه متغیر به عنوان پارامتر های مورد نیازمان در آن می گنجانیم. اولی page است برای دریافت شماره صفحه و دومی perPage است برای دریافت تعداد کاربر های دریافتی در هر صفحه و مقدار سوم filter است برای جست و جو کردن کاربر خاص. وارد path شده و method خود را get می گذاریم و مسیری که ساخته ایم را در دسته user قرار می دهیم، برای path خود خلاصه و توضیحاتی در نظر می گیریم و شروع می کنیم به تعریف سه پارامتر برای هر سه آن ها نام و اینکه داخل path باشند و توضیحات و schema در نظر می گیریم و با قرار دادن allowEmptyValue برابر با true، به آن ها اجازه می دهیم که خالی هم ارسال شوند. در انتها مانند  دفعات گذشته  برای پاسخ برنامه ریزی می کنیم و حالت آن را نیز مشخص می کنیم.




