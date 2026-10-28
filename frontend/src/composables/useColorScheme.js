import { computed, ref } from "vue";
import { useTheme } from "vuetify";

const STORAGE_KEY = "ffw-color-scheme";

function readInitialScheme() {
  try {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored === "light" || stored === "dark") return stored;
  } catch (e) {
    // Storage can be blocked (private mode); fall back to the OS setting.
  }
  return window.matchMedia?.("(prefers-color-scheme: dark)").matches
    ? "dark"
    : "light";
}

function applyScheme(value) {
  document.documentElement.dataset.theme = value;
}

// Shared across components so the header toggle and Vuetify stay in sync.
const scheme = ref(readInitialScheme());
applyScheme(scheme.value);

export function useColorScheme() {
  const vuetifyTheme = useTheme();
  vuetifyTheme.global.name.value = scheme.value;

  const isDark = computed(() => scheme.value === "dark");

  function toggle() {
    scheme.value = isDark.value ? "light" : "dark";
    applyScheme(scheme.value);
    vuetifyTheme.global.name.value = scheme.value;
    try {
      localStorage.setItem(STORAGE_KEY, scheme.value);
    } catch (e) {
      // Not persisted; the choice still applies for this visit.
    }
  }

  return { scheme, isDark, toggle };
}
