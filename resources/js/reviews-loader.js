/** Génére les étoiles pour les notes */
function generateStars(rating) {
    let starsHtml = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating - fullStars >= 0.5;

    for (let i = 0; i < fullStars; i++) {
        starsHtml += '<i class="fas fa-star text-yellow-400"></i>';
    }

    if (hasHalfStar) {
        starsHtml += '<i class="fas fa-star-half-alt text-yellow-400"></i>';
    }

    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    for (let i = 0; i < emptyStars; i++) {
        starsHtml += '<i class="far fa-star text-gray-300"></i>';
    }

    return starsHtml;
}

/** Formate date => YYYY-MM-DD en DD/MM/YYYY */
function formatDate(dateString) {
    if (!dateString) return 'Date inconnue';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

/** Échappe les caractères HTML => contre le XSS (Cross-Site Scripting) mais surtout pour éviter les problèmes d'affichage */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/** Récupére et affiche les avis */
window.fetchAndDisplayReviews = function(driverId, container) {
    console.log('fetchAndDisplayReviews appelé avec driverId:', driverId);

    if (!driverId || !container) {
        console.error('driverId ou container manquant:', { driverId, container });
        return;
    }

    // Loader
    container.innerHTML = `
        <div class="text-center text-gray-500 py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto"></div>
            <p class="mt-4">Chargement des avis...</p>
        </div>`;

    fetch(`/api/avis/conducteur/${driverId}`)
        .then(response => {
            console.log('Réponse reçue:', response.status);
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            const { reviews, average_rating, total_ratings } = data;

            if (!reviews || reviews.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-comment-slash text-4xl mb-4"></i>
                        <p>Aucun avis pour le moment</p>
                    </div>`;
                return;
            }

            // Génération des avis
            let reviewsHtml = '';
            reviews.forEach(review => {
                const reviewDate = formatDate(review.date);
                const stars = generateStars(review.note || 0);
                const authorName = review.user && review.user.name ? escapeHtml(review.user.name) : 'Anonyme';
                const reviewText = review.review ? escapeHtml(review.review) : '';
                const commentText = review.comment ? escapeHtml(review.comment) : '';

                reviewsHtml += `
                    <div class="review-card bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center">
                                <div class="font-semibold text-gray-800 mr-3">${authorName}</div>
                                <div class="flex items-center">
                                    ${stars}
                                    <span class="ml-2 text-sm font-semibold text-gray-700">${review.note}/5</span>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">${reviewDate}</span>
                        </div>
                        ${reviewText ? `<p class="text-gray-700 mt-2">"${reviewText}"</p>` : ''}
                        ${commentText ? `<p class="text-gray-600 mt-1 text-sm italic">${commentText}</p>` : ''}
                    </div>`;
            });

            container.innerHTML = reviewsHtml;
            console.log(`${reviews.length} avis affichés avec succès`);
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des avis:', error);
            container.innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                    <p>Erreur lors du chargement des avis.</p>
                    <p class="text-sm mt-2">Veuillez réessayer plus tard.</p>
                </div>`;
        });
}
