# MAPP Integration w/ Laravel

> :warning: **DO NOT USE THIS CODE**: (use it at your own risk). This is very unstable, not well written, it's only a proof of concept for learning purpose!

Add in `.env`

```bash
MAPP_URL=https://up.to/api/version
MAPP_USERNAME=
MAPP_PASSWORD=
```

Add in `config/services.php`

```php
// ...
'mapp' => [
    'url' => env('MAPP_URL', ''),
    'username' => env('MAPP_USERNAME', ''),
    'password' => env('MAPP_PASSWORD', ''),
],
// ...
```


Modify `app/Providers/AppServiceProvider.php`

```php

use Dentsu\MAPP\MAPP;

//... inside register

$this->app->singleton(
    MAPP::class, 
    fn() => new MAPP(
        config('services.mapp.url'),
        config('services.mapp.username'),
        config('services.mapp.password')
    )
);
```

Use it in Dependency Injection for example in controller

```php
use Dentsu\MAPP\MAPP;

// ...
public function __construct(private MAPP $mapp) {}
// ...
public function example(string $email)
{
    $parameters = [
        'param1' => 'value1',
        'param2' => 'value2',
        'param3' => 'value3',
    ];
  
    $this->mapp->sendEmail($email, $templateID, $parameters);
}
```