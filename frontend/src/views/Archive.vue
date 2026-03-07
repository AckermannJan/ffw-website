<template>
  <div>
    <v-row
      v-for="(page, index) in archiveData"
      no-gutters
      :key="index"
      class="page mb-10"
    >
      <v-col
        class="page__img"
        v-if="['lg', 'xl', 'md'].includes($vuetify.breakpoint.name)"
        align-self="center"
      >
        <img :src="page.startBild" alt="Vorschaubild Seite" />
      </v-col>
      <v-col cols="12" md="4" lg="5" class="page__text">
        <h2 class="mb-3">{{ page.post_title }}</h2>
        <p class="page__text--hide">
          {{ page.post_content.substr(0, 250) + "..." }}
        </p>
        <v-btn :to="'/seite/' + page.post_name" color="#af4a45" class="btn"
          >Weiterlesen</v-btn
        >
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  name: "Archive",
  computed: {
    ...mapGetters("archive", {
      isLoading: "isLoading",
      archiveData: "archiveData"
    })
  },
  mounted() {
    this.loadArchive();
  },
  methods: {
    ...mapActions("archive", {
      loadArchive: "loadArchive"
    })
  },
  metaInfo() {
    return {
      title: "Feuerwehr Mühltal Traisa | Archiv",
      meta: [
        {
          name: "title",
          content: "Feuerwehr Mühltal Traisa | Archiv"
        }
      ]
    };
  }
};
</script>

<style scoped lang="scss">
.page {
  background-color: rgba(59, 59, 59, 0.34);
  &__img {
    margin-bottom: -7px;
    img {
      width: 100%;
      &::after {
      }
    }
  }
  &__text {
    border-left: solid 10px #af4a45;
    position: relative;
    padding-bottom: 40px;
    &--hide {
      text-overflow: ellipsis;
      max-height: 190px;
      overflow: hidden;
    }
    h2 {
      margin-top: 30px;
      padding-left: 20px;
      line-height: 1.4rem;
    }
    p {
      padding-left: 20px;
    }
    &::after {
      content: "";
      position: absolute;
      width: 0;
      height: 0;
      border-style: solid;
      border-width: 7.5px 0 7.5px 10px;
      border-color: transparent transparent transparent #af4a45;
      left: 0;
      top: 30px;
    }
  }
}

.btn {
  position: absolute;
  right: 0;
  bottom: 0;
  margin: {
    right: 15px;
    bottom: 15px;
  }
  color: #fff;
}
</style>
