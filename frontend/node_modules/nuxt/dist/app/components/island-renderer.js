import { createVNode, defineComponent, onErrorCaptured } from "vue";
import { createError } from "../composables/error.js";
import { findReservedRootIslandPropKey } from "../island-props.js";
import { islandComponents } from "#build/components.islands.mjs";
export default defineComponent({
  name: "IslandRenderer",
  props: {
    context: {
      type: Object,
      required: true
    }
  },
  async setup(props) {
    const name = props.context.name;
    const component = Object.hasOwn(islandComponents, name) ? islandComponents[name] : void 0;
    if (!component) {
      throw createError({
        status: 404,
        statusText: `Island component not found: ${props.context.name}`
      });
    }
    onErrorCaptured((e) => {
      console.log(e);
    });
    const loader = component.__asyncLoader;
    const reservedKey = findReservedRootIslandPropKey(props.context.props, loader ? await loader() : component);
    if (reservedKey) {
      if (import.meta.dev) {
        console.warn(`Island \`${name}\` was sent a top-level \`${reservedKey}\` prop it does not declare, so the request was rejected. Declare it as a prop on the island, or set \`inheritAttrs: false\`.`);
      }
      throw createError({
        status: 400,
        statusText: "Invalid island request props"
      });
    }
    return () => createVNode(component || "span", { ...props.context.props, "data-island-uid": "" });
  }
});
