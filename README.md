# حذف کاربر و media آن توسط admin با بررسی دسترسی درخواست دهنده

###### برای اینکه بخواهیم ساختار حذف یک کاربر را بسازیم، لازم است تا ابتدا یک route را در api.php ایجاد نماییم.
```bash
Route::delete('delete-user-by-admin/{user}', [UserController::class, 'deleteUserByAdmin'])->middleware(['can:'.Permissions::DELETE_ANY_ACCOUNT]);
```
###### برای حذف کاربر یک route از نوع delete می سازیم که مانند get است اما معین شده برای حذف  هر چه که ما می خواهیم. در مبحث REST API و RESTFull API، یکی از مولفه ها، استفاده از نوع route هماهنگ با نوع عملی است که قرار است اتفاق بی افتد.
###### مسیر خود را از نوع delete ساختیم و ارجاع دادیم به تابع deleteUserByAdmin در UserController و دسترسی DELETE_ANY_ACCOUNT را که ویژه مدیر سایت است را به آن دادیم.

###### حال وارد UserController می شویم و تابع deleteUserByAdmin را می سازیم.
```bash
public function deleteUserByAdmin(User $user)
{
    if (Auth::user()->getRoleNames()[0] !== Roles::ADMIN) {
        return $this->failResponse([], 403);
    }
    if ($user->media)
        $this->deleteMedia($user->media);
    if ($user->delete()) {
        return $this->successResponse([
            'message' => 'User Deleted',
        ]);
    }
    return $this->failResponse();
}
```
###### تابع deleteUserByAdmin را می سازیم و در پارامتر های آن مدل User را قرار می دهیم تا وقتی ID ارسال شد، کاربر را شناسایی کند. در تابع ابتدا بررسی می کنیم آیا درخواست دهنده مدیر است یا خیر، اگر نبود درخواست او را باز می گردانیم. مرحله بعدی قبل از آنکه بخواهیم کاربر را حذف نماییم، ابتدا تصویر avatar او را حذف می کنیم. در گام بعدی دستور حذف کاربر را می دهیم و شرط می کنیم اگر حذف شد، یک پیام موفقیت برای درخواست دهنده ارسال شود و اعلام شود که کاربر با موفقیت حذف شد. اگر هم در روند حذف مشکلی زخ داد، درخواست دهنده را از مشکل پیش آمده آگاه می سازیم.
###### حال نوبت آن است که وارد api.yaml شویم و مسیر حذف کاربر را در paths بنویسیم تا بتوان از طریق آن درخواست حذف کاربر را داد.
```bash
/api/delete-user-by-admin/{user}:
    delete:
      tags:
        - User
      summery: delete any user
      description: admin can be deleted any user
      parameters:
        - name: user
          in: path
          description: User ID
          schema:
            type: integer
            format: int64
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
###### مسیر را مانند الگو سرور می سازیم و نوع آن را delete می گذاریم و tag آن را user قرار می دهیم تا در دسته بندی کار هایی قرار گیرد که مربوط به user است. برای مسیر خود خلاصه و توضیحاتی در نظر می گیریم و یک پارامتری به وجود می آوریم تا به وسیله آن ID کاربر خود را دریافت کنیم. نوع پارامتر و توضیحی که نیاز دارد را برای آن می نویسیم و پر کردن آن را اجباری می کنیم. پاسخ را نیز مانند گذشته به همان منوال می نویسیم. برای درخواست خود نیاز داریم تا هویت کاربر احراز شود، از همین رو security را فعال می کنیم.
###### حال همه چی آماده است تا بتوان یک کاربر را حذف نمود.





