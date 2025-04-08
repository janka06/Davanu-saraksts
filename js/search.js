document.getElementById("searchInput").addEventListener("keyup", function() {
    let query = this.value.toLowerCase();

    let items = document.querySelectorAll(".ieraksts");

    items.forEach(function(item) {
        let name = item.getAttribute("data-name").toLowerCase();

        if (name.includes(query)) {
            item.style.display = "block"; // Show matching items
        } else {
            item.style.display = "none"; // Hide non-matching items
        }
    });
});
