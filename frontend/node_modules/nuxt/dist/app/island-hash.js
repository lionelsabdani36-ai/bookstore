import { hash } from "ohash";
export function filterIslandProps(props) {
  if (!props) {
    return {};
  }
  const out = {};
  for (const key in props) {
    if (!key.startsWith("data-v-")) {
      out[key] = props[key];
    }
  }
  return out;
}
export function serializeIslandProps(props) {
  return JSON.stringify(filterIslandProps(props));
}
export function computeIslandHash(name, serializedProps, context, source) {
  let parsed;
  try {
    parsed = JSON.parse(serializedProps);
  } catch {
    parsed = serializedProps;
  }
  return hash([name, parsed, context, source]).replace(/[-_]/g, "");
}
