<template>
  <v-container>
    <v-row>
      <v-col cols="12" sm="6">
        <router-link to="kindergruppen/jugendfeuerwehr/info-2">
          <div class="box">
            <img
              src="http://wordpress.feuerwehr-traisa.de/wp-content/uploads/2025/05/Gruppenb_jugend-scaled.jpg"
              alt="Gruppenbild der Jugend"
            />
            <h2>zur Jugendfeuerwehr</h2>
          </div>
        </router-link>
      </v-col>
      <v-col cols="12" sm="6">
        <router-link to="kindergruppen/wichtelwehr/info">
          <div class="box">
            <img
              src="http://wordpress.feuerwehr-traisa.de/wp-content/uploads/2025/05/Gruppenb_wichtel-scaled.jpg"
              alt="Gruppenbild der Jugend"
            />
            <h2>zur Wichtelwehr</h2>
          </div>
        </router-link>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "Index",
  metaInfo() {
    return {
      title: "Feuerwehr Mühltal Traisa | Kindergruppen",
      meta: [
        {
          name: "title",
          content: "Feuerwehr Mühltal Traisa | Kindergruppen"
        }
      ]
    };
  }
};
</script>

<style scoped lang="scss">
.box {
  background-color: #af4a45;
  margin-bottom: 10px;
  border-radius: 3px;
  position: relative;
  cursor: pointer;
  text-decoration: none;
  text-align: center;
  & img {
    width: 100%;
  }
  & h2 {
    display: block;
    padding-top: 10px;
    padding-bottom: 20px;
    color: #fff;
    text-decoration: underline;
  }
}
</style>
