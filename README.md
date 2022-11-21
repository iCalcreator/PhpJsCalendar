# PhpJsCalendar

is the PHP class package managing

> JSCalendar: A JSON Representation of Calendar Data ([rfc8984])

> support transformation from/to iCal ([rfc5545]) using [iCalcreator]


###### Usage

For package class and property structure, examine [rfc8984] 8.2.6.<br>
All class properties has `get`,`set` and `is\<prop\>Set` methods, <br>
for 'array' properties `get`, `get\<Prop\>Count`, `add` and `set`methods,<br>
for detail review the [classPropList]

For transformation guidelines from/to iCal [rfc5545] (requires [iCalcreator]), please review the [propClassList].
Due to [rfc8984] / [rfc5545] disparity, iCal transformation tests may fail.

To support the development, maintenance and test process
[PHPCompatibility], [PHPStan] and [php-arguments-detector] are included.

###### Support

For support use [github.com/PhpJsCalendar]. Non-emergence support issues are, unless sponsored, fixed in due time.


###### Sponsorship

Donation using [paypal.me/kigkonsult] are appreciated.
For invoice, please e-mail</a>.

###### Installation

Composer

From the Command Line:

```
composer require kigkonsult/phpjscalendar
```

In your composer.json:

```
{
    "require": {
        "kigkonsult/phpjscalendar": ">=1.0"
    }
}
```

###### License

PhpJsCalendar is licensed under the LGPLv3 License.

[classPropList]:docs/classPropList.md
[iCalcreator]:https://github.com/iCalcreator/iCalcreator
[github.com/PhpJsCalendar]:https://github.com/iCalcreator/PhpJsCalendar/issues
[paypal.me/kigkonsult]:https://paypal.me/kigkonsult
[PHPCompatibility]:https://github.com/PHPCompatibility/PHPCompatibility
[PHPStan]:https://github.com/phpstan/phpstan
[php-arguments-detector]:https://github.com/DeGraciaMathieu/php-arguments-detector
[propClassList]:docs/propClassList.md
[rfc5545]:https://www.rfc-editor.org/info/rfc5545
[rfc8984]:https://www.rfc-editor.org/info/rfc8984
