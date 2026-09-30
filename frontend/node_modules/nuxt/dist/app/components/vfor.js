export const MAX_VFOR_LENGTH = 1e4;
export function vforBound(source) {
  if (typeof source !== "number" || source <= MAX_VFOR_LENGTH) {
    return source;
  }
  if (import.meta.dev) {
    console.warn(`A v-for in a server component asked for ${source} iterations; only the first ${MAX_VFOR_LENGTH} were rendered. Island props come from the request, so the count is capped. Paginate the data, or clamp the value before passing it to the island.`);
  }
  return MAX_VFOR_LENGTH;
}
