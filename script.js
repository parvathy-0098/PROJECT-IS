 // Modal functionality
 const modal = document.getElementById("cartModal");
 const closeModal = document.getElementById("closeModal");

 const addToCartBtns = document.querySelectorAll(".add-to-cart-btn");

 addToCartBtns.forEach(btn => {
     btn.addEventListener("click", function() {
         const productName = this.getAttribute("data-name");
         const productPrice = this.getAttribute("data-price");

         document.getElementById("product_name").value = productName;
         document.getElementById("product_price").value = productPrice;

         modal.style.display = "block";
     });
 });

 closeModal.addEventListener("click", function() {
     modal.style.display = "none";
 });

 window.addEventListener("click", function(event) {
     if (event.target == modal) {
         modal.style.display = "none";
     }
 });