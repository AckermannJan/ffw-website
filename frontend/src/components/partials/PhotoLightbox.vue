<template>
  <v-dialog
    :model-value="index !== null"
    fullscreen
    :scrim="false"
    transition="fade-transition"
    @update:model-value="(open) => !open && close()"
  >
    <div
      class="lightbox"
      role="dialog"
      aria-modal="true"
      :aria-label="label"
      tabindex="-1"
      @keydown.left.prevent="step(-1)"
      @keydown.right.prevent="step(1)"
      @click.self="close"
      @touchstart.passive="onTouchStart"
      @touchend="onTouchEnd"
    >
      <div class="lightbox__bar">
        <span class="lightbox__count" aria-live="polite">
          {{ index + 1 }} / {{ photos.length }}
        </span>
        <button
          ref="closeBtn"
          type="button"
          class="lightbox__btn"
          aria-label="Schließen"
          @click="close"
        >
          <v-icon size="26">mdi-close</v-icon>
        </button>
      </div>

      <img
        v-if="current"
        :key="current.src"
        class="lightbox__img"
        :src="current.src"
        :alt="current.alt"
        @click.self="close"
      />

      <template v-if="photos.length > 1">
        <button
          type="button"
          class="lightbox__btn lightbox__nav lightbox__nav--prev"
          aria-label="Vorheriges Bild"
          @click="step(-1)"
        >
          <v-icon size="30">mdi-chevron-left</v-icon>
        </button>
        <button
          type="button"
          class="lightbox__btn lightbox__nav lightbox__nav--next"
          aria-label="Nächstes Bild"
          @click="step(1)"
        >
          <v-icon size="30">mdi-chevron-right</v-icon>
        </button>
      </template>
    </div>
  </v-dialog>
</template>

<script>
// Full-screen photo viewer: arrows, keyboard (←/→, Esc via v-dialog) and
// swipe on phones. Opened by setting `index`, closed via "update:index".
export default {
  name: "PhotoLightbox",
  props: {
    // [{ src, alt }]
    photos: { type: Array, default: () => [] },
    index: { type: Number, default: null },
  },
  emits: ["update:index"],
  data: () => ({
    touchX: null,
    returnFocus: null,
  }),
  computed: {
    current() {
      return this.index === null ? null : this.photos[this.index];
    },
    label() {
      return this.current
        ? `Bild ${this.index + 1} von ${this.photos.length}`
        : "Bilder";
    },
  },
  watch: {
    index(value, old) {
      // Focus the close button on open, so keys work right away, and give
      // focus back to the opening thumbnail on close.
      if (value !== null && old === null) {
        this.returnFocus = document.activeElement;
        this.$nextTick(() => this.$refs.closeBtn?.focus());
      } else if (value === null && old !== null) {
        const target = this.returnFocus;
        this.returnFocus = null;
        setTimeout(() => target?.focus?.(), 0);
      }
    },
  },
  methods: {
    close() {
      this.$emit("update:index", null);
    },
    step(delta) {
      const n = this.photos.length;
      if (!n || this.index === null) return;
      this.$emit("update:index", (this.index + delta + n) % n);
    },
    onTouchStart(event) {
      this.touchX = event.changedTouches[0].clientX;
    },
    onTouchEnd(event) {
      if (this.touchX === null) return;
      const dx = event.changedTouches[0].clientX - this.touchX;
      this.touchX = null;
      if (Math.abs(dx) > 50) this.step(dx < 0 ? 1 : -1);
    },
  },
};
</script>

<style scoped lang="scss">
// Always dark, whatever the site theme: photos read best on black.
.lightbox {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 64px 72px;
  background: rgba(10, 8, 8, 0.94);
  outline: none;

  @media (max-width: 640px) {
    padding: 64px 8px;
  }

  &__img {
    display: block;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: var(--ffw-radius);
    user-select: none;
  }

  &__bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px 8px 20px;
    color: #ffffff;
  }

  &__count {
    font-size: 0.9375rem;
    font-weight: 700;
  }

  &__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    cursor: pointer;

    &:hover {
      background: var(--ffw-red);
    }

    &:focus-visible {
      outline: 3px solid #ffffff;
      outline-offset: 2px;
    }
  }

  &__nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);

    &--prev {
      left: 12px;
    }
    &--next {
      right: 12px;
    }

    // On phones the arrows sit at the bottom so they don't cover the photo;
    // swiping works too.
    @media (max-width: 640px) {
      top: auto;
      bottom: 8px;
      transform: none;

      &--prev {
        left: calc(50% - 56px);
      }
      &--next {
        right: calc(50% - 56px);
      }
    }
  }
}
</style>
