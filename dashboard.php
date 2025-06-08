<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RFM - Dashboard</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" type="text/css" href="css/index.css" />
    <link rel="stylesheet" type="text/css" href="css/styles.css">
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
      <div class="container">
        <a class="navbar-brand fw-bold" href="#">Remote File Manager</a>
      </div>
      <p><a href="logout.php" class="btn btn-danger">Logout</a></p>
    </nav>

    <main class="container">
      <!-- Upload Card -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h4 class="card-title mb-3">Upload a File</h4>

          <form id="uploadForm" enctype="multipart/form-data" class="mb-3">
            <div id="dropArea" tabindex="0">
              Drag & Drop file here or click to select
            </div>
            <input
              type="file"
              name="file[]"
              id="fileInput"
              class="form-control mt-3"
              required
              multiple
            />
            <button type="submit" class="btn btn-primary mt-3">Upload</button>
          </form>

          <div
            class="progress mb-3"
            style="height: 20px; display: none"
            id="progressContainer"
          >
            <div
              class="progress-bar progress-bar-striped progress-bar-animated"
              role="progressbar"
              style="width: 0%"
              aria-valuemin="0"
              aria-valuemax="100"
              id="uploadProgress"
            ></div>
          </div>

          <div id="alertPlaceholder"></div>
        </div>
      </div>

      <!-- Files List Card -->
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3">Uploaded Files</h4>

          <!-- Search bar -->
          <div class="mb-3">
            <input
              type="text"
              id="searchInput"
              class="form-control"
              placeholder="Search files by name or type..."
            />
          </div>

          <div class="table-wrap">
            <table class="table table-striped table-hover align-middle">
              <thead>
                <tr>
                  <th>File Name</th>
                  <th>Type</th>
                  <th>Size</th>
                  <th>Uploaded At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="fileList">
                <!-- Files will be dynamically inserted here -->
              </tbody>
            </table>
            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-3">
              <ul class="pagination justify-content-center" id="pagination">
                <!-- Pagination items will be dynamically inserted here -->
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </main>

    <!-- Toast Container -->

    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
      <div id="toastContainer" class="toast-container"></div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1">
      <div class="modal-dialog">
        <form id="updateForm" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update File Info</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body">
            
            <input type="hidden" id="updateFileId" name="id" />
            <div class="mb-3">
              <label for="updateName" class="form-label">File Name</label>
              <input
                type="text"
                class="form-control"
                id="updateName"
                name="original_name"
                required
              />
            </div>
            <div class="mb-3">
              <label for="updateFormat" class="form-label">Format</label>
              <input
                type="text"
                class="form-control"
                id="updateFormat"
                name="format"
                required
              />
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning">Update</button>
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <footer class="text-center text-muted mt-5 mb-3">RFM
      &copy; <span id="footerYear"></span> Remote File Manager | Powered by
      iTechnology
    </footer>

    <script src="js/index.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>