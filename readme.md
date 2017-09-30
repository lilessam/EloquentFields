### Eloquent-Fields Generator Package For Laravel
This package actually contains a helper function and a trait.
Its basic target is to reduce time that developers spend writing Creation & Update forms markup for Eloquent Models.

It's basic idea is to use the `FormGenerator` trait in your model and write a tiny array with your fields' preferences. Then you can use the helper function to generate a creation or update form.

#### Installing.
###### Via composer
```shell
composer require lilessam/eloquent-fields dev-master
```
Then in your model use the trait `Lilessam\EloquentFields\Traits\FormGenerator` like the following:
```PHP
<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Lilessam\EloquentFields\Traits\FormGenerator;

class Fee extends Model
{
    use FormGenerator;
}

```

#### How to use it?
By defining a static array in your model called `$fields` and its structure should be as following example.
```PHP
public static $fields = [
    'field_name_attribute' => [ // Field name attribute should be the column name in DB as well to retreive its value in update form generation.
        'label'    => 'اسم الحقل', // Field Label.
        'input'    => 'input', // Field Type.
        'type'     => 'text'  // Field Input Type.
    ],
];
```
This will generate a bootstrap `form-group` div contains this field.
Then you can generate a creation form fields in your view files like the following example:
```HTML
<form class="form-horizontal" method="post">
    {!! generate_fields('App\Fee') !!}
    {!! csrf_field() !!}
    <button type="submit" class="btn btn-primary">حفظ!</button>
</form>
```
And you can create an update form fields by passing a second parameter contains the object id.
```HTML
<form class="form-horizontal" method="post">
    {!! generate_fields('App\Fee', 1) !!}
    {!! csrf_field() !!}
    <button type="submit" class="btn btn-primary">حفظ!</button>
</form>
```
So this actually will generate an update form fields for the row which its id is 1.

#### Full Guide
##### $fields array writing rules.
1. Must provide `label`.
2. Must provide `input` which can be `input`, `select` or `textarea`.

If the `input` index is `input` you must provide a `type` index to provide the input type like `text` or `number` or `date` etc.

If the `input` type is `select` you can provide an `options` index which will contain an array for the select dropdown options. Or you can provide a `relation` index which will get options from a relation in the eloquent model.

##### Relation array
There's three types of Eloquent Relatioships supported till now in the package. You have to provide a `type` index in the relation array to specify the relation type based on the following:
1. `belongsTo` its type will be `one` because we will only select one option in this case.
2. `hasMany` Or `belongsToMany`: their type will be `many` because we will select multiple values.

If the `type` index in the relation array is `one` you will need to provide
* `model` index which will be the relation model.
* `column` which is the foreign key.
* `selectFrom` which will be the column we need to display in the option from the relation model.
* `valueFrom` which will be the column from the relational model that will be the value of the option of the select dropdown.

If the `type` index in the relation array is `many` you will need to provide 
* `name` index which will be the name of the relation. For example if my model is `User` and it has many `Role` so the name of the relation will be `roles` or whatever the name of the relation you created in the `User` model.

You will also need to provide a `selectFrom` and `valueFrom` as well.

##### Additional Preferences
There are multiple additional things that can be very usefull in all your fields specifications.
1. `label_classes` which is the label class attribute value. You can override the default classes which are `control-label col-md-4`.
2. `input_div_classes` which is the class attribute of the div that contains the input code. By default it's `col-md-6`.
3. `input_classes`:which is the class attribute of the input itself. By default it's `form-control`.
4. `input_id` which is the id attribute of the input itself. By default it's the field name.
5. `inject_attributes` which allows you to add additional attributes to the input. It just takes the string and put it in the input tag.


#### Examples.
A normal text field:
```PHP
'name' => [
        'label' => 'Your name',
        'input' => 'input',
        'type' => 'text'
    	],
```
A number field:
```PHP
'age' => [
        'label' => 'Your age',
        'input' => 'input',
        'type' => 'number'
    	],
```
If the model belongs to a role and we wanna make a select field for the roles.
```PHP
'role_id' => [
    'label'    =>'Role',
    'input'    => 'select',
    'relation' => [
        'model'      => 'App\Role',
        'type'       => 'one',
        'column'     => 'role_id',
        'selectFrom' => 'name', 
        'valueFrom'  => 'id',
        // This is how selectFrom and valueFrom works
        // <option value="{{ $role->id }}">{{ $role->name }}</option>
    ],
],
```
What if the model belongs to many roles?
```PHP
'roles[]' => [
    'label'    => 'Roles',
    'input'    => 'select',
    'relation' => [
        'name' => 'roles',
        'model' => 'App\Role',
        'type' => 'many',
        'selectFrom' => 'name',
        'valueFrom' => 'id'
    ],
    'inject_attributes' => 'multiple'
],
```

A full example how should be the $fields array of a user model.
```PHP
<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Lilessam\EloquentFields\Traits\FormGenerator;

class User extends Model
{
    use FormGenerator;

    public static $fields = [
        'name'  => [
            'label' => 'Your name',
            'input' => 'input',
            'type'  => 'text',
        ],
        'age'   => [
            'label' => 'Your age',
            'input' => 'input',
            'type'  => 'number',
        ],
        'roles[]' => [
            'label'    => 'Roles',
            'input'    => 'select',
            'relation' => [
                'name'       => 'roles',
                'model'      => 'App\Role',
                'type'       => 'many',
                'selectFrom' => 'name',
                'valueFrom'  => 'id',
            ],
            'inject_attributes' => 'multiple'
        ],
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }
}
```

Then you can just call `generate_fields('App\User')` to generate a creation form fields or `generate_fields('App\User', 1)` to generate an update form fields for User whose id is 1.

Notice that you can pass a third parameter to `generate_fields` parameter to except one or many fields from being generated in the form.

So if we called `generate_fields('App\User', null, 'age')` a creation form will be created without `age` field. You can also pass an array so `generate_fields('App\User', null, ['age', 'roles'])` will create a creation form without roles and age fields.

##### Don't hesitate to make a pull request or post an issue if found.
