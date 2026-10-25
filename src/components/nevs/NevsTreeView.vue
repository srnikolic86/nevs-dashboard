<template>
  <ul class="nevs-tree-view">
    <li v-for="node in nodes" :key="node[idField]" class="nevs-tree-node">
      <div class="nevs-tree-row" :class="{'nevs-tree-row-selected': node[idField] === selected}">
        <span class="nevs-tree-toggle" @click="toggle(node)">
          <i v-if="node.children && node.children.length > 0"
             :class="isExpanded(node) ? 'fa-solid fa-caret-down' : 'fa-solid fa-caret-right'"></i>
          <i v-else class="fa-regular fa-circle nevs-tree-leaf"></i>
        </span>
        <span class="nevs-tree-label" @click="select(node)">{{ node[labelField] }}</span>
        <span v-if="tag && tag(node)" class="nevs-tree-tag">{{ tag(node) }}</span>
        <span class="nevs-tree-actions">
          <template v-for="(action, aKey) in actions" :key="aKey">
            <span v-if="actionVisible(action, node)" class="nevs-tree-action" :title="action.tooltip"
                  @click.stop="$emit('action', action.name, node)"><i :class="action.icon"></i></span>
          </template>
        </span>
      </div>
      <NevsTreeView
          v-if="node.children && node.children.length > 0 && isExpanded(node)"
          :tree="node.children"
          :id-field="idField"
          :label-field="labelField"
          :parent-field="parentField"
          :selected="selected"
          :actions="actions"
          :tag="tag"
          @select="select"
          @action="(name, n) => $emit('action', name, n)"></NevsTreeView>
    </li>
  </ul>
</template>

<script>
export default {
  name: "NevsTreeView",
  props: {
    items: {
      type: Array,
      default: null
    },
    tree: {
      type: Array,
      default: null
    },
    idField: {
      type: String,
      default: 'id'
    },
    labelField: {
      type: String,
      default: 'name'
    },
    parentField: {
      type: String,
      default: 'parent_id'
    },
    selected: {
      type: [Number, String],
      default: null
    },
    // Per-node action buttons: { name, icon, tooltip, show }. `show` may be a boolean or a
    // (node) => boolean predicate; omitted means always visible. Clicking emits ('action', name, node).
    actions: {
      type: Array,
      default: () => []
    },
    // Optional (node) => string returning a small badge/tag shown next to the node label.
    tag: {
      type: Function,
      default: null
    }
  },
  emits: ['select', 'action'],
  data() {
    return {
      collapsed: {}
    }
  },
  computed: {
    nodes() {
      if (this.tree !== null) return this.tree;
      return this.buildTree(this.items || []);
    }
  },
  methods: {
    buildTree(items) {
      let map = {};
      let roots = [];
      for (let item of items) {
        map[item[this.idField]] = Object.assign({}, item, {children: []});
      }
      for (let item of items) {
        let node = map[item[this.idField]];
        let parent = item[this.parentField];
        if (parent === null || parent === undefined || parent === 0 || parent === '0' || parent === '' || !map[parent]) {
          roots.push(node);
        } else {
          map[parent].children.push(node);
        }
      }
      return roots;
    },
    isExpanded(node) {
      return this.collapsed[node[this.idField]] !== true;
    },
    toggle(node) {
      if (!node.children || node.children.length === 0) return;
      this.collapsed[node[this.idField]] = this.isExpanded(node);
    },
    select(node) {
      this.$emit('select', node);
    },
    actionVisible(action, node) {
      if (typeof action.show === 'function') return action.show(node);
      return action.show !== false;
    }
  }
}
</script>

<style scoped>
.nevs-tree-view {
  list-style: none;
  margin: 0;
  padding-left: 15px;
}

.nevs-tree-view.nevs-tree-view {
  padding-left: 18px;
}

.nevs-tree-node {
  margin: 0;
}

.nevs-tree-row {
  display: flex;
  align-items: center;
  padding: 4px 6px;
  border-radius: 6px;
  cursor: default;
}

.nevs-tree-row:hover {
  background: rgba(0, 0, 0, 0.05);
}

.nevs-tree-row-selected {
  background: rgba(0, 0, 0, 0.09);
  font-weight: 600;
}

.nevs-tree-toggle {
  width: 18px;
  text-align: center;
  cursor: pointer;
  color: #888;
}

.nevs-tree-leaf {
  font-size: 7px;
  vertical-align: middle;
  color: #bbb;
}

.nevs-tree-label {
  cursor: pointer;
  padding: 0 4px;
  flex: 1;
}

.nevs-tree-tag {
  font-size: 11px;
  color: #555;
  background: rgba(0, 0, 0, 0.07);
  padding: 1px 8px;
  border-radius: 10px;
  white-space: nowrap;
  margin-left: 4px;
}

.nevs-tree-actions {
  display: inline-flex;
  gap: 6px;
  margin-left: 6px;
  opacity: 0;
  transition: opacity 0.1s ease;
}

.nevs-tree-row:hover .nevs-tree-actions {
  opacity: 1;
}

.nevs-tree-action {
  cursor: pointer;
  color: #888;
  padding: 0 2px;
}

.nevs-tree-action:hover {
  color: #333;
}
</style>
