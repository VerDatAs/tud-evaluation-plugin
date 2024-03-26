# VerDatAsEva Plugin

The evaluation component ILIAS plugin for the assistance system developed as part of the VerDatAs project.

The following requirements should be met:

* ILIAS 8.0 - 8.x
* PHP >= 8.0

## Installation

``` shell
# execute the following commands from your ILIAS root
mkdir -p Customizing/global/plugins/Services/UIComponent/UserInterfaceHook
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook
git clone https://github.com/VerDatAs/tud-evaluation-plugin.git VerDatAsEva
# navigate back to your ILIAS root
cd /var/www/html
composer du
```

Locate to `Administration | Extending ILIAS | Plugins` and install, configure and activate `VerDatAsEva`.

## Configuration

Define the following settings:

* TAS-Backend URL (e.g., `https://tud-tas.example.com`)
* LRS-Type (i.e., an LRS type created in `Administration | Extending ILIAS | LRS`)
* Show lecturer view to all users (i.e., whether students should also be displayed the statistics of the entire course)
* xAPI version (e.g., `1.0.3`)
* LRS Store ID (i.e., the unique ID of the LRS store, in which the statements are written)
  * You can retrieve it using `LearningLocker`:
    1. Navigate to `Data | Source`,
    2. Filter by the according `Store` using the `Explore` menu,
    3. Switch into the text mode using the button with the code icon,
    4. Copy the value of `$oid`.

## Usage

* After successfully configuring and activating the plugin, the evaluation component is ready to use.
* Navigate into a course and click on the `Statistics` tab to open it.
* Depending on the role and the settings defined, either the lecturer or student view is shown.

## Development

* If you use the ILIAS docker setup described [here](https://github.com/VerDatAs/all-ilias), which is located within the
  same folder such as `tud-evaluation-plugin`, you can run `sh local_development.sh` to reload your changes made.

## License

This plugin is licensed under the GPL v3 License (for further information, see [LICENSE](LICENSE)).

## Libraries used

* Guzzle: an extensible PHP HTTP client – MIT license – https://github.com/guzzle/guzzle
* [tud-evaluation](https://github.com/VerDatAs/tud-evaluation): the frontend application of the evaluation component for the assistance system – GPL v3 license
  * Retrieve the code and license information here: [templates/main.js](templates/main.js)
  * The following libraries are used by [tud-evaluation](https://github.com/VerDatAs/tud-evaluation):

|    Name    |   Version  |   License  |     URL    |
| ---------- | ---------- | ---------- | ---------- |
| @babel/parser | 7.22.10 | MIT | https://github.com/babel/babel |
| @babel/runtime | 7.23.2 | MIT | https://github.com/babel/babel |
| @floating-ui/core | 1.5.0 | MIT | https://github.com/floating-ui/floating-ui |
| @floating-ui/dom | 1.5.3 | MIT | https://github.com/floating-ui/floating-ui |
| @floating-ui/utils | 0.1.4 | MIT | https://github.com/floating-ui/floating-ui |
| @floating-ui/vue | 1.0.2 | MIT | https://github.com/floating-ui/floating-ui |
| @fontsource/material-icons-round | 5.0.7 | Apache-2.0 | https://github.com/fontsource/font-files |
| @jridgewell/sourcemap-codec | 1.4.15 | MIT | https://github.com/jridgewell/sourcemap-codec |
| @popperjs/core | 2.11.8 | MIT | https://github.com/popperjs/popper-core |
| @types/web-bluetooth | 0.0.18 | MIT | https://github.com/DefinitelyTyped/DefinitelyTyped |
| @vue/compiler-core | 3.3.4 | MIT | https://github.com/vuejs/core |
| @vue/compiler-dom | 3.3.4 | MIT | https://github.com/vuejs/core |
| @vue/compiler-sfc | 3.3.4 | MIT | https://github.com/vuejs/core |
| @vue/compiler-ssr | 3.3.4 | MIT | https://github.com/vuejs/core |
| @vue/devtools-api | 6.5.0 | MIT | https://github.com/vuejs/vue-devtools |
| @vue/reactivity-transform | 3.3.4 | MIT | https://github.com/vuejs/core |
| @vue/reactivity | 3.3.4 | MIT | https://github.com/vuejs/core |
| @vue/runtime-core | 3.3.4 | MIT | https://github.com/vuejs/core |
| @vue/runtime-dom | 3.3.4 | MIT | https://github.com/vuejs/core |
| @vue/server-renderer | 3.3.4 | MIT | https://github.com/vuejs/core |
| @vue/shared | 3.3.4 | MIT | https://github.com/vuejs/core |
| @vueform/multiselect | 2.6.3 | MIT | https://github.com/vueform/multiselect |
| @vueuse/core | 10.5.0 | MIT | https://github.com/vueuse/vueuse |
| @vueuse/metadata | 10.5.0 | MIT | https://github.com/vueuse/vueuse |
| @vueuse/shared | 10.5.0 | MIT | https://github.com/vueuse/vueuse |
| bootstrap-vue-next | 0.14.5 | MIT | https://github.com/bootstrap-vue-next/bootstrap-vue-next |
| bootstrap | 5.3.2 | MIT | https://github.com/twbs/bootstrap |
| csstype | 3.1.2 | MIT | https://github.com/frenic/csstype |
| date-fns | 2.30.0 | MIT | https://github.com/date-fns/date-fns |
| echarts | 5.4.3 | Apache-2.0 | https://github.com/apache/echarts |
| estree-walker | 2.0.2 | MIT | https://github.com/Rich-Harris/estree-walker |
| events | 3.3.0 | MIT | https://github.com/Gozala/events |
| magic-string | 0.30.3 | MIT | https://github.com/rich-harris/magic-string |
| nanoid | 3.3.6 | MIT | https://github.com/ai/nanoid |
| picocolors | 1.0.0 | ISC | https://github.com/alexeyraspopov/picocolors |
| pinia | 2.1.6 | MIT | https://github.com/vuejs/pinia |
| postcss | 8.4.28 | MIT | https://github.com/postcss/postcss |
| regenerator-runtime | 0.14.0 | MIT | https://github.com/facebook/regenerator/tree/main/packages/runtime |
| resize-detector | 0.3.0 | MIT | https://github.com/Justineo/resize-detector |
| sax | 1.3.0 | ISC | https://github.com/isaacs/sax-js |
| source-map-js | 1.0.2 | BSD-3-Clause | https://github.com/7rulnik/source-map-js |
| tslib | 2.3.0 | 0BSD | https://github.com/Microsoft/tslib |
| typescript | 5.1.6 | Apache-2.0 | https://github.com/Microsoft/TypeScript |
| vue-demi | 0.13.11 | MIT | https://github.com/antfu/vue-demi |
| vue-demi | 0.14.5 | MIT | https://github.com/antfu/vue-demi |
| vue-demi | 0.14.6 | MIT | https://github.com/antfu/vue-demi |
| vue-echarts | 6.6.1 | MIT | https://github.com/ecomfe/vue-echarts |
| vue-router | 4.2.4 | MIT | https://github.com/vuejs/router |
| vue3-toastify | 0.1.13 | MIT | https://github.com/jerrywu001/vue3-toastify |
| vue | 3.3.4 | MIT | https://github.com/vuejs/core |
| xml2js | 0.6.2 | MIT | https://github.com/Leonidas-from-XIV/node-xml2js |
| xmlbuilder | 11.0.1 | MIT | https://github.com/oozcitak/xmlbuilder-js |
| zrender | 5.4.4 | BSD-3-Clause | https://github.com/ecomfe/zrender |
