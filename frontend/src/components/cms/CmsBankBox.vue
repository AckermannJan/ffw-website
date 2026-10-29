<template>
  <div class="bank">
    <div class="bank__label">IBAN</div>
    <div class="bank__row">
      <span class="bank__iban">{{ iban }}</span>
      <button type="button" class="bank__copy" @click="copy">
        <v-icon size="18">{{
          copied ? "mdi-check" : "mdi-content-copy"
        }}</v-icon>
        {{ copied ? "Kopiert" : "Kopieren" }}
      </button>
    </div>
    <template v-if="bic">
      <div class="bank__label">BIC</div>
      <div class="bank__bic">{{ bic }}</div>
    </template>
  </div>
</template>

<script>
export default {
  name: "CmsBankBox",
  props: {
    iban: { type: String, required: true },
    bic: { type: String, default: "" },
  },
  data: () => ({ copied: false }),
  methods: {
    async copy() {
      try {
        await navigator.clipboard.writeText(this.iban.replace(/\s/g, ""));
        this.copied = true;
        setTimeout(() => (this.copied = false), 2000);
      } catch (e) {
        // Clipboard can be blocked (http, permissions); the IBAN stays
        // visible to copy by hand.
      }
    },
  },
};
</script>

<style scoped lang="scss">
.bank {
  margin: 4px 0 16px;
  padding: 14px 16px;
  background: var(--ffw-panel);
  border-radius: var(--ffw-radius);

  &__label {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ffw-muted);

    &:not(:first-child) {
      margin-top: 10px;
    }
  }

  &__row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
  }

  &__iban,
  &__bic {
    font-size: 1.125rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    font-variant-numeric: tabular-nums;
  }

  &__copy {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-height: 40px;
    padding: 0 14px;
    border: 2px solid var(--ffw-red);
    border-radius: var(--ffw-radius);
    background: transparent;
    color: var(--ffw-red-text);
    font: inherit;
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
  }
}
</style>
