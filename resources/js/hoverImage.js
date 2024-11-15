// GERE LA PREVISUALISATION DES IMAGES AU SURVOL DE LEUR NOM DANS LES FORMULAIRES

const selectElement = document.getElementById('images');
const imagePreview = document.getElementById('imagePreview');

// Affiche l'image au survol d'une option
selectElement.addEventListener('mouseover', (event) => {
    const option = event.target;
    if (option.tagName === 'OPTION') {
        imagePreview.src = option.getAttribute('data-image');
        imagePreview.style.display = 'block'; // Affiche l'image
    }
});
// Cache l'image lorsque la souris quitte le <select>
selectElement.addEventListener('mouseleave', () => {
    imagePreview.style.display = 'none';
});