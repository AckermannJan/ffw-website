<template>
  <Report>
    <template v-slot:headline> Technik </template
    ><template v-slot:content style="text-overflow: ellipsis;">
      <h2>Fahrzeuge</h2>
      <v-row>
        <v-col>
          <router-link to="/technik/kdow" style="text-decoration: none">
            <div class="item">
              <div class="item__img">
                <img
                  src="https://wordpress.feuerwehr-traisa.de/wp-content/themes/FFW/imgs/cars/kdow.jpg"
                  alt="KDOW"
                />
              </div>
              <div class="item__name">KDOW</div>
            </div>
          </router-link>
        </v-col>
        <v-col>
          <router-link to="/technik/mtf" style="text-decoration: none">
            <div class="item">
              <div class="item__img">
                <img
                  src="https://wordpress.feuerwehr-traisa.de/wp-content/uploads/2016/08/Vorsschaukleinnn.png"
                  alt="MTF"
                />
              </div>
              <div class="item__name">MTF</div>
            </div>
          </router-link>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <router-link to="/technik/lf-106" style="text-decoration: none">
            <div class="item">
              <div class="item__img">
                <img
                  src="https://wordpress.feuerwehr-traisa.de/wp-content/themes/FFW/imgs/cars/lf106.jpg"
                  alt="LF 10/6"
                />
              </div>
              <div class="item__name">LF 10</div>
            </div>
          </router-link>
        </v-col>
        <v-col>
          <router-link to="/technik/mlf" style="text-decoration: none">
            <div class="item">
              <div class="item__img">
                <img
                  src="https://wordpress.feuerwehr-traisa.de/wp-content/uploads/2025/05/MLF_Icon.png"
                  alt="MLF"
                />
              </div>
              <div class="item__name">MLF</div>
            </div>
          </router-link>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <router-link to="/technik/anhaenger" style="text-decoration: none">
            <div class="item">
              <div class="item__img">
                <img
                  src="https://wordpress.feuerwehr-traisa.de/wp-content/themes/FFW/imgs/cars/anhaenger.jpg"
                  alt="Anhänger"
                />
              </div>
              <div class="item__name">Anhänger</div>
            </div>
          </router-link>
        </v-col>
        <v-col>
          <router-link to="/technik/rettungsboot" style="text-decoration: none">
            <div class="item">
              <div class="item__img">
                <img
                  src="https://wordpress.feuerwehr-traisa.de/wp-content/uploads/2019/06/Bild-Boot.png"
                  alt="Boot"
                />
              </div>
              <div class="item__name">Rettungsboot</div>
            </div>
          </router-link>
        </v-col>
      </v-row>

      <h2>Feuerwehrhaus</h2>
      <v-row>
        <v-col cols="12" sm="6">
          <router-link
            to="/technik/feuerwehrhaus"
            style="text-decoration: none"
          >
            <div class="item item--small">
              <div class="item__img">
                <img
                  src="https://wordpress.feuerwehr-traisa.de/wp-content/themes/FFW/imgs/fwhaus-aussen.jpg"
                  alt="Feuerwehrhaus"
                />
              </div>
              <div class="item__name">Feuerwehrhaus</div>
            </div>
          </router-link>
        </v-col>
      </v-row>
    </template>
  </Report>
</template>

<script>
import Report from "../components/partials/Report/Report";
export default {
  name: "Technik",
  components: { Report },
  metaInfo() {
    return {
      title: "Feuerwehr Mühltal Traisa | Technik",
      meta: [
        {
          name: "title",
          content: "Feuerwehr Mühltal Traisa | Technik"
        }
      ]
    };
  }
};
</script>

<style scoped lang="scss">
.item {
  text-overflow: ellipsis;
  overflow: hidden;
  &__img {
    border-radius: 90px;
    overflow: hidden;
    height: 90px;
    width: 90px;
    border: solid 5px rgba(175, 74, 69, 0.4);
    background-color: #fff;
    float: left;
    img {
      height: 60px;
      margin-top: 10px;
    }
  }
  &__name {
    font-size: 1.25rem;
    font-weight: bold;
    padding-top: 25px;
    margin-left: 95px;
    color: black;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
  &--small {
    .item__img {
      border-radius: 75px;
      height: 65px;
      width: 65px;
      img {
        height: 60px;
        margin-top: 0;
      }
    }
    .item__name {
      font-size: 1rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  }
}
</style>
