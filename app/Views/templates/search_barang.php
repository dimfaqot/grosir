  <!-- <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content bg-dark text-light">
              <div class="modal-body">
                  <div class="form-floating position-relative">
                      <input type="text" id="searchInput" class="form-control bg-dark text-light" placeholder="Cari...">
                      <label for="searchInput" class="text-secondary">Cari Produk</label>
                      <div id="searchResults" class="bg-dark text-light"></div>
                  </div>
              </div>
          </div>
      </div>
  </div>


  <div class="form-floating mb-3">
      <input type="text" id="productInput" name="product" class="form-control bg-dark text-light" placeholder="Pilih Produk..." readonly>
      <label for="productInput" class="text-secondary">Produk</label>
  </div> -->


  <script>
      let data_selcted = {};
      let mode = 'add'; // ← bisa diganti sesuai kebutuhan
      let data = <?= json_encode(barang($order)); ?>;

      $(document).on('click', ".barang", (e) => {
          e.preventDefault();
          console.log("Ok");
          $('#searchModal').modal('show');
      });

      $('#searchModal').on('shown.bs.modal', () => {
          $('#searchInput').trigger('focus').select();
      });

      $('#searchInput').on('keyup', function() {
          const searchTerm = $(this).val().toLowerCase();
          const resultsContainer = $('#searchResults');

          if (searchTerm.length > 0) {
              const matchedProducts = data.filter(product =>
                  product.barang.toLowerCase().includes(searchTerm)
              );

              if (matchedProducts.length > 0) {
                  let resultsHTML = '';
                  matchedProducts.forEach(e => {
                      resultsHTML += `
                    <div class="result-item" data-id="${e.id}">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">${e.barang}</span>
                            <span class="text-muted">${angka(e.harga)}</span>
                        </div>
                    </div>`;
                  });

                  resultsContainer.html(resultsHTML).show();

                  //   $('.result-item').on('click', function() {
                  //       const id = $(this).data("id");
                  //       handleProductSelection(id);
                  //       $('#searchModal').modal('hide');
                  //       $('#searchInput').val('');
                  //       resultsContainer.hide();
                  //   });
              } else {
                  resultsContainer.html('<div class="result-item text-muted">No data found</div>').show();
              }
          } else {
              resultsContainer.hide();
          }
      });

      function handleProductSelection(id) {
          const selected = data.find(e => e.id == id);
          if (!selected) return;

          data_selcted = selected;

          if (mode === 'add') {
              $('[name="produk"]').val(selected.barang); // atau apapun yg dibutuhkan saat Add
          } else if (mode === 'edit') {
              $('[name="produk"]').val(`[EDIT] ${selected.barang}`); // misalnya berbeda treatment
          }

          console.log('Selected:', data_selcted);
      }

      $(document).on('click', function(e) {
          if (!$(e.target).closest('#searchInput').length && !$(e.target).closest('#searchResults').length) {
              $('#searchResults').hide();
          }
      });

      function adjustResultsWidth() {
          const inputWidth = $('#searchInput').parent().width();
          $('#searchResults').css('width', inputWidth + 'px');
      }

      $('#searchModal').on('shown.bs.modal', adjustResultsWidth);
      $(window).on('resize', adjustResultsWidth);
  </script>