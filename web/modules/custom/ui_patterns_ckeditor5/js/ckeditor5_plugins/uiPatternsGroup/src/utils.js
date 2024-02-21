export function normalizeConfig(patternDefinitions = []) {
  const normalizedDefinitions = [];

  for (const definition of patternDefinitions) {
    definition.options.forEach(pattern_option => {
      const originalPatternOptionName = pattern_option.name;

      pattern_option.name = `${definition.id}:${originalPatternOptionName}`;
      pattern_option.template =`${definition.template}:${originalPatternOptionName}`
      normalizedDefinitions.push({...pattern_option});
    });
  }
  return normalizedDefinitions;
}
