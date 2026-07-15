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

    <h2>Rich Text Editor</h2>
    <NevsCard>
      <NevsWysiwyg v-model="examples.wysiwyg" :label="'WYSIWYG (Quill)'"></NevsWysiwyg>
    </NevsCard>

    <h2>File Upload</h2>
    <NevsCard>
      <NevsUpload v-model="examples.upload" :label="'Upload'" :accept="'.jpg,.png,.pdf'"></NevsUpload>
    </NevsCard>

    <h2>Multi Upload</h2>
    <NevsCard>
      <NevsMultiUpload :max-size-mb="10" @uploaded="onFilesUploaded"></NevsMultiUpload>
    </NevsCard>

    <h2>Photo Upload</h2>
    <NevsCard>
      <NevsPhotoUpload v-model="examples.photo"></NevsPhotoUpload>
    </NevsCard>

    <h2>Tabs</h2>
    <NevsCard>
      <NevsTabs v-model="activeTab" :tabs="tabs"></NevsTabs>
      <NevsTab :model-value="activeTab" value="overview">
        <p>Overview pane. NevsTabs renders the strip; each NevsTab shows its slot while its <code>value</code> matches the active tab.</p>
      </NevsTab>
      <NevsTab :model-value="activeTab" value="details">
        <p>Details pane content goes here.</p>
      </NevsTab>
      <NevsTab :model-value="activeTab" value="settings">
        <p>Settings pane content goes here.</p>
      </NevsTab>
    </NevsCard>

    <h2>Grids</h2>
    <NevsCard>
      <h3>Simple Grid</h3>
      <NevsSimpleGrid :fields="gridFields" :records="gridRecords" :empty-text="'No records.'" @row-click="onRowClick">
        <template #actions="{ record }">
          <i class="fa-solid fa-pen-to-square" title="Edit" @click.stop="onRowEdit(record)"></i>
        </template>
      </NevsSimpleGrid>

      <h3>Tiny Grid</h3>
      <NevsTinyGrid :columns="tinyColumns" :records="tinyRecords" :actions="tinyActions"
                    :empty-text="'No items.'" @action="onTinyAction"></NevsTinyGrid>
    </NevsCard>

    <h2>Data Table</h2>
    <NevsCard>
      <NevsTable
          :default-sort="table.defaultSort"
          :fields="table.fields"
          :total-records="table.totalRecords"
          @reload="reloadTable">
        <tr class="nevs-table-filters">
          <td><NevsTextField v-model="table.filters.first_name"></NevsTextField></td>
          <td><NevsTextField v-model="table.filters.last_name"></NevsTextField></td>
          <td><NevsTextField v-model="table.filters.email"></NevsTextField></td>
          <td></td>
        </tr>
        <tr v-for="(item, key) in table.records" :key="key">
          <td>{{ item.first_name }}</td>
          <td>{{ item.last_name }}</td>
          <td>{{ item.email }}</td>
          <td>{{ item.active_display }}</td>
        </tr>
      </NevsTable>
    </NevsCard>

    <h2>Chart</h2>
    <NevsCard>
      <NevsChart :labels="chartLabels" :values="chartValues" :title="'Monthly Revenue'" :suffix="' €'"></NevsChart>
    </NevsCard>

    <h2>Calendar</h2>
    <NevsCard>
      <NevsCalendar :events="calendarEvents" @range-change="onRangeChange" @event-click="onEventClick"></NevsCalendar>
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
import moment from "moment/moment";
import NevsCard from "@/components/nevs/NevsCard.vue";
import NevsTable from "@/components/nevs/NevsTable.vue";
import NevsTextField from "@/components/nevs/NevsTextField.vue";
import NevsMaskedField from "@/components/nevs/NevsMaskedField.vue";
import NevsTextArea from "@/components/nevs/NevsTextArea.vue";
import NevsNumberField from "@/components/nevs/NevsNumberField.vue";
import NevsDateField from "@/components/nevs/NevsDateField.vue";
import NevsCheckbox from "@/components/nevs/NevsCheckbox.vue";
import NevsSelect from "@/components/nevs/NevsSelect.vue";
import NevsMultipleSelect from "@/components/nevs/NevsMultipleSelect.vue";
import NevsAutocomplete from "@/components/nevs/NevsAutocomplete.vue";
import NevsMultipleAutocomplete from "@/components/nevs/NevsMultipleAutocomplete.vue";
import NevsUpload from "@/components/nevs/NevsUpload.vue";
import NevsButton from "@/components/nevs/NevsButton.vue";
import NevsActions from "@/components/nevs/NevsActions.vue";
import NevsWysiwyg from "@/components/nevs/NevsWysiwyg.vue";
import NevsMultiUpload from "@/components/nevs/NevsMultiUpload.vue";
import NevsPhotoUpload from "@/components/nevs/NevsPhotoUpload.vue";
import NevsTabs from "@/components/nevs/NevsTabs.vue";
import NevsTab from "@/components/nevs/NevsTab.vue";
import NevsSimpleGrid from "@/components/nevs/NevsSimpleGrid.vue";
import NevsTinyGrid from "@/components/nevs/NevsTinyGrid.vue";
import NevsChart from "@/components/nevs/NevsChart.vue";
import NevsCalendar from "@/components/nevs/NevsCalendar.vue";

export default {
  name: "ModuleHome",
  components: {
    NevsCard,
    NevsTable,
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
    NevsActions,
    NevsWysiwyg,
    NevsMultiUpload,
    NevsPhotoUpload,
    NevsTabs,
    NevsTab,
    NevsSimpleGrid,
    NevsTinyGrid,
    NevsChart,
    NevsCalendar
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
        upload: {id: null, name: '', link: ''},
        wysiwyg: '<p>Edit <strong>rich</strong> text with the <em>Quill</em> editor.</p>',
        photo: {id: 0, name: '', link: ''}
      },
      activeTab: 'overview',
      tabs: [
        {value: 'overview', label: 'Overview'},
        {value: 'details', label: 'Details'},
        {value: 'settings', label: 'Settings'}
      ],
      gridFields: [
        {name: 'name', label: 'Name'},
        {name: 'role', label: 'Role'},
        {name: 'score', label: 'Score', align: 'R'}
      ],
      gridRecords: [
        {name: 'Ada Lovelace', role: 'Engineer', score: 98},
        {name: 'Alan Turing', role: 'Researcher', score: 95},
        {name: 'Grace Hopper', role: 'Admiral', score: 99}
      ],
      tinyColumns: [
        {key: 'item', label: 'Item', align: 'left'},
        {key: 'qty', label: 'Qty', align: 'right', width: '80px'},
        {key: 'price', label: 'Price', align: 'right', width: '120px'}
      ],
      tinyRecords: [
        {item: 'Widget', qty: 3, price: '12.00 €'},
        {item: 'Gadget', qty: 1, price: '49.90 €'},
        {item: 'Gizmo', qty: 7, price: '4.25 €'}
      ],
      tinyActions: [
        {name: 'edit', icon: 'fa-solid fa-pen-to-square', tooltip: 'Edit'},
        {name: 'delete', icon: 'fa-solid fa-trash', tooltip: 'Delete'}
      ],
      chartLabels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      chartValues: [4200, 5100, 4800, 6300, 7200, 6900],
      calendarEvents: [],
      filterTimer: null,
      table: {
        fields: [
          {name: 'first_name', label: 'Name'},
          {name: 'last_name', label: 'Surname'},
          {name: 'email', label: 'E-mail'},
          {name: 'active', width: '90px', label: 'Active'}
        ],
        filters: {first_name: '', last_name: '', email: '', active: -1},
        records: [],
        totalRecords: 0,
        lastRequest: null,
        defaultSort: {field: 'first_name', descending: false}
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
  watch: {
    'table.filters': {
      // Debounced: reload the table half a second after the user stops typing in a filter.
      handler() {
        if (this.filterTimer !== null) {
          clearTimeout(this.filterTimer);
        }
        let vm = this;
        this.filterTimer = setTimeout(() => {
          vm.reloadTable(vm.table.lastRequest);
        }, 500);
      },
      deep: true
    }
  },
  methods: {
    reloadTable(request) {
      let vm = this;
      request.filters = this.table.filters;
      this.table.lastRequest = JSON.parse(JSON.stringify(request));
      // Server-side paginated/sorted/filtered. total_records may come back as -1 (capped) -> NevsTable shows "N+".
      this.$API.APICall('get', 'users', request, (data, success) => {
        if (success) {
          vm.table.records = data.records;
          vm.table.totalRecords = data.total_records;
        } else {
          vm.$LOCAL_BUS.TriggerEvent('popup', {text: vm.$LANG.Get('alerts.serverError'), type: 'alert'});
        }
      });
    },
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
    },
    onFilesUploaded(files) {
      this.$LOCAL_BUS.TriggerEvent('notification', {text: files.length + ' file(s) uploaded.'});
    },
    onRowClick(record) {
      this.$LOCAL_BUS.TriggerEvent('notification', {text: 'Row clicked: ' + record.name});
    },
    onRowEdit(record) {
      this.$LOCAL_BUS.TriggerEvent('notification', {text: 'Edit: ' + record.name});
    },
    onTinyAction(name, record) {
      this.$LOCAL_BUS.TriggerEvent('notification', {text: name + ': ' + record.item});
    },
    onRangeChange() {
      // In a real module the parent would fetch events for the newly visible range here.
    },
    onEventClick(event) {
      this.$LOCAL_BUS.TriggerEvent('notification', {text: 'Event: ' + event.name});
    },
    buildCalendarEvents() {
      // Events are dated relative to today so they land on the calendar's default (current) view.
      const slot = (dayOffset, hour, minute, durationMinutes) => {
        const start = moment().add(dayOffset, 'days').set({hour: hour, minute: minute, second: 0});
        const end = start.clone().add(durationMinutes, 'minutes');
        return {start: start.format('YYYY-MM-DD HH:mm'), end: end.format('YYYY-MM-DD HH:mm')};
      };
      this.calendarEvents = [
        {name: 'Team standup', color: '#4d4dff', ...slot(0, 9, 30, 30)},
        {name: 'Design review', color: '#2e9e5b', ...slot(0, 13, 0, 60)},
        {name: '1:1 meeting', color: '#e0912f', ...slot(1, 11, 0, 45)},
        {name: 'Sprint planning', color: '#c8362f', ...slot(2, 10, 0, 90)}
      ];
    }
  },
  mounted() {
    this.buildCalendarEvents();
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

.nevs-content h3 {
  margin: 16px 0 8px;
}
</style>
