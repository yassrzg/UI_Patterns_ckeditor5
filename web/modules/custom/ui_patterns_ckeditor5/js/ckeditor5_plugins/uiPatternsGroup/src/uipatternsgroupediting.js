// cSpell:ignore uipatternsgroupcommand

import {Plugin} from "ckeditor5/src/core";
import UiPatternsGroupCommand from "./uipatternsgroupcommand";
import {normalizeConfig} from './utils';

export default class UiPatternsGroupEditing extends Plugin {
  /**
   * @inheritDoc
   */
  static get pluginName() {
    return 'UiPatternsGroupEditing';
  }

  /**
   * @inheritDoc
   */
  static get requires() {
    return ['GeneralHtmlSupport', 'DataSchema'];
  }

  init() {
    const editor = this.editor;
    // console.log(editor.config.get('UiPatternsGroup.options'));
    const normalizedPatternDefinitions = normalizeConfig(editor.config.get('UiPatternsGroup.options'));
    console.log(normalizedPatternDefinitions, 'normalizedPatternDefinitions');

    editor.commands.add('UiPatternsGroup', new UiPatternsGroupCommand(editor, normalizedPatternDefinitions));

    this._defineSchema();
  }

  /**
   * Allow the remove format plugin to remove the classes.
   */
  _defineSchema() {
    const schema = this.editor.model.schema;
    // console.log(schema);
    const htmlSupport = this.editor.plugins.get('GeneralHtmlSupport');
    const dataSchema = this.editor.plugins.get('DataSchema');

    // Loop on the blocks definitions to get the attribute name and add
    // formatting.
    for (const definition in schema.getDefinitions()) {
      const schemaDefinitions = dataSchema.getDefinitionsForModel(definition);
      const schemaDefinition = schemaDefinitions.find(schemaDefinition => (schemaDefinition.model == definition) && (schemaDefinition.isBlock == true));

      if (schemaDefinition === undefined) {
        continue;
      }

      const attributeName = htmlSupport.getGhsAttributeNameForElement(schemaDefinition.view);
      schema.setAttributeProperties(attributeName, {
        isFormatting: true
      });
    }

    // Even if htmlAttributes should no more exist. Set it in case of plugins
    // not updated.
    schema.setAttributeProperties('htmlAttributes', {
      isFormatting: true
    });
  }
}
