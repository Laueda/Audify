const DEEZER_BASE = "http://localhost:8088/audify/deezer.php?endpoint=";
const champRecherche = document.querySelector("#champ-recherche");
const boutonRecherche = document.querySelector("#btn-recherche");
const zoneStatut = document.querySelector("#zone-statut");
const zoneErreur = document.querySelector("#zone-erreur");
const listeArtistes = document.querySelector("#liste-artistes");
const boutonsGenre = document.querySelectorAll(".btn-genre");

const genres_ids = {
  Pop: 132,
  Rock: 152,
  Jazz: 129,
  Rap: 116,
  Classique: 98,
  Electro: 106,
  "R&B": 165,
  Metal: 464,
  Folk: 466,
  Soul: 169,
};

function formaterAbonnes(nb) {
  if (nb >= 1000000) return (nb / 1000000).toFixed(1) + "M fans";
  if (nb >= 1000) return Math.floor(nb / 1000) + "K fans";
  return nb + " fans";
}

function creerCarteArtiste(artiste) {
  const carte = document.createElement("article");
  carte.classList.add("carte-artiste");
  const image = artiste.picture_medium || artiste.picture || "";
  const fans = artiste.nb_fan || 0;
  const divVisuel = document.createElement("div");
  divVisuel.className = "visuel-artiste";
  const img = document.createElement("img");
  img.src = image;
  img.alt = artiste.name;
  divVisuel.appendChild(img);
  const divMeta = document.createElement("div");
  divMeta.className = "meta-artiste";
  const h2 = document.createElement("h2");
  h2.textContent = artiste.name;
  const p = document.createElement("p");
  p.className = "fans-artiste";
  p.textContent = formaterAbonnes(fans);
  divMeta.appendChild(h2);
  divMeta.appendChild(p);
  carte.appendChild(divVisuel);
  carte.appendChild(divMeta);
  carte.addEventListener("click", function () {
    window.location.href =
      "artiste.php?id=" + artiste.id + "&name=" + artiste.name;
  });
  return carte;
}

function afficherArtistes(artistes) {
  listeArtistes.textContent = "";
  for (const artiste of artistes) {
    const carte = creerCarteArtiste(artiste);
    listeArtistes.appendChild(carte);
  }
}

function melangerTableau(tableau) {
  const copie = [];
  for (const item of tableau) {
    copie.push(item);
  }
  for (let i = copie.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    const temp = copie[i];
    copie[i] = copie[j];
    copie[j] = temp;
  }
  return copie;
}

async function chargerArtistesParGenre(genre) {
  const id = genres_ids[genre];
  zoneStatut.textContent = "Chargement des artistes " + genre + "...";
  zoneErreur.textContent = "";
  zoneErreur.hidden = true;
  listeArtistes.textContent = "";
  try {
    const response = await fetch(DEEZER_BASE + "genre/" + id + "/artists");
    if (!response.ok) {
      zoneErreur.textContent = "Erreur HTTP " + response.status;
      zoneErreur.hidden = false;
      zoneStatut.textContent = "Erreur de chargement.";
      return;
    }
    const data = await response.json();
    if (!data.data || data.data.length === 0) {
      zoneStatut.textContent = "Aucun artiste trouvé.";
      return;
    }
    const melanges = melangerTableau(data.data);
    const selectionnes = [];
    for (let i = 0; i < 12 && i < melanges.length; i++) {
      selectionnes.push(melanges[i]);
    }
    afficherArtistes(selectionnes);
    zoneStatut.textContent =
      selectionnes.length + " artiste(s) " + genre + " affichés.";
  } catch (erreur) {
    zoneErreur.textContent = "Impossible de charger les artistes.";
    zoneErreur.hidden = false;
    zoneStatut.textContent = "Erreur de chargement.";
  }
}

async function chargerArtistesPopulaires() {
  zoneStatut.textContent = "Chargement des artistes populaires...";
  zoneErreur.textContent = "";
  zoneErreur.hidden = true;
  listeArtistes.textContent = "";
  try {
    const response = await fetch(DEEZER_BASE + "chart/0/artists?limit=50");
    if (!response.ok) {
      zoneErreur.textContent = "Erreur HTTP " + response.status;
      zoneErreur.hidden = false;
      zoneStatut.textContent = "Erreur de chargement.";
      return;
    }
    const data = await response.json();
    if (!data.data || data.data.length === 0) {
      zoneStatut.textContent = "Aucun artiste trouvé.";
      return;
    }
    const melanges = melangerTableau(data.data);
    const selectionnes = [];
    for (let i = 0; i < 12 && i < melanges.length; i++) {
      selectionnes.push(melanges[i]);
    }
    afficherArtistes(selectionnes);
    zoneStatut.textContent =
      selectionnes.length + " artiste(s) populaires affichés.";
  } catch (erreur) {
    zoneErreur.textContent = "Impossible de charger les artistes.";
    zoneErreur.hidden = false;
    zoneStatut.textContent = "Erreur de chargement.";
  }
}

async function rechercherArtiste(terme) {
  if (terme === "") return;
  zoneStatut.textContent = "Recherche de " + terme + "...";
  zoneErreur.textContent = "";
  zoneErreur.hidden = true;
  listeArtistes.textContent = "";
  try {
    const response = await fetch(
      DEEZER_BASE + "search/artist?q=" + terme + "&limit=20",
    );
    if (!response.ok) {
      zoneErreur.textContent = "Erreur HTTP " + response.status;
      zoneErreur.hidden = false;
      zoneStatut.textContent = "Erreur de chargement.";
      return;
    }
    const data = await response.json();
    if (!data.data || data.data.length === 0) {
      zoneStatut.textContent = "Aucun artiste trouvé pour " + terme + ".";
      return;
    }
    afficherArtistes(data.data);
    zoneStatut.textContent =
      data.data.length + " artiste(s) trouvés pour " + terme + ".";
  } catch (erreur) {
    zoneErreur.textContent = "Impossible de charger les artistes.";
    zoneErreur.hidden = false;
    zoneStatut.textContent = "Erreur de chargement.";
  }
}

boutonRecherche.addEventListener("click", function () {
  rechercherArtiste(champRecherche.value);
});

champRecherche.addEventListener("keydown", function (e) {
  if (e.key === "Enter") {
    rechercherArtiste(champRecherche.value);
  }
});

for (const btn of boutonsGenre) {
  btn.addEventListener("click", function () {
    champRecherche.value = "";
    chargerArtistesParGenre(btn.dataset.genre);
  });
}

const parametresURL = new URLSearchParams(window.location.search);
const termeURL = parametresURL.get("q");
if (termeURL && termeURL.trim() !== "") {
  champRecherche.value = termeURL;
  rechercherArtiste(termeURL);
} else {
  chargerArtistesPopulaires();
}
