<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOA Archive Copy</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; margin: 24px; }
        h1 { margin: 0 0 8px; }
        .meta { margin-bottom: 16px; }
        .meta p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #333; padding: 8px; font-size: 12px; }
        th { background: #f2f2f2; text-align: left; }
        .right { text-align: right; }
        .footer { margin-top: 20px; display: flex; gap: 40px; }
        .sig { min-width: 240px; }
        .line { border-top: 1px solid #111; margin-top: 30px; padding-top: 6px; }
    </style>
</head>
<body>
    <h1>Statement of Account (Archived Copy)</h1>
    <div class="meta">
        <p><strong>SOA #:</strong> {{ $soa['soa_number'] }}</p>
        <p><strong>Date:</strong> {{ $soa['date'] }}</p>
        <p><strong>Billing ID:</strong> {{ $soa['billing_id'] }}</p>
        <p><strong>Client:</strong> {{ $soa['client_name'] }}</p>
        <p><strong>Address:</strong> {{ $soa['client_address'] }}</p>
        <p><strong>SIPA Ref #:</strong> {{ $soa['sipa_ref_no'] }}</p>
        <p><strong>Week Period:</strong> {{ $soa['week_period'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Container #</th>
                <th>EIR #</th>
                <th>Size</th>
                <th>Destination</th>
                <th>Remarks</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($soa['items'] as $item)
            <tr>
                <td>{{ $item['delivery_date'] }}</td>
                <td>{{ $item['container_no'] }}</td>
                <td>{{ $item['eir_no'] }}</td>
                <td>{{ $item['size'] }}</td>
                <td>{{ $item['destination'] }}</td>
                <td>{{ $item['remarks'] }}</td>
                <td class="right">{{ $item['amount'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="6" class="right"><strong>Total</strong></td>
                <td class="right"><strong>{{ $soa['total_amount'] }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="sig">
            <div class="line">{{ $soa['prepared_by'] }}</div>
            <div>Prepared By</div>
        </div>
        <div class="sig">
            <div class="line">{{ $soa['checked_by'] }}</div>
            <div>Checked By</div>
        </div>
    </div>
</body>
</html>
