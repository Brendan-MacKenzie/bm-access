### Description
This packages adds an easy way to manage the visibility of your hidden fields of any model in the response data. This package uses Spatie's laravel-permissions package for testing roles and is currently a requirement to be able to use this package.

### Installation

Install the package using Composer
```
composer require brendan-mackenzie/bm-access
```

Add trait to your model class
```
use AccessManager;
```

Now you can add the `$protectedFields` variable and any *test* functions to you model class and write some rules for your hidden fields
```
$protectedFields = [
    'fieldOne' => 'self.created_by',
    'fieldTwo' => 'function',
    'fieldThree' => 'admin'
    'fieldFour' => [
        'self', // short for self.id
        'admin'
        'manager:can see field four'
        'can see field four&&can see user fields'
        '(can see all fields || can see all fields)&&can see user fields'
    ]
];

private function fieldTwoFieldTest()
{
    return true;
}
```