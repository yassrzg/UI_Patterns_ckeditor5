!function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t():"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?exports.CKEditor5=t():(e.CKEditor5=e.CKEditor5||{},e.CKEditor5.uiStylesInline=t())}(self,(()=>(()=>{var e={"ckeditor5/src/core.js":(e,t,s)=>{e.exports=s("dll-reference CKEditor5.dll")("./src/core.js")},"ckeditor5/src/ui.js":(e,t,s)=>{e.exports=s("dll-reference CKEditor5.dll")("./src/ui.js")},"ckeditor5/src/utils.js":(e,t,s)=>{e.exports=s("dll-reference CKEditor5.dll")("./src/utils.js")},"dll-reference CKEditor5.dll":e=>{"use strict";e.exports=CKEditor5.dll}},t={};function s(n){var o=t[n];if(void 0!==o)return o.exports;var i=t[n]={exports:{}};return e[n](i,i.exports,s),i.exports}s.d=(e,t)=>{for(var n in t)s.o(t,n)&&!s.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},s.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t);var n={};return(()=>{"use strict";s.d(n,{default:()=>a});var e=s("ckeditor5/src/core.js");class t extends e.Command{constructor(e,t){super(e),this.set("value",[]),this.set("enabledStyles",[]),this._styleDefinitions=t}refresh(){const e=new Set,t=new Set;for(const s of this._styleDefinitions){t.add(s.name);o(this._getValueFromFirstAllowedNode("htmlSpan"),s.classes)&&e.add(s.name)}this.enabledStyles=Array.from(t).sort(),this.isEnabled=this.enabledStyles.length>0,this.value=this.isEnabled?Array.from(e).sort():[]}execute({styleName:e}){const t=this.editor.model,s=t.document.selection,n=this.editor.plugins.get("GeneralHtmlSupport"),o=this._styleDefinitions.find((({name:t})=>t==e)),i=!this.value.includes(o.name);t.change((()=>{i?(n.removeModelHtmlClass("span",o.excluded_classes,s),n.addModelHtmlClass("span",o.classes,s)):n.removeModelHtmlClass("span",o.classes,s)}))}_getValueFromFirstAllowedNode(e){const t=this.editor.model,s=t.schema,n=t.document.selection;if(n.isCollapsed)return n.getAttribute(e);for(const t of n.getRanges())for(const n of t.getItems())if(s.checkAttribute(n,e))return n.getAttribute(e);return null}}function o(e,t){return!(!e||!e.classes)&&t.every((t=>e.classes.includes(t)))}class i extends e.Plugin{static get pluginName(){return"UiStylesInlineEditing"}static get requires(){return["GeneralHtmlSupport"]}init(){const e=this.editor,s=function(e=[]){const t=[];for(const s of e)s.options.forEach((e=>{const n=e.name;e.name=`${s.id}:${n}`,t.push({...e})}));return t}(e.config.get("uiStylesInline.options"));e.commands.add("uiStylesInline",new t(e,s))}}var r=s("ckeditor5/src/ui.js"),l=s("ckeditor5/src/utils.js");class d extends e.Plugin{static get pluginName(){return"UiStylesInlineUI"}init(){const e=this.editor,t=e.ui.componentFactory,s=Drupal.t,n=e.config.get("uiStylesInline.options");n.forEach((e=>{this._addButton(e)})),t.add("UiStylesInline",(o=>{const i=(0,r.createDropdown)(o),l=e.commands.get("uiStylesInline");i.bind("isEnabled").to(l);const d=[];return n.forEach((e=>{d.push(t.create(`UIStylesInline:${e.id}`))})),(0,r.addToolbarToDropdown)(i,d,{enableActiveItemFocusOnDropdownOpen:!1,isVertical:!0,ariaLabel:s("UI Styles inline toolbar")}),i.buttonView.set({label:s("Styles (inline)"),withText:!0,tooltip:!0}),i.bind("class").to(l,"value",(e=>{const t=["ck-ui-styles-inline-dropdown"];return e.length>0&&t.push("ck-ui-styles-inline-dropdown-active"),t.join(" ")})),this.listenTo(i,"execute",(t=>{e.execute(t.source.commandName,{styleName:t.source.commandParam}),e.editing.view.focus()})),i}))}_addButton(e){const t=this.editor;t.ui.componentFactory.add(`UIStylesInline:${e.id}`,(s=>{const n=new l.Collection,o=t.commands.get("uiStylesInline");e.options.forEach((t=>{const s=`${e.id}:${t.name}`,i={type:"button",model:new r.Model({commandName:"uiStylesInline",commandParam:s,label:t.name,withText:!0})};i.model.bind("isOn").to(o,"value",(e=>!!e.includes(s))),n.add(i)}));const i=(0,r.createDropdown)(s);return(0,r.addListToDropdown)(i,n),i.buttonView.set({label:e.label,withText:!0}),i.bind("class").to(o,"value",(t=>{const s=["ck-ui-styles-inline-dropdown-style-dropdown"];return t.find((t=>t.includes(`${e.id}`)))&&s.push("ck-ui-styles-inline-dropdown-style-dropdown-active"),s.join(" ")})),i}))}}class c extends e.Plugin{static get pluginName(){return"UiStylesInline"}static get requires(){return[i,d]}}const a={UiStylesInline:c}})(),n=n.default})()));