const piece = document.getElementById("piece");
const conteneur = document.getElementById("captcha-container");
const input = document.getElementById("captcha_puzzle");
const message = document.getElementById("message-captcha");
const bouton = document.querySelector('button[type="submit"]');

bouton.disabled = true;

let indices = [0, 1, 2, 3, 4, 5, 6, 7, 8];

for (let i = indices.length - 1; i > 0; i--) {
  const j = Math.floor(Math.random() * (i + 1));
  let temp = indices[i];
  indices[i] = indices[j];
  indices[j] = temp;
}

indices.forEach(function (index) {
  const piece = document.createElement("div");

  const col = index % 3;
  const ligne = Math.floor(index / 3);

  piece.style.width = "60px";
  piece.style.height = "60px";
  piece.style.backgroundImage = "url('images/A.png')";
  piece.style.backgroundSize = "180px 180px";
  piece.style.backgroundPosition = "-" + col * 60 + "px -" + ligne * 60 + "px";
  piece.style.border = "1px solid #c8a415";
  piece.style.cursor = "grab";
  piece.draggable = true;
  piece.dataset.index = index;

  conteneur.appendChild(piece);
});

let pieceDeplacee = null;

conteneur.addEventListener("dragstart", function (e) {
  pieceDeplacee = e.target;
  e.target.style.opacity = "0.5";
});

conteneur.addEventListener("dragend", function (e) {
  e.target.style.opacity = "1";
});

conteneur.addEventListener("dragover", function (e) {
  e.preventDefault();
});

conteneur.addEventListener("drop", function (e) {
  e.preventDefault();

  if (e.target === pieceDeplacee) return;
  if (!e.target.dataset.index) return;

  const parent = conteneur;
  const cible = e.target;

  const divs = Array.from(parent.children);
  const posDraggee = divs.indexOf(pieceDeplacee);
  const posCible = divs.indexOf(cible);

  if (posDraggee < posCible) {
    parent.insertBefore(pieceDeplacee, cible.nextSibling);
    parent.insertBefore(cible, parent.children[posDraggee]);
  } else {
    parent.insertBefore(pieceDeplacee, cible);
    parent.insertBefore(cible, parent.children[posDraggee + 1]);
  }

  verifier();
});

function verifier() {
  const pieces = conteneur.querySelectorAll("div");
  let correct = true;

  for (let i = 0; i < pieces.length; i++) {
    if (parseInt(pieces[i].dataset.index) !== i) {
      correct = false;
      break;
    }
  }

  if (correct) {
    message.style.color = "#2ecc71";
    message.textContent = "Puzzle complété !";
    input.value = "1";
    bouton.disabled = false;
  } else {
    message.style.color = "#e74c3c";
    message.textContent = "Continue...";
    input.value = "0";
    bouton.disabled = true;
  }
}
