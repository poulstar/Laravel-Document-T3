# دریافت پست ها به صورت صفحه بندی شده با اطلاعات کامل و آموزش استفاده از Postgresql جهت تبدیل point به مختصات طول و عرض جغرافیایی برای مشاهده عمومی به همراه اجرا لایک بر روی هر پست و آموزش boot نویسی در مدل

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
###### حال نوبت آن است که وارد api.php شده و فرآیند لایک را پیاده سازی کنیم.
```bash
Route::get('posts/{post}/like', [PostController::class, 'likePost'])->middleware(['can:'.Permissions::LIKE_ANY_POST]);
```
###### یک route از نوع get می سازیم و به تابع likePost در PostController ارجاع می دهیم و دسترسی LIKE_ANY_POST را بر روی آن می گذاریم.
###### در PostController تابع likePost را به صورت زیر می نویسیم.
```bash
public function likePost(Post $post) {
    $upVote = UpVote::where('user_id', Auth::id())
        ->where('post_id', $post->id)
        ->first();
    if($upVote) {
        $upVote->delete();
        return $this->successResponse(['message' => 'Post like removed', 'vote' => -1]);
    }else {
        $upVote = new UpVote();
        $upVote->user()->associate(Auth::id());
        $upVote->post()->associate($post->id);
        $upVote->save();
        return $this->successResponse(['message' => 'Post like added', 'vote' => 1]);
    }
}
```
###### در تابع likePost از مدل Post استفاده می کنیم تا داده های مربوط به پست مورد نظر را بیابد و در ابتدا یک جست و جو می کنیم نسبت به اینکه آیا فردی که درخواست لایک داده، تاکنون این پست را لایک کرده یا خیر. اگر حاصل query به داده ای رسید، در شرط اول دستور حذف upVote مورد نظر را می دهیم و نتیجه را با پیام موفقیت به درخواست دهنده باز می گردانیم و اگر upVote خالی بود، یک upVote جدید می سازیم و آن را به کاربر درخواست دهنده و پست مورد نظر نسبت می دهیم و نتیجه را باز می گردانیم.
###### برای تابع likePost نیاز است تا موارد زیر را use کنیم.
```bash
use Illuminate\Support\Facades\Auth;
use App\Models\UpVote;
```
###### دقت کنید تاکنون در جدول up_votes یک رکورد کم یا زیاد شده است، در حالی که تعداد لایک های هر پست در ستون up_vote_count پست نگه داری می شود و نتیجه کار ما موجب تغییر آن نشده است، از همین رو این کار را از طریق boot در مدل UpVote انجام می دهیم.
```bash
protected static function boot()
{
    parent::boot();
    UpVote::created(function (UpVote $upVote) {
        $post = $upVote->post;
        $post->increment('up_vote_count', 1);
        $post->save();
    });
    UpVote::deleted(function (UpVote $upVote) {
        $post = $upVote->post;
        $post->decrement('up_vote_count', 1);
        $post->save();
    });
}
```
###### تابع boot را می نویسیم، در ابتدا دستور می دهیم parent boot را اجرا می کنیم سپس شرایط خود را تعریف می نماییم. برای مطالعه بیشتر می توانید به <a href="https://laravel.com/docs/10.x/eloquent">Laravel Eloquent</a> رجوع کنید. برای دو حالت مختلف برنامه ریزی می کنیم، یکی زمانی که یک vote ساخته شد و دیگری زمانی که یک vote پاک شد. در حالتی که ساخته شد، از طریق vote به پست مورد نظر می رسیم و مقدار up_vote_count را یکی افزایش می دهیم و زمانی که حذف شد همین عمل را در جهت کاهش انجام می دهیم.
###### برای ساختن ساختار قابل مشاهده جهت آزمایش کد های خود وارد api.yaml شده و path مورد نظر را در مجموعه paths می نویسیم.
```bash
/api/posts/{post}/like:
    get:
      tags:
        - Post
      summery: like post
      description: user can like or dislike post
      parameters:
        - name: post
          in: path
          description: Post ID
          schema:
            type: integer
          required: true
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
###### وقتی path را مطابق الگو سرور نوشتیم، method آن را get می گذاریم و خلاصه و توضیحاتی برای آن در نظر می گیریم. برای لایک کردن یک پست نیاز داریم تا ID یک پست را دریافت کنیم، از همین رو پارامتر post  را می نویسیم و برایش یک توضیحی در نظر می گیریم و نوع آن را مشخص کرده و پر کردن آن را اجباری می نماییم. باز هم مانند گذشته responses را نوشته و بعد آن به دلیل نیاز به احراز هویت شدن، security را می نویسیم.
###### حال مشاهده paginate شده پست ها و همچنین امکان like هر پست که می خواهیم فراهم است و می توانیم از آن استفاده نماییم.
