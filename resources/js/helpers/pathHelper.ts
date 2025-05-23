export const getBackPath = (path, depth) => {
    if (!path.length) {
        return ''
    }

    const split = path.split("/");
    if (split.length === 1) {
        return ''
    }

    return split.slice(0, split.length - depth).join("/") + "/";
}
