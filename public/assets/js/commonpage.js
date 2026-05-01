(() => {
  const badge = document.getElementById("cartBadge");
  if (!badge) return;

  let count = 0;

  document.querySelectorAll(".rental-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      count += 1;
      badge.textContent = count;

      // small feedback
      btn.style.transform = "translateY(-2px)";
      setTimeout(() => (btn.style.transform = ""), 140);
    });
  });
})();

function goToDetails(itemKey) {
  console.log(itemKey)
    window.location.href = `/rental/${encodeURIComponent(itemKey)}`;
}
