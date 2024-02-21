import UiPatternsGroupEditing from './uipatternsgroupediting';
import UiPatternsGroupUI from './uipatternsgroupui';
import {Plugin} from 'ckeditor5/src/core';

export default class UiPatternsGroup extends Plugin {

  /**
   * @inheritDoc
   */
  static get pluginName() {
    return 'UiPatternsGroup';
  }

  static get requires() {
    return [UiPatternsGroupEditing, UiPatternsGroupUI];
  }
}
