<template>
    <div>
        <table v-if="records.length > 0" class="nevs-simple-grid" :class="{'nevs-simple-grid-fixed': fixedLayout}">
            <thead>
                <tr>
                    <th v-for="field in fields" :key="field.name"
                        :style="{textAlign: field.align === 'R' ? 'right' : 'left', width: field.width || null}">{{ field.label }}</th>
                    <th v-if="$slots.actions"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(record, key) in records" :key="key" :class="rowClass ? rowClass(record) : null">
                    <td v-for="field in fields" :key="field.name"
                        :class="{'nevs-simple-grid-linked': clickable}"
                        :style="{textAlign: field.align === 'R' ? 'right' : 'left', width: field.width || null}"
                        @click="rowClick(record)">
                        <slot :name="'cell-' + field.name" :record="record">{{ record[field.name] }}</slot>
                    </td>
                    <td v-if="$slots.actions" class="nevs-simple-grid-actions">
                        <slot name="actions" :record="record"></slot>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-else>{{ emptyText }}</div>
    </div>
</template>

<script>
export default {
    name: "NevsSimpleGrid",
    props: {
        fields: {
            type: Array,
            default: () => []
        },
        records: {
            type: Array,
            default: () => []
        },
        emptyText: {
            type: String,
            default: ''
        },
        clickable: {
            type: Boolean,
            default: true
        },
        rowClass: {
            type: Function,
            default: null
        },
        // When true the table uses a fixed layout so that per-field `width` values are honoured exactly. Lets
        // separate grids share identical column widths and line up visually. Off by default (auto layout).
        fixedLayout: {
            type: Boolean,
            default: false
        }
    },
    emits: ['row-click'],
    methods: {
        rowClick(record) {
            if (this.clickable) {
                this.$emit('row-click', record);
            }
        }
    }
}
</script>

<style scoped>
.nevs-simple-grid {
    width: 100%;
    border-collapse: collapse;
}

.nevs-simple-grid-fixed {
    table-layout: fixed;
}

.nevs-simple-grid-fixed td {
    overflow-wrap: break-word;
}

.nevs-simple-grid th, .nevs-simple-grid td {
    text-align: left;
    padding: 8px;
    border-bottom: 1px solid #eee;
}

.nevs-simple-grid-linked {
    cursor: pointer;
}

.nevs-simple-grid-actions {
    text-align: right;
    width: 1px;
    white-space: nowrap;
}
</style>
