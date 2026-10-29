import { ref } from "vue";

// Whether this device can place a call from a tel: link. Phones and tablets
// (touch as the main input) open their dialer; on desktops a tel: link
// usually does nothing, so the 112 numbers are shown as plain text there.
const QUERY = "(pointer: coarse)";

const media = window.matchMedia?.(QUERY);
const canCall = ref(media ? media.matches : true);

// Follows changes, e.g. a tablet docked to a keyboard and touchpad.
media?.addEventListener?.("change", (event) => {
  canCall.value = event.matches;
});

export function usePhoneCalls() {
  return { canCall };
}
