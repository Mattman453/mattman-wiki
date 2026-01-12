import { derived, readable } from "svelte/store";
import { mobileVW } from "../css/_export.module.scss";

export const windowInnerWidth = readable(window.innerWidth, (set) => {
    const handleResize = () => { set(window.innerHeight); };
    window.addEventListener("resize", handleResize);
    return () => { window.removeEventListener("resize", handleResize); }
});

export const isMobile = derived(windowInnerWidth, ($windowInnerWidth) => {
    return $windowInnerWidth <= parseInt(mobileVW.replace("px", ""));
});
