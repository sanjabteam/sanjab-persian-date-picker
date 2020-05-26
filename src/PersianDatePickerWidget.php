<?php

namespace SanjabPersianDatePicker;

use stdClass;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Sanjab\Widgets\Widget;
use Sanjab\Helpers\SearchType;

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


    /**
     * Get search types.
     *
     * @return array|SearchType[]
     */
    protected function searchTypes(): array
    {
        return [
            SearchType::create('empty', trans('sanjab::sanjab.is_empty')),
            SearchType::create('not_empty', trans('sanjab::sanjab.is_not_empty')),
            SearchType::create('equal', trans('sanjab::sanjab.equal'))
                        ->addWidget(PersianDatePickerWidget::create('search', trans('sanjab::sanjab.equal'))->time($this->property('time'))),
            SearchType::create('not_equal', trans('sanjab::sanjab.not_equal'))
                        ->addWidget(PersianDatePickerWidget::create('search', trans('sanjab::sanjab.not_equal'))->time($this->property('time'))),
            SearchType::create('more', trans('sanjab::sanjab.more'))
                        ->addWidget(PersianDatePickerWidget::create('search', trans('sanjab::sanjab.more'))->time($this->property('time'))),
            SearchType::create('more_or_eqaul', trans('sanjab::sanjab.more_or_eqaul'))
                        ->addWidget(PersianDatePickerWidget::create('search', trans('sanjab::sanjab.more_or_eqaul'))->time($this->property('time'))),
            SearchType::create('less', trans('sanjab::sanjab.less'))
                        ->addWidget(PersianDatePickerWidget::create('search', trans('sanjab::sanjab.less'))->time($this->property('time'))),
            SearchType::create('less_or_eqaul', trans('sanjab::sanjab.less_or_eqaul'))
                        ->addWidget(PersianDatePickerWidget::create('search', trans('sanjab::sanjab.less_or_eqaul'))->time($this->property('time'))),
            SearchType::create('between', trans('sanjab::sanjab.between'))
                        ->addWidget(PersianDatePickerWidget::create('first', trans('sanjab::sanjab.between'))->time($this->property('time')))
                        ->addWidget(PersianDatePickerWidget::create('second', trans('sanjab::sanjab.between'))->time($this->property('time'))),
            SearchType::create('not_between', trans('sanjab::sanjab.not_between'))
                        ->addWidget(PersianDatePickerWidget::create('first', trans('sanjab::sanjab.not_between'))->time($this->property('time')))
                        ->addWidget(PersianDatePickerWidget::create('second', trans('sanjab::sanjab.not_between'))->time($this->property('time'))),
        ];
    }

    /**
     * To override search query modify.
     *
     * @param Builder $query
     * @param string $type
     * @param mixed $search
     * @return void
     */
    protected function search(Builder $query, string $type = null, $search = null)
    {
        if (is_array($search)) {
            foreach ($search as $key => $value) {
                try {
                    $search[$key] = Verta::parse($value)->DateTime();
                    if ($this->property('time')) {
                        $search[$key]->setTime($search[$key]->format('H'), $search[$key]->format('i'));
                    }
                } catch (\Exception $exception) {
                    return;
                }
            }
            if ($this->property('time')) {
                $search['second']->modify('+59 seconds');
            } else {
                $search['second']->modify('+23 hours +59 minutes +59 seconds');
            }
        } else {
            try {
                $search = Verta::parse($search)->DateTime();
                if ($this->property('time')) {
                    $search->setTime($search->format('H'), $search->format('i'));
                }
                if (in_array($type, ['equal', 'not_equal'])) {
                    $search = ['first' => $search];
                    $search['second'] = clone $search['first'];
                    if ($this->property('time')) {
                        $search['second']->modify('+59 seconds');
                    } else {
                        $search['second']->modify('+23 hours +59 minutes +59 seconds');
                    }
                    $search = array_values($search);
                }
            } catch (\Exception $exception) {
                return;
            }
        }
        switch ($type) {
            case 'equal':
                $query->whereBetween($this->property('name'), $search);
                break;
            case 'not_equal':
                $query->whereNotBetween($this->property('name'), $search);
                break;
            case 'more':
                $query->where($this->property('name'), '>', $search);
                break;
            case 'more_or_eqaul':
                $query->where($this->property('name'), '>=', $search);
                break;
            case 'less':
                $query->where($this->property('name'), '<', $search);
                break;
            case 'less_or_eqaul':
                $query->where($this->property('name'), '<=', $search);
                break;
            case 'between':
                $query->whereBetween($this->property('name'), [$search['first'] < $search['second'] ? $search['first'] : $search['second'], $search['first'] < $search['second'] ? $search['second'] : $search['first']]);
                break;
            case 'not_between':
                $query->whereNotBetween($this->property('name'), [$search['first'] < $search['second'] ? $search['first'] : $search['second'], $search['first'] < $search['second'] ? $search['second'] : $search['first']]);
                break;
            default:
                if (intval($search->format('Y')) >= 1900 && intval($search->format('Y')) <= 2200) {
                    if ($this->property('time')) {
                        $query->where($this->property('name'), '=', $search);
                    } else {
                        $query->whereDate($this->property('name'), '=', $search);
                    }
                }
                break;
        }
    }
}
