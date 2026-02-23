<template>
  <div class="nevs-content">

    <h2>Text Inputs</h2>
    <NevsCard>
      <NevsTextField v-model="examples.text" :label="'Text Field'" :hint="'Enter any text'"></NevsTextField>
      <NevsTextField v-model="examples.textReadonly" :label="'Read Only'" :readonly="true"></NevsTextField>
      <NevsTextField v-model="examples.textError" :label="'With Error'" :error="'This field is required'"></NevsTextField>
      <NevsMaskedField v-model="examples.masked" :label="'Masked (NNN-NNN-NNNN)'" :mask="'NNN-NNN-NNNN'"></NevsMaskedField>
      <NevsTextArea v-model="examples.textarea" :label="'Text Area'" :hint="'Enter multiple lines of text'"></NevsTextArea>
    </NevsCard>

    <h2>Number & Date & Checkbox</h2>
    <NevsCard>
      <NevsNumberField v-model="examples.number" :label="'Number Field'" :decimal-places="2"></NevsNumberField>
      <NevsDateField v-model="examples.date" :label="'Date Field'"></NevsDateField>
      <NevsCheckbox v-model="examples.checkbox" :label="'Checkbox'"></NevsCheckbox>
    </NevsCard>

    <h2>Selects & Autocomplete</h2>
    <NevsCard>
      <NevsSelect v-model="examples.select" :label="'Select'" :options="colorOptions" :nullable="true"></NevsSelect>
      <NevsMultipleSelect v-model="examples.multipleSelect" :label="'Multiple Select'" :options="colorOptions"></NevsMultipleSelect>
      <NevsAutocomplete v-model="examples.autocomplete" :label="'Autocomplete'" :options="fruitOptions"></NevsAutocomplete>
      <NevsMultipleAutocomplete v-model="examples.multipleAutocomplete" :label="'Multiple Autocomplete'" :options="fruitOptions"></NevsMultipleAutocomplete>
    </NevsCard>

    <h2>File Upload</h2>
    <NevsCard>
      <NevsUpload v-model="examples.upload" :label="'Upload'" :accept="'.jpg,.png,.pdf'"></NevsUpload>
    </NevsCard>

    <h2>Buttons</h2>
    <NevsCard>
      <div class="buttons-showcase">
        <NevsButton>Default</NevsButton>
        <NevsButton class="primary">Primary</NevsButton>
        <NevsButton class="secondary">Secondary</NevsButton>
        <NevsButton class="warning">Warning</NevsButton>
        <NevsButton class="error">Error</NevsButton>
        <NevsButton class="success">Success</NevsButton>
      </div>
      <div class="buttons-showcase">
        <NevsButton class="primary" @click="triggerNotification">
          <i class="fa-solid fa-bell"></i> Trigger Notification
        </NevsButton>
        <NevsButton @click="triggerAlert">Popup: Alert</NevsButton>
        <NevsButton @click="triggerConfirm">Popup: Confirm</NevsButton>
        <NevsButton @click="triggerInput">Popup: Input</NevsButton>
      </div>
    </NevsCard>

    <h2>Actions Dropdown</h2>
    <NevsCard>
      <NevsActions :width="'200px'" :actions="actionsExample" :context="actionsContext"></NevsActions>
    </NevsCard>

  </div>
</template>

<script>
import NevsCard from "@/components/nevs/NevsCard";
import NevsTextField from "@/components/nevs/NevsTextField";
import NevsMaskedField from "@/components/nevs/NevsMaskedField";
import NevsTextArea from "@/components/nevs/NevsTextArea";
import NevsNumberField from "@/components/nevs/NevsNumberField";
import NevsDateField from "@/components/nevs/NevsDateField";
import NevsCheckbox from "@/components/nevs/NevsCheckbox";
import NevsSelect from "@/components/nevs/NevsSelect";
import NevsMultipleSelect from "@/components/nevs/NevsMultipleSelect";
import NevsAutocomplete from "@/components/nevs/NevsAutocomplete";
import NevsMultipleAutocomplete from "@/components/nevs/NevsMultipleAutocomplete";
import NevsUpload from "@/components/nevs/NevsUpload";
import NevsButton from "@/components/nevs/NevsButton";
import NevsActions from "@/components/nevs/NevsActions";

export default {
  name: "ModuleHome",
  components: {
    NevsCard,
    NevsTextField,
    NevsMaskedField,
    NevsTextArea,
    NevsNumberField,
    NevsDateField,
    NevsCheckbox,
    NevsSelect,
    NevsMultipleSelect,
    NevsAutocomplete,
    NevsMultipleAutocomplete,
    NevsUpload,
    NevsButton,
    NevsActions
  },
  data() {
    return {
      examples: {
        text: 'Hello World',
        textReadonly: 'Cannot edit this',
        textError: '',
        masked: '',
        textarea: '',
        number: 1234.56,
        date: '2026-02-23',
        checkbox: true,
        select: null,
        multipleSelect: [],
        autocomplete: '',
        multipleAutocomplete: [],
        upload: {id: null, name: '', link: ''}
      },
      fruitOptions: ['Apple', 'Banana', 'Blueberry', 'Grape', 'Lime', 'Orange', 'Strawberry', 'Watermelon'],
      colorOptions: [
        {label: 'Red', value: 1},
        {label: 'Blue', value: 2},
        {label: 'Green', value: 3},
        {label: 'Yellow', value: 4},
        {label: 'Purple', value: 5},
        {label: 'Orange', value: 6},
      ],
      actionsExample: [
        {
          label: 'Edit',
          action: (context) => {
            this.$LOCAL_BUS.TriggerEvent('notification', {text: 'Edit clicked for: ' + context.name});
          },
          visible: () => { return true; }
        },
        {
          label: 'Delete',
          action: (context) => {
            this.$LOCAL_BUS.TriggerEvent('popup', {
              type: 'confirm',
              text: 'Delete ' + context.name + '?',
              callback: (response) => {
                if (response) {
                  this.$LOCAL_BUS.TriggerEvent('notification', {text: context.name + ' deleted.'});
                }
              }
            });
          },
          visible: () => { return true; }
        }
      ],
      actionsContext: {id: 1, name: 'Example Item'}
    }
  },
  methods: {
    triggerNotification() {
      this.$LOCAL_BUS.TriggerEvent('notification', {text: 'This is a notification!', duration: 3000});
    },
    triggerAlert() {
      this.$LOCAL_BUS.TriggerEvent('popup', {type: 'alert', text: 'This is an alert popup!'});
    },
    triggerConfirm() {
      this.$LOCAL_BUS.TriggerEvent('popup', {
        type: 'confirm',
        text: 'Are you sure?',
        callback: (response) => {
          this.$LOCAL_BUS.TriggerEvent('notification', {text: 'Confirmed: ' + response});
        }
      });
    },
    triggerInput() {
      this.$LOCAL_BUS.TriggerEvent('popup', {
        type: 'input',
        text: 'Enter a value:',
        default: 'default value',
        callback: (response) => {
          this.$LOCAL_BUS.TriggerEvent('notification', {text: 'Input: ' + response});
        }
      });
    }
  },
  mounted() {
    this.$store.commit('selectMenu', 'home');
    this.$store.commit('selectSubMenu', null);
    this.$store.commit('setBreadcrumbs', [
      {
        label: this.$LANG.Get('modules.home'),
        link: null
      }
    ]);
  }
}
</script>

<style scoped>
.buttons-showcase {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 8px;
}
</style>
