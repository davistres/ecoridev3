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
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

/** Récupére et affiche les avis */
window.fetchAndDisplayReviews = function(driverId, container) {
    if (!driverId || !container) {
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
            if (!response.ok) {
                throw new Error('La réponse du réseau n\'était pas correcte');
            }
            return response.json();
        })
        .then(data => {
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
                const reviewDate = review.date ? formatDate(review.date) : 'Date inconnue';
                const stars = generateStars(review.note);
                const authorName = review.user ? review.user.name : 'Anonyme';

                reviewsHtml += `
                    <div class="review-card bg-gray-50 rounded-lg p-4">
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
                        ${review.review ? `<p class="text-gray-600 italic">"${review.review}"</p>` : ''}
                        ${review.comment ? `<p class="text-gray-600 italic">"${review.comment}"</p>` : ''}
                    </div>`;
            });

            container.innerHTML = reviewsHtml;
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des avis:', error);
            container.innerHTML = '<div class="text-center text-red-500 py-8">Erreur lors du chargement des avis. Veuillez réessayer plus tard.</div>';
        });
}
