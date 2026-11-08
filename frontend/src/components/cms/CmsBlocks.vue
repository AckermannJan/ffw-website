<template>
  <template v-for="(block, index) in blocks" :key="index">
    <!-- Content from our own WordPress, already rendered with v-html before. -->
    <div v-if="block.type === 'html'" class="cms-prose" v-html="block.html" />

    <figure v-else-if="block.type === 'figure'" class="cms-figure">
      <img :src="block.src" :alt="block.alt" loading="lazy" />
      <figcaption v-if="block.caption">{{ block.caption }}</figcaption>
    </figure>

    <div v-else-if="block.type === 'downloads'" class="cms-downloads">
      <p v-if="block.note && block.noteFirst" class="cms-downloads__intro">
        {{ block.note }}
      </p>
      <a
        v-for="link in block.links"
        :key="link.href"
        :href="link.href"
        class="cms-downloads__link"
        target="_blank"
        rel="noopener"
      >
        <v-icon size="20">mdi-file-download-outline</v-icon>
        {{ link.label }}
      </a>
      <p v-if="block.note && !block.noteFirst" class="cms-downloads__note">
        {{ block.note }}
      </p>
    </div>

    <CmsBankBox
      v-else-if="block.type === 'bank'"
      :iban="block.iban"
      :bic="block.bic"
    />
  </template>
</template>

<script>
import CmsBankBox from "./CmsBankBox.vue";

export default {
  name: "CmsBlocks",
  components: { CmsBankBox },
  props: {
    blocks: { type: Array, required: true },
  },
};
</script>
