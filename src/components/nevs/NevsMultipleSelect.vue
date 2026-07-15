<template>
    <div :style="wrapperStyle" class="nevs-field">
        <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
        <div class="nevs-select nevs-field-content" ref="fieldContent" @click="dropdownClick">
            <span v-if="selected.length === 0">&nbsp;</span>
            <div @click.stop="removeOption(key)" class="nevs-select-multiple-pill" v-for="(option, key) in selected"
                 :key="key">
                {{ option.label }} <i class="fa-solid fa-trash"></i></div>
            <span class="nevs-dropdown-icon"><i class="fa-solid fa-angle-down"></i></span>
            <div class="nevs-clear-float"></div>
        </div>
        <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
        <!-- The dropdown is teleported to <body> and positioned via fixed coordinates so it is never clipped
             by a scrollable ancestor (e.g. a capped modal). -->
        <Teleport to="body">
            <Transition name="dropdown">
                <div v-show="showDropdown" class="nevs-dropdown-frame nevs-dropdown-frame-floating" :style="dropdownStyle">
                    <div class="nevs-dropdown-search-container">
                        <input @focusout="toggleDropdown" ref="searchField" v-model="search"
                               class="nevs-dropdown-search"
                               type="text"/>
                    </div>
                    <div class="nevs-dropdown-options" :style="optionsStyle">
                        <div v-for="(option, key) in filteredOptions" :key="key"
                             class="nevs-dropdown-option" @click="selectOption(option)">
                            {{ option.label }}
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
    name: "NevsMultipleSelect",
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
        modelValue: Array
    },
    emits: [
        'update:modelValue'
    ],
    data() {
        return {
            showHint: false,
            fullyLoaded: false,
            selected: [],
            allOptions: [],
            showDropdown: false,
            dropdownStyle: {},
            optionsStyle: {},
            search: '',
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
                if (this.search.length < this.minimumSearchLength) {
                    return [];
                }
            }

            let filtered = [];
            for (let option of this.allOptions) {
                if (this.$HELPERS.ToCroatianLower(option.label).search(this.$HELPERS.ToCroatianLower(this.search)) !== -1) {
                    filtered.push(option);
                }
            }
            return filtered;
        }
    },
    watch: {
        modelValue: {
            handler() {
                let emitValue = this.getEmitValue();
                if (this.modelValue.length === emitValue.length && this.modelValue.every((v, i) => v === emitValue[i])) {
                    this.setValue(this.modelValue);
                }
            },
            deep: true
        },
        // Ajax selects search on the server: on every (debounced) keystroke the endpoint is re-queried with the
        // search term, so it only ever returns a capped, matching set instead of the whole table.
        search() {
            if (!this.ajax) return;
            if (this.searchTimer) clearTimeout(this.searchTimer);
            let vm = this;
            this.searchTimer = setTimeout(() => {
                vm.loadAPIOptions(vm.search);
            }, 250);
        }
    },
    methods: {
        setValue(values) {
            let newValues = [];
            for (let value of values) {
                let option = this.getOptionByValue(value);
                newValues.push(option);
            }
            this.selected = newValues;
        },
        getOptionByValue(value) {
            for (let option of this.allOptions) {
                if (option.value === value) {
                    return option;
                }
            }
            return null;
        },
        getEmitValue() {
            let emitValue = [];
            for (let selectedItem of this.selected) {
                emitValue.push(selectedItem.value);
            }
            return emitValue;
        },
        selectOption(option) {
            let existingIndex = this.selected.indexOf(option);
            if (existingIndex === -1) {
                this.selected.push(option);
            }
            this.$emit('update:modelValue', this.getEmitValue());
        },
        removeOption(key) {
            this.selected.splice(key, 1);
            this.$emit('update:modelValue', this.getEmitValue());
        },
        dropdownClick() {
            this.toggleDropdown();
            if (this.showDropdown) {
                this.$nextTick(() => {
                    this.$refs.searchField.focus();
                });
            }
        },
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            this.showHint = !this.showHint;
            if (this.showDropdown) {
                this.search = '';
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
            // Space taken by the search box above the options; the options list is capped to whatever is left.
            const searchHeight = 64;
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

            let optionsMax = Math.max(available - searchHeight, 80);
            if (optionsMax > 300) optionsMax = 300;
            this.optionsStyle = {maxHeight: optionsMax + 'px'};
        },
        loadAPIOptions(search = '') {
            let vm = this;
            this.$API.APICall('get', this.ajax, {protected: this.modelValue, search: search}, (data, success) => {
                if (success) {
                    vm.allOptions = data;
                    vm.$nextTick(() => {
                        if (vm.modelValue !== undefined) {
                            vm.setValue(vm.modelValue);
                        }
                    });
                }
            });
        }
    },
    mounted() {
        if (!this.ajax) {
            this.allOptions = this.options;
            if (this.modelValue !== undefined) {
                this.setValue(this.modelValue);
            }
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
