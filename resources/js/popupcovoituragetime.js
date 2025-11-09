class TripNotificationManager {
    constructor() {
        this.storageKey = 'ecoride_trip_notifications';
        this.closedNotificationsKey = 'ecoride_closed_notifications';
        this.currentNotification = null;
        this.checkInterval = null;
        this.userTrips = [];
        this.closedNotifications = new Set();
    }

    init(userTrips, userId) {
        this.userTrips = userTrips || [];
        this.userId = userId;

        this.loadClosedNotifications();
        this.loadNotificationState();
        this.checkAndShowNotifications();
        this.startPeriodicCheck();

        this.setupEventListeners();
    }

    loadClosedNotifications() {
        const stored = localStorage.getItem(this.closedNotificationsKey);
        if (stored) {
            try {
                const data = JSON.parse(stored);
                if (data.userId === this.userId) {
                    this.closedNotifications = new Set(data.notifications || []);
                }
            } catch (e) {
                console.error('Erreur lors du chargement des notifications fermées:', e);
            }
        }
    }

    saveClosedNotifications() {
        localStorage.setItem(this.closedNotificationsKey, JSON.stringify({
            userId: this.userId,
            notifications: Array.from(this.closedNotifications)
        }));
    }

    clearClosedNotifications() {
        this.closedNotifications.clear();
        localStorage.removeItem(this.closedNotificationsKey);
    }

    loadNotificationState() {
        const stored = localStorage.getItem(this.storageKey);
        if (stored) {
            try {
                const state = JSON.parse(stored);
                if (state.userId === this.userId && state.notification) {
                    this.currentNotification = state.notification;
                    this.restoreNotification();
                }
            } catch (e) {
                console.error('Erreur lors du chargement de l\'état des notifications:', e);
            }
        }
    }

    saveNotificationState() {
        if (this.currentNotification) {
            localStorage.setItem(this.storageKey, JSON.stringify({
                userId: this.userId,
                notification: this.currentNotification,
                timestamp: Date.now()
            }));
        } else {
            localStorage.removeItem(this.storageKey);
        }
    }

    restoreNotification() {
        if (this.currentNotification) {
            this.showNotification(
                this.currentNotification.type,
                this.currentNotification.data,
                false
            );
        }
    }

    getNotificationKey(type, tripId) {
        return `${type}_${tripId}`;
    }

    isNotificationClosed(type, tripId) {
        return this.closedNotifications.has(this.getNotificationKey(type, tripId));
    }

    checkAndShowNotifications() {
        const now = new Date();

        for (const trip of this.userTrips) {
            const departureDateTime = new Date(trip.departure_date + ' ' + trip.departure_time);
            const timeDiff = departureDateTime - now;
            const minutesDiff = Math.floor(timeDiff / 1000 / 60);

            const isToday = now.toDateString() === departureDateTime.toDateString();
            const isTwoHoursBefore = minutesDiff <= 120 && minutesDiff > 0;
            const isTenMinutesLate = minutesDiff < -10;
            const isTwentyMinutesLate = minutesDiff < -20;
            const isOneHourFortyLate = minutesDiff < -100;

            if (trip.is_driver) {
                if (isOneHourFortyLate && !trip.trip_started && !this.isNotificationClosed('driver_late_1h40', trip.id)) {
                    this.showDriverLateWarning(trip);
                } else if (isTenMinutesLate && !trip.trip_started && !this.isNotificationClosed('driver_late_10min', trip.id)) {
                    this.showDriverLateNotification(trip);
                } else if (isTwoHoursBefore && !this.isNotificationClosed('two_hours_before', trip.id)) {
                    this.showTwoHoursBeforeNotification(trip);
                } else if (isToday && minutesDiff > 120 && !this.isNotificationClosed('today', trip.id)) {
                    this.showTodayNotification(trip);
                }
            } else {
                if (isTwentyMinutesLate && !trip.trip_started && !this.isNotificationClosed('passenger_late_20min', trip.id)) {
                    this.showPassengerCancelOption(trip);
                } else if (isTenMinutesLate && !trip.trip_started && !this.isNotificationClosed('passenger_late_10min', trip.id)) {
                    this.showPassengerLateNotification(trip);
                } else if (isTwoHoursBefore && !this.isNotificationClosed('two_hours_before', trip.id)) {
                    this.showTwoHoursBeforeNotification(trip);
                } else if (isToday && minutesDiff > 120 && !this.isNotificationClosed('today', trip.id)) {
                    this.showTodayNotification(trip);
                }
            }
        }
    }

    showTodayNotification(trip) {
        const data = {
            tripId: trip.id,
            departureTime: trip.departure_time,
            cityDep: trip.city_dep,
            cityArr: trip.city_arr,
            isDriver: trip.is_driver
        };
        this.showNotification('today', data);
    }

    showTwoHoursBeforeNotification(trip) {
        const data = {
            tripId: trip.id,
            departureTime: trip.departure_time,
            cityDep: trip.city_dep,
            cityArr: trip.city_arr,
            isDriver: trip.is_driver
        };
        this.showNotification('two_hours_before', data);
    }

    showDriverLateNotification(trip) {
        const data = {
            tripId: trip.id,
            departureTime: trip.departure_time,
            cityDep: trip.city_dep,
            cityArr: trip.city_arr
        };
        this.showNotification('driver_late_10min', data);
    }

    showDriverLateWarning(trip) {
        const data = {
            tripId: trip.id,
            departureTime: trip.departure_time,
            cityDep: trip.city_dep,
            cityArr: trip.city_arr
        };
        this.showNotification('driver_late_1h40', data);
    }

    showPassengerLateNotification(trip) {
        const data = {
            tripId: trip.id,
            departureTime: trip.departure_time,
            cityDep: trip.city_dep,
            cityArr: trip.city_arr,
            driverName: trip.driver_name
        };
        this.showNotification('passenger_late_10min', data);
    }

    showPassengerCancelOption(trip) {
        const data = {
            tripId: trip.id,
            departureTime: trip.departure_time,
            cityDep: trip.city_dep,
            cityArr: trip.city_arr,
            driverName: trip.driver_name
        };
        this.showNotification('passenger_late_20min', data);
    }

    showNotification(type, data, save = true) {
        this.closeCurrentNotification();

        this.currentNotification = { type, data };

        if (save) {
            this.saveNotificationState();
        }

        const container = this.createNotificationContainer(type, data);
        document.body.appendChild(container);

        setTimeout(() => {
            container.classList.add('show');
        }, 100);
    }

    createNotificationContainer(type, data) {
        const container = document.createElement('div');
        container.className = 'trip-notification fixed bottom-4 right-4 z-50 max-w-sm bg-white rounded-lg shadow-2xl border-2 transform transition-all duration-300 translate-x-full';
        container.id = 'trip-notification-container';

        let content = '';
        let borderColor = 'border-blue-500';
        let canClose = true;

        switch (type) {
            case 'today':
                borderColor = 'border-blue-500';
                content = this.getTodayNotificationContent(data);
                break;
            case 'two_hours_before':
                borderColor = 'border-orange-500';
                content = this.getTwoHoursBeforeContent(data);
                break;
            case 'driver_late_10min':
                borderColor = 'border-red-500';
                content = this.getDriverLate10MinContent(data);
                break;
            case 'driver_late_1h40':
                borderColor = 'border-red-700';
                content = this.getDriverLate1h40Content(data);
                break;
            case 'passenger_late_10min':
                borderColor = 'border-yellow-500';
                content = this.getPassengerLate10MinContent(data);
                break;
            case 'passenger_late_20min':
                borderColor = 'border-red-500';
                content = this.getPassengerLate20MinContent(data);
                break;
        }

        container.classList.add(borderColor);
        container.innerHTML = content;

        if (canClose) {
            const closeBtn = container.querySelector('.close-notification');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.closeCurrentNotification());
            }
        }

        return container;
    }

    getTodayNotificationContent(data) {
        const role = data.isDriver ? 'conducteur' : 'passager';
        return `
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 text-xl mr-2"></i>
                        <h4 class="font-bold text-gray-800">Rappel de covoiturage</h4>
                    </div>
                    <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-700">
                    Vous avez un covoiturage prévu aujourd'hui en tant que <strong>${role}</strong> à <strong>${data.departureTime}</strong> de <strong>${data.cityDep}</strong> à <strong>${data.cityArr}</strong>.
                </p>
            </div>
        `;
    }

    getTwoHoursBeforeContent(data) {
        const role = data.isDriver ? 'conducteur' : 'passager';
        return `
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-orange-500 text-xl mr-2"></i>
                        <h4 class="font-bold text-gray-800">Départ dans 2 heures !</h4>
                    </div>
                    <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-700">
                    Votre covoiturage en tant que <strong>${role}</strong> de <strong>${data.cityDep}</strong> à <strong>${data.cityArr}</strong> commence dans environ 2 heures (${data.departureTime}).
                </p>
            </div>
        `;
    }

    getDriverLate10MinContent(data) {
        return `
            <div class="p-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-2"></i>
                        <h4 class="font-bold text-gray-800">Retard détecté</h4>
                    </div>
                    <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-700 mb-3">
                    Votre covoiturage de <strong>${data.cityDep}</strong> à <strong>${data.cityArr}</strong> devait démarrer à ${data.departureTime}. Tout va bien ?
                </p>
                <p class="text-sm text-gray-600 mb-2">
                    Si vous pensez être davantage en retard, contactez la plateforme :
                </p>
                <p class="text-sm font-semibold text-blue-600 mb-2">
                    <i class="fas fa-phone mr-1"></i> 01 23 45 67 89
                </p>
                <a href="/contact" class="inline-block w-full text-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-sm font-semibold">
                    Contacter la plateforme
                </a>
            </div>
        `;
    }

    getDriverLate1h40Content(data) {
        return `
            <div class="p-4 bg-red-50">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-700 text-xl mr-2"></i>
                        <h4 class="font-bold text-red-700">Alerte importante</h4>
                    </div>
                    <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-800 font-semibold mb-2">
                    Attention ! Votre covoiturage sera supprimé dans 20 minutes si vous ne le démarrez pas.
                </p>
                <p class="text-sm text-gray-700">
                    Trajet : <strong>${data.cityDep}</strong> → <strong>${data.cityArr}</strong>
                </p>
            </div>
        `;
    }

    getPassengerLate10MinContent(data) {
        return `
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-yellow-500 text-xl mr-2"></i>
                        <h4 class="font-bold text-gray-800">Retard possible</h4>
                    </div>
                    <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-700">
                    Le conducteur <strong>${data.driverName}</strong> n'a pas encore démarré le trajet de <strong>${data.cityDep}</strong> à <strong>${data.cityArr}</strong> prévu à ${data.departureTime}. Un retard est possible.
                </p>
            </div>
        `;
    }

    getPassengerLate20MinContent(data) {
        return `
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-2"></i>
                        <h4 class="font-bold text-gray-800">Retard important</h4>
                    </div>
                    <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-700 mb-3">
                    Le conducteur <strong>${data.driverName}</strong> n'a toujours pas démarré le trajet. Vous pouvez annuler votre participation si vous le souhaitez.
                </p>
                <button class="cancel-participation-btn w-full px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-sm font-semibold">
                    Annuler ma participation
                </button>
            </div>
        `;
    }

    closeCurrentNotification() {
        const container = document.getElementById('trip-notification-container');
        if (container) {
            container.classList.remove('show');
            setTimeout(() => {
                container.remove();
            }, 300);
        }

        if (this.currentNotification) {
            const key = this.getNotificationKey(this.currentNotification.type, this.currentNotification.data.tripId);
            this.closedNotifications.add(key);
            this.saveClosedNotifications();
        }

        this.currentNotification = null;
        this.saveNotificationState();
    }

    startPeriodicCheck() {
        this.checkInterval = setInterval(() => {
            this.checkAndShowNotifications();
        }, 60000);
    }

    setupEventListeners() {
        window.addEventListener('beforeunload', () => {
            this.saveNotificationState();
        });

        document.addEventListener('trip-started', (e) => {
            if (this.currentNotification && this.currentNotification.data.tripId === e.detail.tripId) {
                this.closeCurrentNotification();
            }
        });

        document.addEventListener('trip-completed', (e) => {
            if (this.currentNotification && this.currentNotification.data.tripId === e.detail.tripId) {
                this.closeCurrentNotification();
            }

            this.userTrips = this.userTrips.filter(trip => trip.id !== e.detail.tripId);
            this.checkAndShowNotifications();
        });
    }

    destroy() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }
        this.closeCurrentNotification();
    }
}

window.TripNotificationManager = TripNotificationManager;

