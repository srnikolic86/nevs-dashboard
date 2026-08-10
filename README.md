# Table of contents
- [Intro](#intro)
- [Tested with](#tested-with)
- [Quick setup](#quick-setup)
- [Text field](#text-field)
- [Masked field](#masked-field)
- [Text area](#text-area)
- [Select](#select)
- [Multiple select](#multiple-select)
- [Autocomplete](#autocomplete)
- [Multiple autocomplete](#multiple-autocomplete)
- [Date field](#date-field)
- [Number field](#number-field)
- [Checkbox](#checkbox)
- [File upload](#file-upload)
- [API access](#api-access)
- [Local event bus](#local-event-bus)
- [Buttons](#buttons)
- [Notifications](#notifications)
- [Popups](#popups)
- [Data table](#data-table)
- [Table filters](#table-filters)
- [Simple grid](#simple-grid)
- [Tiny grid](#tiny-grid)
- [Tree view](#tree-view)
- [Chart](#chart)
- [Photo upload](#photo-upload)
- [Multi upload](#multi-upload)
- [Tabs](#tabs)
- [Calendar](#calendar)
- [WYSIWYG editor](#wysiwyg-editor)
- [Translations](#translations)
- [Card](#card)
- [Menu](#menu)
- [Top bar](#top-bar)

# Intro
This is a web application template. It uses [NevsPHP](https://github.com/srnikolic86/nevsphp-example) for the backend and Vue.js for the frontend.\
It has a log in screen and a user management module.

# Tested with
 - PHP 8.3
 - Node.js 22.22.0
 - NPM 10.9.4
 - MariaDB 11.0.2

# Quick setup
1. copy _api/App/config.php.example_ into _api/Config/App/config.php_ and setup your database connection
2. run _composer install_ in _api_ folder
3. run _php migrate.php_ in _api/Console_ folder
4. set correct permissions to _api/Storage/Uploads_ folder
5. copy _src/config.json.example_ into _src/config.json_
6. run _npm ci_ in the root folder
7. run _npm run serve_ in the root folder

Default credentials are:
```
E-mail: admin@admin.com
Password: 12345
```

# Text field
```html
<NevsTextField v-model="value"></NevsTextField>
```
| Prop     | Type     | Description                                                                   |
|----------|----------|-------------------------------------------------------------------------------|
| v-model  | String   | value                                                                         |
| readonly | Boolean  | set to _true_ if you want this field to be readonly                           |
| width    | String   | CSS directly outputted into _width_ (defaults to _'100%'_)                    |
| label    | String   | label of the field                                                            |
| hint     | String   | hint for the field                                                            |
| error    | String   | error message                                                                 |
| type     | String   | outputted into _type_ attribute of HTML _\<input\>_ tag, defaults to _'text'_ |
| name     | String   | outputted into _name_ attribute of HTML _\<input\>_ tag if set                |

# Masked field
```html
<NevsMaskedField v-model="value" :mask="'CCC-LLL-nnn'"></NevsMaskedField>
```
| Prop       | Type       | Description                                                                                                                                                                                                                                                                                                                                                                                                   |
|------------|------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| v-model    | String     | value                                                                                                                                                                                                                                                                                                                                                                                                         |
| readonly   | Boolean    | set to _true_ if you want this field to be readonly                                                                                                                                                                                                                                                                                                                                                           |
| width      | String     | CSS directly outtputed into _width_ (defaults to _'100%'_)                                                                                                                                                                                                                                                                                                                                                    |
| label      | String     | label of the field                                                                                                                                                                                                                                                                                                                                                                                            |
| hint       | String     | hint for the field                                                                                                                                                                                                                                                                                                                                                                                            |
| error      | String     | error message                                                                                                                                                                                                                                                                                                                                                                                                 |
| mask       | String     | C - any character mandatory<br/>c - any character optional<br/>A - any letter or digit mandatory<br/>a - any letter or digit optional<br/>L - any letter mandatory<br/>l - any letter optional<br/>N - any digit mandatory<br/>n - any digit optional<br/>X - any character besides letters and digits mandatory<br/>x - any character besides letters and digits optional<br/>\ - next character  is escaped |

# Text area
```html
<NevsTextArea v-model="value"></NevsTextArea>
```
| Prop       | Type      | Description                                                |
|------------|-----------|------------------------------------------------------------|
| v-model    | String    | value                                                      |
| readonly   | Boolean   | set to _true_ if you want this field to be readonly        |
| width      | String    | CSS directly outtputed into _width_ (defaults to _'100%'_) |
| label      | String    | label of the field                                         |
| hint       | String    | hint for the field                                         |
| error      | String    | error message                                              |

# Select
```html
<NevsSelect v-model="selectedValue"></NevsSelect>
```
| Prop                  | Type              | Description                                                                                        |
|-----------------------|-------------------|----------------------------------------------------------------------------------------------------|
| v-model               | String or Integer | value                                                                                              |
| readonly              | Boolean           | set to _true_ if you want this field to be readonly                                                |
| width                 | String            | CSS directly outtputed into _width_ (defaults to _'100%'_)                                         |
| label                 | String            | label of the field                                                                                 |
| hint                  | String            | hint for the field                                                                                 |
| error                 | String            | error message                                                                                      |
| options               | Array             | array of options (used only if _ajax_ is not set)                                                  |
| ajax                  | String            | API endpoint to fetch options                                                                      |
| minimum-search-length | Number            | minimum length of the search term to be considered when filtering results                          |
| protected             | Number, String    | passed to the API as _protected_ GET parameter and is to be used to handle soft deleted options    |
| nullable              | Boolean           | if this is used there will be a blank option with a value of _null_ on the top of filtered options |
Options are in this form:
```json
{
  "label": "option 1",
  "value": 1
}
```

# Multiple select
```html
<NevsMultipleSelect v-model="selectedValues"></NevsMultipleSelect>
```
| Prop                  | Type                         | Description                                                                                     |
|-----------------------|------------------------------|-------------------------------------------------------------------------------------------------|
| v-model               | Array of strings or integers | value                                                                                           |
| width                 | String                       | CSS directly outtputed into _width_ (defaults to _'100%'_)                                      |
| label                 | String                       | label of the field                                                                              |
| hint                  | String                       | hint for the field                                                                              |
| error                 | String                       | error message                                                                                   |
| options               | Array                        | array of options (used only if _ajax_ is not set)                                               |
| ajax                  | String                       | API endpoint to fetch options                                                                   |
| minimum-search-length | Number                       | minimum length of the search term to be considered when filtering results                       |
| protected             | Array                        | passed to the API as _protected_ GET parameter and is to be used to handle soft deleted options |
Options are in this form:
```json
{
  "label": "option 1",
  "value": 1
}
```

# Autocomplete
```html
<NevsAutocomplete v-model="selectedValue"></NevsAutocomplete>
```
| Prop                  | Type        | Description                                                               |
|-----------------------|-------------|---------------------------------------------------------------------------|
| v-model               | String      | value                                                                     |
| width                 | String      | CSS directly outtputed into _width_ (defaults to _'100%'_)                |
| label                 | String      | label of the field                                                        |
| hint                  | String      | hint for the field                                                        |
| error                 | String      | error message                                                             |
| options               | Array       | array of options (used only if _ajax_ is not set)                         |
| ajax                  | String      | API endpoint to fetch options                                             |
| minimum-search-length | Number      | minimum length of the search term to be considered when filtering results |

# Multiple autocomplete
```vue
<NevsMultipleAutocomplete v-model="selectedValue"></NevsMultipleAutocomplete>
```
| Prop                  | Type    | Description                                                               |
|-----------------------|---------|---------------------------------------------------------------------------|
| v-model               | String  | value                                                                     |
| width                 | String  | CSS directly outtputed into _width_ (defaults to _'100%'_)                |
| label                 | String  | label of the field                                                        |
| hint                  | String  | hint for the field                                                        |
| error                 | String  | error message                                                             |
| options               | Array   | array of options (used only if _ajax_ is not set)                         |
| ajax                  | String  | API endpoint to fetch options                                             |
| minimum-search-length | Number  | minimum length of the search term to be considered when filtering results |

# Date field
```html
<NevsDateField v-model="selectedDate"></NevsDateField>
```
| Prop      | Type     | Description                                                                                                     |
|-----------|----------|-----------------------------------------------------------------------------------------------------------------|
| v-model   | String   | value in YYYY-MM-DD format                                                                                      |
| readonly  | Boolean  | set to _true_ if you want this field to be readonly                                                             |
| width     | String   | CSS directly outtputed into _width_ (defaults to _'100%'_)                                                      |
| label     | String   | label of the field                                                                                              |
| hint      | String   | hint for the field                                                                                              |
| error     | String   | error message                                                                                                   |
| format    | String   | date format to be used for display and input (moment.js formatting is accepted, default value is _'D.M.YYYY.'_) |
| picker    | Boolean  | if the picker should be shown or not (default value is _true_)                                                  |

# Number field

```html
<NevsNumberField v-model="value"></NevsNumberField>
```
| Prop               | Type             | Description                                                        |
|--------------------|------------------|--------------------------------------------------------------------|
| v-model            | Number or String | value with '.' as decimal separator and without thousand separator |
| readonly           | Boolean          | set to _true_ if you want this field to be readonly                |
| width              | String           | CSS directly outputted into _width_ (defaults to _'100%'_)         |
| label              | String           | label of the field                                                 |
| hint               | String           | hint for the field                                                 |
| error              | String           | error message                                                      |
| thousand-separator | String           | character used to separate thousands (default value is _'.'_)      |
| decimal-separator  | String           | character used to separate decimals (default value is _','_)       |
| decimal-places     | Number           | number of decimal places (default value is 2)                      |

# Checkbox
```html
<NevsCheckbox v-model="checkboxValue"></NevsCheckbox>
```
| Prop      | Type      | Description                                                                         |
|-----------|-----------|-------------------------------------------------------------------------------------|
| v-model   | Boolean   | value                                                                               |
| readonly  | Boolean   | set to _true_ if you want this field to be readonly                                 |
| size      | String    | CSS directly outputted into _font-size_ of the Font Awesome icon (default _'25px'_) |
| width     | String    | CSS directly outputted into _width_ (defaults to _'100%'_)                          |
| label     | String    | label of the field                                                                  |
| hint      | String    | hint for the field                                                                  |
| error     | String    | error message                                                                       |

# File upload
```html
<NevsUpload :accept="'.jpg'" v-model="uploadValue"></NevsUpload>
```
| Prop    | Type       | Description                                                                   |
|---------|------------|-------------------------------------------------------------------------------|
| v-model | Object     | value                                                                         |
| accept  | String     | This is directly outputted into _accept_ attribute of the HTML file input tag |
| width   | String     | CSS directly outtputed into _width_ (defaults to _'100%'_)                    |
| label   | String     | label of the field                                                            |
| hint    | String     | hint for the field                                                            |
| error   | String     | error message                                                                 |
Here is a JSON example of the value (object):
```json
{
    "id": 1,
    "name": "",
    "link": ""
}
```
| Property | Type   | Description                                                                   |
|----------|--------|-------------------------------------------------------------------------------|
| id       | Number | ID of the file in the _uploads_ table.                                        |
| name     | String | Name of the file.                                                             |
| link     | String | Link to be used to access the file.                                           |

# Actions dropdown
```html
<NevsActions :width="'200px'" :actions="actions" :context="context"></NevsActions>
```
| Prop    | Type   | Description                                                                        |
|---------|--------|------------------------------------------------------------------------------------|
| actions | Array  | array of objects each containing the label, the action and the visibility function |
| context | Object | this object is passed to action                                                    |
| width   | String | CSS directly outtputed into _width_ (defaults to _'100%'_)                         |
Here is an example of the actions element:
```js
{
    label: 'Do something',
    action: (context) => {
        // do something based on context
    },
    visible: (context) => {
        return true; // true or false based on context
    }
}
```

# API access
To access the API you should use $API global property.\
Here is an example of how to use it from any Vue component:
```javascript
this.$API.APICall(type, endpoint, payload, (data, success) => {
    if (success) {
        // do something
    }
});
```
| Parameter | Type         | Description                                                             |
|-----------|--------------|-------------------------------------------------------------------------|
| type      | String       | request type (_'get'_, _'post'_, _'put'_, _'delete'_...)                |
| endpoint  | String       | base url is in _src/config.json_                                        |
| payload   | Object/array | Object or array to be converted to JSON and sent as the request payload |
| data      | Object/array | response of the request parsed from JSON                                |
| success   | Boolean      | _true_ if the request was successful, _false_ if it was not             |

# Local event bus
There is an event bus that can be used to trigger global events between components in the current browser tab.\
To listen to an event do something like this in any component:
```javascript
this.$LOCAL_BUS.ListenToEvent('event-name', (data) => {
  //do something
});
```
To trigger an event do something like this in any component:
```javascript
this.$LOCAL_BUS.TriggerEvent('event-name', data);
```

# Cross tab event bus
There is an event bus that can be used to trigger global events between components across all opened tabs using this application.\
To listen to an event do something like this in any component:
```javascript
this.$CROSS_TAB_BUS.ListenToEvent('event-name', (data) => {
  //do something
});
```
To trigger an event do something like this in any component:
```javascript
this.$CROSS_TAB_BUS.TriggerEvent('event-name', data);
```

# Buttons
```html
<NevsButton @click="doSomething">button label</NevsButton>
```
A button can be styled using these classes:
 - primary
 - secondary
 - warning
 - error
 - success

# Notifications
To display a simple notification do something like this in any component:
```javascript
this.$LOCAL_BUS.TriggerEvent('notification', { text: 'Some notification text', duration: 2000 });
```
Duration defines for how many milliseconds will the notification last. It is optional and default value is _3000_.

# Popups
To display a popup do something like this in any component:
```javascript
this.$LOCAL_BUS.TriggerEvent('popup', { type: 'alert', text: 'Alert text!' });

this.$LOCAL_BUS.TriggerEvent('popup', { type: 'confirm', text: 'Are you sure?', callback: (response) => {
  //do something (response is true or false)
}});

this.$LOCAL_BUS.TriggerEvent('popup', { type: 'input', text: 'Input something:', default: 'default value', callback: (response) => {
  //do something (response is a string)
}});
```

# Data table
```html
<NevsTable 
  :width="'500px'"
  :fields="fields"
  :total-records="totalRecords"
  :default-sort="sort"
  @reload="reload">
    <tr v-for="(item, key) in items" :key="key">
      <td>{{ item.field1 }}</td>
      <td>{{ item.field2 }}</td>
      <td>{{ item.field3 }}</td>
      <td>{{ item.field4 }}</td>
      <td>{{ item.field5 }}</td>
    </tr>
</NevsTable>
```

| Prop          | Type   | Description                                                                                          |
|---------------|--------|------------------------------------------------------------------------------------------------------|
| width         | String | outputted directly into CSS _width_ property                                                         |
| height        | String | outputted directly into CSS _height_ property                                                        |
| fields        | Array  | array of objects holding information about each table field                                          |
| total-records | Number | total number of filtered records across all pages                                                    |
| default-sort  | Object | object that describes information about default sorting _{ "field": "field1", "descending": false }_ |

Event _reload_ takes one parameter with information about current filters, sorting and pagination. It should refresh table rows (_items_ array in the example) to display the desired page.

Here is a JSON example of an object that goes into _fields_ array:
```json
{
  "name": "field1",
  "label": "Field 1",
  "width": "150px",
  "sortable": false
}
```
| Property | Description                                                                                                                    |
|----------|--------------------------------------------------------------------------------------------------------------------------------|
| width    | This is optional and it defaults to _"auto"_ (this is outputted directly into css _width_ property of _\<td\>_ in the header). |
| sortable | This is optional and it defaults to _true_ (if _false_ the user will not be able to sort using this column).                   |


# Table filters
A collapsible panel meant to sit directly above a [_NevsTable_](#data-table). Collapsed it is a single
header row listing the currently applied filters; expanded it reveals the filter fields themselves.
```html
<NevsTableFilters :summary="filterSummary" @clear="clearFilter">
    <NevsTextField :label="'Name'" v-model="filters.name"></NevsTextField>
    <NevsSelect :label="'Status'" :options="statuses" :nullable="true"
                v-model="filters.status" @select="filterStatusLabel = $event.label"></NevsSelect>
</NevsTableFilters>
<NevsTable :fields="fields" :total-records="totalRecords" @reload="reload">
    <!-- rows -->
</NevsTable>
```

| Prop             | Type    | Description                                                                        |
|------------------|---------|------------------------------------------------------------------------------------|
| summary          | Array   | human readable description of the applied filters (see below)                      |
| title            | String  | panel title, defaults to the _labels.filters_ translation                          |
| default-expanded | Boolean | if _true_ the panel starts expanded (default _false_)                              |

Filter fields go into the default slot. They are laid out in columns of _$table-filters-field-width_
and fall back to full width below the responsive breakpoint.

Here is a JSON example of a _summary_ entry:
```json
{
  "name": "status",
  "label": "Status",
  "value": "Active"
}
```
| Property | Description                                                                                            |
|----------|---------------------------------------------------------------------------------------------------------|
| label    | name of the filter to display                                                                          |
| value    | applied value to display, entries with an empty value are dropped                                      |
| name     | optional identifier, entries that have it get a reset icon that emits _clear_                          |

Because entries with an empty value are dropped, the whole list of filters can be handed over and the
component will pick the active ones. Event _clear_ is emitted with the whole entry when its reset icon
is clicked; the component does not own the filter values, so resetting one is up to the parent.

Note that _NevsSelect_ emits a _select_ event carrying the whole chosen option, which is how a filter's
label (rather than its value) can be put into the summary.

# Simple grid
A lightweight, non-paginated table for rendering an array of records. Cell content and a per-row actions
column can be customised with slots.
```html
<NevsSimpleGrid :fields="fields" :records="records" :empty-text="'No records.'" @row-click="onRowClick">
    <template #cell-score="{ record }">{{ record.score }} pts</template>
    <template #actions="{ record }">
        <i class="fa-solid fa-pen-to-square" @click.stop="edit(record)"></i>
    </template>
</NevsSimpleGrid>
```
| Prop         | Type     | Description                                                                              |
|--------------|----------|------------------------------------------------------------------------------------------|
| fields       | Array    | column definitions (see below)                                                           |
| records      | Array    | array of record objects                                                                  |
| empty-text   | String   | text shown when _records_ is empty                                                       |
| clickable    | Boolean  | if rows are clickable (default _true_)                                                   |
| row-class    | Function | optional _(record) => string_ returning a CSS class for the row                          |
| fixed-layout | Boolean  | if _true_ uses a fixed table layout so per-field _width_ values are honoured exactly     |

Event _row-click_ is emitted with the clicked record.

Each element of _fields_ is in this form:
```json
{
  "name": "score",
  "label": "Score",
  "align": "R",
  "width": "120px"
}
```
| Property | Description                                                                    |
|----------|--------------------------------------------------------------------------------|
| name     | key of the property in the record (also the name of the _cell-<name>_ slot)    |
| label    | column header text                                                             |
| align    | optional, _"R"_ right-aligns the column (defaults to left)                     |
| width    | optional CSS width for the column                                              |

Slots: _cell-<name>_ (scoped, provides _record_) overrides how a cell is rendered; _actions_ (scoped, provides _record_) renders a trailing actions column.

# Tiny grid
A compact, capped-width grid for small lists (e.g. line items) with an optional row action column.
```html
<NevsTinyGrid :columns="columns" :records="records" :actions="actions" @action="onAction"></NevsTinyGrid>
```
| Prop        | Type     | Description                                                                     |
|-------------|----------|---------------------------------------------------------------------------------|
| columns     | Array    | column definitions (see below), required                                        |
| records     | Array    | array of record objects, required                                               |
| actions     | Array    | optional row action icons (see below)                                           |
| empty-text  | String   | text for the single full-width row shown when there are no records              |
| is-inactive | Function | optional _(record) => boolean_ marking a row as inactive (dimmed + italic)      |
| max-width   | String   | optional override of the grid's default max width (e.g. _'900px'_)              |

Event _action_ is emitted with _(name, record)_ when a row action icon is clicked.

Each element of _columns_ is in this form:
```json
{
  "key": "price",
  "label": "Price",
  "align": "right",
  "width": "120px"
}
```
Each element of _actions_ is in this form:
```json
{
  "name": "delete",
  "icon": "fa-solid fa-trash",
  "tooltip": "Delete",
  "show": true
}
```
_show_ is optional and may be a boolean or a _(record) => boolean_ predicate; omitted means always visible. Slot _cell-<key>_ (scoped, provides _record_ and _index_) overrides cell rendering.

# Tree view
Renders a hierarchy of records as a collapsible tree, with optional per-node actions and a badge.
```html
<NevsTreeView :items="categories" :selected="selectedCategoryId" :actions="actions"
              :tag="(node) => node.count" @select="selectCategory" @action="categoryAction"></NevsTreeView>
```

| Prop         | Type            | Description                                                                     |
|--------------|-----------------|----------------------------------------------------------------------------------|
| items        | Array           | flat array of records, the tree is built from _parent-field_                    |
| tree         | Array           | already nested records (each with a _children_ array), used instead of _items_  |
| id-field     | String          | name of the identifier property (default _"id"_)                               |
| label-field  | String          | name of the property to display (default _"name"_)                             |
| parent-field | String          | name of the parent identifier property (default _"parent_id"_)                 |
| selected     | Number / String | identifier of the highlighted node                                             |
| actions      | Array           | per-node action buttons, shown while the row is hovered                        |
| tag          | Function        | optional _(node) => string_ returning a small badge shown next to the label     |

Pass either _items_ or _tree_. With _items_ the component builds the hierarchy itself, treating a
_parent-field_ that is _null_, _0_, empty or unknown as a root.

Event _select_ is emitted with the clicked node. Event _action_ is emitted with two parameters, the
action's _name_ and the node it was triggered on.

Here is a JSON example of an action:
```json
{
  "name": "delete",
  "icon": "fa-solid fa-trash",
  "tooltip": "Delete",
  "show": true
}
```
_show_ is optional and may be a boolean or a _(node) => boolean_ predicate; omitted means always visible.

# Chart
A dependency-free bar chart rendered as inline SVG.
```html
<NevsChart :labels="labels" :values="values" :title="'Monthly Revenue'" :suffix="' €'"></NevsChart>
```
| Prop       | Type   | Description                                                        |
|------------|--------|--------------------------------------------------------------------|
| labels     | Array  | array of category labels (X axis)                                  |
| values     | Array  | array of numeric values, aligned with _labels_                    |
| title      | String | optional chart title                                              |
| suffix     | String | optional string appended to value labels (e.g. _' €'_)           |
| height     | Number | chart height in pixels (default _280_)                            |
| empty-text | String | text shown when _labels_ is empty                                 |

# Photo upload
A square avatar-style uploader that previews the selected image and supports replacing/removing it.
```html
<NevsPhotoUpload v-model="photo"></NevsPhotoUpload>
```
| Prop    | Type   | Description                                                     |
|---------|--------|-----------------------------------------------------------------|
| v-model | Object | value (same _{ id, name, link }_ shape as the file upload)     |
| size    | String | CSS size of the square (default _'120px'_)                     |
| accept  | String | outputted into _accept_ of the file input (default _'image/*'_) |

# Multi upload
A drag-and-drop dropzone for uploading several files at once, with client-side size and type validation.
```html
<NevsMultiUpload :max-size-mb="10" @uploaded="onUploaded"></NevsMultiUpload>
```
| Prop        | Type   | Description                                                          |
|-------------|--------|----------------------------------------------------------------------|
| extensions  | Array  | allowed file extensions (defaults to a common document/image set)   |
| max-size-mb | Number | maximum size per file in MB (default _25_)                          |

Event _uploaded_ is emitted with an array of the uploaded file objects once the upload completes.

# Tabs
A tab strip (_NevsTabs_) paired with one or more tab panes (_NevsTab_). The strip and every pane are bound to the same active value.
```html
<NevsTabs v-model="activeTab" :tabs="tabs"></NevsTabs>
<NevsTab :model-value="activeTab" value="overview">Overview content</NevsTab>
<NevsTab :model-value="activeTab" value="details">Details content</NevsTab>
```
_NevsTabs_ props:
| Prop    | Type              | Description                                                        |
|---------|-------------------|--------------------------------------------------------------------|
| v-model | String or Integer | the value of the currently active tab                             |
| tabs    | Array             | array of _{ value, label, visible? }_; a tab is shown unless _visible_ is explicitly _false_ |

_NevsTab_ props:
| Prop        | Type              | Description                                                    |
|-------------|-------------------|----------------------------------------------------------------|
| value       | String or Integer | this pane's identifier                                        |
| model-value | String or Integer | the active tab value; the pane's slot is shown while it equals _value_ |

# Calendar
A month/week/day calendar that renders events and asks the parent to load data for the visible range.
```html
<NevsCalendar :events="events" @range-change="onRangeChange" @event-click="onEventClick"></NevsCalendar>
```
| Prop   | Type  | Description               |
|--------|-------|---------------------------|
| events | Array | array of event objects (see below) |

Events are in this form:
```json
{
  "name": "Team standup",
  "start": "2026-07-15 09:30",
  "end": "2026-07-15 10:00",
  "color": "#4d4dff",
  "html": ""
}
```
| Property | Description                                                              |
|----------|--------------------------------------------------------------------------|
| name     | event title                                                              |
| start    | start datetime in _"YYYY-MM-DD HH:mm(:ss)"_ format                       |
| end      | end datetime in _"YYYY-MM-DD HH:mm(:ss)"_ format                         |
| color    | optional event colour                                                    |
| html     | optional custom HTML for the event tooltip                               |

Event _range-change_ is emitted (on mount, navigation and view switch) so the parent can fetch events for the newly visible range. Event _event-click_ is emitted with the original event object when an event is clicked.

# WYSIWYG editor
A rich text editor backed by [Quill](https://quilljs.com/). The value is the editor's HTML.
```html
<NevsWysiwyg v-model="html" :label="'Description'"></NevsWysiwyg>
```
| Prop     | Type    | Description                                          |
|----------|---------|------------------------------------------------------|
| v-model  | String  | value as an HTML string                             |
| label    | String  | label of the field                                  |
| error    | String  | error message                                        |
| readonly | Boolean | set to _true_ to make the editor read only          |

# Translations
Default locale is set in _config.json_ for frontend and _config.php_ for backend.\
Language files are located in _src/languages_ for frontend and _api/Languages_ for backend.\
Each user has _locale_ stored in the database.\
Here is an example of a language file:
```json
{
  "buttons": {
    "ok": "OK",
    "cancel": "Cancel",
    "yes": "Yes",
    "no": "No"
  }
}
```
To get translated values on frontend use something like this in any component:
```javascript
this.$LANG.Get('buttons.ok')
```
To get translated values on backend use something like this in any PHP class:
```php
Language::Get('buttons.ok')
```

# Card
```html
<NevsCard>
    <!-- some content -->
</NevsCard>
```
_NevsCard_ can be used to frame fields and other content when creating forms.

# Menu

```html
<NevsMainMenu @toggleMenu="toggleMenu" v-show="showMenu" :items="items"
              :logo="logo" :collapse="true"></NevsMainMenu>
```
_NevsMainMenu_ can be used in combination with [_NevsTopBar_](#top-bar) to create a responsive side menu.

| Prop          | Type    | Description                                                  |
|---------------|---------|--------------------------------------------------------------|
| v-show        | Boolean | should be bound to a boolean that hides and shows the menu   |
| items         | Array   | contains an array of menu items                              |
| logo          | String  | contains a link to an image that is displayed on the top     |
| collapse      | Boolean | shrinks the menu to an icon only rail (default _false_)      |

Event _toggleMenu_ should toggle the boolean(_v-show_).

With _collapse_ enabled the menu sits at _$menu-collapsed-width_ showing only the item icons, and expands
back to _$menu-width_ over the content while the mouse is over it. Sub-items are hidden while it is
collapsed, so the group that owns the selected sub-item carries the highlight instead. The prop only has
an effect above the responsive breakpoint, below it the menu is toggled by the top bar as usual.

Here is a JSON example of an item:
```json
 {
  "id": "home",
  "label": "Home",
  "link": "/",
  "icon": "<i class=\"fa-solid fa-house\"></i>",
  "children": []
}
```
| Property | Description                                             |
|----------|---------------------------------------------------------|
| id       | unique name of that item within the menu                |
| external | true if you just want to open this link in a new window |
| label    | text to display                                         |
| link     | link to open on click                                   |
| icon     | icon to display                                         |
| children | array of child items (only 2 levels are allowed)        |
If item's _id_ is _'---'_ it will be shown as a separator (only works in first level of items).

# Top bar
```html
 <NevsTopBar @toggleMenu="toggleMenu" :breadcrumbs="breadcrumbs" :buttons="buttons"></NevsTopBar>
```
_NevsTopBar_ can be used in combination with [_NevsMainMenu_](#menu) to create a responsive side menu and a top bar with breadcrumbs and buttons.

| Prop        | Type   | Description                                              |
|-------------|--------|----------------------------------------------------------|
| breadcrumbs | Array  | contains breadcrumbs that need to be displayed           |
| buttons     | Array  | contains buttons that need to be displayed               |

Event _toggleMenu_ should toggle the boolean(_v-show_ of _NevsMainMenu_).

Here is a JSON example of a breadcrumb:
```json
 {
  "label": "Users",
  "link": "/users"
}
```
| Property | Description                                                                        |
|----------|------------------------------------------------------------------------------------|
| label    | text to display                                                                    |
| link     | link to open on click, if this is _null_ than the breadcrumb will not be clickable |

Here is an example of a button:
```js
let button = {
    icon: '<i class="fa-solid fa-right-from-bracket"></i>',
    tooltip: 'logout',
    action: () => {
        //do something
    }
}

```
| Property | Description              |
|----------|--------------------------|
| icon     | icon to display          |
| tooltip  | tooltip to display       |
| action   | function to run on click |
