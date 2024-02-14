import { Plugin } from 'ckeditor5/src/core';
import { Model, createDropdown, addListToDropdown } from 'ckeditor5/src/ui';
import { Collection } from 'ckeditor5/src/utils';

export default class UiPatternsGroupUI extends Plugin {
  /**
   * @inheritDoc
   */
  static get pluginName() {
    return 'UiPatternsGroupUI';
  }

  /**
   * @inheritDoc
   */
  init() {
    const editor = this.editor;
    const componentFactory = editor.ui.componentFactory;
    const t = Drupal.t;
    const options = editor.config.get('UiPatternsGroup.options');
    console.log(options, 'options');

    // Prepare pattern buttons.
    const patternItemDefinitions = new Collection();
    options.forEach(pattern => {
      const normalizedPatternName = pattern.id;
      const patternDef = {
        type: 'button',
        model: new Model({
          commandName: 'UiPatternsGroup',
          commandParam: normalizedPatternName,
          label: pattern.label, // Use the label property as the label for the dropdown option
          withText: true,
        }),
      };

      // Mark pattern active depending on the command.
      const uiPatternsGroupCommand = editor.commands.get('UiPatternsGroup');
      patternDef.model.bind('isOn').to(uiPatternsGroupCommand, 'value', value => {
        return value === normalizedPatternName;
      });

      patternItemDefinitions.add(patternDef);
    });

    // UI Pattern group plugin dropdown.
    const dropdownView = createDropdown(editor.locale);
    // Add pattern options to the dropdown.
    addListToDropdown(dropdownView, patternItemDefinitions);
    dropdownView.buttonView.set({
      label: t('Patterns (group)'),
      withText: true,
      tooltip: true,
    });

    // As it is (or seems to be) currently not possible to bind the isOn of
    // dropdownView.buttonView to the command, apply a class on dropdownView
    // and add custom styling.
    const uiPatternsGroupCommand = editor.commands.get('UiPatternsGroup');
    dropdownView.bind('class').to(uiPatternsGroupCommand, 'value', value => {
      const classes = [
        'ck-ui-patterns-group-dropdown',
      ];
      if (value !== null) {
        classes.push('ck-ui-patterns-group-dropdown-active');
      }
      return classes.join(' ');
    });

    // Execute command.
    this.listenTo(dropdownView, 'execute', evt => {
      editor.execute(evt.source.commandName, { patternName: evt.source.commandParam });
      editor.editing.view.focus();
    });

    componentFactory.add('UiPatternsGroup', () => dropdownView);
  }
}
