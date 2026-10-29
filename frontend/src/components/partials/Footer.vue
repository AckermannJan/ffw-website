<template>
  <footer class="site-footer">
    <div class="ffw-wrap">
      <div class="site-footer__grid">
        <div>
          <div class="site-footer__small">Wichtige</div>
          <h3 class="site-footer__big">Links</h3>
          <ul class="site-footer__links">
            <li>
              <a href="https://www.feuerwehr-muehltal.de/"
                >Freiwillige Feuerwehr Mühltal</a
              >
            </li>
            <li>
              <a href="https://www.kfv-dadi.de/"
                >Kreisfeuerwehrverband Darmstadt-Dieburg</a
              >
            </li>
            <li>
              <a href="https://www.feuerwehr-nieder-ramstadt.de/"
                >Feuerwehr Nieder-Ramstadt</a
              >
            </li>
            <li>
              <a href="https://ff-traisa.de/seite/info-buerger"
                >Infos für Bürger</a
              >
            </li>
            <li>
              <a href="https://www.muehltal.de/"
                >Informations-Seite der Gemeinde Mühltal</a
              >
            </li>
          </ul>
        </div>
        <div>
          <router-link to="/kontakt" class="site-footer__head-link">
            <div class="site-footer__small">Unser</div>
            <h3 class="site-footer__big">Kontakt</h3>
          </router-link>
          <p>
            Freiwillige Feuerwehr Mühltal Traisa<br />
            Im Rosengarten 19<br />
            64367 Mühltal Traisa
          </p>
          <p>
            Telefon:
            <a href="tel:+496151145400">(0 61 51) 14 54 00</a><br />
            (nicht immer besetzt)<br />
            Telefax: (0 61 51) 2 79 17 21
          </p>
          <p class="site-footer__emergency">
            Im Notfall:
            <a v-if="canCall" href="tel:112">112</a>
            <span v-else>112</span>
          </p>
        </div>
        <div>
          <router-link to="/impressum" class="site-footer__head-link">
            <div class="site-footer__small">Rechtliches/</div>
            <h3 class="site-footer__big">Impressum</h3>
          </router-link>
          <p>
            Kein Gewähr auf Richtigkeit/Aktualität. Inhalte auf dieser Seite
            gehören Feuerwehr Traisa e.V. (sofern nicht anders angegeben).
          </p>
        </div>
        <div>
          <router-link to="/archiv" class="site-footer__head-link">
            <div class="site-footer__small">Unser</div>
            <h3 class="site-footer__big">Archiv</h3>
          </router-link>
          <p>Ältere Berichte und Seiten</p>
        </div>
      </div>
      <div class="site-footer__bottom">
        <span>© {{ year }} Freiwillige Feuerwehr Traisa e.V.</span>
        <span>Design und Umsetzung Jan Ackermann</span>
      </div>
    </div>
  </footer>
</template>

<script>
import { usePhoneCalls } from "@/composables/usePhoneCalls";

export default {
  name: "AppFooter",
  setup() {
    const { canCall } = usePhoneCalls();
    return { canCall };
  },
  data: () => ({ year: new Date().getFullYear() }),
};
</script>

<style scoped lang="scss">
.site-footer {
  background: var(--ffw-foot);
  color: var(--ffw-foot-ink);
  border-top: 4px solid var(--ffw-red);
  padding: 48px 0 28px;
  font-size: 0.875rem;

  &__grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 32px;

    @media (max-width: 960px) {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    @media (max-width: 640px) {
      grid-template-columns: minmax(0, 1fr);
    }

    // Each column is a container, so the big words can scale with it.
    > div {
      min-width: 0;
      container-type: inline-size;
    }
  }

  &__small {
    font-size: 0.8125rem;
  }

  &__big {
    margin: 0 0 16px;
    // "Impressum" is about 5.4em wide: shrink it in narrow columns instead
    // of letting it run into the next one.
    font-size: min(3rem, 18cqi);
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.02em;
    color: var(--ffw-foot-head);

    @media (max-width: 640px) {
      font-size: 2.375rem;
    }
  }

  &__head-link {
    display: block;
    text-decoration: none;
    color: inherit;
  }

  &__links {
    margin: 0;
    padding: 0;
    list-style: none;

    li + li {
      margin-top: 6px;
    }
  }

  p {
    margin: 0 0 10px;
  }

  a {
    color: var(--ffw-foot-ink);
  }
  a:hover {
    color: #ffffff;
  }

  &__emergency {
    font-weight: 800;
    color: #ffffff;

    a {
      color: #ffffff;
    }
  }

  &__bottom {
    margin-top: 40px;
    padding-top: 18px;
    border-top: 1px dotted rgba(255, 255, 255, 0.25);
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 8px;
    font-size: 0.8125rem;
  }
}
</style>
