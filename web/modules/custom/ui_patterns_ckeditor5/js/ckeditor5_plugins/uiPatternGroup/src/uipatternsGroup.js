import {Plugin} from 'ckeditor5/src/core';
import UiPatternsGroupEditing from './uipatternsgroupediting';
import UiPatternsGroupUI from './uipatternsgroupui';

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
