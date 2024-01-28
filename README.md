# پیاده سازی پاسخ های JSON موفقیت و شکست و همچنین تابع های مورد نیاز جهت ذخیره تصویر کاربر و پست

###### برای اینکه بتوانیم سرور خود را برای حالت ها مختلف آماده کنیم، باید وضعیت های مختلف را در نظر بگیریم و کد نویسی های آن را انجام دهیم. اولین نیاز برای بازگرداندن  پاسخ درست یا همان 200 شبکه است، از همین رو به صورت زیر می نویسیم. برای این کار وارد Controller.php می شویم.
```bash
function successResponse($data = [], $code = 200)
{
    return response()->json(
        array_merge([
            'message' => 'Your Request Succeeded',
        ], $data),
        $code
    );
}
```
###### تابع پاسخ موفقیت ما دو پارامتر می گیرد که اولی داده ای است که می خواهیم ارسال کنیم و دومی شماره code ی است که مد نظر ماست. دقت کنید که این مقدار ها به صورت پیش فرض مقدار دهی شده است. وقتی تابع صدا زده می شود، پاسخی در قالب json باز می گردانیم که پارامتر اول را به داده تخصیص می دهیم و پارامتر دوم را به شماره code. دقت کنید که پارامتر اول با استفاده از array_merge، دو داده موجود در خود را مجدد به یک آرایه تبدیل می کند.
###### حال همین کار را برای حالت شکست خوردن در نظر می گیریم و به صورت زیر می نویسیم.
```bash
function failResponse($data = [], $code = 504)
{
    return response()->json(
        array_merge([
            'message' => 'Your Request Failed',
        ], $data),
        $code
    );
}
```
###### در حالت پاسخ شکست، تنها مواردی که تغییر می کند، عبارت اند از کد 504 که به صورت پیش فرض تنظیم شده است که نشان دهنده ی مشکل سرور است و پیامی که داخل array_merge است هم به متن جدید تغییر می کند.

###### دو تابع دیگر نیاز است تا برای کار خود، یکی برای اینکه بتوانیم عکس یک پست را ذخیره کنیم و دیگری آواتار یک کاربر را، دقت کنید یک مسئله پیش روی ما است، اینکه ما نیاز داریم موقع ثبت کردن تصویر چک کنیم آیا تصویری نسبت به آن object ثبت شده است یا خیر، به همین دلیل یک تابع می سازیم که media را به آن می دهیم تا بررسی کند آیا وجود دارد یا خیر، اگر وجود داشت آن را برای ما پاک نماید. از همین رو کد زیر را می نویسیم.
```bash
function deleteMedia($media)
{
    if ($media) {
        foreach ($media as $file) {
            File::delete($file->url);
            $file->delete();
        }
    }
    return back();
}
```
###### حال برای کار خود ابتدا تابع مربوط به ذخیره تصویر را می  سازیم.
```bash
function storePostMedia($file, $model_id, $user_id)
{
    $post = Post::where('id', $model_id)->with('media')->first();
    if (isset($post->media)) {
        $this->deleteMedia($post->media);
    }
    $name = Carbon::now()->getTimestamp() . '.' . $file->extension();
    $file->storePubliclyAs('public/media/', $name);
    $media = new Media([
        'size' => $file->getSize(),
        'mime_type' => $file->getMimeType(),
        'url' => 'storage/media/' . $name
    ]);
    $media->user()->associate($user_id);
    $media->save();

    $post = Post::find($model_id);
    $post->media()->sync($media, ['create_at' => Carbon::now()]);
    $post->save();
    return back();
}
```
###### تابع را به گونه ای آماده می کنیم بتواند فایل را ذخیره کند و همزمان به مدل مربوطه نسبت دهد که اینجا همان پست است و همچنین کدام کاربر آن را به ثبت رسانده است را تعیین کنیم، به همین منظور ابتدا بررسی می کنیم در پست ها آیا این پست وجود دارد؟ اگر وجود داشت، آیا media دارد؟ اگر داشت اقدام به پاک کردن آن می نماییم. پس از آن از طریق فایل یک نام برای آن می سازیم و دستور ذخیره سازی فایل را می دهیم. در مرحله بعدی یک media جدید می سازیم و ستون های جدول آن را با استفاده از فایل خود پر می کنیم، دقت کنید که media را به user مربوطه خود نسبت می دهیم تا بدانیم این تصویر توسط کدام کاربر ساخته شده است. در مرحله آخر تصویری که ساخته شده را به پست جاری وصل می کنیم تا از طریق آن بتوانیم به تصویر پست برسیم.

###### بعد آن نوبت می رسد به ذخیره آواتار یک کاربر، به همین منظور تابع زیر را درست می کنیم.
```bash
function storeUserAvatar($file, $model_id)
{
    $user = User::where('id', $model_id)->with('media')->first();
    if (isset($user->media)) {
        $this->deleteMedia($user->media);
    }
    $name = Carbon::now()->getTimestamp() . '.' . $file->extension();
    $file->storePubliclyAs('public/media/', $name);
    $media = new Media([
        'size' => $file->getSize(),
        'mime_type' => $file->getMimeType(),
        'url' => 'storage/media/' . $name
    ]);
    $media->user()->associate($model_id);
    $media->save();

    $user = User::find($model_id);
    $user->media()->sync($media, ['create_at' => Carbon::now()]);
    $user->save();
    return back();
}
```
###### در تابع ذخیره ساز آواتار، نیاز به فایل و مدل داریم که مدل ما همان user است. پس ابتدا بررسی می کنیم اصلا همچین کاربری وجود دارد؟ اگر وجود داشت آیا media دارد یا خیر؟ اگر داشت ابتدا media آن را پاک می کنیم و بعد برای ذخیره تصویر جدید اقدام می کنیم. پس از گذر این مرحله نوبت می رسد به ساخت نام فایل و بعد آن ذخیره تصویر خود، و وقتی ذخیره شده، یک رکورد media می سازیم برای تصویری که ذخیره شده است، حال media موجود را به user مورد نظر خود که همان model هست، نسبت می دهیم. پس از آن کاربر را پیدا کردیم؛ تصویر را به نامش ثبت می کنیم.

###### برای کلیه مواردی که مطرح شده است، نیاز است موارد زیر use شود.
```bash
use Illuminate\Support\Facades\File;
use App\Models\Post;
use Carbon\Carbon;
use App\Models\Media;
use App\Models\User;
```

###### حال همه چی برای شروع یک RESTFull API آماده است.



