<template>
  <v-row
    justify="center"
    no-gutters
    class="footerWrapper"
    style="padding: 0 20px"
  >
    <v-col xl="7" lg="9" md="11" sm="12" xs="12">
      <v-row>
        <v-col>
          <div class="smalltitel">Wichtige</div>
          <h3 class="bigtitel">Links</h3>
          <a href="https://www.feuerwehr-muehltal.de/"
            >Freiwillige Feuerwehr Mühltal</a
          ><br />
          <a href="https://www.kfv-dadi.de/"
            >Kreisfeuerwehrverband Darmstadt-Dieburg</a
          ><br />
          <a href="https://www.feuerwehr-nieder-ramstadt.de/"
            >Feuerwehr Nieder-Ramstadt</a
          ><br />
          <a href="https://ff-traisa.de/seite/info-buerger">Infos für Bürger</a
          ><br />
          <a href="https://www.muehltal.de/"
            >Informations-Seite der Gemeinde Mühltal</a
          ><br />
        </v-col>
        <v-col>
          <router-link to="/kontakt">
            <div class="smalltitel">Unser</div>
            <h3 class="bigtitel">Kontakt</h3>
          </router-link>
          <p>
            Freiwillige Feuerwehr Mühltal Traisa<br />
            Im Rosengarten 19<br />
            64367 Mühltal Traisa
          </p>
          <p>
            Telefon: (0 61 51) 14 54 00<br />
            (Nicht immer besetzt)<br />
            Telefax: (0 61 51) 2 79 17 21<br />
            Im Notfall: 112
          </p>
        </v-col>
        <v-col>
          <router-link to="/impressum">
            <div class="smalltitel">Rechtliches/</div>
            <h3 class="bigtitel">Impressum</h3>
          </router-link>
          <p>
            Kein Gewähr auf Richtigkeit/ Aktualität. Inhalte auf dieser Seite
            gehören Feuerwehr Traisa e.V. (sofern nicht anders angegeben)
          </p>
          <p>
            Design und Umsetzung Jan Ackermann
          </p>
        </v-col>
        <v-col>
          <router-link to="/archiv">
            <div class="smalltitel">Unser</div>
            <h3 class="bigtitel">Archiv</h3>
          </router-link>
        </v-col>
      </v-row>
    </v-col>
  </v-row>
</template>

<style type="scss" scoped>
.footerWrapper {
  background-color: #333333;
  width: 100%;
  border-top: 5px solid rgb(175, 74, 69);
}
.smalltitel {
  color: grey;
  font-size: 15px;
}
.bigtitel {
  margin-bottom: 0;
  margin-top: -23px;
  margin-left: -3px;
  color: rgb(175, 74, 69);
  font-size: 50px;
}
a {
  font-size: 0.8rem;
  margin-top: -2px;
  color: grey !important;
  text-decoration: none;
}
p {
  font-size: 0.8rem;
  margin-top: -2px;
  color: grey;
  text-decoration: none;
}
</style>
<script></script>
