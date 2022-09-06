<template>
  <div :style="wrapperStyle" class="nevs-field">
    <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
    <input v-model='formattedValue' :readonly="readonly" class="nevs-date-field nevs-field-content" type="text"
           @focusin="openDropdown" @focusout="hideHint"/>
    <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
    <div class="nevs-dropdown-holder">
      <Transition name="datepicker">
        <div v-show="showPicker" class="nevs-date-field-picker">
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
    </div>
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
        }
      }
    },
    hideHint() {
      if (!this.picker) {
        this.showHint = false;
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
  }
}

</script>
