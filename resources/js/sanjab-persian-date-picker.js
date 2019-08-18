var SanjabPersianDatePickerPlugin = {};
SanjabPersianDatePickerPlugin.install = function (Vue, options) {
    Vue.component('persian-date-picker', require('vue-persian-datetime-picker').default);
}
export default SanjabPersianDatePickerPlugin;
