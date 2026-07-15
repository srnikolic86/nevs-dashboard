<template>
    <div :style="wrapperStyle" class="nevs-field">
        <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
        <input @focusin="toggleDropdown" @focusout="toggleDropdown" v-model='selected' type="text"
               ref="fieldContent" class="nevs-autocomplete nevs-field-content"/>
        <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
        <!-- The dropdown is teleported to <body> and positioned via fixed coordinates so it is never clipped
             by a scrollable ancestor (e.g. a capped modal). -->
        <Teleport to="body">
            <Transition name="dropdown">
                <div v-show="showDropdown" class="nevs-dropdown-frame nevs-dropdown-frame-floating" :style="dropdownStyle">
                    <div class="nevs-dropdown-options" :style="optionsStyle">
                        <div v-for="(option, key) in filteredOptions" :key="key"
                             class="nevs-dropdown-option" @click="selectOption(option)">
                            {{ option }}
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
        <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{
                error
            }}</span>
    </div>
</template>

<script>

export default {
    name: "NevsAutocomplete",
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
        options: Array,
        ajax: String,
        minimumSearchLength: Number,
        modelValue: String
    },
    emits: [
        'update:modelValue'
    ],
    data() {
        return {
            showHint: false,
            selected: '',
            allOptions: [],
            showDropdown: false,
            dropdownStyle: {},
            optionsStyle: {},
            searchTimer: null,
            crossTabReloadHandle: null
        }
    },
    computed: {
        wrapperStyle() {
            let style = {};
            if (this.width) {
                style.width = this.width;
            }
            return style;
        },

        filteredOptions() {
            if (this.minimumSearchLength) {
                if (this.selected.length < this.minimumSearchLength) {
                    return [];
                }
            }

            let filtered = [];
            for (let option of this.allOptions) {
                if (this.$HELPERS.ToCroatianLower(option).search(this.$HELPERS.ToCroatianLower(this.selected)) !== -1) {
                    filtered.push(option);
                }
            }
            return filtered;
        }
    },
    watch: {
        modelValue() {
            if (this.modelValue !== this.selected) {
                this.setValue(this.modelValue);
            }
        },
        selected() {
            this.selectOption(this.selected);
            // Ajax autocompletes search on the server: on every (debounced) keystroke the endpoint is re-queried
            // with the typed text, so it only ever returns a capped, matching set instead of the whole table.
            if (!this.ajax) return;
            if (this.searchTimer) clearTimeout(this.searchTimer);
            let vm = this;
            this.searchTimer = setTimeout(() => {
                vm.loadAPIOptions(vm.selected);
            }, 250);
        }
    },
    methods: {
        setValue(value) {
            this.selected = value;
        },
        selectOption(option) {
            this.selected = option;
            this.$emit('update:modelValue', this.selected);
        },
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            this.showHint = !this.showHint;
            if (this.showDropdown) {
                this.updateDropdownPosition();
                // Keep the teleported dropdown aligned with the field while it is open.
                window.addEventListener('scroll', this.updateDropdownPosition, true);
                window.addEventListener('resize', this.updateDropdownPosition);
            } else {
                window.removeEventListener('scroll', this.updateDropdownPosition, true);
                window.removeEventListener('resize', this.updateDropdownPosition);
            }
        },
        updateDropdownPosition() {
            if (!this.$refs.fieldContent) return;
            let rect = this.$refs.fieldContent.getBoundingClientRect();

            // The dropdown is fixed-positioned (teleported to <body>), so it can't grow the page scroll.
            // Keep it inside the viewport: open upward when there isn't enough room below, and cap the
            // options list to the space actually available so the whole dropdown always fits on screen.
            const margin = 8;
            const spaceBelow = window.innerHeight - rect.bottom - margin;
            const spaceAbove = rect.top - margin;
            const openUp = spaceBelow < 200 && spaceAbove > spaceBelow;
            const available = openUp ? spaceAbove : spaceBelow;

            let style = {
                position: 'fixed',
                left: rect.left + 'px',
                width: rect.width + 'px',
                zIndex: 200
            };
            if (openUp) {
                // Reset the base rule's `top: 0`, otherwise the frame stretches from the top of the
                // screen down to the field and fills the whole height.
                style.top = 'auto';
                style.bottom = (window.innerHeight - rect.top) + 'px';
            } else {
                style.top = rect.bottom + 'px';
                style.bottom = 'auto';
            }
            this.dropdownStyle = style;

            let optionsMax = Math.max(available, 80);
            if (optionsMax > 300) optionsMax = 300;
            this.optionsStyle = {maxHeight: optionsMax + 'px'};
        },
        loadAPIOptions(search = '') {
            let vm = this;
            this.$API.APICall('get', this.ajax, {protected: this.protected, search: search}, (data, success) => {
                if (success) {
                    vm.allOptions = data;
                    vm.$nextTick(() => {
                        vm.setValue(vm.modelValue);
                    });
                }
            });
        }
    },
    mounted() {
        if (!this.ajax) {
            this.allOptions = this.options;
            this.setValue(this.modelValue);
        } else {
            this.loadAPIOptions();
        }
        let vm = this;
        this.crossTabReloadHandle = this.$CROSS_TAB_BUS.ListenToEvent('reload-data', () => {
            vm.loadAPIOptions();
        });
    },
    unmounted() {
        if (this.crossTabReloadHandle !== null) {
            this.$CROSS_TAB_BUS.UnbindEvent(this.crossTabReloadHandle);
        }
        window.removeEventListener('scroll', this.updateDropdownPosition, true);
        window.removeEventListener('resize', this.updateDropdownPosition);
    }
}

</script>
