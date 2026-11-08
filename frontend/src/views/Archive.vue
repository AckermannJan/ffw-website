<template>
  <article class="archiv">
    <header class="ffw-notch archiv__head">
      <h1>Archiv</h1>
      <div class="archiv__sub">Ältere Berichte der Feuerwehr Traisa</div>
    </header>

    <div class="archiv__body">
      <template v-if="!entries">
        <div v-for="n in 4" :key="n" class="row row--loading" />
      </template>

      <template v-else>
        <div class="toolbar">
          <label class="search">
            <span class="ffw-visually-hidden">Berichte durchsuchen</span>
            <v-icon size="20" class="search__icon">mdi-magnify</v-icon>
            <input
              v-model.trim="query"
              type="search"
              class="search__input"
              placeholder="Berichte durchsuchen"
            />
          </label>
          <div
            v-if="categories.length > 1"
            class="chips"
            role="group"
            aria-label="Nach Bereich filtern"
          >
            <button
              v-for="chip in chips"
              :key="chip.id"
              type="button"
              :class="['chip', { 'chip--on': filter === chip.id }]"
              :aria-pressed="filter === chip.id ? 'true' : 'false'"
              @click="filter = chip.id"
            >
              {{ chip.label }}
            </button>
          </div>
        </div>

        <p class="count" aria-live="polite">
          {{ matches.length }}
          {{ matches.length === 1 ? "Bericht" : "Berichte" }}
        </p>

        <section v-for="year in years" :key="year.year" class="year">
          <h2 class="year__title">{{ year.year }}</h2>
          <router-link
            v-for="entry in year.entries"
            :key="entry.id"
            :to="'/seite/' + entry.slug"
            class="row"
          >
            <div class="ffw-img row__img">
              <img
                v-if="entry.image"
                :src="entry.image"
                :alt="entry.imageAlt"
                loading="lazy"
              />
            </div>
            <div class="row__text">
              <div class="row__meta">
                <template v-if="entry.category">
                  <span class="row__cat">{{ entry.category }}</span>
                  <span aria-hidden="true">·</span>
                </template>
                <time :datetime="entry.date.format('YYYY-MM-DD')">
                  {{ entry.date.format("DD.MM.YYYY") }}
                </time>
              </div>
              <h3 class="row__title">{{ entry.title }}</h3>
              <p class="row__excerpt">{{ entry.excerpt }}</p>
            </div>
          </router-link>
        </section>

        <p v-if="!matches.length" class="muted">
          Keine Berichte gefunden.
          <button type="button" class="ffw-link reset" @click="reset">
            Suche zurücksetzen
          </button>
        </p>

        <button
          v-if="hidden > 0"
          type="button"
          class="ffw-btn ffw-btn--outline more"
          @click="limit += PAGE_SIZE"
        >
          Weitere Berichte laden ({{ hidden }})
        </button>
      </template>
    </div>
  </article>
</template>

<script>
import { mapState, mapActions } from "pinia";
import { useArchiveStore } from "@/store/modules/archive";
import { usePageSeo } from "@/utils/seo";
import { momentInstance } from "@/utils/moment";

const PAGE_SIZE = 20;

// Uploads are stored with http:// URLs; the site runs on https.
const https = (url = "") => url.replace(/^http:\/\//, "https://");

// "excerpt" comes from the API (30 words); older API responses only have
// the stripped post_content, so it is cut here instead.
const shorten = (text = "", max = 180) => {
  const clean = text.replace(/\s+/g, " ").trim();
  if (clean.length <= max) return clean;
  return clean.slice(0, clean.lastIndexOf(" ", max)) + " …";
};

// Archived reports (Types field "archiv" = 1), newest first, grouped by
// year. Category, excerpt and image alt come from getArchive.
export default {
  name: "Archive",
  setup() {
    usePageSeo({
      title: "Archiv",
      description:
        "Ältere Berichte der Freiwilligen Feuerwehr Traisa: Feste, Übungen, Ausbildung, Jugendfeuerwehr und Vereinsleben seit 2014 – durchsuchbar nach Jahr und Bereich.",
    });
  },
  data: () => ({
    query: "",
    filter: "all",
    limit: PAGE_SIZE,
    PAGE_SIZE,
  }),
  computed: {
    ...mapState(useArchiveStore, ["isLoading", "archiveData"]),
    entries() {
      if (this.isLoading || !Array.isArray(this.archiveData)) return null;
      return this.archiveData
        .map((post) => ({
          id: post.ID,
          slug: post.post_name,
          title: post.post_title,
          category: post.kategorie || "",
          date: momentInstance(post.post_date),
          excerpt: shorten(post.excerpt || post.post_content),
          image: post.startBild ? https(post.startBild) : "",
          imageAlt: post.startBildAlt || "",
          search: `${post.post_title} ${post.post_content}`.toLowerCase(),
        }))
        .sort((a, b) => b.date - a.date);
    },
    categories() {
      const counts = {};
      for (const e of this.entries || []) {
        if (e.category) counts[e.category] = (counts[e.category] || 0) + 1;
      }
      return Object.keys(counts).sort((a, b) => counts[b] - counts[a]);
    },
    chips() {
      return [
        { id: "all", label: "Alle" },
        ...this.categories.map((c) => ({ id: c, label: c })),
      ];
    },
    matches() {
      const words = this.query.toLowerCase().split(/\s+/).filter(Boolean);
      return (this.entries || []).filter(
        (e) =>
          (this.filter === "all" || e.category === this.filter) &&
          words.every((w) => e.search.includes(w)),
      );
    },
    // While searching or filtering, show every match.
    visible() {
      const narrowed = this.query || this.filter !== "all";
      return narrowed ? this.matches : this.matches.slice(0, this.limit);
    },
    hidden() {
      return this.matches.length - this.visible.length;
    },
    years() {
      const groups = [];
      for (const entry of this.visible) {
        const year = entry.date.year();
        let group = groups.find((g) => g.year === year);
        if (!group) {
          group = { year, entries: [] };
          groups.push(group);
        }
        group.entries.push(entry);
      }
      return groups;
    },
  },
  mounted() {
    this.loadArchive();
  },
  methods: {
    ...mapActions(useArchiveStore, ["loadArchive"]),
    reset() {
      this.query = "";
      this.filter = "all";
    },
  },
};
</script>

<style scoped lang="scss">
@import "Archive";
</style>
