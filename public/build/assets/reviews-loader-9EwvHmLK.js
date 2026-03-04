function p(e){let t="";const s=Math.floor(e),r=e-s>=.5;for(let n=0;n<s;n++)t+='<i class="fas fa-star text-yellow-400"></i>';r&&(t+='<i class="fas fa-star-half-alt text-yellow-400"></i>');const o=5-s-(r?1:0);for(let n=0;n<o;n++)t+='<i class="far fa-star text-gray-300"></i>';return t}function x(e){return e?new Date(e).toLocaleDateString("fr-FR",{day:"2-digit",month:"2-digit",year:"numeric"}):"Date inconnue"}function l(e){if(!e)return"";const t=document.createElement("div");return t.textContent=e,t.innerHTML}window.fetchAndDisplayReviews=function(e,t){if(console.log("fetchAndDisplayReviews appelé avec driverId:",e),!e||!t){console.error("driverId ou container manquant:",{driverId:e,container:t});return}t.innerHTML=`
        <div class="text-center text-gray-500 py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto"></div>
            <p class="mt-4">Chargement des avis...</p>
        </div>`,fetch(`/api/avis/conducteur/${e}`).then(s=>{if(console.log("Réponse reçue:",s.status),!s.ok)throw new Error(`Erreur HTTP: ${s.status}`);return s.json()}).then(s=>{console.log("Données reçues:",s);const{reviews:r,average_rating:o,total_ratings:n}=s;if(!r||r.length===0){t.innerHTML=`
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-comment-slash text-4xl mb-4"></i>
                        <p>Aucun avis pour le moment</p>
                    </div>`;return}let i="";r.forEach(a=>{const d=x(a.date),u=p(a.note||0),f=a.user&&a.user.name?l(a.user.name):"Anonyme",c=a.review?l(a.review):"",m=a.comment?l(a.comment):"";i+=`
                    <div class="review-card bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center">
                                <div class="font-semibold text-gray-800 mr-3">${f}</div>
                                <div class="flex items-center">
                                    ${u}
                                    <span class="ml-2 text-sm font-semibold text-gray-700">${a.note}/5</span>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">${d}</span>
                        </div>
                        ${c?`<p class="text-gray-700 mt-2">"${c}"</p>`:""}
                        ${m?`<p class="text-gray-600 mt-1 text-sm italic">${m}</p>`:""}
                    </div>`}),t.innerHTML=i,console.log(`${r.length} avis affichés avec succès`)}).catch(s=>{console.error("Erreur lors de la récupération des avis:",s),t.innerHTML=`
                <div class="text-center text-red-500 py-8">
                    <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                    <p>Erreur lors du chargement des avis.</p>
                    <p class="text-sm mt-2">Veuillez réessayer plus tard.</p>
                </div>`})};
