<template>
    <div :style="wrapperStyle" class="nevs-field">
        <span v-if="label!== '' || reserveHeights" class="nevs-field-label">{{ label }}</span>
        <div class="nevs-select nevs-field-content" @click="dropdownClick">
            <span v-if="selected.length === 0">&nbsp;</span>
            <div @click.stop="removeOption(key)" class="nevs-select-multiple-pill" v-for="(option, key) in selected"
                 :key="key">
                {{ option }} <i class="fa-solid fa-trash"></i></div>
            <span class="nevs-dropdown-icon"><i class="fa-solid fa-angle-down"></i></span>
            <div class="nevs-clear-float"></div>
        </div>
        <span v-if="(hint!== '' || reserveHeights) && showHint" class="nevs-field-hint">{{ hint }}</span>
        <div class="nevs-dropdown-holder">
            <Transition name="dropdown">
                <div v-show="showDropdown" class="nevs-dropdown-frame">
                    <div class="nevs-dropdown-search-container">
                        <input
                            @keyup.enter="addSearchToSelected" @focusout="toggleDropdown" ref="searchField"
                            v-model="search"
                            class="nevs-dropdown-search" type="text"/>
                    </div>
                    <div class="nevs-dropdown-options">
                        <div v-for="(option, key) in filteredOptions" :key="key"
                             class="nevs-dropdown-option" @click="selectOption(option)">
                            {{ option }}
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
        <span v-if="(error!== '' || reserveHeights) && (!showHint || hint==='')" class="nevs-field-error">{{
                error
            }}</span>
    </div>
</template>

<script>

export default {
    name: "NevsMultipleAutocomplete",
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
        protected: Array,
        modelValue: Array
    },
    emits: [
        'update:modelValue'
    ],
    data() {
        return {
            showHint: false,
            selected: [],
            allOptions: [],
            showDropdown: false,
            search: '',
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
                if (this.$HELPERS.ToCroatianLower(option).search(this.$HELPERS.ToCroatianLower(this.selected)) !== -1) {
                    filtered.push(option);
                }
            }
            return filtered;
        }
    },
    watch: {
        modelValue: {
            handler() {
                if (this.modelValue.length === this.selected.length && this.modelValue.every((v, i) => v === this.selected[i])) {
                    this.setValue(this.modelValue);
                }
            },
            deep: true
        }
    },
    methods: {
        addSearchToSelected() {
            this.selectOption(this.search);
            this.search = '';
        },
        setValue(values) {
            let newValues = [];
            for (let value of values) {
                newValues.push(value);
            }
            this.selected = newValues;
        },
        selectOption(option) {
            let existingIndex = this.selected.indexOf(option);
            if (existingIndex === -1) {
                this.selected.push(option);
            }
            this.$emit('update:modelValue', this.selected);
        },
        removeOption(key) {
            this.selected.splice(key, 1);
            this.$emit('update:modelValue', this.selected);
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
            }
        },
        loadAPIOptions() {
            let vm = this;
            this.$API.APICall('get', this.ajax, {protected: this.protected}, (data, success) => {
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
    }
}
</script>
