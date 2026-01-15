export function convertSpaceToUnderscore(str) {
    if (!str) return null;
    return str.replace(/\s+/g, '_');
}
