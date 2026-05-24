{{-- resources/views/gracias.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FletesCun · ¡Gracias!</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 60%, #F0FDF4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .thanks-card {
            background: #fff;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            max-width: 500px;
            text-align: center;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #DCFCE7;
            color: #16A34A;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 24px;
        }
        h1 { color: #1B3A6B; font-weight: 700; margin-bottom: 16px; font-size: 1.75rem; }
        p { color: #64748B; line-height: 1.6; margin-bottom: 32px; }
        .folio-badge {
            display: inline-block;
            background: #F1F5F9;
            color: #475569;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 700;
            margin-bottom: 24px;
        }
        .btn-home {
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #fff;
            border: none;
            padding: 14px 32px;
            border-radius: 14px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        .btn-home:hover { transform: translateY(-2px); color: #fff; }
    </style>
</head>
<body>
    <div class="thanks-card">
        <div class="success-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h1>¡Cotización enviada!</h1>
        <p>Hemos recibido tu solicitud correctamente. Un asesor de FletesCun te contactará a la brevedad para confirmar los detalles finales.</p>
        
        @if(session('folio'))
            <div class="folio-badge">
                Folio: {{ session('folio') }}
            </div>
        @endif

        <br>
        <a href="{{ route('inicio') }}" class="btn-home">
            Volver al inicio
        </a>
    </div>
</body>
</html>
