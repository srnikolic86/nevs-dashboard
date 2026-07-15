<template>
    <div class="nevs-calendar">
        <div class="nevs-calendar-toolbar">
            <div class="nevs-calendar-nav">
                <span class="nevs-calendar-nav-button" @click="prev"><i class="fa-solid fa-chevron-left"></i></span>
                <span class="nevs-calendar-today-button" @click="today">{{ $LANG.Get('labels.calendarToday') }}</span>
                <span class="nevs-calendar-nav-button" @click="next"><i class="fa-solid fa-chevron-right"></i></span>
            </div>
            <div class="nevs-calendar-title">{{ title }}</div>
            <div class="nevs-calendar-tools">
                <label v-if="view !== 'month'" class="nevs-calendar-compact">
                    <input type="checkbox" v-model="compact"> {{ $LANG.Get('labels.calendarCompact') }}
                </label>
                <div class="nevs-calendar-views">
                    <span class="nevs-calendar-view-button" :class="{'active': view === 'month'}"
                          @click="setView('month')">{{ $LANG.Get('labels.calendarMonth') }}</span>
                    <span class="nevs-calendar-view-button" :class="{'active': view === 'week'}"
                          @click="setView('week')">{{ $LANG.Get('labels.calendarWeek') }}</span>
                    <span class="nevs-calendar-view-button" :class="{'active': view === 'day'}"
                          @click="setView('day')">{{ $LANG.Get('labels.calendarDay') }}</span>
                </div>
            </div>
        </div>

        <!-- MONTH VIEW -->
        <div v-if="view === 'month'" class="nevs-calendar-month">
            <div class="nevs-calendar-month-header">
                <div v-for="(header, key) in weekdayHeaders" :key="key" class="nevs-calendar-month-header-cell">
                    {{ header }}
                </div>
            </div>
            <div class="nevs-calendar-month-grid">
                <div v-for="(cell, key) in monthCells" :key="key" class="nevs-calendar-month-cell"
                     :class="{'nevs-calendar-month-cell-outside': !cell.inMonth, 'nevs-calendar-month-cell-today': cell.isToday}">
                    <div class="nevs-calendar-month-daynum">{{ cell.dayNum }}</div>
                    <div class="nevs-calendar-month-events">
                        <div v-for="(event, eventKey) in cell.events" :key="eventKey" class="nevs-calendar-pill"
                             :style="{backgroundColor: event.color, color: event.textColor}" :title="event.tooltip"
                             @click="selectEvent(event.source)">
                            <span class="nevs-calendar-pill-time">{{ event.startLabel }}</span> {{ event.name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- WEEK / DAY VIEW -->
        <div v-else class="nevs-calendar-timegrid">
            <div class="nevs-calendar-timegrid-header">
                <div class="nevs-calendar-gutter-header"></div>
                <div v-for="(day, key) in visibleDays" :key="key" class="nevs-calendar-day-header"
                     :class="{'nevs-calendar-day-header-today': day.isToday}">{{ day.label }}</div>
            </div>
            <div class="nevs-calendar-timegrid-body">
                <div class="nevs-calendar-gutter">
                    <div v-for="(hour, key) in displayHourLabels" :key="key" class="nevs-calendar-hour-label"
                         :style="{height: hourHeight + 'px'}">{{ hour }}</div>
                </div>
                <div v-for="(day, key) in visibleDays" :key="key" class="nevs-calendar-day-column"
                     :style="{height: gridHeight + 'px'}">
                    <div v-for="(hour, hourKey) in displayHourLabels" :key="hourKey" class="nevs-calendar-hour-line"
                         :style="{height: hourHeight + 'px'}"></div>
                    <div v-for="(event, eventKey) in day.events" :key="eventKey" class="nevs-calendar-event"
                         :style="{top: event.top + 'px', height: event.height + 'px', left: event.leftStyle, width: event.widthStyle, backgroundColor: event.color, color: event.textColor}"
                         :title="event.tooltip" @click="selectEvent(event.source)">
                        <div class="nevs-calendar-event-title">{{ event.startLabel }}–{{ event.endLabel }} {{ event.name }}</div>
                        <div v-if="event.html" class="nevs-calendar-event-html" v-html="event.html"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment/moment";

const PARSE_FORMATS = ['YYYY-MM-DD HH:mm:ss', 'YYYY-MM-DD HH:mm'];

export default {
    name: "NevsCalendar",
    props: {
        // Array of { name, start, end, color, html }. start/end are "YYYY-MM-DD HH:mm(:ss)" strings.
        events: {
            type: Array,
            default: () => []
        }
    },
    emits: [
        // Fired whenever the visible range changes (mount, navigation, view switch) so the parent can fetch data.
        'range-change',
        // Fired with the original event object when the user clicks an event, so the parent can act on it.
        'event-click'
    ],
    data() {
        return {
            view: 'week',
            cursor: moment().format('YYYY-MM-DD'),
            hourHeight: 48,
            // When true, week/day views collapse the timeline to only the hours that actually contain events.
            compact: false
        }
    },
    computed: {
        cursorMoment() {
            return moment(this.cursor, 'YYYY-MM-DD');
        },
        range() {
            let m = this.cursorMoment;
            if (this.view === 'month') {
                return {
                    start: m.clone().startOf('month').startOf('isoWeek'),
                    end: m.clone().endOf('month').endOf('isoWeek')
                };
            }
            if (this.view === 'day') {
                return {start: m.clone().startOf('day'), end: m.clone().endOf('day')};
            }
            return {start: m.clone().startOf('isoWeek'), end: m.clone().endOf('isoWeek')};
        },
        rangeSignature() {
            return this.range.start.format('YYYY-MM-DD') + '|' + this.range.end.format('YYYY-MM-DD') + '|' + this.view;
        },
        title() {
            let m = this.cursorMoment;
            if (this.view === 'month') {
                return this.capitalize(m.format('MMMM YYYY.'));
            }
            if (this.view === 'day') {
                return this.capitalize(m.format('dddd, D.M.YYYY.'));
            }
            return this.range.start.format('D.M.') + ' – ' + this.range.end.format('D.M.YYYY.');
        },
        weekdayHeaders() {
            let headers = [];
            for (let day = 1; day <= 7; day++) {
                headers.push(this.capitalize(moment().isoWeekday(day).format('ddd')));
            }
            return headers;
        },
        // The hour indices (0-23) rendered in week/day views. Normally the full day; in compact mode only the
        // hours overlapped by at least one event across the visible day(s), so empty time slots are hidden.
        visibleHourIndices() {
            if (!this.compact) {
                let all = [];
                for (let hour = 0; hour < 24; hour++) all.push(hour);
                return all;
            }
            let used = {};
            let day = this.range.start.clone();
            let last = (this.view === 'day') ? this.range.start.clone() : this.range.end.clone();
            while (day.isSameOrBefore(last, 'day')) {
                for (let event of this.eventsForDay(day)) {
                    let startMinutes = event.start.hours() * 60 + event.start.minutes();
                    let endMinutes = event.end.hours() * 60 + event.end.minutes();
                    if (endMinutes <= startMinutes) endMinutes = startMinutes + 30;
                    let firstHour = Math.floor(startMinutes / 60);
                    let lastHour = Math.min(Math.floor((endMinutes - 1) / 60), 23);
                    for (let hour = firstHour; hour <= lastHour; hour++) used[hour] = true;
                }
                day.add(1, 'day');
            }
            return Object.keys(used).map((hour) => parseInt(hour, 10)).sort((a, b) => a - b);
        },
        // Maps an hour index to its row position within the (possibly compressed) timeline.
        hourPositionMap() {
            let map = {};
            this.visibleHourIndices.forEach((hour, index) => {
                map[hour] = index;
            });
            return map;
        },
        displayHourLabels() {
            return this.visibleHourIndices.map((hour) => String(hour).padStart(2, '0') + ':00');
        },
        gridHeight() {
            return this.visibleHourIndices.length * this.hourHeight;
        },
        parsedEvents() {
            return this.events.map((event) => {
                let start = moment(event.start, PARSE_FORMATS);
                let end = moment(event.end, PARSE_FORMATS);
                let startLabel = start.format('HH:mm');
                let endLabel = end.format('HH:mm');
                // Plain-text version of the custom HTML for the hover tooltip (title attribute).
                let details = (event.html || '').replace(/<\/div>/gi, '\n').replace(/<[^>]*>/g, '').replace(/\n+/g, '\n').trim();
                let tooltip = startLabel + '–' + endLabel + ' ' + event.name + (details ? '\n' + details : '');
                return {
                    source: event,
                    name: event.name,
                    color: event.color,
                    // Text colour chosen for the best contrast against this event's background.
                    textColor: this.contrastColor(event.color),
                    html: event.html,
                    start: start,
                    end: end,
                    startLabel: startLabel,
                    endLabel: endLabel,
                    tooltip: tooltip
                };
            });
        },
        monthCells() {
            let cells = [];
            let day = this.range.start.clone();
            let today = moment().format('YYYY-MM-DD');
            let cursorMonth = this.cursorMoment.month();
            while (day.isSameOrBefore(this.range.end, 'day')) {
                cells.push({
                    dayNum: day.format('D'),
                    inMonth: day.month() === cursorMonth,
                    isToday: day.format('YYYY-MM-DD') === today,
                    events: this.eventsForDay(day)
                });
                day.add(1, 'day');
            }
            return cells;
        },
        visibleDays() {
            let days = [];
            let today = moment().format('YYYY-MM-DD');
            let day = this.range.start.clone();
            let last = (this.view === 'day') ? this.range.start.clone() : this.range.end.clone();
            while (day.isSameOrBefore(last, 'day')) {
                days.push({
                    label: this.capitalize(day.format('ddd D.M.')),
                    isToday: day.format('YYYY-MM-DD') === today,
                    events: this.positionedEventsForDay(day)
                });
                day.add(1, 'day');
            }
            return days;
        }
    },
    watch: {
        rangeSignature() {
            this.emitRange();
        }
    },
    methods: {
        capitalize(text) {
            return text.charAt(0).toUpperCase() + text.slice(1);
        },
        // Picks the text colour (near-black or white) with the best WCAG contrast against the given event
        // background, so event labels stay readable whatever session-type colour is used. Returns null for an
        // unparseable colour so the stylesheet default applies.
        contrastColor(color) {
            let hex = (typeof color === 'string' ? color : '').trim().replace('#', '');
            if (hex.length === 3) hex = hex.split('').map((c) => c + c).join('');
            if (!/^[0-9a-fA-F]{6}$/.test(hex)) return null;
            let channel = (value) => {
                let c = value / 255;
                return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
            };
            let luminance = 0.2126 * channel(parseInt(hex.slice(0, 2), 16))
                + 0.7152 * channel(parseInt(hex.slice(2, 4), 16))
                + 0.0722 * channel(parseInt(hex.slice(4, 6), 16));
            let contrastWhite = 1.05 / (luminance + 0.05);
            let contrastDark = (luminance + 0.05) / 0.05;
            return contrastWhite >= contrastDark ? '#ffffff' : '#1d2939';
        },
        eventsForDay(day) {
            return this.parsedEvents
                .filter((event) => event.start.isSame(day, 'day'))
                .sort((a, b) => a.start.valueOf() - b.start.valueOf());
        },
        positionedEventsForDay(day) {
            let events = this.eventsForDay(day).map((event) => {
                let startMinutes = event.start.hours() * 60 + event.start.minutes();
                // Use a minimum visible duration of 30 minutes so tiny blocks still get a side-by-side slot.
                let endMinutes = startMinutes + Math.max(event.end.diff(event.start, 'minutes'), 30);
                return {
                    ...event,
                    startMinutes: startMinutes,
                    endMinutes: endMinutes,
                    col: 0,
                    columns: 1
                };
            });

            this.assignColumns(events);

            let gap = 2;
            events.forEach((event) => {
                let height = this.spanHeight(event.startMinutes, event.endMinutes) - 2;
                if (height < 22) height = 22;
                event.top = this.minuteToY(event.startMinutes);
                event.height = height;
                event.leftStyle = 'calc(' + (event.col / event.columns * 100) + '% + ' + gap + 'px)';
                event.widthStyle = 'calc(' + (1 / event.columns * 100) + '% - ' + (gap * 2) + 'px)';
            });

            return events;
        },
        // Vertical pixel offset of a minute-of-day within the (possibly compressed) timeline.
        minuteToY(minutes) {
            let hour = Math.floor(minutes / 60);
            let position = this.hourPositionMap[hour];
            if (position === undefined) position = 0;
            return position * this.hourHeight + (minutes % 60) / 60 * this.hourHeight;
        },
        // Pixel height of an interval, counting only the portions that fall inside visible hours. In compact
        // mode this self-clamps to the rendered hours; in full mode it equals the plain duration.
        spanHeight(startMinutes, endMinutes) {
            let total = 0;
            for (let hour of this.visibleHourIndices) {
                let hourStart = hour * 60;
                let hourEnd = hourStart + 60;
                let overlap = Math.min(endMinutes, hourEnd) - Math.max(startMinutes, hourStart);
                if (overlap > 0) total += overlap / 60 * this.hourHeight;
            }
            return total;
        },
        // Lays overlapping events side by side: events are grouped into clusters of (transitively) overlapping
        // events; within a cluster each event takes the first column whose previous event has already ended, and
        // every event in the cluster is widened to 1 / (columns used in that cluster). Events are pre-sorted by start.
        assignColumns(events) {
            let cluster = [];
            let columnEnds = [];
            let clusterEnd = -1;

            let flush = () => {
                cluster.forEach((event) => {
                    event.columns = columnEnds.length;
                });
                cluster = [];
                columnEnds = [];
                clusterEnd = -1;
            };

            for (let event of events) {
                if (cluster.length > 0 && event.startMinutes >= clusterEnd) {
                    flush();
                }
                let placed = false;
                for (let column = 0; column < columnEnds.length; column++) {
                    if (columnEnds[column] <= event.startMinutes) {
                        event.col = column;
                        columnEnds[column] = event.endMinutes;
                        placed = true;
                        break;
                    }
                }
                if (!placed) {
                    event.col = columnEnds.length;
                    columnEnds.push(event.endMinutes);
                }
                cluster.push(event);
                clusterEnd = Math.max(clusterEnd, event.endMinutes);
            }
            if (cluster.length > 0) {
                flush();
            }
        },
        prev() {
            this.cursor = this.cursorMoment.subtract(1, this.navigationUnit()).format('YYYY-MM-DD');
        },
        next() {
            this.cursor = this.cursorMoment.add(1, this.navigationUnit()).format('YYYY-MM-DD');
        },
        today() {
            this.cursor = moment().format('YYYY-MM-DD');
        },
        setView(view) {
            this.view = view;
        },
        navigationUnit() {
            if (this.view === 'month') return 'month';
            if (this.view === 'day') return 'day';
            return 'week';
        },
        selectEvent(event) {
            this.$emit('event-click', event);
        },
        emitRange() {
            this.$emit('range-change', {
                start: this.range.start.format('YYYY-MM-DD'),
                end: this.range.end.format('YYYY-MM-DD'),
                view: this.view
            });
        }
    },
    mounted() {
        this.emitRange();
    }
}
</script>

<style scoped>
</style>
