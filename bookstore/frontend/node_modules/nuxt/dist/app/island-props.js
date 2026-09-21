export function findUnsafeIslandPropKey(value) {
  const pending = [value];
  const seen = /* @__PURE__ */ new Set();
  while (pending.length) {
    const current = pending.pop();
    if (!current || typeof current !== "object" || seen.has(current)) {
      continue;
    }
    seen.add(current);
    for (const key of Object.keys(current)) {
      if (key === "template") {
        return key;
      }
      pending.push(current[key]);
    }
  }
}
export function findReservedRootIslandPropKey(value, component) {
  if (!value || typeof value !== "object" || Array.isArray(value) || !Object.hasOwn(value, "as")) {
    return;
  }
  const options = component;
  if (options.inheritAttrs === false || declaresProp(options.props, "as")) {
    return;
  }
  return "as";
}
function declaresProp(props, name) {
  if (!props) {
    return false;
  }
  return Array.isArray(props) ? props.includes(name) : Object.hasOwn(props, name);
}
