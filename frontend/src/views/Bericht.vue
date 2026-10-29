<template>
  <div class="bericht-page">
    <nav aria-label="Brotkrumen" class="crumbs">
      <router-link to="/" class="ffw-link">Startseite</router-link>
      <span aria-hidden="true">›</span>
      <router-link to="/archiv" class="ffw-link">Aktuelles</router-link>
      <span aria-hidden="true">›</span>
      <span>{{ ready ? page.post_title : "Bericht" }}</span>
    </nav>

    <article class="bericht">
      <template v-if="!ready">
        <div class="bericht__top" aria-hidden="true">
          <div class="skeleton skeleton--meta" />
          <div class="skeleton skeleton--title" />
        </div>
        <div class="bericht__hero skeleton" aria-hidden="true" />
      </template>

      <template v-else>
        <header class="bericht__top">
          <div class="meta">
            <template v-if="category">
              <span class="meta__cat">{{ category }}</span>
              <span aria-hidden="true">·</span>
            </template>
            <time :datetime="date.format('YYYY-MM-DD')">
              {{ date.format("D. MMMM YYYY") }}
            </time>
          </div>
          <h1 class="bericht__title">{{ page.post_title }}</h1>
          <p v-if="lead" class="bericht__lead">{{ lead }}</p>
        </header>

        <button
          v-if="hero"
          type="button"
          class="bericht__hero"
          :aria-label="`${hero.alt || 'Titelbild'} vergrößern`"
          @click="photoIndex = 0"
        >
          <img :src="hero.src" :alt="hero.alt" />
        </button>

        <div class="bericht__body">
          <div class="bericht__content">
            <CmsContent :html="page.post_content" />
          </div>

          <section v-if="gallery.length" aria-labelledby="bericht-photos">
            <h2 id="bericht-photos" class="bericht__section">Bilder</h2>
            <ul class="gallery">
              <li v-for="photo in gallery" :key="photo.src">
                <button
                  type="button"
                  class="photo"
                  :aria-label="`${photo.alt} vergrößern`"
                  @click="photoIndex = photo.index"
                >
                  <img :src="photo.src" :alt="photo.alt" loading="lazy" />
                  <span class="photo__zoom" aria-hidden="true">
                    <v-icon size="20">mdi-magnify-plus-outline</v-icon>
                  </span>
                </button>
              </li>
            </ul>
          </section>

          <footer class="bericht__foot">
            <router-link to="/" class="ffw-link bericht__back">
              <v-icon size="18">mdi-arrow-left</v-icon>
              Zurück zu Aktuelles
            </router-link>
            <router-link v-if="cta" :to="cta.to" class="ffw-btn">
              {{ cta.label }}
            </router-link>
          </footer>
        </div>

        <PhotoLightbox v-model:index="photoIndex" :photos="photos" />
      </template>
    </article>

    <section v-if="more.length" class="more" aria-labelledby="bericht-more">
      <h2 id="bericht-more" class="more__title">Weitere Berichte</h2>
      <div class="more__grid">
        <router-link
          v-for="item in more"
          :key="item.ID"
          :to="'/seite/' + item.post_name"
          class="card"
        >
          <div class="ffw-img card__img">
            <img
              v-if="item.startBild"
              :src="https(item.startBild)"
              :alt="item.startBildAlt || ''"
              loading="lazy"
            />
          </div>
          <div class="card__bar" />
          <div class="card__text">
            <PostMeta :page="item" />
            <h3 class="card__title">{{ item.post_title }}</h3>
          </div>
        </router-link>
      </div>
    </section>
  </div>
</template>

<script>
import { mapState, mapActions } from "pinia";
import { usePageStore } from "@/store/modules/page";
import { useIndexStore } from "@/store/modules/index";
import { useHead } from "@unhead/vue";
import { computed } from "vue";
import { momentInstance } from "@/utils/moment";
import CmsContent from "@/components/cms/CmsContent.vue";
import PhotoLightbox from "@/components/partials/PhotoLightbox.vue";
import PostMeta from "@/components/home/PostMeta.vue";

// Uploads are stored with http:// URLs; the site runs on https.
const https = (url = "") => url.replace(/^http:\/\//, "https://");

// Where readers can join in, by report category (see ffw_category in
// wpTheme/functions.php: Types field "kategorie" or the parent page title).
const CTAS = {
  Verein: { label: "Mitglied im Verein werden", to: "/seite/verein" },
  Einsatzabteilung: {
    label: "Mitmachen in der Einsatzabteilung",
    to: "/einsatzabteilung",
  },
  Jugendfeuerwehr: {
    label: "Zur Jugendfeuerwehr",
    to: "/kindergruppen?gruppe=jugend",
  },
  Wichtelwehr: { label: "Zur Wichtelwehr", to: "/kindergruppen" },
};

// News reports (/seite/:slug). They are WordPress pages; category, date
// and alt texts come from the start page data when the report is listed
// there (getIndexInfo), older reports show only their publish date.
export default {
  name: "Bericht",
  components: { CmsContent, PhotoLightbox, PostMeta },
  setup() {
    const pageStore = usePageStore();
    useHead({
      title: computed(
        () =>
          "Feuerwehr Mühltal Traisa | " +
          (pageStore.pageDetails.post_title || ""),
      ),
    });
  },
  data: () => ({
    photoIndex: null,
  }),
  computed: {
    ...mapState(usePageStore, { page: "pageDetails", isLoading: "isLoading" }),
    ...mapState(useIndexStore, { indexPages: "pages" }),
    slug() {
      return this.$route.params.pageSlug;
    },
    // The store still holds the previous page while the next one loads.
    ready() {
      return !this.isLoading && this.page.post_name === this.slug;
    },
    indexEntry() {
      return (this.indexPages || []).find((p) => p.ID === this.page.ID) || {};
    },
    category() {
      return this.indexEntry.kategorie || "";
    },
    date() {
      return momentInstance(
        this.indexEntry.veranstaltungsdatum || this.page.post_date,
      );
    },
    // Only a real WordPress excerpt; the generated one repeats the text.
    lead() {
      return (this.page.post_excerpt || "").trim();
    },
    hero() {
      if (!this.page.startBild) return null;
      return {
        src: https(this.page.startBild),
        alt: this.indexEntry.startBildAlt || "",
      };
    },
    // "any_attached_images" is a Types radio field: "1" shows the gallery.
    attached() {
      if (String(this.page.any_attached_images) !== "1") return [];
      return Object.values(this.page.attached_images || {})
        .filter(Boolean)
        .map(https);
    },
    // Lightbox: title image first, then the gallery without duplicates.
    photos() {
      const list = [];
      if (this.hero) {
        list.push({ src: this.hero.src, alt: this.hero.alt || "Titelbild" });
      }
      const rest = this.attached.filter((src) => src !== this.hero?.src);
      rest.forEach((src, i) =>
        list.push({ src, alt: `Bild ${i + 1} von ${rest.length}` }),
      );
      return list;
    },
    gallery() {
      const offset = this.hero ? 1 : 0;
      return this.photos
        .slice(offset)
        .map((photo, i) => ({ ...photo, index: i + offset }));
    },
    cta() {
      return CTAS[this.category] || null;
    },
    more() {
      return (this.indexPages || [])
        .filter((p) => p.ID !== this.page.ID)
        .slice(0, 2);
    },
  },
  watch: {
    slug: "load",
  },
  mounted() {
    this.load();
  },
  methods: {
    ...mapActions(usePageStore, ["getPageById"]),
    https,
    load() {
      this.photoIndex = null;
      if (this.slug) this.getPageById(this.slug);
    },
  },
};
</script>

<style scoped lang="scss">
@import "Bericht";
</style>
