<!-- resources/views/pdf/report.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Annuel</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            background-color: #f7f7f7;
            color: #333;
        }

        h1 {
            color: #CC8400;
            text-align: center;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .content {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .table-wrapper {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #F6BB42;
            color: white;
            font-weight: bold;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table td {
            background-color: #fff;
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
    <h1>Rapport Annuel des Stocks</h1>
    
    <div class="content">
        <p><strong>Année :</strong> {{ $year }}</p>
        <p><strong>Type de matériel :</strong> {{ $materialType }}</p>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Désignation</th>
                        <th>Stock</th>
                        <th>Prix Unitaire</th>
                        <th>Valeur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materiels as $materiel)
                        <tr>
                            <td>{{ $materiel->Designation }}</td>
                            <td>{{ $materiel->Quantite }}</td>
                            <td>{{ number_format($materiel->PrixUnitaire, 2, ',', ' ') }} Ariary</td>
                            <td>{{ number_format($materiel->Valeur, 2, ',', ' ') }} Ariary</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; {{ date('Y') }} | Madagascar National Parks Andringitra</p>
        <p>Lot III H 50 Ambohitsoa | Email: eddy_dparg@mnparks.mg</p>
    </div>
</body>
</html>
