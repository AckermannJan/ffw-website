<template>
  <div class="splash" role="status" aria-live="polite">
    <div class="splash__inner">
      <span
        class="splash__emblem"
        :style="{ '--emblem': `url(${emblem})` }"
        aria-hidden="true"
      />
      <div class="splash__name">Freiwillige Feuerwehr Traisa</div>
      <p class="splash__claim">
        Unsere Freizeit für Ihre Sicherheit –
        <span class="splash__years">seit {{ years }} Jahren</span>
      </p>
      <div class="splash__bar" aria-hidden="true" />
      <span class="ffw-visually-hidden">Seite wird geladen</span>
    </div>
  </div>
</template>

<script>
// "Retten · Löschen · Bergen · Schützen" emblem, tinted via CSS mask.
import emblem from "@/assets/img/emblem.png";
import { FOUNDED_YEAR } from "@/utils/constants";

// Shown while the start data loads (App.vue).
export default {
  name: "SplashScreen",
  data: () => ({
    emblem,
    years: new Date().getFullYear() - FOUNDED_YEAR,
  }),
};
</script>

<style scoped lang="scss">
.splash {
  position: fixed;
  inset: 0;
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: var(--ffw-bg);
  color: var(--ffw-ink);
  text-align: center;

  &__inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 30rem;
  }

  &__emblem {
    width: 104px;
    height: 104px;
    margin-bottom: 24px;
    background: var(--ffw-red);
    mask: var(--emblem) center / contain no-repeat;
    -webkit-mask: var(--emblem) center / contain no-repeat;

    @media (max-width: 640px) {
      width: 84px;
      height: 84px;
    }
  }

  &__name {
    padding-bottom: 8px;
    border-bottom: 2px dotted var(--ffw-red);
    font-size: 0.8125rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--ffw-red-text);
  }

  &__claim {
    margin: 16px 0 28px;
    font-size: 1.75rem;
    font-weight: 800;
    font-style: italic;
    line-height: 1.2;

    @media (max-width: 640px) {
      font-size: 1.375rem;
    }
  }

  // Own line, as in the start page hero.
  &__years {
    display: block;
    color: var(--ffw-red-text);
  }

  // Slim indeterminate bar: a red segment sliding across a pale track.
  &__bar {
    position: relative;
    width: 160px;
    height: 4px;
    overflow: hidden;
    border-radius: 2px;
    background: var(--ffw-line);

    &::after {
      content: "";
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      width: 40%;
      border-radius: 2px;
      background: var(--ffw-red);
      animation: splash-slide 1.2s ease-in-out infinite;
    }

    @media (prefers-reduced-motion: reduce) {
      &::after {
        width: 100%;
        animation: none;
        opacity: 0.6;
      }
    }
  }
}

@keyframes splash-slide {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(250%);
  }
}
</style>
