<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Matériel</title>
    <style>
        /* Global styles */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #f4f7fb;
            color: #333;
        }

        /* Container for the content */
        .container {
            width: 80%;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        /* Title styles */
        h1 {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #291f1f;
        }
        h2 {
            font-size: 1.6em;
            margin-top: 0;
            color: #F6BB42;
        }
        h3 {
            font-size: 1.4em;
            margin-top: 30px;
            color: #CC8400;
        }

        /* List and table styles */
        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border: 1px solid #ddd; 
            font-size: 1.1em;
        }
        th {
            background-color: #F6BB42;
            color: #fff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        /* No data message */
        .no-data {
            text-align: center;
            font-style: italic;
            color: #777;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            table {
                font-size: 0.9em;
            }
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }

        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Fiche de Stock</h1>
        <h2>Détails du Matériel : {{ $materiel->Designation }}</h2>
        <ul>
            <li><strong>Code Matériel:</strong> {{ $materiel->CodeMateriel }}</li>
            <li><strong>Type:</strong> {{ $materiel->Type }}</li>
            <li><strong>Quantité en Stock:</strong> {{ $materiel->Quantite }}</li>
            <li><strong>Prix Unitaire:</strong> {{ number_format($materiel->PrixUnitaire, 2, ',', ' ') }} Ariary</li>
        </ul>
        <h3>Sorties et Réceptions associées</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Bon Réception</th>
                    <th>Bon Sortie</th>
                    <th>Quantité Reçue</th>
                    <th>Quantité Sortante</th>
                    <th>Destinataire</th>
                </tr>
            </thead>
            <tbody>
                @if ($materiel->sorties->isEmpty() && $materiel->receptions->isEmpty())
                    <tr>
                        <td colspan="6" class="no-data">Aucune sortie ou réception disponible pour ce matériel.</td>
                    </tr>
                @else
                    @foreach ($materiel->receptions as $reception)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($reception->DateReception)->format('d/m/Y') }}</td>
                            <td>{{ $reception->BonReception }}</td>
                            <td>-</td>
                            <td>{{ $reception->QuantiteRecu }}</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @endforeach
                    @foreach ($materiel->sorties as $sortie)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sortie->DateSortie)->format('d/m/Y') }}</td>
                            <td>-</td>
                            <td>{{ $sortie->BonSortie }}</td>
                            <td>-</td>
                            <td>{{ $sortie->QuantiteSortant }}</td>
                            <td>{{ $sortie->Destinataire }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} | Madagascar National Parks Andringitra</p>
        <p>Lot III H 50 Ambohitsoa | Email: eddy_dparg@mnparks.mg</p>
    </div>

</body>
</html>
