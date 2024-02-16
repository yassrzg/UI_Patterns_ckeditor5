// cSpell:ignore schemadefinitions selectables

import {Command} from 'ckeditor5/src/core';
import {first} from 'ckeditor5/src/utils';


export default class UiPatternsGroupCommand extends Command {

  constructor(editor, patternDefinitions) {
    super(editor);

    /**
     * Set of currently applied styles on the current selection.
     *
     * @readonly
     * @observable
     * @member {Array.<String>} #value
     */
    this.set('value', []);

    /**
     * Names of enabled styles (styles that can be applied to the current selection).
     *
     * @readonly
     * @observable
     * @member {Array.<String>} #enabledPatterns
     */
    this.set('enabledPatterns', []);

    this._patternDefinitions = patternDefinitions;
    patternDefinitions = editor.config.get('UiPatternsGroup.options');
    console.log(patternDefinitions, 'patternDefinitions');
  }

  /**
   * @inheritDoc
   */
  refresh() {
    const model = this.editor.model;
    const selection = model.document.selection;
    const htmlSupport = this.editor.plugins.get('GeneralHtmlSupport');
    const dataSchema = this.editor.plugins.get('DataSchema');

    const value = new Set();
    // const enabledStyles = new Set();
    const enabledPatterns = new Set();
    // Block styles.
    const firstBlock = first(selection.getSelectedBlocks());

    if (firstBlock) {
      const ancestorBlocks = firstBlock.getAncestors({includeSelf: true, parentFirst: true});

      for (const block of ancestorBlocks) {
        // E.g. reached a model table when the selection is in a cell.
        // The command should not modify ancestors of a table.
        if (model.schema.isLimit(block)) {
          break;
        }

        // Get element from block name.
        const schemaDefinitions = dataSchema.getDefinitionsForModel(block.name);
        const schemaDefinition = schemaDefinitions.find(schemaDefinition => (schemaDefinition.model == block.name) && (schemaDefinition.isBlock == true));

        if (schemaDefinition === undefined) {
          continue;
        }

        const attributeName = htmlSupport.getGhsAttributeNameForElement(schemaDefinition.view);

        if (!model.schema.checkAttribute(block, attributeName)) {
          continue;
        }

        for (const definition of this._patternDefinitions) {
          // Compared to CKE5 style plugin, here styles are always active.
          enabledPatterns.add(definition.name);

          // Check if this block pattern is active.
          const ghsAttributeValue = block.getAttribute(attributeName);
          // const patternAttributeValue = block.getAttribute(attributeName);

          if (hasAllClasses(ghsAttributeValue, definition.classes)) {
            value.add(definition.name);
          }
        }
      }
    }

    this.enabledPatterns = Array.from(enabledPatterns).sort();
    this.isEnabled = this.enabledPatterns.length > 0;
    this.value = this.isEnabled ? Array.from(value).sort() : [];
  }

  execute({patternName}) {
    console.log(patternName, 'patternName');
    const model = this.editor.model;
    const selection = model.document.selection;
    const htmlSupport = this.editor.plugins.get('GeneralHtmlSupport');
    const dataSchema = this.editor.plugins.get('DataSchema');
    console.log(dataSchema, 'dataSchema');

    const definition = this._patternDefinitions.find(({ name }) => name.startsWith(patternName));
    console.log(definition, 'definition');
    const shouldAddPattern = !this.value.includes(definition.name);

    if (!definition) {
      console.error(`Pattern definition not found for ${patternName}`);
      return;
    }

    // const shouldAddPattern = !this.value.includes(definition.id);

    model.change(() => {
      const selectables = getAffectedBlocks(selection.getSelectedBlocks(), model.schema);

      for (const selectable of selectables) {
        const schemaDefinitions = dataSchema.getDefinitionsForModel(selectable.name);
        const schemaDefinition = schemaDefinitions.find(
          (schemaDefinition) => schemaDefinition.model === selectable.name && schemaDefinition.isBlock === true
        );

        if (!schemaDefinition) {
          continue;
        }

        if (shouldAddPattern) {
          // Check if definition.fileName is defined
          // if (!definition.fileName) {
          //   console.error('definition.fileName is undefined');
          //   return;
          // }

          fetch('/api/ui-patterns')
            .then((response) => {
              if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
              }
              return response.json();
            })
            .then((data) => {
              console.log(data, 'data');
              // Assuming data is an array of pattern objects with a 'path' property
              const patternPath = data.find((path) => path.endsWith(patternName));
              // const pattern = data.find((pattern) => pattern.path && pattern.path.includes(patternName));
              // console.log(patternPath, 'pattern');

              if (!patternPath) {
                console.log('Pattern not found');
                throw new Error(`Pattern ${patternName} not found`);
              }

              // Now you have the specific pattern, you can fetch the Twig template
              fetch(`/api/ui-patterns/${patternName}/content`)
                .then((response) => {
                  if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                  }
                  return response.json();
                })
                .then((data) => {
                  // The content of the file is now in the "data.content" variable
                  console.log('Contenu brut du modèle Twig :', data.content);

                  let content = data.content;
                  // Remplacez les balises <button> par des balises <span>
                  content = content.replace(/<button\b([^>]*)>/g, '<span$1>');
                  content = content.replace(/<\/button>/g, '</span>');
                  // const match = content.match(/attributes\.add(class|Class)\(['"]([^'"]+)['"]\)/i);

                  // const parser = new DOMParser();
                  // const doc = parser.parseFromString(content, 'text/html');
                  // console.log(doc, 'doc');
                  // // Récupération de toutes les balises dans l'objet DOM
                  // const allElements = doc.querySelectorAll('*');
                  //
                  // // Parcours de chaque balise et récupération des informations
                  // allElements.forEach((element) => {
                  //   const tagName = element.tagName.toLowerCase(); // Nom de la balise en minuscules
                  //   const attributes = Array.from(element.attributes).map((attr) => ({
                  //     name: attr.name,
                  //     value: attr.value,
                  //   }));
                  //   const content = element.innerHTML;
                  //
                  //   // Vous pouvez maintenant utiliser ces informations comme nécessaire
                  //   console.log('Balise:', tagName);
                  //   console.log('Attributs:', attributes);
                  //   console.log('Contenu:', content);
                  // });
                  // console.log(content,'content2');
                  // const twigData = {
                  //   expanded: true,
                  //   title: 'Mon Titre',
                  //   content: 'Mon contenu',
                  // }
                  // const Twig = require('twig');
                  // const renderedHtml = Twig.twig({ data: content }).render(twigData);

                  const editorInstance = this.editor;

                  // console.log('Contenu rendu par Twig :', renderedHtml);
                  editorInstance.setData(content, true);
                  console.log(editorInstance.setData(content, true), 'editorInstance.setData(content, true)');



                  // Insert the content into CKEditor5
                  // const editorInstance = ...; // Get your CKEditor5 instance here
                  // editorInstance.setData(data.content);
                })
                .catch((error) => console.error(error));
            })
            .catch((error) => console.error(error));

          htmlSupport.removeModelHtmlClass(schemaDefinition.view, definition.excluded_classes, selectable);
          htmlSupport.addModelHtmlClass(schemaDefinition.view, definition.classes, selectable);
        } else {
          htmlSupport.removeModelHtmlClass(schemaDefinition.view, definition.classes, selectable);
        }
      }
    });
  }
}
function getPatternPathByPatternName(patternName) {
  // Exemple d'implémentation basée sur les chemins fournis
  const patternPaths = [
    "/var/www/html/d10_ckeditor5/web/themes/contrib/ui_suite_dsfr/templates/patterns/accordion/pattern-accordion",
    // Ajoutez d'autres chemins selon votre besoin
  ];

  const patternPath = patternPaths.find((path) => path.includes(patternName));
  return patternPath;
}
// Verifies if all classes are present in the given GHS attribute.
function hasAllClasses(ghsAttributeValue, classes) {

  if (!ghsAttributeValue || !ghsAttributeValue.classes) {
    return false;
  }

  return classes.every(className => ghsAttributeValue.classes.includes(className));
}

// Returns a set of elements that should be affected by the block-style change.
function getAffectedBlocks(selectedBlocks, schema) {
  const blocks = new Set();

  for (const selectedBlock of selectedBlocks) {
    const ancestorBlocks = selectedBlock.getAncestors({includeSelf: true, parentFirst: true});

    for (const block of ancestorBlocks) {
      if (schema.isLimit(block)) {
        break;
      }

      blocks.add(block);
    }
  }
  console.log(blocks, 'blocks');
  return blocks;
}
