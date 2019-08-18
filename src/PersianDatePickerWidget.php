<?php

namespace SanjabPersianDatePicker;

use stdClass;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Sanjab\Widgets\Widget;

/**
 * Persian date picker widget.
 *
 * @method $this    time(bool $val)     can select time or not.
 */
class PersianDatePickerWidget extends Widget
{
    public function init()
    {
        $this->tag("persian-date-picker");
    }


    protected function store(Request $request, Model $item)
    {
        $faNum = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $enNum = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        if ($request->filled($this->property("name"))) {
            $item->{ $this->property("name") } = Carbon::instance(
                Verta::parse(str_replace($faNum, $enNum, $request->input($this->property("name"))))->DateTime()
            );
        }
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        if ($item->{ $this->property("name") }) {
            if ($this->property("time")) {
                $response->{ $this->property('name') } = verta($item->{ $this->property("name") })->format("Y/n/j H:i:s");
            } else {
                $response->{ $this->property('name') } = verta($item->{ $this->property("name") })->format("Y/n/j");
            }
        }
    }

    public function postInit()
    {
        $this->setProperty("placeHolder", $this->property("title"));
        if ($this->property("time")) {
            $this->setProperty("type", "datetime");
            $this->setProperty("format", "jYYYY/jMM/jDD HH:mm:ss");
            $this->rules('jdatetime');
        } else {
            $this->setProperty("type", "date");
            $this->setProperty("format", "jYYYY/jMM/jDD");
            $this->rules('jdate');
        }
    }
}
