document.addEventListener("DOMContentLoaded", function () {
  const statusElements = document.querySelectorAll(".stock-status");
  statusElements.forEach((el) => {
    const stock = parseInt(el.dataset.stock);
    if (stock > 0) {
      el.textContent = "Disponible";
      el.classList.add("status", "disponible");
    } else {
      el.textContent = "Agotado";
      el.classList.add("status", "agotado");
    }
  });
});