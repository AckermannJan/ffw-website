<template>
  <div>
    <div class="ffw-section-title news-title">
      <h2>Aktuelles</h2>
      <router-link to="/archiv" class="ffw-link">Alle Berichte</router-link>
    </div>

    <router-link
      v-if="featured"
      :to="'/seite/' + featured.post_name"
      class="feature"
    >
      <div class="ffw-img feature__img">
        <img
          v-if="featured.startBild"
          :src="featured.startBild"
          :alt="imageAlt(featured)"
        />
      </div>
      <div class="feature__bar" />
      <div class="feature__body">
        <PostMeta :page="featured" />
        <h3 class="feature__title">{{ featured.post_title }}</h3>
        <p class="feature__excerpt">{{ excerpt(featured, 260) }}</p>
        <span class="read-more">
          Weiterlesen <v-icon size="16">mdi-arrow-right</v-icon>
        </span>
      </div>
    </router-link>

    <div class="cards">
      <router-link
        v-for="page in rest"
        :key="page.ID"
        :to="'/seite/' + page.post_name"
        class="card"
      >
        <div class="ffw-img card__img">
          <img
            v-if="page.startBild"
            :src="page.startBild"
            :alt="imageAlt(page)"
            loading="lazy"
          />
        </div>
        <div class="card__bar" />
        <div class="card__body">
          <PostMeta :page="page" />
          <h3 class="card__title">{{ page.post_title }}</h3>
          <p class="card__excerpt">{{ excerpt(page, 150) }}</p>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useIndexStore } from "@/store";
import PostMeta from "@/components/home/PostMeta.vue";

export default {
  name: "Home",
  components: { PostMeta },
  computed: {
    ...mapState(useIndexStore, ["pages"]),
    featured() {
      return this.pages[0];
    },
    rest() {
      return this.pages.slice(1);
    },
  },
  methods: {
    // "excerpt" comes from wpTheme (page excerpt or first 40 words); older
    // API responses only have post_content. Either way it is cut to fit.
    excerpt(page, max) {
      const text = (page.excerpt || page.post_content || "")
        .replace(/\s+/g, " ")
        .trim();
      if (text.length <= max) return text;
      return text.slice(0, text.lastIndexOf(" ", max)) + " …";
    },
    // "startBildAlt" is the alt text from the WordPress media library.
    imageAlt(page) {
      return page.startBildAlt || "";
    },
  },
};
</script>

<style scoped lang="scss">
.news-title h2 {
  font-size: 1.625rem;
}

.read-more {
  margin-top: auto;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--ffw-red-text);
}

.feature {
  display: grid;
  grid-template-columns: minmax(0, 1.15fr) 8px minmax(0, 1fr);
  background: var(--ffw-panel);
  border-radius: var(--ffw-radius);
  overflow: hidden;
  color: var(--ffw-ink);
  text-decoration: none;

  &__img {
    min-height: 340px;
  }

  &__bar {
    background: var(--ffw-red);
  }

  &__body {
    padding: 28px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  &__title {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1.2;
  }

  &__excerpt {
    margin: 0;
    color: var(--ffw-muted);
  }

  &:hover &__title {
    text-decoration: underline;
  }

  @media (max-width: 640px) {
    grid-template-columns: minmax(0, 1fr);

    &__img {
      min-height: 200px;
    }
    &__bar {
      height: 6px;
    }
    &__body {
      padding: 20px;
    }
    &__title {
      font-size: 1.375rem;
    }
  }
}

.cards {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 24px;
  margin-top: 24px;

  @media (max-width: 640px) {
    grid-template-columns: minmax(0, 1fr);
  }
}

.card {
  display: flex;
  flex-direction: column;
  background: var(--ffw-surface);
  border: 1px solid var(--ffw-line);
  border-radius: var(--ffw-radius);
  overflow: hidden;
  color: var(--ffw-ink);
  text-decoration: none;

  &__img {
    aspect-ratio: 16 / 9;
  }

  &__bar {
    height: 6px;
    background: var(--ffw-red);
  }

  &__body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
  }

  &__title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 800;
    line-height: 1.3;
  }

  &__excerpt {
    margin: 0;
    font-size: 0.9375rem;
    color: var(--ffw-muted);
  }

  &:hover &__title {
    text-decoration: underline;
  }
}
</style>
