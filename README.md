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
- [Translations](#translations)
- [Card](#card)
- [Menu](#menu)
- [Top bar](#top-bar)

# Intro
This is a web application template. It uses [NevsPHP](https://github.com/srnikolic86/nevsphp-example) for the backend and Vue.js for the frontend.\
It has a log in screen and a user management module.

# Tested with
 - PHP 8.1
 - Node.js 18.4.0
 - NPM 8.12.1
 - MariaDB 10.5.10

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
              :logo="logo"></NevsMainMenu>
```
_NevsMainMenu_ can be used in combination with [_NevsTopBar_](#top-bar) to create a responsive side menu.

| Prop          | Type    | Description                                                  |
|---------------|---------|--------------------------------------------------------------|
| v-show        | Boolean | should be bound to a boolean that hides and shows the menu   |
| items         | Array   | contains an array of menu items                              |
| logo          | String  | contains a link to an image that is displayed on the top     |

Event _toggleMenu_ should toggle the boolean(_v-show_).

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
| Property | Description                                      |
|----------|--------------------------------------------------|
| id       | unique name of that item within the menu         |
| label    | text to display                                  |
| link     | link to open on click                            |
| icon     | icon to display                                  |
| children | array of child items (only 2 levels are allowed) |
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
