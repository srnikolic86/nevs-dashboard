<template>
    <table class="nevs-tiny-grid" :style="tableStyle">
        <thead v-if="hasHeader">
            <tr>
                <th v-for="(column, index) in columns" :key="index" :style="columnStyle(column)">{{ column.label }}</th>
                <th v-if="actions.length > 0"></th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="records.length === 0 && emptyText !== ''">
                <td :colspan="totalColumns" class="nevs-tiny-grid-empty">{{ emptyText }}</td>
            </tr>
            <tr v-for="(record, index) in records" :key="index" :class="{'nevs-tiny-grid-inactive': isRowInactive(record)}">
                <td v-for="(column, cIndex) in columns" :key="cIndex" :style="columnStyle(column)">
                    <!-- A module can supply custom cell markup (a colour swatch, a combined value, ...) via a
                         #cell-<key> scoped slot; otherwise the plain record value for the column is shown. -->
                    <slot :name="'cell-' + column.key" :record="record" :index="index">{{ record[column.key] }}</slot>
                </td>
                <td v-if="actions.length > 0" class="nevs-tiny-grid-actions">
                    <template v-for="(action, aIndex) in actions" :key="aIndex">
                        <span v-if="actionVisible(action, record)" :title="action.tooltip" class="nevs-tiny-grid-action"
                              @click="$emit('action', action.name, record)"><i :class="action.icon"></i></span>
                    </template>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>

export default {
    name: "NevsTinyGrid",
    props: {
        // Column definitions: { key, label, align: 'left'|'right', width }. A column without a label
        // renders an empty header cell (and, if no column has a label, the header is omitted entirely).
        columns: {
            type: Array,
            required: true
        },
        records: {
            type: Array,
            required: true
        },
        // Row action icons: { name, icon, tooltip, show }. Clicking emits ('action', name, record).
        // `show` may be a boolean or a (record) => boolean predicate; omitted means always visible.
        actions: {
            type: Array,
            default: () => []
        },
        // Text for the single full-width row shown when there are no records. Empty means no empty row.
        emptyText: {
            type: String,
            default: ''
        },
        // Optional (record) => boolean marking a row as inactive (dimmed + italic).
        isInactive: {
            type: Function,
            default: null
        },
        // Optional override of the grid's default max width (e.g. '900px').
        maxWidth: {
            type: String,
            default: ''
        }
    },
    emits: ['action'],
    computed: {
        hasHeader() {
            return this.columns.some((column) => column.label);
        },
        totalColumns() {
            return this.columns.length + (this.actions.length > 0 ? 1 : 0);
        },
        tableStyle() {
            return this.maxWidth !== '' ? {maxWidth: this.maxWidth} : {};
        }
    },
    methods: {
        columnStyle(column) {
            let style = {textAlign: column.align || 'left'};
            if (column.width) {
                style.width = column.width;
            }
            return style;
        },
        isRowInactive(record) {
            return this.isInactive !== null ? this.isInactive(record) : false;
        },
        actionVisible(action, record) {
            if (typeof action.show === 'function') {
                return action.show(record);
            }
            return action.show !== false;
        }
    }
}
</script>

<style scoped lang="scss">
@use '../../scss/nevs/vars' as *;
@use '../../scss/nevs/grids' as g;

.nevs-tiny-grid {
    @include g.mini-grid;
}

.nevs-tiny-grid-actions {
    text-align: right;
    white-space: nowrap;
}

.nevs-tiny-grid-action {
    @include g.grid-action;
}

.nevs-tiny-grid-inactive td {
    color: $ink-faint;
    font-style: italic;
}

.nevs-tiny-grid-empty {
    color: $ink-faint;
    font-style: italic;
}
</style>
