<template>
  <div class="gallery">
    <section v-if="thumbs.length" :aria-labelledby="headingId">
      <h2 :id="headingId" class="gallery__title">{{ title }}</h2>
      <ul class="gallery__grid">
        <li v-for="photo in thumbs" :key="photo.src">
          <button
            type="button"
            class="gallery__photo"
            :aria-label="`${photo.alt} vergrößern`"
            @click="open(photo.index)"
          >
            <img :src="photo.src" :alt="photo.alt" loading="lazy" />
            <span class="gallery__zoom" aria-hidden="true">
              <v-icon size="20">mdi-magnify-plus-outline</v-icon>
            </span>
          </button>
        </li>
      </ul>
    </section>
    <PhotoLightbox v-model:index="index" :photos="photos" />
  </div>
</template>

<script>
import PhotoLightbox from "./PhotoLightbox.vue";

let uid = 0;

// Thumbnail grid ("Bilder") with the full-screen viewer. `photos` is the
// whole set the viewer pages through; `skip` hides leading photos from the
// grid (e.g. a report's title image, opened via `open(0)` from outside).
export default {
  name: "PhotoGallery",
  components: { PhotoLightbox },
  props: {
    // [{ src, alt }]
    photos: { type: Array, default: () => [] },
    skip: { type: Number, default: 0 },
    title: { type: String, default: "Bilder" },
  },
  data: () => ({
    index: null,
    headingId: `gallery-${++uid}`,
  }),
  computed: {
    thumbs() {
      return this.photos
        .map((photo, index) => ({ ...photo, index }))
        .slice(this.skip);
    },
  },
  watch: {
    // A new page or alarm brings new photos: close the viewer.
    photos() {
      this.index = null;
    },
  },
  methods: {
    open(index) {
      this.index = index;
    },
  },
};
</script>

<style scoped lang="scss">
$bp-wide: 1120px;
$bp-phone: 640px;

.gallery {
  &__title {
    margin: 0 0 14px;
    padding-bottom: 6px;
    border-bottom: 2px dotted var(--ffw-red);
    font-size: 1.3125rem;
    font-weight: 800;
    line-height: 1.3;
  }

  &__grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin: 0;
    padding: 0;
    list-style: none;

    @media (max-width: $bp-wide) {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  &__photo {
    position: relative;
    display: block;
    width: 100%;
    aspect-ratio: 4 / 3;
    padding: 0;
    overflow: hidden;
    border: 0;
    border-radius: var(--ffw-radius);
    background: var(--ffw-panel);
    cursor: zoom-in;

    img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.2s;
    }

    &:hover img {
      transform: scale(1.03);
    }

    &:focus-visible {
      outline: 3px solid var(--ffw-red);
      outline-offset: 2px;
    }
  }

  &__zoom {
    position: absolute;
    right: 8px;
    bottom: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.55);
    color: #ffffff;
  }
}
</style>
