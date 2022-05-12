# PhpJsCalendar

is the PHP class package managing

> JSCalendar: A JSON Representation of Calendar Data ([rfc8984])

> support transformation from/to iCal ([rfc5545]) using [iCalcreator]


###### Usage

For package class and property structure, examine [rfc8984] 8.2.6.
All properties has `get`,`set` and `is\<prop\>Set` methods, 
for 'array' properties `get`, `get\<Prop\>Count`, `add` and `set`methods.

For transformation guidelines from/to iCal, please review [classPropsList].
Due to [rfc8984] / [rfc5545] disparity, iCal transformation tests may fail.


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

[classPropsList]:docs/classPropsList.md
[iCalcreator]:https://github.com/iCalcreator/iCalcreator
[github.com/PhpJsCalendar]:https://github.com/iCalcreator/PhpJsCalendar/issues
[paypal.me/kigkonsult]:https://paypal.me/kigkonsult
[rfc5545]:https://www.rfc-editor.org/info/rfc5545
[rfc8984]:https://www.rfc-editor.org/info/rfc8984
