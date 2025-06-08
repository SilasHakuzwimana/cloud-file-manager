document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("footerYear").textContent = new Date().getFullYear();

  const uploadForm = document.getElementById("uploadForm");
  const fileInput = document.getElementById("fileInput");
  const dropArea = document.getElementById("dropArea");
  const progressContainer = document.getElementById("progressContainer");
  const uploadProgress = document.getElementById("uploadProgress");
  const alertPlaceholder = document.getElementById("alertPlaceholder");
  const fileList = document.getElementById("fileList");
  const searchInput = document.getElementById("searchInput");
  const pagination = document.getElementById("pagination");

  const rowsPerPage = 4;
  let currentPage = 1;
  let allRows = [];

  function showAlert(message, type = "success") {
    const toastId = "toast" + Date.now();
    const toast = document.createElement("div");
    toast.className = `toast align-items-center text-bg-${type} border-0`;
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;
    const container = document.getElementById("toastContainer");
    container.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
    bsToast.show();

    toast.addEventListener("hidden.bs.toast", () => {
      toast.remove();
    });
  }

  // Drag & Drop handlers
  ["dragenter", "dragover"].forEach((eventName) => {
    dropArea.addEventListener(eventName, (e) => {
      e.preventDefault();
      e.stopPropagation();
      dropArea.classList.add("dragover");
    });
  });

  ["dragleave", "drop"].forEach((eventName) => {
    dropArea.addEventListener(eventName, (e) => {
      e.preventDefault();
      e.stopPropagation();
      dropArea.classList.remove("dragover");
    });
  });

  dropArea.addEventListener("drop", (e) => {
    const files = e.dataTransfer.files;
    if (files.length) {
      fileInput.files = files;
    }
  });

  dropArea.addEventListener("click", () => fileInput.click());

  uploadForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!fileInput.files.length) {
      showAlert("Please select at least one file to upload.", "warning");
      return;
    }

    const formData = new FormData();

    // Append all selected files using "file[]" key (matches PHP)
    for (let i = 0; i < fileInput.files.length; i++) {
      formData.append("file[]", fileInput.files[i]);
    }

    progressContainer.style.display = "block";
    uploadProgress.style.width = "0%";

    try {
      const response = await fetch("upload.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) throw new Error("Network response was not ok");

      // Since PHP returns an array of results, parse as an array
      const results = await response.json();

      // results is an array, show success/fail for each file
      results.forEach((result) => {
        if (result.success) {
          showAlert(
            `File "${result.original_name}" uploaded successfully!`,
            "success"
          );
        } else {
          showAlert(
            `Failed to upload "${result.original_name}": ${result.error}`,
            "danger"
          );
        }
      });

      loadFiles();
      uploadForm.reset();
    } catch (err) {
      showAlert("Error: " + err.message, "danger");
    } finally {
      progressContainer.style.display = "none";
    }
  });

  function formatBytes(bytes) {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
  }

  async function loadFiles() {
    try {
      const response = await fetch("fetch_files.php");
      if (!response.ok) throw new Error("Failed to fetch files");
      const result = await response.json();

      if (!result.files.length) {
        fileList.innerHTML = `<tr><td colspan="5" class="text-center text-muted">No files uploaded yet.</td></tr>`;
        pagination.innerHTML = "";
        return;
      }

      allRows = result.files.map((file) => {
        const tr = document.createElement("tr");
        tr.setAttribute("data-id", file.id);
        const fileNameTd = document.createElement("td");
        const fileLink = document.createElement("a");
        fileLink.href = file.file_url;
        fileLink.target = "_blank";
        fileLink.rel = "noopener noreferrer";
        fileLink.textContent = file.original_name;
        fileNameTd.appendChild(fileLink);
        tr.appendChild(fileNameTd);

        const typeTd = document.createElement("td");
        typeTd.textContent = file.format;
        tr.appendChild(typeTd);

        const sizeTd = document.createElement("td");
        sizeTd.textContent = formatBytes(file.bytes);
        tr.appendChild(sizeTd);

        const dateTd = document.createElement("td");
        dateTd.textContent = new Date(file.created_at + " UTC").toLocaleString(
          "en-KE",
          {
            timeZone: "Africa/Kigali",
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
          }
        );
        tr.appendChild(dateTd);

        const actionTd = document.createElement("td");

        const viewBtn = document.createElement("a");
        viewBtn.href = file.file_url;
        viewBtn.target = "_blank";
        viewBtn.className = "btn btn-sm btn-outline-primary me-1";
        viewBtn.textContent = "View";
        actionTd.appendChild(viewBtn);

        const updateBtn = document.createElement("button");
        updateBtn.className = "btn btn-sm btn-outline-warning me-1";
        updateBtn.textContent = "Update";
        updateBtn.setAttribute("data-id", file.id);
        updateBtn.onclick = () => showUpdateModal(file);
        actionTd.appendChild(updateBtn);

        const delBtn = document.createElement("button");
        delBtn.className = "btn btn-sm btn-outline-danger";
        delBtn.textContent = "Delete";
        delBtn.onclick = () => {
          if (confirm("Are you sure you want to delete this file?")) {
            deleteFile(file.public_id);
          }
        };
        actionTd.appendChild(delBtn);

        tr.appendChild(actionTd);
        return tr;
      });

      currentPage = 1;
      renderTableAndPagination();
    } catch (err) {
      showAlert("Error loading files: " + err.message, "danger");
    }
  }

  function renderTableAndPagination() {
    const query = searchInput.value.trim().toLowerCase();
    const filteredRows = allRows.filter((tr) => {
      const fileName = tr.children[0].textContent.toLowerCase();
      const fileType = tr.children[1].textContent.toLowerCase();
      return fileName.includes(query) || fileType.includes(query);
    });

    const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
    if (currentPage > totalPages) currentPage = totalPages;

    fileList.innerHTML = "";
    const start = (currentPage - 1) * rowsPerPage;
    const pageRows = filteredRows.slice(start, start + rowsPerPage);
    pageRows.forEach((row) => fileList.appendChild(row));

    renderPagination(totalPages);
  }

  function renderPagination(totalPages) {
    pagination.innerHTML = "";

    for (let i = 1; i <= totalPages; i++) {
      const li = document.createElement("li");
      li.className = "page-item " + (i === currentPage ? "active" : "");
      const a = document.createElement("a");
      a.className = "page-link";
      a.href = "#";
      a.textContent = i;
      a.addEventListener("click", (e) => {
        e.preventDefault();
        currentPage = i;
        renderTableAndPagination();
      });
      li.appendChild(a);
      pagination.appendChild(li);
    }
  }

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      currentPage = 1;
      renderTableAndPagination();
    });
  }

  function showUpdateModal(file) {
    document.getElementById("updateFileId").value = file.id;
    document.getElementById("updateName").value = file.original_name;
    document.getElementById("updateFormat").value = file.format;

    const modal = new bootstrap.Modal(document.getElementById("updateModal"));
    modal.show();
  }

  document
    .getElementById("updateForm")
    .addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      try {
        const response = await fetch("update_files.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();
        if (result.success) {
          showAlert("File info updated successfully.", "success");
          bootstrap.Modal.getInstance(
            document.getElementById("updateModal")
          ).hide();
          loadFiles();
        } else {
          showAlert("Update failed: " + result.error, "danger");
        }
      } catch (err) {
        showAlert("Update error: " + err.message, "danger");
      }
    });

  async function deleteFile(publicId) {
    const formData = new FormData();
    formData.append("public_id", publicId);

    try {
      const response = await fetch("delete_file.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) throw new Error("Network response was not ok");

      const result = await response.json();
      if (result.success) {
        showAlert("File deleted successfully.", "success");
        loadFiles();
      } else {
        showAlert("Delete failed: " + result.error, "danger");
      }
    } catch (err) {
      showAlert("Delete error: " + err.message, "danger");
    }
  }

  loadFiles(); // Initial load
});

// Progress bar update function with percentage display
function updateProgress(percentage) {
  const progressBar = document.getElementById("uploadProgress");
  const progressText = document.getElementById("progressText");
  const progressContainer = document.getElementById("progressContainer");

  if (percentage > 0) {
    progressContainer.style.display = "block";
  }

  progressBar.style.width = percentage + "%";
  progressBar.setAttribute("aria-valuenow", percentage);
  progressText.textContent = Math.round(percentage) + "%";

  if (percentage >= 100) {
    setTimeout(() => {
      progressContainer.style.display = "none";
    }, 1000);
  }
}

// Toast notification function with proper positioning
function showToast(message, type = "success") {
  const toastContainer = document.getElementById("toastContainer");
  const toastId = "toast-" + Date.now();

  const toastHtml = `
          <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
              <div class="toast-body">
                ${message}
              </div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
          </div>
        `;

  toastContainer.insertAdjacentHTML("beforeend", toastHtml);

  const toastElement = document.getElementById(toastId);
  const toast = new bootstrap.Toast(toastElement, {
    autohide: true,
    delay: 5000,
  });

  toast.show();

  // Remove toast element after it's hidden
  toastElement.addEventListener("hidden.bs.toast", () => {
    toastElement.remove();
  });
}
