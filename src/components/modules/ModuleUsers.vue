<template>
  <div class="nevs-content">
    <div class="nevs-content-top-buttons">
      <NevsButton class="primary" @click="addClick">
        <i class="fa-solid fa-plus"></i>
        {{ $LANG.Get('buttons.add') }}
      </NevsButton>
    </div>
    <NevsCard>
      <NevsTable
          :default-sort="tableData.defaultSort"
          :fields="tableData.fields"
          :height="tableData.height"
          :total-records="tableData.totalRecords"
          @reload="reloadTable">
        <tr class="nevs-table-filters">
          <td>
            <NevsTextField v-model="tableData.filters.first_name"></NevsTextField>
          </td>
          <td>
            <NevsTextField v-model="tableData.filters.last_name"></NevsTextField>
          </td>
          <td>
            <NevsTextField v-model="tableData.filters.email"></NevsTextField>
          </td>
          <td>
            <NevsSelect v-model="tableData.filters.active" :options="$HELPERS.GetYesNoAllOptions(this)"></NevsSelect>
          </td>
          <td></td>
        </tr>
        <tr v-for="(item, key) in tableData.records" :key="key">
          <td class="nevs-table-linked-cell" @click="openClick(item.id)">{{ item.first_name }}</td>
          <td class="nevs-table-linked-cell" @click="openClick(item.id)">{{ item.last_name }}</td>
          <td class="nevs-table-linked-cell" @click="openClick(item.id)">{{ item.email }}</td>
          <td class="nevs-table-linked-cell" @click="openClick(item.id)">{{ item.active_display }}</td>
          <td>
            <span :title="$LANG.Get('tooltips.openInNewWindow')" class="nevs-table-button"
                  @click="openClick(item.id, true)"><i class="fa-solid fa-up-right-from-square"></i></span>
          </td>
        </tr>
      </NevsTable>
    </NevsCard>
  </div>
</template>

<script>
import NevsCard from "@/components/nevs/NevsCard";
import NevsButton from "@/components/nevs/NevsButton";
import NevsTable from "@/components/nevs/NevsTable";
import NevsTextField from "@/components/nevs/NevsTextField";
import NevsSelect from "@/components/nevs/NevsSelect";

export default {
  name: "ModuleUsers",
  components: {NevsSelect, NevsTextField, NevsTable, NevsButton, NevsCard},
  data() {
    return {
      tableData: {
        fields: [
          {
            name: 'first_name',
            label: this.$LANG.Get('fields.firstName')
          },
          {
            name: 'last_name',
            label: this.$LANG.Get('fields.lastName')
          },
          {
            name: 'email',
            label: this.$LANG.Get('fields.email')
          },
          {
            name: 'active',
            width: '70px',
            label: this.$LANG.Get('fields.userActive')
          },
          {
            name: 'actions',
            width: '50px',
            label: ''
          }
        ],
        filters: {
          first_name: '',
          last_name: '',
          email: '',
          active: 1
        },
        records: [],
        lastRequest: null,
        totalRecords: 0,
        defaultSort: {
          field: 'first_name',
          descending: false
        },
        filterTimer: null,
        height: '0px'
      }
    }
  },
  watch: {
    'tableData.filters': {
      handler() {
        if (this.filterTimer !== null) {
          clearTimeout(this.filterTimer);
        }
        let vm = this;
        this.filterTimer = setTimeout(function () {
          vm.reloadTable(vm.tableData.lastRequest);
          vm.filterTimerOn = false;
        }, 500);
      },
      deep: true
    }
  },
  methods: {
    openClick(id, newWindow = false) {
      if (newWindow) {
        let routeData = this.$router.resolve('/users/' + id);
        window.open(routeData.href);
      } else {
        this.$router.push('/users/' + id);
      }
    },
    addClick() {
      this.$router.push('/users/0');
    },
    reloadTable(request) {
      let vm = this;
      request.filters = this.tableData.filters;
      this.tableData.lastRequest = JSON.parse(JSON.stringify(request));
      this.$API.APICall('get', 'users', request, (data, success) => {
        if (success) {
          vm.tableData.records = data.records;
          vm.tableData.totalRecords = data.total_records;
        } else {
          vm.$LOCAL_BUS.TriggerEvent('popup', {text: vm.$LANG.Get('alerts.serverError'), type: 'alert'});
        }
      });
    },
    resolveWindowResize() {
      this.tableData.height = window.innerHeight - 205 + 'px';
    }
  },
  mounted() {
    if(!this.$store.state.user.permissions.includes('MANAGE_USERS')) {
      this.$LOCAL_BUS.TriggerEvent('popup', {text: this.$LANG.Get('alerts.unauthorized'), type: 'alert'});
      this.$router.push('/');
    }
    this.$store.commit('selectMenu', 'users');
    this.$store.commit('selectSubMenu', null);
    this.$store.commit('setBreadcrumbs', [
      {
        label: this.$LANG.Get('modules.users'),
        link: null
      }
    ]);
    window.addEventListener('resize', this.resolveWindowResize);
    this.resolveWindowResize();
    let vm = this;
    this.$CROSS_TAB_BUS.ListenToEvent('reload-users', () => {
      vm.reloadTable(vm.tableData.lastRequest);
    });
  }
}
</script>

<style scoped>

</style>