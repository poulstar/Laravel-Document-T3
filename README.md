# اجرای رابطه های ما بین مدل و آموزش Polymorphism

###### حال نوبت آن است که شروع کنیم به تعریف رابطه های ORM در مدل ها تا در آینده بتوانیم از آنها استفاده کنیم و از query زدن جلو گیری کنیم.

###### ابتدا وارد مدل Comment می شویم و شروع می کنیم به تعریف رابطه های بین دیگر مدل ها. ابتدا مواردی از مدل که نیاز داریم fillable باشد و بشود از بیرون آن را پر کرد، تعیین می کنیم.
```bash
protected $fillable = [
    "user_id",
    "post_id",
    "parent_id",
    "title",
    "text",
    "status"
];
```
###### سپس ارتباط با User را مشخص می کنیم. قطعا هر کاربر ما می تواند یک تا بی نهایت Comment دهد و قطعا هر Comment ما متعلق به یک User است، از همین رو رابطه از سمت Comment به User به صورت belongsTo تعریف می شود و از سمت User به Comment به صورت hasMany تعریف می شود.
###### در Comment به صورت زیر می نویسیم.
```bash
public function user()
{
    return $this->belongsTo(User::class);
}
```
###### در user به صورت زیر می نویسیم.
```bash
public function comments()
{
    return $this->hasMany(Comment::class);
}
```
###### ارتباط Comment با Post هم به همین صورت است، هر Comment قطعا برای دو Post نیست و بر روی یک Post به نمایش در می آید و از آن طرف هر Post می تواند یک تا بی نهایت Comment داشته باشد.
###### برای Comment می نویسیم.
```bash
public function post()
{
    return $this->belongsTo(Post::class);
}
```
###### برای Post می نویسیم.
```bash
public function comments()
{
    return $this->hasMany(Comment::class);
}
```
###### ما در migration مدل Comment ستونی تعریف کردیم که id پدر یا والد یا همان parent را در خود جای می دهد و می تواند هم خالی باشد، به عبارتی یک Comment می تواند ریشه باشد و زیر مجموعه هیچ Comment ی نباشد و می تواند هم زیر مجموعه باشد، از همین رو اگر بخواهیم رابطه ای در مدل تعریف کنیم، باید دقت کنیم اگر Comment ی دارای parent است، قطعا یک parent بیشتر ندارد. پس رابطه از نوع belongsTo است. از همین رو رابطه را به صورت زیر می نویسیم.

```bash
public function parent()
{
    return $this->belongsTo(Comment::class, 'parent_id');
}
```
###### گاه پیش می آید که ما می خواهیم بدون query زدن، از طریق ORM به Comment های زیر مجموعه برسیم. از همین رو رابطه ای تعریف می کنیم که بتوانیم از طریق آن کلیه Comment ها را بگیریم. قطعا چون یک Comment می تواند بیش از یک زیر مجموعه داشته باشد، رابطه از نوع hasMany است.
```bash
public function children()
{
    return $this->hasMany(Comment::class, 'parent_id', 'id');
}
```
###### حال نوبت آن است که به سراغ مدل UpVote برویم. در این مدل دو ارتباط دیده می شود، اول اینکه هر vote به یک Post داده می شود و هر vote را قطعا یک User ثبت کرده است. از همین رو وارد یکی یکی بررسی می کنیم و کار را سامان دهی می کنیم. ابتدا به سراغ UpVote با Post می رویم. قطعا هر UpVote من متعلق است به یک Post و هر پست من می تواند یک تا بی نهایت UpVote داشته باشد. از همین رو رابطه از سمت UpVote به Post از نوع belongsTo است و از Post به UpVote از نوع hasMany، حال می رویم کد های آن ها را بنویسیم.
###### در UpVote می نویسیم.
```bash
public function post()
{
    return $this->belongsTo(Post::class);
}
```
###### در Post می نویسیم.
```bash
public function votes()
{
    return $this->hasMany(UpVote::class);
}
```
###### حال همین مبحث برای User ما نیز برقرار است. هر UpVote قطعا توسط یک User ثبت شده و هر User می تواند یک تا بی نهایت UpVote ثبت کند. از همین رو رابطه از سمت UpVote به User از نوع belongsTo است و رابطه از سمت User به UpVote از نوع hasMany می باشد.
###### در UpVote می نویسیم.
```bash
public function user()
{
    return $this->belongsTo(User::class);
}
```
###### در User می نویسیم.
```bash
public function upVotes()
{
    return $this->hasMany(UpVote::class);
}
```
###### حال می رسیم به مدل Post، رابطه ای در Post، با User ما وجود دارد، اینکه یک Post را بیش از یک User نمی تواند ثبت کند پس مشخص است رابطه از سمت Post به User یک رابطه belongsTo است و از آن طرف قطعا یک User می تواند یک تا بی نهایت Post بگذارد، به همین دلیل رابطه از سمت User به Post از نوع hasMany است. حال به صورت زیر عمل می کنیم.
###### در Post می نویسیم.
```bash
public function user()
{
    return $this->belongsTo(User::class);
}
```
###### در User می نویسیم.
```bash
public function posts()
{
    return $this->hasMany(Post::class);
}
```
###### حال وارد رابطه بعدی در Post می شویم که رابطه ما بین Post و Media است. برای اینکه شروع کنیم به نوشتن رابطه کمی با هم تحلیل کنیم و با علم بیشتر به سراغ نوشتن کد برویم. دقت کنید که ما یک مدل User داریم که می تواند یک Media داشته باشد و یک مدل Post داریم که آن هم می تواند Media داشته باشد، می شد در همان جدول خودشان، ستونی طراحی کرد که آدرس عکس را در آن ذخیره کنیم همراه با اطلاعات آن عکس، حال تصمیم گرفتیم یک جدول جداگانه داشته باشیم تحت عنوان media، اما باز در جدول media ما ستونی برای id مربوط به post و user نداریم که بخواهیم بفهمیم کدام media مربوط به user یا post است. به همین دلیل جدول واسطی به نام model_has_media ساخته شده است که در آن اطلاعات را نگه داریم، اما داخل آن ما شناسه model را داریم و نوع model و شناسه media را، حال تکلیف چیست اگر یکبار model شماره یک user باشد بعدی model شماره یک post. در این حالت مشخص نیست که شماره یک منظور رکورد موجود در جدول users است یا posts. برای اینکه تعیین کنیم کدام یک متعلق به user و کدام یک متعلق به post است، از type کمک می گیریم، اینکه در ساختار خود گاهی برای یک رکورد می شود به یک model مربوط باشد یکبار دیگر به دیگر مدل، چند ریختی  یا polymorphism می گویند. برای اینکه درک این موضوع راحت تر شود بیاییم یک مثال را با هم مرور کنیم. فرض کنید در یک کارخانه مدل های مختلف ماشین وجود دارد، مثلا bmw و benz. حال bmw ماشینی طراحی کرده که نام آن 110 است و benz هم همینطور، حال اگر بخواهیم آن را در یک جدول car_type نگه داریم و زمانی که گزارشی از تمام ماشین ها خواسته شد، ارائه دهیم، مشخص نیست که مدل 110 مربوط به کدام model است، به همین منظور نوع model را ذخیره می کنیم، به اینکه می شود در یک سیستم یکبار یک رکورد را به نام یک model ثبت کرد و یکبار به نام دیگر مدل، چند ریختی یا polymorphism می گویند. حال برویم سراغ کد نویسی خود، ما نیاز داریم برای Post و User یک Media اختصاصی داشته باشیم اما این امکان را می گذاریم که بتواند بیش از یک Media هم داشته باشد، شاید نیاز شود. از همین رو در بحث کد نویسی به صورت زیر عمل می کنیم.
###### در مدل Post می نویسیم.
```bash
public function media()
{
    return $this->morphToMany(Media::class, 'model', 'model_has_media');
}
```
###### در مدل User می نویسیم.
```bash
public function media()
{
    return $this->morphToMany(Media::class, 'model', 'model_has_media');
}
```
###### و چون در مدل Media فقط نیاز داریم از طریق ORM به User برسیم، یک رابطه بیشتر تعریف نمی کنیم.
```bash
public function user()
{
    return $this->belongsTo(User::class);
}
```
