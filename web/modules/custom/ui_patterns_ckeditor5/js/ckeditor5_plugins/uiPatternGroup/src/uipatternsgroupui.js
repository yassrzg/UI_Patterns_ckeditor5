import {Plugin} from 'ckeditor5/src/core';
import {Model, createDropdown, addListToDropdown, addToolbarToDropdown} from 'ckeditor5/src/ui';
import {Collection} from 'ckeditor5/src/utils';

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
    const options = editor.config.get('uiPatternsGroup.options');

    // Prepare pattern buttons.
    options.forEach(pattern => {
      this._addButton(pattern);
    });

    componentFactory.add('UiPatternsGroup', locale => {
      const dropdownView = createDropdown(locale);
      const uiPatternsGroupCommand = editor.commands.get('uiPatternsGroup');

      // The entire dropdown will be disabled together with the command (e.g.
      // when the editor goes read-only).
      dropdownView.bind('isEnabled').to(uiPatternsGroupCommand);

      // Add existing pattern buttons to dropdown's toolbar.
      const buttons = [];
      options.forEach(pattern => {
        buttons.push(componentFactory.create(`UiPatternsGroup:${pattern.id}`));
      });

      addToolbarToDropdown(dropdownView, buttons, {
        enableActiveItemFocusOnDropdownOpen: false,
        isVertical: true,
        ariaLabel: t('UI Patterns group toolbar')
      });

      // Configure dropdown properties and behavior.
      dropdownView.buttonView.set({
        label: t('Patterns (group)'),
        withText: true,
        tooltip: true,
      });

      // As it is (or seems to be) currently not possible to bind the isOn of
      // dropdownView.buttonView to the command, apply a class on dropdownView
      // and add custom styling.
      dropdownView.bind('class').to(uiPatternsGroupCommand, 'value', value => {
        const classes = [
          'ck-ui-patterns-group-dropdown'
        ];
        if (value.length > 0) {
          classes.push('ck-ui-patterns-group-dropdown-active');
        }
        return classes.join(' ');
      });

      // Execute command.
      this.listenTo(dropdownView, 'execute', evt => {
        editor.execute(evt.source.commandName, {patternName: evt.source.commandParam});
        editor.editing.view.focus();
      });

      return dropdownView;
    });
  }

  /**
   * Helper method for initializing the button and linking it with an appropriate command.
   *
   * @private
   * @param {Array} pattern A pattern structure.
   */
  _addButton(pattern) {
    const editor = this.editor;

    editor.ui.componentFactory.add(`UiPatternsGroup:${pattern.id}`, locale => {
      const patternItemDefinitions = new Collection();
      const uiPatternsGroupCommand = editor.commands.get('uiPatternsGroup');

      // Loop on pattern options.
      pattern.options.forEach(pattern_option => {
        const normalizedPatternOptionName = `${pattern.id}:${pattern_option.name}`;
        const patternDef = {
          type: 'button',
          model: new Model({
            commandName: 'uiPatternsGroup',
            commandParam: normalizedPatternOptionName,
            label: pattern_option.name,
            withText: true,
          })
        };

        // Mark pattern option active depending on the command.
        patternDef.model.bind('isOn').to(uiPatternsGroupCommand, 'value', value => {
          return !!value.includes(normalizedPatternOptionName);
        });

        patternItemDefinitions.add(patternDef);
      });

      // UI Pattern group plugin dropdown.
      const dropdownView = createDropdown(locale);
      // Add second level items.
      addListToDropdown(dropdownView, patternItemDefinitions);
      dropdownView.buttonView.set({
        label: pattern.label,
        withText: true,
      });

      // As it is (or seems to be) currently not possible to bind the isOn of
      // dropdownView.buttonView to the command, apply a class on dropdownView
      // and add custom styling.
      dropdownView.bind('class').to(uiPatternsGroupCommand, 'value', value => {
        const classes = [
          'ck-ui-patterns-group-dropdown-pattern-dropdown'
        ];
        if (value.find(name => name.includes(`${pattern.id}`))) {
          classes.push('ck-ui-patterns-group-dropdown-pattern-dropdown-active');
        }
        return classes.join(' ');
      });

      return dropdownView;
    });
  }
}
