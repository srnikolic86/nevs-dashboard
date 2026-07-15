<template>
    <div class="nevs-chart">
        <div v-if="title" class="nevs-chart-title">{{ title }}</div>
        <div v-if="labels.length === 0" class="nevs-chart-empty">{{ emptyText }}</div>
        <div v-else class="nevs-chart-scroll">
            <svg :width="chartWidth" :height="height" :viewBox="'0 0 ' + chartWidth + ' ' + height"
                 preserveAspectRatio="xMinYMin meet">
                <!-- horizontal grid lines + y axis labels -->
                <g v-for="(line, key) in gridLines" :key="'g' + key">
                    <line :x1="plotLeft" :y1="line.y" :x2="chartWidth - plotRight" :y2="line.y"
                          class="nevs-chart-grid-line" stroke-width="1"></line>
                    <text :x="plotLeft - 8" :y="line.y + 4" text-anchor="end" class="nevs-chart-axis-label">
                        {{ formatNumber(line.value) }}
                    </text>
                </g>
                <!-- bars -->
                <g v-for="(bar, key) in bars" :key="'b' + key">
                    <rect :x="bar.x" :y="bar.y" :width="bar.width" :height="bar.height" :rx="4"
                          class="nevs-chart-bar">
                        <title>{{ bar.label }}: {{ formatNumber(bar.value) }}{{ suffix }}</title>
                    </rect>
                    <text v-if="bar.height > 0" :x="bar.x + bar.width / 2" :y="bar.y - 6" text-anchor="middle"
                          class="nevs-chart-value">{{ formatNumber(bar.value) }}</text>
                    <text :x="bar.x + bar.width / 2" :y="height - plotBottom + 16" text-anchor="end"
                          :transform="'rotate(-40 ' + (bar.x + bar.width / 2) + ' ' + (height - plotBottom + 16) + ')'"
                          class="nevs-chart-x-label">{{ trim(bar.label) }}</text>
                </g>
                <!-- x axis -->
                <line :x1="plotLeft" :y1="height - plotBottom" :x2="chartWidth - plotRight" :y2="height - plotBottom"
                      class="nevs-chart-axis-line" stroke-width="1"></line>
            </svg>
        </div>
    </div>
</template>

<script>
export default {
    name: "NevsChart",
    props: {
        labels: {
            type: Array,
            default: () => []
        },
        values: {
            type: Array,
            default: () => []
        },
        title: {
            type: String,
            default: ''
        },
        suffix: {
            type: String,
            default: ''
        },
        height: {
            type: Number,
            default: 280
        },
        emptyText: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            plotLeft: 55,
            plotRight: 15,
            plotTop: 25,
            plotBottom: 70,
            barSlot: 58
        }
    },
    computed: {
        chartWidth() {
            let needed = this.plotLeft + this.plotRight + this.labels.length * this.barSlot;
            return Math.max(needed, 320);
        },
        maxValue() {
            let max = Math.max(0, ...this.values.map((v) => Number(v) || 0));
            return this.niceCeil(max);
        },
        plotHeight() {
            return this.height - this.plotTop - this.plotBottom;
        },
        gridLines() {
            let steps = 4;
            let lines = [];
            for (let i = 0; i <= steps; i++) {
                let value = (this.maxValue / steps) * i;
                let y = this.plotTop + this.plotHeight - (this.plotHeight * i) / steps;
                lines.push({value: value, y: y});
            }
            return lines;
        },
        bars() {
            let bars = [];
            let barWidth = 32;
            let gap = (this.barSlot - barWidth) / 2;
            for (let i = 0; i < this.labels.length; i++) {
                let value = Number(this.values[i]) || 0;
                let barHeight = this.maxValue > 0 ? (this.plotHeight * value) / this.maxValue : 0;
                bars.push({
                    x: this.plotLeft + i * this.barSlot + gap,
                    y: this.plotTop + this.plotHeight - barHeight,
                    width: barWidth,
                    height: barHeight,
                    value: value,
                    label: this.labels[i]
                });
            }
            return bars;
        }
    },
    methods: {
        niceCeil(value) {
            if (value <= 0) return 1;
            let magnitude = Math.pow(10, Math.floor(Math.log10(value)));
            let normalized = value / magnitude;
            let nice;
            if (normalized <= 1) nice = 1;
            else if (normalized <= 2) nice = 2;
            else if (normalized <= 5) nice = 5;
            else nice = 10;
            return nice * magnitude;
        },
        formatNumber(value) {
            let rounded = Math.round(value * 100) / 100;
            let parts = rounded.toString().split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            let out = parts[0];
            if (parts[1]) out += ',' + parts[1];
            return out;
        },
        trim(label) {
            let text = String(label);
            return text.length > 14 ? text.substring(0, 13) + '…' : text;
        }
    }
}
</script>

<style scoped lang="scss">
@use '../../scss/nevs/vars' as *;

.nevs-chart {
    width: 100%;
    max-width: 100%;
    min-width: 0;
}

.nevs-chart-title {
    font-weight: bold;
    margin-bottom: 8px;
}

.nevs-chart-empty {
    color: $chart-muted-color;
    font-style: italic;
    padding: 20px 0;
}

.nevs-chart-scroll {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
}

.nevs-chart-scroll svg {
    display: block;
}

.nevs-chart-grid-line {
    stroke: $chart-grid-line;
}

.nevs-chart-axis-line {
    stroke: $chart-axis-line;
}

.nevs-chart-bar {
    fill: $chart-bar-background;
    transition: fill 0.15s ease;
}

.nevs-chart-bar:hover {
    fill: $chart-bar-hover-background;
}

.nevs-chart-axis-label {
    font-size: 10px;
    fill: $chart-muted-color;
}

.nevs-chart-value {
    font-size: 10px;
    fill: $chart-label-color;
    font-weight: bold;
}

.nevs-chart-x-label {
    font-size: 10px;
    fill: $chart-label-color;
}
</style>
