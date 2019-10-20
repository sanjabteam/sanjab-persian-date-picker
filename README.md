# Persian Date Picker for [Sanjab](https://github.com/sanjabteam/sanjab)

## Installation

Before start make sure you know how [laravel mix](http://laravel.com/docs/mix) works!

---

Install npm packages including laravel mix.

```bash
npm install
```

---

Install the sanjab npm package.

```bash
npm install sanjab --save-dev
```

---

Install sanjab Persian Date picker via npm.
```bash
npm install sanjab-persian-date-picker --save-dev
```

> You should install the same version of the composer package if you don't have latest version.

```bash
npm install sanjab@VERSION --save-dev
```
---

Install sanjab Persian Date picker via composer.

```bash
composer require sanjabteam/sanjab-persian-date-picker
```

---

Create sanjab JS for [custom compile](https://sanjabteam.github.io/compile.html) if not created before.

`resources/js/sanjab.js`:
```js
require('sanjab');

Vue.use(require('sanjab-persian-date-picker').default); // Add this to support persian date picker

if (document.querySelector('#sanjab_app')) {
    window.sanjabApp = new Vue({
        el: '#sanjab_app',
    });
}
```

---

add js file to `webpack.mix.js` for compile.
```js
mix.js('resources/js/sanjab.js', 'public/vendor/sanjab/js')
```

---

Compile and you are ready.

```bash
npm run prod
```

## Usage

```php
use SanjabPersianDatePicker\PersianDatePickerWidget;

// Date only
$this->widgets[] = PersianDatePickerWidget::create('birth_date')
    ->rules('jdate_before:'.verta()->formatDate())
    ->required();

// Date and time
$this->widgets[] = PersianDatePickerWidget::create('released_at')
    ->rules('jdate_before:'.verta()->formatDate())
    ->required()
    ->time(true);
```


## Credits
- [Verta](https://github.com/hekmatinasser/verta)
- [vue-persian-datetime-picker](https://github.com/talkhabi/vue-persian-datetime-picker)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
