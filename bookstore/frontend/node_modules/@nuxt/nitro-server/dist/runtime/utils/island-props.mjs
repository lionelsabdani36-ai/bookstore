



export const MAX_ISLAND_BODY_BYTES = 64 * 1024;

export const MAX_ISLAND_PROP_DEPTH = 64;

export function exceedsMaxDepth(raw, maxDepth = MAX_ISLAND_PROP_DEPTH) {
	let depth = 0;
	let inString = false;
	let escaped = false;
	for (let i = 0; i < raw.length; i++) {
		const ch = raw[i];
		if (inString) {
			if (escaped) {
				escaped = false;
			} else if (ch === "\\") {
				escaped = true;
			} else if (ch === "\"") {
				inString = false;
			}
			continue;
		}
		if (ch === "\"") {
			inString = true;
		} else if (ch === "{" || ch === "[") {
			if (++depth > maxDepth) {
				return true;
			}
		} else if (ch === "}" || ch === "]") {
			if (depth > 0) {
				depth--;
			}
		}
	}
	return false;
}

export function exceedsMaxBytes(raw, maxBytes = MAX_ISLAND_BODY_BYTES) {
	return Buffer.byteLength(raw, "utf8") > maxBytes;
}
