!function(t,e){"object"==typeof exports&&"object"==typeof module?module.exports=e():"function"==typeof define&&define.amd?define([],e):"object"==typeof exports?exports.CKEditor5=e():(t.CKEditor5=t.CKEditor5||{},t.CKEditor5.uiPatternsGroup=e())}(self,(()=>(()=>{var t={"ckeditor5/src/core.js":(t,e,o)=>{t.exports=o("dll-reference CKEditor5.dll")("./src/core.js")},"ckeditor5/src/ui.js":(t,e,o)=>{t.exports=o("dll-reference CKEditor5.dll")("./src/ui.js")},"ckeditor5/src/utils.js":(t,e,o)=>{t.exports=o("dll-reference CKEditor5.dll")("./src/utils.js")},"dll-reference CKEditor5.dll":t=>{"use strict";t.exports=CKEditor5.dll}},e={};function o(n){var s=e[n];if(void 0!==s)return s.exports;var r=e[n]={exports:{}};return t[n](r,r.exports,o),r.exports}o.d=(t,e)=>{for(var n in e)o.o(e,n)&&!o.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:e[n]})},o.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e);var n={};return(()=>{"use strict";o.d(n,{default:()=>d});var t=o("ckeditor5/src/core.js"),e=o("ckeditor5/src/utils.js");class s extends t.Command{constructor(t,e){super(t),this.set("value",[]),this.set("enabledPatterns",[]),this._patternDefinitions=e,e=t.config.get("UiPatternsGroup.options"),console.log(e,"patternDefinitions")}refresh(){const t=this.editor.model,o=t.document.selection,n=this.editor.plugins.get("GeneralHtmlSupport"),s=this.editor.plugins.get("DataSchema"),i=new Set,a=new Set,c=(0,e.first)(o.getSelectedBlocks());if(c){const e=c.getAncestors({includeSelf:!0,parentFirst:!0});for(const o of e){if(t.schema.isLimit(o))break;const e=s.getDefinitionsForModel(o.name).find((t=>t.model==o.name&&1==t.isBlock));if(void 0===e)continue;const c=n.getGhsAttributeNameForElement(e.view);if(t.schema.checkAttribute(o,c))for(const t of this._patternDefinitions){a.add(t.name);r(o.getAttribute(c),t.classes)&&i.add(t.name)}}}this.enabledPatterns=Array.from(a).sort(),this.isEnabled=this.enabledPatterns.length>0,this.value=this.isEnabled?Array.from(i).sort():[]}execute({patternName:t}){console.log(t,"patternName");const e=this.editor.model,o=e.document.selection,n=this.editor.plugins.get("GeneralHtmlSupport"),s=this.editor.plugins.get("DataSchema");console.log(s,"dataSchema");const r=this._patternDefinitions.find((({name:e})=>e.startsWith(t)));console.log(r,"definition");const i=!this.value.includes(r.name);r?e.change((()=>{const a=function(t,e){const o=new Set;for(const n of t){const t=n.getAncestors({includeSelf:!0,parentFirst:!0});for(const n of t){if(e.isLimit(n))break;o.add(n)}}return console.log(o,"blocks"),o}(o.getSelectedBlocks(),e.schema);for(const e of a){const o=s.getDefinitionsForModel(e.name).find((t=>t.model===e.name&&!0===t.isBlock));o&&(i?(fetch("/api/ui-patterns").then((t=>{if(!t.ok)throw new Error(`HTTP error! status: ${t.status}`);return t.json()})).then((e=>{console.log(e,"data");if(!e.find((e=>e.endsWith(t))))throw console.log("Pattern not found"),new Error(`Pattern ${t} not found`);fetch(`/api/ui-patterns/${t}/content`).then((t=>{if(!t.ok)throw new Error(`HTTP error! status: ${t.status}`);return t.json()})).then((t=>{console.log(t.content,"hello end")})).catch((t=>console.error(t)))})).catch((t=>console.error(t))),n.removeModelHtmlClass(o.view,r.excluded_classes,e),n.addModelHtmlClass(o.view,r.classes,e)):n.removeModelHtmlClass(o.view,r.classes,e))}})):console.error(`Pattern definition not found for ${t}`)}}function r(t,e){return!(!t||!t.classes)&&e.every((e=>t.classes.includes(e)))}class i extends t.Plugin{static get pluginName(){return"UiPatternsGroupEditing"}static get requires(){return["GeneralHtmlSupport","DataSchema"]}init(){const t=this.editor,e=function(t=[]){const e=[];for(const o of t)o.options.forEach((t=>{const n=t.name;t.name=`${o.id}:${n}`,e.push({...t})}));return e}(t.config.get("UiPatternsGroup.options"));t.commands.add("UiPatternsGroup",new s(t,e)),this._defineSchema()}_defineSchema(){const t=this.editor.model.schema,e=this.editor.plugins.get("GeneralHtmlSupport"),o=this.editor.plugins.get("DataSchema");for(const n in t.getDefinitions()){const s=o.getDefinitionsForModel(n).find((t=>t.model==n&&1==t.isBlock));if(void 0===s)continue;const r=e.getGhsAttributeNameForElement(s.view);t.setAttributeProperties(r,{isFormatting:!0})}t.setAttributeProperties("htmlAttributes",{isFormatting:!0})}}var a=o("ckeditor5/src/ui.js");class c extends t.Plugin{static get pluginName(){return"UiPatternsGroupUI"}init(){const t=this.editor,o=t.ui.componentFactory,n=Drupal.t,s=t.config.get("UiPatternsGroup.options");console.log(s,"options");const r=new e.Collection;s.forEach((e=>{const o=e.id,n={type:"button",model:new a.Model({commandName:"UiPatternsGroup",commandParam:o,label:e.label,withText:!0})},s=t.commands.get("UiPatternsGroup");n.model.bind("isOn").to(s,"value",(t=>t===o)),r.add(n)}));const i=(0,a.createDropdown)(t.locale);(0,a.addListToDropdown)(i,r),i.buttonView.set({label:n("Patterns (group)"),withText:!0,tooltip:!0});const c=t.commands.get("UiPatternsGroup");i.bind("class").to(c,"value",(t=>{const e=["ck-ui-patterns-group-dropdown"];return null!==t&&e.push("ck-ui-patterns-group-dropdown-active"),e.join(" ")})),this.listenTo(i,"execute",(e=>{t.execute(e.source.commandName,{patternName:e.source.commandParam}),t.editing.view.focus()})),o.add("UiPatternsGroup",(()=>i))}}class l extends t.Plugin{static get pluginName(){return"UiPatternsGroup"}static get requires(){return[i,c]}}const d={UiPatternsGroup:l}})(),n=n.default})()));