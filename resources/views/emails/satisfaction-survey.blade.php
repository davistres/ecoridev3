<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de satisfaction - EcoRide</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .email-header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .email-body {
            padding: 30px 20px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .trip-info {
            background-color: #f9fafb;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .trip-info p {
            margin: 8px 0;
            font-size: 14px;
        }

        .trip-info strong {
            color: #1f2937;
        }

        .important-notice {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .important-notice p {
            margin: 0;
            font-size: 14px;
            color: #92400e;
        }

        .important-notice strong {
            color: #78350f;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s;
        }

        .cta-button:hover {
            transform: translateY(-2px);
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .email-footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer p {
            margin: 5px 0;
        }

        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🌱 EcoRide</h1>
            <p>Votre avis compte pour nous !</p>
        </div>

        <div class="email-body">
            <p class="greeting">Bonjour {{ $passengerName }},</p>

            <p>Nous espérons que votre covoiturage s'est bien déroulé !</p>

            <div class="trip-info">
                <p><strong>📍 Trajet :</strong> {{ $cityDep }} → {{ $cityArr }}</p>
                <p><strong>📅 Date :</strong> {{ $departureDate }}</p>
                <p><strong>👤 Conducteur :</strong> {{ $driverName }}</p>
            </div>

            <p>Afin d'améliorer continuellement notre service et de garantir la meilleure expérience possible à tous nos
                utilisateurs, nous vous invitons à remplir le <strong>formulaire de satisfaction</strong> concernant ce
                covoiturage.</p>

            <div class="important-notice">
                <p><strong>⚠️ Important :</strong> Le remplissage de ce formulaire est <strong>obligatoire</strong>. Il
                    ne vous prendra que quelques minutes et nous permettra de maintenir un haut niveau de qualité au sein
                    de notre communauté.</p>
            </div>

            <p>Pour accéder au formulaire, veuillez vous connecter à votre compte EcoRide en cliquant sur le bouton
                ci-dessous :</p>

            <div class="button-container">
                <a href="{{ $loginUrl }}" class="cta-button">
                    🔐 Se connecter et remplir le formulaire
                </a>
            </div>

            <div class="divider"></div>

            <p style="font-size: 14px; color: #6b7280;">
                Une fois connecté, rendez-vous dans votre tableau de bord pour accéder au formulaire de satisfaction.
            </p>

            <p style="margin-top: 30px;">
                Merci de votre confiance et à bientôt sur EcoRide !
            </p>

            <p style="font-weight: 600; color: #10b981; margin-top: 20px;">
                L'équipe EcoRide 🌿
            </p>
        </div>

        <div class="email-footer">
            <p><strong>EcoRide</strong> - Covoiturage écologique et solidaire</p>
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
            <p style="margin-top: 10px;">
                © {{ date('Y') }} EcoRide. Tous droits réservés.
            </p>
        </div>
    </div>
</body>

</html>

