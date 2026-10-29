<template>
  <article class="technik">
    <header class="ffw-notch technik__head">
      <h1>Technik</h1>
    </header>

    <div class="technik__body">
      <section>
        <h2 class="technik__title">Fahrzeuge</h2>
        <ul class="vehicles">
          <li v-for="vehicle in vehicles" :key="vehicle.to">
            <router-link :to="vehicle.to" class="vehicle">
              <span class="vehicle__ring">
                <img :src="vehicle.image" :alt="'Fahrzeug ' + vehicle.short" />
              </span>
              <span class="vehicle__text">
                <span class="vehicle__short">{{ vehicle.short }}</span>
                <span class="vehicle__long">{{ vehicle.long }}</span>
              </span>
            </router-link>
          </li>
        </ul>
      </section>

      <section>
        <h2 class="technik__title">Feuerwehrhaus</h2>
        <router-link to="/technik/feuerwehrhaus" class="house">
          <div class="ffw-img house__img">
            <img
              :src="house.image"
              alt="Feuerwehrhaus Traisa mit den drei Toren der Fahrzeughalle"
              loading="lazy"
            />
          </div>
          <div class="house__bar" />
          <div class="house__text">
            <h3>Unser Feuerwehrhaus</h3>
            <div class="house__address">
              <v-icon size="20">mdi-map-marker-outline</v-icon>
              <span>Im Rosengarten 19<br />64367 Mühltal Traisa</span>
            </div>
            <p>
              Fahrzeughalle, Ausbildungsraum und Treffpunkt für
              Einsatzabteilung, Jugendfeuerwehr und Wichtelwehr – und jedes Jahr
              Schauplatz des Gerätehausfests.
            </p>
            <span class="house__more">
              Mehr erfahren <v-icon size="16">mdi-arrow-right</v-icon>
            </span>
          </div>
        </router-link>
      </section>
    </div>
  </article>
</template>

<script>
import { usePageSeo } from "@/utils/seo";

const WP = "https://wordpress.feuerwehr-traisa.de/wp-content";

// Joins word parts with soft hyphens, so long German compounds only break
// (with a hyphen) at these joints when a phone screen is too narrow.
const compound = (...parts) => parts.join(String.fromCharCode(173));

// Static page; each vehicle links to its WordPress page under /technik/…
// Some pictures still live in the old "FFW" theme folder (themes/FFW).
export default {
  name: "Technik",
  setup() {
    usePageSeo({
      title: "Technik",
      description:
        "Fahrzeuge und Feuerwehrhaus der Feuerwehr Traisa: Kommandowagen, Mannschaftstransportfahrzeug, LF 10, MLF, Anhänger und Rettungsboot im Überblick.",
    });
  },
  data: () => ({
    vehicles: [
      {
        short: "KDOW",
        long: "Kommandowagen",
        to: "/technik/kdow",
        image: `${WP}/themes/FFW/imgs/cars/kdow.jpg`,
      },
      {
        short: "MTF",
        long: compound("Mannschafts", "transport", "fahrzeug"),
        to: "/technik/mtf",
        image: `${WP}/uploads/2016/08/Vorsschaukleinnn.png`,
      },
      {
        short: "LF 10",
        long: compound("Lösch", "gruppen", "fahrzeug"),
        to: "/technik/lf-106",
        image: `${WP}/themes/FFW/imgs/cars/lf106.jpg`,
      },
      {
        short: "MLF",
        long: "Mittleres " + compound("Lösch", "fahrzeug"),
        to: "/technik/mlf",
        image: `${WP}/uploads/2025/05/MLF_Icon.png`,
      },
      {
        short: "Anhänger",
        long: compound("Fahrzeug", "anhänger"),
        to: "/technik/anhaenger",
        image: `${WP}/themes/FFW/imgs/cars/anhaenger.jpg`,
      },
      {
        short: "Rettungsboot",
        long: "Wasserrettung",
        to: "/technik/rettungsboot",
        image: `${WP}/uploads/2019/06/Bild-Boot.png`,
      },
    ],
    house: { image: `${WP}/uploads/2016/02/1-fwhaus-aussen.jpg` },
  }),
};
</script>

<style scoped lang="scss">
@import "Technik";
</style>
