<template>
  <div class="cms">
    <div
      v-if="parsed.intro.length"
      :class="['cms__intro', { 'cms__intro--lead': hasSections }]"
    >
      <CmsBlocks :blocks="parsed.intro" />
    </div>

    <section
      v-for="section in parsed.sections"
      :key="section.title"
      :class="['cms-section', { 'cms-section--media': isMedia(section) }]"
    >
      <h2 class="cms-section__title">{{ section.title }}</h2>

      <div v-if="isMedia(section)" class="cms-section__media">
        <CmsBlocks :blocks="figures(section)" />
        <div class="cms-section__media-text">
          <CmsBlocks :blocks="withoutFigures(section)" />
        </div>
      </div>
      <CmsBlocks v-else :blocks="section.lead" />

      <div v-if="section.cards.length" class="cms-cards">
        <div v-for="card in section.cards" :key="card.title" class="cms-card">
          <h3 class="cms-card__title">{{ card.title }}</h3>
          <CmsBlocks :blocks="card.blocks" />
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import CmsBlocks from "./CmsBlocks.vue";
import { parseCmsSections } from "@/utils/cmsSections";

export default {
  name: "CmsContent",
  components: { CmsBlocks },
  props: {
    html: { type: String, default: "" },
  },
  computed: {
    parsed() {
      return parseCmsSections(this.html);
    },
    hasSections() {
      return this.parsed.sections.length > 0;
    },
  },
  methods: {
    // A section with a picture and text shows them side by side.
    isMedia(section) {
      const figures = this.figures(section).length;
      return figures === 1 && section.lead.length > 1;
    },
    figures(section) {
      return section.lead.filter((b) => b.type === "figure");
    },
    withoutFigures(section) {
      return section.lead.filter((b) => b.type !== "figure");
    },
  },
};
</script>

<style lang="scss">
@import "CmsContent";
</style>
