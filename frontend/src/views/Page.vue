<template>
  <div>
    <template v-if="!isLoading">
      <Report>
        <template v-slot:headline>
          {{ pageDetails.post_title }}
        </template>
        <template v-slot:content>
          <div v-html="pageDetails.post_content" class="report__content" />
        </template>
      </Report>
      <v-carousel
        v-if="
          Object.keys(pageDetails.attached_images).length > 0 &&
            parseInt(pageDetails.any_attached_images) === 1
        "
        show-arrows-on-hover
      >
        <v-carousel-item
          v-for="(item, i) in pageDetails.attached_images"
          :key="i"
          :src="item"
        ></v-carousel-item>
      </v-carousel>
    </template>
    <Loader v-else />
  </div>
</template>

<script>
import Loader from "../components/partials/Loader.vue";
import { mapGetters, mapActions } from "vuex";
import Report from "../components/partials/Report/Report";

export default {
  computed: {
    ...mapGetters("page", {
      pageDetails: "pageDetails",
      isLoading: "isLoading"
    })
  },
  props: {
    slug: {
      default: null
    }
  },
  mounted() {
    this.getPageById(this.slug || this.$route.params.pageSlug);
  },
  methods: {
    ...mapActions("page", {
      getPageById: "getPageById"
    })
  },
  components: {
    Report,
    Loader
  },
  metaInfo() {
    return {
      title: "Feuerwehr Mühltal Traisa | " + this.pageDetails.post_title
    };
  }
};
</script>

<style type="scss">
img {
  max-width: 100%;
  height: auto;
}
.v-image__image--cover {
  background-size: 100% auto !Important;
}
</style>
