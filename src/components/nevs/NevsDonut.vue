<template>
    <div class="nevs-donut">
        <div v-if="title" class="nevs-donut-title">{{ title }}</div>
        <div class="nevs-donut-body">
            <svg :width="size" :height="size" :viewBox="'0 0 ' + size + ' ' + size" class="nevs-donut-svg">
                <!-- The track shows through wherever there is nothing to draw, so an empty group still reads
                     as a ring rather than as a missing chart. -->
                <circle :cx="center" :cy="center" :r="radius" fill="none" :stroke-width="thickness"
                        class="nevs-donut-track"></circle>
                <!-- Each slice is the same circle dashed to its own share and rotated to start where the
                     previous one ended, which keeps the whole chart to one <circle> per slice. The colour is
                     set through style rather than the stroke attribute because a presentation attribute
                     cannot resolve a custom property. -->
                <circle v-for="(arc, key) in arcs" :key="key" :cx="center" :cy="center" :r="radius" fill="none"
                        :stroke-width="thickness"
                        :style="{stroke: arc.color}"
                        :stroke-dasharray="arc.length + ' ' + (circumference - arc.length)"
                        :stroke-dashoffset="-arc.offset"
                        :transform="'rotate(-90 ' + center + ' ' + center + ')'"
                        class="nevs-donut-arc">
                    <title>{{ arc.label }}: {{ arc.percentageDisplay }}</title>
                </circle>
                <text :x="center" :y="hasSlices ? center - 2 : center + 4" text-anchor="middle"
                      class="nevs-donut-center-value">{{ centerValue }}</text>
                <text v-if="centerLabel && hasSlices" :x="center" :y="center + 18" text-anchor="middle"
                      class="nevs-donut-center-label">{{ centerLabel }}</text>
            </svg>
            <div class="nevs-donut-legend">
                <div v-if="!hasSlices" class="nevs-donut-empty">{{ emptyText }}</div>
                <div v-for="(arc, key) in arcs" :key="key" class="nevs-donut-legend-row">
                    <span class="nevs-donut-legend-dot" :style="{background: arc.color}"></span>
                    <span class="nevs-donut-legend-label" :title="arc.label">{{ arc.label }}</span>
                    <span class="nevs-donut-legend-percentage">{{ arc.percentageDisplay }}</span>
                    <span class="nevs-donut-legend-value">{{ arc.display }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "NevsDonut",
    props: {
        // [{label, percentage, display}] - largest first; percentages are expected to add up to 100.
        slices: {
            type: Array,
            default: () => []
        },
        title: {
            type: String,
            default: ''
        },
        // Shown in the middle of the ring, e.g. the group's total.
        centerValue: {
            type: String,
            default: ''
        },
        centerLabel: {
            type: String,
            default: ''
        },
        emptyText: {
            type: String,
            default: ''
        },
        size: {
            type: Number,
            default: 168
        },
        thickness: {
            type: Number,
            default: 20
        }
    },
    data() {
        return {
            // How many slice colours the theme defines. Read from the stylesheet on mount so the component
            // cycles through exactly the series in $donut-slice-colors, however long a project makes it; the
            // fallback only ever applies if the custom property is missing.
            sliceCount: 10
        }
    },
    computed: {
        center() {
            return this.size / 2;
        },
        radius() {
            return (this.size - this.thickness) / 2;
        },
        circumference() {
            return 2 * Math.PI * this.radius;
        },
        hasSlices() {
            return this.arcs.length > 0;
        },
        arcs() {
            let arcs = [];
            let offset = 0;
            for (let index = 0; index < this.slices.length; index++) {
                let slice = this.slices[index];
                let percentage = Number(slice.percentage);
                if (!isFinite(percentage) || percentage <= 0) continue;
                percentage = Math.min(100, percentage);
                // Never let accumulated rounding push a slice past the end of the ring.
                let length = Math.min((this.circumference * percentage) / 100, this.circumference - offset);
                if (length <= 0) continue;
                arcs.push({
                    label: slice.label,
                    display: slice.display,
                    percentageDisplay: String(Math.round(percentage * 10) / 10).replace('.', ',') + '%',
                    color: 'var(--nevs-donut-slice-' + ((arcs.length % this.sliceCount) + 1) + ')',
                    length: length,
                    offset: offset
                });
                offset += length;
            }
            return arcs;
        }
    },
    mounted() {
        if (!this.$el || !this.$el.nodeType || typeof getComputedStyle !== 'function') return;
        let declared = parseInt(getComputedStyle(this.$el).getPropertyValue('--nevs-donut-slice-count'));
        if (isFinite(declared) && declared > 0) {
            this.sliceCount = declared;
        }
    }
}
</script>

<style scoped lang="scss">
@use 'sass:list';
@use '../../scss/nevs/vars' as *;

.nevs-donut {
    // The slice series is published as custom properties so the arcs and their legend dots can be coloured
    // from script without the component carrying a palette of its own.
    --nevs-donut-slice-count: #{list.length($donut-slice-colors)};
    @for $i from 1 through list.length($donut-slice-colors) {
        --nevs-donut-slice-#{$i}: #{list.nth($donut-slice-colors, $i)};
    }

    min-width: 0;
}

.nevs-donut-title {
    font-weight: 600;
    color: $donut-title-color;
    margin-bottom: 10px;
}

.nevs-donut-body {
    display: flex;
    align-items: center;
    gap: 18px;
    flex-wrap: wrap;
}

.nevs-donut-svg {
    flex: 0 0 auto;
}

.nevs-donut-track {
    stroke: $donut-track;
}

.nevs-donut-arc {
    transition: stroke-dasharray 0.4s ease, stroke-dashoffset 0.4s ease;
}

.nevs-donut-center-value {
    font-size: 15px;
    font-weight: 700;
    fill: $donut-center-value-color;
}

.nevs-donut-center-label {
    font-size: 12px;
    fill: $donut-center-label-color;
}

.nevs-donut-legend {
    flex: 1 1 200px;
    min-width: 0;
}

.nevs-donut-empty {
    color: $donut-empty-color;
    font-style: italic;
}

.nevs-donut-legend-row {
    display: flex;
    align-items: baseline;
    gap: 8px;
    padding: 3px 0;
}

.nevs-donut-legend-dot {
    flex: 0 0 auto;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.nevs-donut-legend-label {
    flex: 1 1 auto;
    min-width: 0;
    color: $donut-legend-label-color;
    font-size: 13px;
    // Catalogue names get long; the row stays one line and the amount stays readable.
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.nevs-donut-legend-percentage {
    flex: 0 0 auto;
    font-weight: 700;
    color: $donut-legend-percentage-color;
    font-size: 13px;
    white-space: nowrap;
}

.nevs-donut-legend-value {
    flex: 0 0 auto;
    color: $donut-legend-value-color;
    font-size: 12px;
    white-space: nowrap;
}
</style>
