<template>
    <div :style="wrapperStyle" class="nevs-field">
        <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
        <input v-model='formattedValue' :readonly="readonly" class="nevs-date-field nevs-field-content" type="text"
               ref="fieldInput" @focusin="openDropdown" @focusout="hideHint"/>
        <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
        <!-- Teleported to <body> with fixed coordinates so the picker is never clipped by a scrollable ancestor
             (e.g. a capped modal). -->
        <Teleport to="body">
            <Transition name="datepicker">
                <div v-show="showPicker" ref="picker" class="nevs-date-field-picker" :style="pickerStyle">
                    <div class="nevs-date-field-picker-year">
                        <div class="nevs-date-field-picker-year-left" @click="changeYear(-1);"><i
                            class="fa-solid fa-angle-left"></i>
                        </div>
                        {{ this.pickerDisplay.year }}
                        <div class="nevs-date-field-picker-year-right" @click="changeYear(1);"><i
                            class="fa-solid fa-angle-right"></i></div>
                    </div>
                    <div class="nevs-date-field-picker-month">
                        <div class="nevs-date-field-picker-month-left" @click="changeMonth(-1);"><i
                            class="fa-solid fa-angle-left"></i></div>
                        {{ this.pickerDisplay.monthName }}
                        <div class="nevs-date-field-picker-month-right" @click="changeMonth(1);"><i
                            class="fa-solid fa-angle-right"></i></div>
                    </div>
                    <div class="nevs-date-field-picker-week">
                        <div v-for="(weekday, key) in weekdays" :key="key"
                             :style="weekdayStyle" class="nevs-date-field-picker-weekday">
                            {{ weekday }}
                        </div>
                        <div class="nevs-clear-float"/>
                    </div>
                    <div class="nevs-date-field-picker-days">
                        <div v-for="(day, key) in pickerDays" :key="key"
                             :class="{'nevs-date-field-picker-day-selected': checkSelected(day), 'nevs-date-field-picker-day-disabled': !day.currentMonth}"
                             :style="dayStyle" class="nevs-date-field-picker-day"
                             @click="dateClick(day)">
                            {{ day.day }}
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
        <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{ error }}</span>
    </div>
</template>

<script>

import moment from 'moment';

export default {
    name: "NevsDateField",
    components: {},
    props: {
        width: {
            type: String,
            default: '100%'
        },
        label: {
            type: String,
            default: ''
        },
        error: {
            type: String,
            default: ''
        },
        hint: {
            type: String,
            default: ''
        },
        reserveHeights: {
            type: Boolean,
            default: false
        },
        format: {
            type: String,
            default: 'D.M.YYYY.'
        },
        picker: {
            type: Boolean,
            default: true
        },
        readonly: {
            type: Boolean,
            default: false
        },
        modelValue: String
    },
    emits: [
        'update:modelValue'
    ],
    data() {
        return {
            showHint: false,
            value: '',
            formattedValue: '',
            baseFormat: 'YYYY-MM-DD',
            showPicker: false,
            weekdays: [],
            pickerDate: {
                'year': null,
                'month': null,
                'day': null
            },
            pickerDisplay: {
                'year': moment().format('YYYY'),
                'monthName': moment().format('MMMM'),
                'month': moment().format('M')
            },
            pickerDays: [],
            pickerStyle: {},
            dayStyle: {},
            weekdayStyle: {
                width: Math.floor(100 / 7) + '%'
            }
        }
    },
    computed: {
        wrapperStyle() {
            let style = {};
            if (this.width) {
                style.width = this.width;
            }
            return style;
        }
    },
    watch: {
        modelValue() {
            if (this.modelValue !== this.value) {
                this.value = this.modelValue;
            }
        },
        formattedValue() {
            this.formattedToBase();
        },
        value() {
            this.baseToFormatted();
            this.$emit('update:modelValue', this.value);
        },
        pickerDate() {
            if (this.pickerDate.year !== null) {
                let date = moment(this.pickerDate.year + '-' + this.pickerDate.month + '.' + this.pickerDate.date, 'YYYY-M-D');
                this.pickerDisplay = {
                    'year': date.format('YYYY'),
                    'monthName': date.format('MMMM'),
                    'month': date.format('M')
                }
            } else {
                this.pickerDisplay = {
                    'year': moment().format('YYYY'),
                    'monthName': moment().format('MMMM'),
                    'month': moment().format('M')
                }
            }
            this.updatePickerData();
        }
    },
    methods: {
        refreshDayStyle() {
            let size = Math.floor(250 / 7);
            let windowWidth = window.innerWidth;
            if (windowWidth <= 500) {
                size = Math.floor((windowWidth - 20) / 7);
            }
            this.dayStyle = {
                width: size + 'px',
                height: size + 'px',
                lineHeight: size + 'px'
            };
        },
        dateClick(day) {
            this.value = moment(this.pickerDisplay.year + '-' + day.month + '-' + day.day, 'YYYY-M-D').format(this.baseFormat);
            this.showPicker = false;
            this.showHint = false;
            this.detachPickerListeners();
            document.removeEventListener('click', this.handleClickOutside);
        },
        updatePickerPosition() {
            if (!this.$refs.fieldInput) return;
            let rect = this.$refs.fieldInput.getBoundingClientRect();
            // Picker is 250px wide; keep it within the viewport horizontally.
            let left = Math.min(rect.left, window.innerWidth - 258);
            if (left < 8) left = 8;

            // The picker is fixed-positioned (teleported to <body>) and can't grow the page scroll, so open it
            // upward when there isn't enough room below to show it fully. Its height varies (6 vs 5 day rows), so
            // measure it when it is already rendered (scroll/resize) and fall back to an estimate on first open.
            const margin = 8;
            const pickerHeight = (this.$refs.picker && this.$refs.picker.offsetHeight) ? this.$refs.picker.offsetHeight : 360;
            const spaceBelow = window.innerHeight - rect.bottom - margin;
            const spaceAbove = rect.top - margin;
            const openUp = spaceBelow < pickerHeight && spaceAbove > spaceBelow;

            let style = {
                position: 'fixed',
                left: left + 'px',
                zIndex: 200
            };
            if (openUp) {
                // Reset the base rule's `top: 0`, otherwise the picker stretches from the top of the screen.
                style.top = 'auto';
                style.bottom = (window.innerHeight - rect.top) + 'px';
            } else {
                style.top = rect.bottom + 'px';
                style.bottom = 'auto';
            }
            this.pickerStyle = style;
        },
        attachPickerListeners() {
            window.addEventListener('scroll', this.updatePickerPosition, true);
            window.addEventListener('resize', this.updatePickerPosition);
        },
        detachPickerListeners() {
            window.removeEventListener('scroll', this.updatePickerPosition, true);
            window.removeEventListener('resize', this.updatePickerPosition);
        },
        changeYear(amount) {
            this.pickerDisplay.year = parseInt(this.pickerDisplay.year) + amount;
            this.updatePickerData();
        },
        changeMonth(amount) {
            this.pickerDisplay.month = parseInt(this.pickerDisplay.month) + amount;
            if (this.pickerDisplay.month > 12) this.pickerDisplay.month = 1;
            if (this.pickerDisplay.month < 1) this.pickerDisplay.month = 12;
            this.pickerDisplay.monthName = moment.months()[this.pickerDisplay.month - 1];
            this.updatePickerData();
        },
        checkSelected(day) {
            let date = moment(this.pickerDisplay.year + '-' + day.month + '-' + day.day, 'YYYY-M-D');
            return this.value === date.format(this.baseFormat);
        },
        updatePickerData() {
            let days = [];

            let firstDayOfMonth = moment(this.pickerDisplay.year + '-' + this.pickerDisplay.month + '-1', 'YYYY-M-D');
            let lastDayOfMonth = moment(this.pickerDisplay.year + '-' + this.pickerDisplay.month + '-' + firstDayOfMonth.daysInMonth(), 'YYYY-M-D');

            let firstDay = firstDayOfMonth.weekday(0);
            let lastDay = lastDayOfMonth.weekday(6);

            let currentDay = firstDay;
            while (currentDay <= lastDay) {
                days.push({
                    day: currentDay.format('D'),
                    month: currentDay.format('M'),
                    weekday: this.weekdays[currentDay.day()],
                    currentMonth: parseInt(currentDay.format('M')) === parseInt(this.pickerDisplay.month)
                });
                currentDay.add('1', 'days');
            }
            this.pickerDays = JSON.parse(JSON.stringify(days));
        },
        openDropdown() {
            if (!this.readonly) {
                this.refreshDayStyle();
                this.showHint = true;
                if (this.picker) {
                    this.showPicker = true;
                    this.updatePickerPosition();
                    this.attachPickerListeners();
                    document.addEventListener('click', this.handleClickOutside);
                }
            }
        },
        hideHint() {
            if (!this.picker) {
                this.showHint = false;
            }
        },
        handleClickOutside(event) {
            // The picker is teleported to <body>, so "outside" must also exclude the picker itself.
            let insideField = this.$el.contains(event.target);
            let insidePicker = this.$refs.picker && this.$refs.picker.contains(event.target);
            if (!insideField && !insidePicker) {
                this.showPicker = false;
                this.showHint = false;
                this.detachPickerListeners();
                document.removeEventListener('click', this.handleClickOutside);
            }
        },
        formattedToBase() {
            let date = moment(this.formattedValue, this.format, true);
            if (!date.isValid()) {
                this.value = null;
                this.pickerDate = {
                    'year': null,
                    'month': null,
                    'day': null
                };
                return;
            }
            this.value = date.format(this.baseFormat);
            this.pickerDate = {
                'year': date.format('YYYY'),
                'month': date.format('M'),
                'day': date.format('D')
            };
        },
        baseToFormatted() {
            if (this.value === null) {
                this.formattedValue = '';
                return;
            }
            let date = moment(this.value, this.baseFormat, true);
            if (!date.isValid()) {
                this.formattedValue = this.value;
                return;
            }
            this.formattedValue = date.format(this.format);
        },
    },
    mounted() {
        moment.locale(this.$store.state.locale);
        this.weekdays = moment.weekdaysMin(true);
        this.pickerDisplay.monthName = moment().format('MMMM');
        this.updatePickerData();
        this.value = this.modelValue;
    },
    unmounted() {
        document.removeEventListener('click', this.handleClickOutside);
        this.detachPickerListeners();
    }
}

</script>
