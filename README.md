# دریافت پست ها به صورت صفحه بندی شده با اطلاعات کامل و آموزش استفاده از Postgresql جهت تبدیل point به مختصات طول و عرض جغرافیایی برای مشاهده عمومی

###### برای اینکه بخواهیم درخواستی دهیم تا پست ها را بر اساس لایک های آن ها دریافت کنیم، یعنی لایک های بیشتر بالاتر باشند و بر اساس لایک به صورت نزولی مرتب شده باشد. از همین رو نیاز است ابتدا route آن را در api.php بسازیم.
###### ابتدا لازم است برای اینکه کلیه کار های مربوط به post جداگانه باشد، یک controller جدید به نام PostController بسازیم.
```bash
php artisan make:controller PostController
```
###### سپس route را به صورت زیر بسازیم.
```bash
Route::get('all-posts-for-dashboard', [PostController::class, 'allPostsForDashboard'])->middleware(['can:'.Permissions::VIEW_ANY_POST]);
```
###### برای route خود از method مورد نیاز یعنی get استفاده می کنیم و آن را ارجاع می دهیم به allPostsForDashboard در PostController و بر روی آن دسترسی VIEW_ANY_POST را اعمال می کنیم تا کاربر هایی که این دسترسی را دارند، بتوانند آن را ببینند.
###### برای route خود نیاز داریم تا PostController را use کنیم.
```bash
use App\Http\Controllers\PostController;
```
###### وارد PostController شده و تابع allPostsForDashboard به صورت زیر می نویسیم.
```bash
public function allPostsForDashboard()
{
    $query = Post::query()
    ->select([
        'id',
        'user_id',
        'title',
        'description',
        'up_vote_count',
        DB::raw('ST_X(location::geometry) AS latitude'),
        DB::raw('ST_Y(location::geometry) AS longitude')
        ])
    ->with('media')
    ->with('user')
    ->orderBy('up_vote_count', 'desc');
    $posts = $query->paginate(4);
    $topPosts = $query->take(3)->get();
    return $this->successResponse([
        'posts' => $this->paginatedSuccessResponse($posts,'posts'),
        'topPosts' => $topPosts,
    ],200);
}
```
###### در داخل تابع یک query می سازیم از مدل Post و بر روی داده ها ستون هایی که می خواهیم را select می کنیم و برای بدست آوردن طول و عرض جغرافیایی، از طریق دستور خام database ی موجود در مستند <a href="https://postgis.net/workshops/postgis-intro/geography.html">postgis</a>، دستور می دهیم تا مقدار محور X به عنوان latitude در آید و محور Y به عنوان longitude. همراه هر رکورد، درخواست می دهیم تا media و اطلاعات کاربر به ثبت رسانده آن را هم به ما بدهد، سر انجام بر اساس تعداد لایک ها به صورت نزولی مرتب می کنیم. وقتی query کامل شد، نتیجه را به صورت یک پیام موفق که در آن لیستی از پست ها به صورت paginatedSuccessResponse  قرار می دهیم و همچنین سه پست برتر را نیز در آن جای می دهیم.
###### برای تابع allPostsForDashboard موارد زیر را use می کنیم.
```bash
use App\Models\Post;
use Illuminate\Support\Facades\DB;
```
###### بعد آن نوبت وارد شدن در api.yaml است تا ظاهر را بسازیم و بتوانیم درخواست را بررسی کنیم، از همین رو وارد بخش paths رفته و path مورد نظر خود را به صورت زیر می سازیم.
```bash
/api/all-posts-for-dashboard?page={page}:
    get:
      tags:
        - Post
      summery: get all post
      description: get all post ant top post for dashboard
      parameters:
        - name: page
          in: path
          description: page number for pagination
          schema:
            type: integer
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
###### الگو path را مطابق سرور می سازیم و متغیر page را در آن قرار می دهیم تا بتوانیم برای صفحات دیگر نیز درخواست دهیم. برای path خود نیز از method همسان با سرور یعنی get استفاده می کنیم و tag آن را برابر با post می گذاریم تا در دسته پست های ما قرار گیرد. برای این path خلاصه و توضیحات در نظر می گیریم و بعد پارامتر page را شروع می کنیم به تعریف کردن، نوع آن را از اعداد صحیح در نظر می گیریم و اجازه می دهیم حتی خالی هم ارسال شود. پس از آن بخش مربوط به responses را مثل سابق می نویسیم. چون نیاز به احراز هویت جهت login بودن لازم است، در انتها security را قرار می دهیم.

