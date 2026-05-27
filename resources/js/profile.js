document.addEventListener('DOMContentLoaded', () => {
  const photoInput = document.getElementById('profilePhoto');
  const savePhotoBtn = document.getElementById('savePhotoBtn');
  const previewImg = document.getElementById('profilePhotoPreview');
  const photoPreview = document.getElementById('photoPreview');

  if (!photoInput || !savePhotoBtn || !photoPreview) {
    return;
  }

  let objectUrl = null;

  photoInput.addEventListener('change', () => {
    const file = photoInput.files?.[0];

    if (!file) {
      savePhotoBtn.disabled = true;
      clearProfileFileError();
      return;
    }

    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (!allowedTypes.includes(file.type)) {
      photoInput.value = '';
      savePhotoBtn.disabled = true;
      showProfileFileError('Escolhe uma imagem válida: JPG, PNG, WebP ou GIF.');
      return;
    }

    const maxSize = 2 * 1024 * 1024;

    if (file.size > maxSize) {
      photoInput.value = '';
      savePhotoBtn.disabled = true;
      showProfileFileError('A imagem não pode ter mais de 2 MB.');
      return;
    }

    savePhotoBtn.disabled = false;
    clearProfileFileError();

    updatePhotoPreview(file);
  });

  document.querySelectorAll('.photo-delete-form').forEach((form) => {
    form.addEventListener('submit', (event) => {
      const confirmed = window.confirm('Tens a certeza que queres remover a tua foto de perfil?');

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });

  function updatePhotoPreview(file) {
    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
      objectUrl = null;
    }

    objectUrl = URL.createObjectURL(file);

    let image = previewImg;

    if (!image) {
      image = document.createElement('img');
      image.id = 'profilePhotoPreview';
      image.alt = 'Pré-visualização da nova foto de perfil';
      photoPreview.prepend(image);
    }

    image.src = objectUrl;
    image.alt = file.name;
    image.style.display = 'block';

    const placeholder = photoPreview.querySelector('.photo-placeholder');

    if (placeholder) {
      placeholder.style.display = 'none';
    }
  }

  function showProfileFileError(message) {
    clearProfileFileError();

    const error = document.createElement('p');

    error.className = 'error-text profile-file-error';
    error.textContent = message;

    photoInput.closest('.photo-form')?.appendChild(error);
  }

  function clearProfileFileError() {
    document.querySelectorAll('.profile-file-error').forEach((error) => {
      error.remove();
    });
  }

  window.addEventListener('beforeunload', () => {
    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
    }
  });
});