import { computed, ref } from "vue";
import { useTheme } from "vuetify";

const STORAGE_KEY = "ffw-color-scheme";

// "system" follows the device setting (also when it changes while the page
// is open); "light" and "dark" are the user's explicit choice.
export const PREFERENCES = ["system", "light", "dark"];

const darkQuery = window.matchMedia?.("(prefers-color-scheme: dark)");

function readPreference() {
  try {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (PREFERENCES.includes(stored)) return stored;
  } catch (e) {
    // Storage can be blocked (private mode); follow the device.
  }
  return "system";
}

// Shared across components so the header button and Vuetify stay in sync.
const preference = ref(readPreference());
const systemDark = ref(Boolean(darkQuery?.matches));
const scheme = computed(() =>
  preference.value === "system"
    ? systemDark.value
      ? "dark"
      : "light"
    : preference.value,
);

let vuetifyTheme = null;

function applyScheme() {
  document.documentElement.dataset.theme = scheme.value;
  if (vuetifyTheme) vuetifyTheme.global.name.value = scheme.value;
}

darkQuery?.addEventListener?.("change", (event) => {
  systemDark.value = event.matches;
  applyScheme();
});
applyScheme();

export function useColorScheme() {
  vuetifyTheme = useTheme();
  applyScheme();

  const isDark = computed(() => scheme.value === "dark");

  function setPreference(value) {
    preference.value = value;
    applyScheme();
    try {
      if (value === "system") localStorage.removeItem(STORAGE_KEY);
      else localStorage.setItem(STORAGE_KEY, value);
    } catch (e) {
      // Not persisted; the choice still applies for this visit.
    }
  }

  // Header button: Gerät → Hell → Dunkel → Gerät …
  function cycle() {
    const next = PREFERENCES[(PREFERENCES.indexOf(preference.value) + 1) % 3];
    setPreference(next);
  }

  return { preference, scheme, isDark, setPreference, cycle };
}
