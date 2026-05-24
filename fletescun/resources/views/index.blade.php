<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLETESCUN | Fletes Cancún y Servicios de Mudanzas Nacionales</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            /* FletesCun UI Tokens */
            --fc-primary: #2563EB;
            --fc-primary-dark: #1B3A6B;
            --fc-primary-light: #60A5FA;
            --fc-bg-main: #ffffff;
            --fc-bg-alt: #F8FAFC;
            --fc-bg-icon: #EFF6FF;
            --fc-border: #E2E8F0;
            --fc-border-light: #F1F5F9;
            
            --fc-text-strong: #0F172A;
            --fc-text-main: #334155;
            --fc-text-muted: #64748B;
            --fc-text-light: #94A3B8;
            
            --fc-accent-warning: #F59E0B;
            --fc-accent-success: #10B981;
            
            /* Sombras modernas */
            --shadow-sm: 0 2px 4px rgba(15, 23, 42, 0.05);
            --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.08);
            --shadow-lg: 0 12px 24px rgba(15, 23, 42, 0.12);
            --shadow-glow: 0 0 20px rgba(37, 99, 235, 0.4);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--fc-text-main);
            background-color: var(--fc-bg-alt);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Tipografía */
        h1, h2, h3, h4, h5, h6 {
            color: var(--fc-text-strong);
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        h2 {
            position: relative;
            display: block;
            padding-bottom: 20px;
            text-align: center;
            width: 100%;
            margin-bottom: 1.5rem;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--fc-primary-light), var(--fc-primary));
            border-radius: 2px;
            margin: 0 auto;
        }

        /* Animaciones Globales */
        @keyframes engineIdle {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-1px) rotate(0.5deg); }
        }

        @keyframes heavyTruckDrive {
            0% { transform: translateX(0) rotate(0deg) translateY(0); }
            15% { transform: translateX(-2px) rotate(-4deg) translateY(3px); filter: drop-shadow(0 2px 4px rgba(37, 99, 235, 0.4)); }
            40% { transform: translateX(40px) rotate(5deg) translateY(-5px); filter: drop-shadow(-15px 5px 8px rgba(37, 99, 235, 0.3)); }
            70% { transform: translateX(10px) rotate(-3deg) translateY(2px); }
            85% { transform: translateX(-3px) rotate(1deg) translateY(-1px); }
            100% { transform: translateX(0) rotate(0deg) translateY(0); }
        }

        /* Navbar Refinada */
        .navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: var(--shadow-sm);
            padding: 0.8rem 0;
            transition: all 0.3s ease;
        }

        .navbar-brand img {
            height: 130px; 
            width: auto;
            transform-origin: bottom center; 
            transition: filter 0.3s ease;
        }

        .navbar-brand img:hover {
            animation: engineIdle 0.08s infinite alternate;
            filter: drop-shadow(0 0 12px rgba(37, 99, 235, 0.5));
        }

        .navbar-brand:active img {
            animation: heavyTruckDrive 0.8s ease-in-out; 
        }

        .nav-link {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--fc-text-muted) !important;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--fc-primary) !important;
            background-color: var(--fc-bg-icon);
        }

        /* Hero Section */
        .hero-section {
            padding: 80px 0 100px;
            background: linear-gradient(135deg, var(--fc-primary-dark) 0%, var(--fc-primary) 50%, #1E40AF 100%);
            position: relative;
            overflow: hidden;
            clip-path: polygon(0 0, 100% 0, 100% 95%, 0 100%);
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(255,255,255,0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(96,165,250,0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .hero-decor-1 {
            position: absolute; top: -10%; right: -5%; width: 40vw; height: 40vw;
            border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Botones con Profundidad */
        .btn-gradient {
            background: linear-gradient(135deg, var(--fc-primary), #1e40af);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3), inset 0 1px 2px rgba(255,255,255,0.2);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-gradient:hover::before {
            left: 100%;
        }

        .btn-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.45);
            color: #ffffff;
        }

        /* Badges Mejorados */
        .badge-blue { 
            background: linear-gradient(135deg, var(--fc-bg-icon), #e0e7ff);
            color: var(--fc-primary); 
            display: inline-flex; 
            align-items: center;
            gap: 8px;
            padding: 10px 18px; 
            border-radius: 30px; 
            font-size: 0.85rem; 
            font-weight: 700; 
            border: 2px solid rgba(37, 99, 235, 0.2);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem !important;
        }

        .badge-blue::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s ease;
        }

        .badge-blue:hover::before {
            left: 100%;
        }

        /* Tarjetas de Servicios */
        .ui-card {
            background: #ffffff;
            border: 1px solid var(--fc-border);
            border-radius: 20px;
            padding: 24px 18px;
            height: 100%;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            z-index: 1;
            box-shadow: var(--shadow-sm);
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border-top: 3px solid transparent;
        }

        .ui-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--fc-primary-light), var(--fc-primary), #1e40af);
            border-radius: 20px 20px 0 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .ui-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 16px 32px rgba(37, 99, 235, 0.15);
            border-color: var(--fc-primary-light);
            z-index: 2;
        }

        .ui-card:hover::before {
            opacity: 1;
        }

        .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--fc-bg-icon), #ffffff);
            border: 2px solid rgba(37, 99, 235, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--fc-primary);
            font-size: 1.4rem;
            margin-bottom: 15px;
            transition: all 0.4s ease;
            box-shadow: inset 0 2px 8px rgba(37, 99, 235, 0.1), 0 4px 12px rgba(37, 99, 235, 0.08);
            position: relative;
        }

        .icon-box::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 14px;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.4), transparent 60%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .ui-card:hover .icon-box {
            background: linear-gradient(135deg, var(--fc-primary), var(--fc-primary-dark));
            color: #ffffff;
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
            border-color: var(--fc-primary-light);
        }

        .ui-card:hover .icon-box::after {
            opacity: 1;
        }

        .ui-card h3 { font-size: 1rem; margin-bottom: 10px; }
        .ui-card p { font-size: 0.9rem; color: var(--fc-text-muted); line-height: 1.5; }

        /* Galería de Imágenes */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .gallery-img-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            aspect-ratio: 4/3;
            box-shadow: var(--shadow-md);
            background-color: var(--fc-border-light);
            border: 2px solid rgba(37, 99, 235, 0.1);
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%);
        }

        .gallery-img-wrapper:hover {
            border-color: var(--fc-primary);
            box-shadow: 0 16px 32px rgba(37, 99, 235, 0.2);
            transform: translateY(-4px);
        }

        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .gallery-img-wrapper::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.6) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .gallery-img-wrapper:hover .gallery-img {
            transform: scale(1.12);
        }

        .gallery-img-wrapper:hover::after {
            opacity: 1;
        }

        /* Testimoniales */
        .testimonial-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border: 2px solid rgba(37, 99, 235, 0.12);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.06);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            min-height: 320px;
        }

        .testimonial-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--fc-primary-light), var(--fc-primary), #1e40af, var(--fc-accent-warning));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(37, 99, 235, 0.15);
            border-color: var(--fc-primary-light);
        }

        .testimonial-card:hover::before {
            opacity: 1;
        }

        .quote-bg {
            position: absolute;
            top: -10px;
            right: 15px;
            font-size: 6rem;
            color: var(--fc-bg-alt);
            z-index: 0;
            font-family: serif;
            line-height: 1;
        }

        .testimonial-content {
            position: relative;
            z-index: 1;
            flex-grow: 1;
            font-size: 0.95rem;
            color: var(--fc-text-main);
            font-style: italic;
            margin-bottom: 20px;
        }

        .testimonial-stars {
            color: var(--fc-accent-warning);
            font-size: 0.9rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
            border-top: 1px solid var(--fc-border-light);
            padding-top: 20px;
            position: relative;
            z-index: 1;
        }

        .author-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--fc-bg-icon);
        }

        .author-avatar-text {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--fc-primary-light), var(--fc-primary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            border: 2px solid var(--fc-bg-icon);
        }

        .author-info h4 {
            margin: 0;
            font-size: 1rem;
            color: var(--fc-text-strong);
        }

        .author-info span {
            font-size: 0.8rem;
            color: var(--fc-text-light);
            display: block;
        }

        /* Ubicacion y Footer */
        .location-info-box {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.06);
            height: 100%;
            border: 2px solid rgba(37, 99, 235, 0.1);
            position: relative;
            overflow: hidden;
        }

        .location-info-box::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.05), transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .map-container {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            height: 100%;
            min-height: 400px;
        }

        footer {
            background: linear-gradient(180deg, #0F172A 0%, #020617 50%, #000000 100%);
            color: var(--fc-text-light);
            padding: 50px 0 20px;
            position: relative;
            border-top: 4px solid var(--fc-primary);
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.5), transparent);
            pointer-events: none;
        }

        .footer-logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .footer-logo-container img {
            max-width: 220px;
            filter: brightness(0) invert(1) drop-shadow(0 0 15px rgba(255,255,255,0.2));
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .footer-logo-container img:hover {
            transform: scale(1.05);
        }

        footer h5 {
            color: #ffffff;
            font-size: 1.15rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .footer-link {
            color: var(--fc-text-light);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
            padding-left: 15px;
            display: inline-block;
        }

        .footer-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--fc-primary);
            opacity: 0.5;
            transition: all 0.3s ease;
            box-shadow: 0 0 8px rgba(37, 99, 235, 0);
        }

        .footer-link:hover {
            color: #ffffff;
            padding-left: 22px;
            text-shadow: 0 0 8px rgba(37, 99, 235, 0.2);
        }

        .footer-link:hover::before {
            opacity: 1;
            background-color: var(--fc-accent-warning);
            box-shadow: 0 0 12px var(--fc-accent-warning);
            transform: translateY(-50%) scale(1.2);
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 1rem;
            padding: 12px;
            border-radius: 10px;
            background: rgba(255,255,255,0.02);
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .contact-item:hover {
            background: rgba(255,255,255,0.06);
            border-left-color: var(--fc-primary);
            padding-left: 16px;
        }

        /* Responsividad */
        /* Animaciones Decorativas */
        @keyframes borderGlow {
            0% { border-color: rgba(37, 99, 235, 0.3); box-shadow: 0 0 10px rgba(37, 99, 235, 0.2); }
            50% { border-color: rgba(37, 99, 235, 0.6); box-shadow: 0 0 20px rgba(37, 99, 235, 0.4); }
            100% { border-color: rgba(37, 99, 235, 0.3); box-shadow: 0 0 10px rgba(37, 99, 235, 0.2); }
        }

        @keyframes textGradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Contenedores con bordes animados */
        .decorated-section {
            border: 2px solid rgba(37, 99, 235, 0.2);
            border-radius: 20px;
            padding: 30px;
            animation: borderGlow 3s ease-in-out infinite;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .decorated-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 100% 0%, rgba(37, 99, 235, 0.05), transparent 50%);
            pointer-events: none;
        }

        .decorated-section:hover {
            border-color: var(--fc-primary);
            box-shadow: 0 0 30px rgba(37, 99, 235, 0.5);
        }

        /* Galería Grid Responsivo */
        .gallery-grid {
            display: grid;
            gap: 12px;
            grid-auto-flow: dense;
        }

        /* Mobile: 2 columnas */
        @media (max-width: 576px) {
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .gallery-img-wrapper {
                aspect-ratio: 1/1;
            }
        }

        /* Tablet: 3 columnas */
        @media (min-width: 577px) and (max-width: 991px) {
            .gallery-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .gallery-img-wrapper {
                aspect-ratio: 4/3;
            }
        }

        /* Desktop: 4 columnas con masonry */
        @media (min-width: 992px) {
            .gallery-grid {
                grid-template-columns: repeat(4, 1fr);
                grid-auto-rows: 200px;
            }
            .gallery-img-wrapper {
                aspect-ratio: auto;
            }
            .gallery-img-wrapper:nth-child(4n+1) {
                grid-row: span 1;
            }
            .gallery-img-wrapper:nth-child(4n+2) {
                grid-row: span 1;
            }
        }

        /* Carrusel de Testimonios */
        .testimonials-carousel {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            gap: 20px;
            padding: 10px 0;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        .testimonials-carousel::-webkit-scrollbar {
            height: 6px;
        }

        .testimonials-carousel::-webkit-scrollbar-track {
            background: var(--fc-bg-alt);
            border-radius: 10px;
        }

        .testimonials-carousel::-webkit-scrollbar-thumb {
            background: var(--fc-primary);
            border-radius: 10px;
        }

        .testimonial-card {
            flex: 0 0 100%;
            scroll-snap-align: start;
        }

        @media (min-width: 577px) {
            .testimonial-card {
                flex: 0 0 calc(50% - 10px);
            }
        }

        @media (min-width: 992px) {
            .testimonial-card {
                flex: 0 0 calc(33.333% - 14px);
            }
        }



        /* Header Responsive */
        @media (max-width: 576px) {
            .navbar-brand img {
                height: 70px;
            }
            .navbar {
                padding: 0.6rem 0;
            }
            .nav-link {
                font-size: 0.85rem;
                padding: 6px 12px !important;
            }
        }

        @media (min-width: 577px) and (max-width: 991px) {
            .navbar-brand img {
                height: 90px;
            }
        }

        @media (min-width: 992px) {
            .navbar-brand img {
                height: 120px;
            }
        }

        /* Footer Acordeón */
        .footer-section-toggle {
            display: none;
            cursor: pointer;
            width: 100%;
            background: none;
            border: none;
            padding: 0;
            text-align: left;
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }

        .footer-section-toggle:hover {
            color: var(--fc-primary-light);
        }

        .footer-section-toggle i {
            transition: transform 0.3s ease;
        }

        .footer-section-toggle.active i {
            transform: rotate(180deg);
        }

        .footer-section-content {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 1;
        }

        .footer-section-content.collapsed {
            max-height: 0;
            opacity: 0;
        }

        @media (max-width: 768px) {
            .footer-section-toggle {
                display: flex;
            }
            section .text-center {
                margin-bottom: 2rem;
            }
            .footer-section-content {
                margin-bottom: 15px;
            }
            footer {
                padding: 30px 0 15px;
            }
            .footer-logo-container {
                padding-bottom: 15px;
                margin-bottom: 15px;
            }
            .footer-logo-container img {
                max-width: 120px;
            }
            .footer-logo-container p {
                font-size: 0.9rem !important;
                margin-top: 0.5rem !important;
            }
            footer h5 {
                font-size: 1rem;
                margin-bottom: 1rem;
            }
            .decorated-section {
                padding: 20px;
            }
            .contact-item {
                margin-bottom: 0.75rem;
                padding: 8px;
            }
        }

        /* Desktop Optimizado */
        @media (min-width: 992px) {
            .container {
                max-width: 1300px;
            }
            section {
                padding-top: 50px !important;
                padding-bottom: 50px !important;
            }
            section .text-center {
                margin-bottom: 3rem;
            }
            .decorated-section {
                padding: 40px;
            }
            .ui-card {
                padding: 32px 24px;
            }
            footer {
                padding: 70px 0 30px;
            }
            .footer-logo-container {
                margin-bottom: 2.5rem;
                padding-bottom: 2rem;
            }
            .footer-logo-container img {
                max-width: 200px;
            }
            footer h5 {
                font-size: 1.25rem;
                margin-bottom: 1.8rem;
            }
            .footer-link {
                font-size: 1rem;
            }
        }

        /* Touch Optimization */
        @media (hover: none) and (pointer: coarse) {
            .ui-card, .testimonial-card, .gallery-img-wrapper {
                transform: none;
            }
            .ui-card:active, .testimonial-card:active, .gallery-img-wrapper:active {
                transform: scale(0.98);
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('inicio') }}" title="Haz clic para arrancar">
                <img src="{{ asset('img/logo_fletes.png') }}" alt="Fletescun Logo" onerror="this.src='https://via.placeholder.com/200x80?text=FLETESCUN'">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars-staggered" style="color: var(--fc-primary-dark); font-size: 1.5rem;"></i>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-1 gap-lg-3">
                    <li class="nav-item"><a class="nav-link active" href="#inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#galeria">Galería</a></li>
                    <li class="nav-item"><a class="nav-link" href="#testimoniales">Reseñas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#ubicacion">Ubicación</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section text-center text-lg-start" id="inicio">
        <div class="hero-decor-1"></div>
        <div class="container position-relative z-1">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="badge-blue mb-4 shadow-sm" style="background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.2);">
                         Servicios Locales y Foráneos
                    </div>
                    <h1 class="display-4 fw-bolder mb-4 text-white">
                        Especialistas en Mudanzas desde <span style="color: #93C5FD; position: relative;">Cancún
                            <svg style="position: absolute; bottom: -10px; left: 0; width: 100%; height: 12px; fill: none; stroke: #F59E0B; stroke-width: 4; stroke-linecap: round;" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 50 10 100 0" /></svg>
                        </span>
                    </h1>
                    <p class="mb-5 text-white opacity-75 fs-5">
                        Rutas compartidas y servicios exclusivos a todo México. Genera tu cotización transparente, revisa tu inventario y firma tu contrato en minutos.
                    </p>
                    <a href="{{ route('cotizar.paso1') }}" class="btn-gradient bg-white text-primary" style="background: #ffffff !important; color: var(--fc-primary-dark) !important;">
                        <i class="fa-solid fa-file-contract"></i> Iniciar Cotización
                    </a>
                </div>
                <div class="col-lg-5 offset-lg-1">
                    <img src="{{ asset('img/imageninicial.jpg') }}" alt="Mudanzas Cancún" class="img-fluid rounded-4 shadow-lg" style="border: 4px solid rgba(255,255,255,0.2);">
                </div>
            </div>
        </div>
    </section>

    <section id="servicios" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <div class="badge-blue mb-3">
                     Catálogo de Servicios
                </div>
                <h2 class="display-6 fw-bold">Logística y Transporte</h2>
                <p class="text-muted fs-5">Opciones adaptadas al volumen de tu inventario.</p>
            </div>
            
            <div class="row g-3">
                <div class="col-6 col-lg-4">
                    <div class="ui-card">
                        <div class="icon-box"><i class="fa-solid fa-people-carry-box"></i></div>
                        <h3>Mudanza Compartida</h3>
                        <p>Esquema económico. Optimiza costos compartiendo el transporte en rutas nacionales sin sacrificar seguridad.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="ui-card" style="border-top: 4px solid var(--fc-accent-warning);">
                        <div class="icon-box" style="background: #FFFBEB; color: #D97706; border-color: #FEF3C7;"><i class="fa-solid fa-star"></i></div>
                        <h3>Mudanza Exclusiva</h3>
                        <p>Transporte directo y exclusivo. Ideal para entregas urgentes, gran volumen o artículos de alto valor.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="ui-card">
                        <div class="icon-box"><i class="fa-solid fa-cube"></i></div>
                        <h3>Empaque y Embalaje</h3>
                        <p>Servicio adicional. Protección profesional de mobiliario y cajas con materiales de alta resistencia.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="ui-card">
                        <div class="icon-box"><i class="fa-solid fa-building"></i></div>
                        <h3>Almacenaje Seguro</h3>
                        <p>Espacios monitoreados 24/7 para resguardar tus pertenencias por el tiempo exacto que necesites.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="ui-card">
                        <div class="icon-box"><i class="fa-solid fa-handshake"></i></div>
                        <h3>Maniobras y Volados</h3>
                        <p>Personal especializado para carga/descarga en pisos altos o accesos complejos sin elevador.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="ui-card">
                        <div class="icon-box"><i class="fa-solid fa-truck"></i></div>
                        <h3>Traslado de Vehículos</h3>
                        <p>Transporte con seguro de carga para automóviles y motocicletas a cualquier estado de la república.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="galeria" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <div class="badge-blue mb-3">
                     Nuestro Trabajo
                </div>
                <h2 class="display-6 fw-bold">Galería de Mudanzas</h2>
                <p class="text-muted fs-5">Cuidamos cada detalle en tus traslados.</p>
            </div>
            
            <div class="gallery-grid">
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/empaque-para-mudanzas-1-576x1024.jpeg" class="gallery-img" alt="Empaque para mudanzas" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/emplayado-de-muebles-2-1-768x1024.jpeg" class="gallery-img" alt="Emplayado de muebles" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/Emplayado-de-muebles-para-mudanzas-1024x576.jpeg" class="gallery-img" alt="Emplayado de muebles para mudanzas" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/emplayado-de-muebles-1-768x1024.jpeg" class="gallery-img" alt="Emplayado de muebles" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/fletes-y-mudanzas-cancun-1-768x1024.jpeg" class="gallery-img" alt="Fletes y mudanzas Cancún" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/maniobras-de-carga-para-mudanzas-576x1024.jpeg" class="gallery-img" alt="Maniobras de carga" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-de-vehiculos-cancun-1-1024x768.jpeg" class="gallery-img" alt="Mudanzas de vehículos Cancún" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-de-vehiculos-1-1024x576.jpeg" class="gallery-img" alt="Mudanzas de vehículos" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-locales-cancun-768x1024.jpeg" class="gallery-img" alt="Mudanzas locales Cancún" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-1.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 1" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-2.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 2" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-3-1024x576.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 3" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-4-1024x576.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 4" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-5-1024x576.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 5" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-6-1024x576.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 6" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-7-576x1024.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 7" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-8-768x1024.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 8" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-9-768x1024.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 9" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-10-768x1024.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 10" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-11-768x1024.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 11" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-12-1-1024x576.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 12" loading="lazy">
                </div>
                <div class="gallery-img-wrapper">
                    <img src="https://fletescun.com/wp-content/uploads/2024/07/mudanzas-y-fletes-cancun-13-576x1024.jpg" class="gallery-img" alt="Mudanzas y fletes Cancún 13" loading="lazy">
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="{{ route('cotizar.paso1') }}" class="btn-gradient">
                    <i class="fa-solid fa-calculator"></i> Cotizar mi Servicio
                </a>
            </div>
        </div>
    </section>

    <section id="testimoniales" class="py-5 bg-alt">
        <div class="container py-5">
            <div class="text-center mb-5">
                <div class="badge-blue mb-3">
<a href="https://www.facebook.com/Fletescun" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
    <i class="fa-brands fa-facebook me-2"></i>
</a> Facebook
                </div>
                <h2 class="display-6 fw-bold">Lo que opinan nuestros clientes</h2>
                <p class="text-muted fs-5">Experiencias reales de servicios realizados en todo México.</p>
            </div>
            
            <div class="testimonials-carousel" id="testimonialsCarousel">
                <div class="testimonial-card">
                    <div class="quote-bg">"</div>
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-content">
                        "Excelente Servicio, cuidaron bien de las cosas, muy puntuales tanto en recepción como entrega, rápidos y confiables."
                    </p>
                    <div class="testimonial-author">
                        <img src="https://scontent.fcjs3-2.fna.fbcdn.net/v/t39.30808-6/323331872_1615416712225556_4902748268591462064_n.jpg?_nc_cat=103&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeGaL75_ynElVG4oN064DF9YCZnbd7UAGokJmdt3tQAaiQnp79w7IBZ7Amln_mDAuiW6tQeDnxnDlCtNpyZs6JDa&_nc_ohc=crwuTjtGx_MQ7kNvgHRf2HV&_nc_ht=scontent.fcjs3-2.fna&oh=00_AYAJ6Asm_Ocui8tKfqPsUvfnyE9KU3rjB5mITNVc__yNHg&oe=66A587D4" 
                             alt="Alan Solis Pereyra" class="author-img" onerror="this.outerHTML='<div class=\'author-avatar-text\'>AS</div>'">
                        <div class="author-info">
                            <h4>Alan Solis Pereyra</h4>
                            <span>Cliente Verificado</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="quote-bg">"</div>
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-content">
                        "Javier siempre muy amable y atento. El servicio excelente la mudanza fue de Cancún a León y las cosas llegaron muy bien. El precio me pareció muy justo. Lo recomiendo ampliamente!!!"
                    </p>
                    <div class="testimonial-author">
                        <img src="https://scontent.fcjs3-2.fna.fbcdn.net/v/t1.6435-9/180530141_10158845859030189_6565145049183210917_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=1d70fc&_nc_eui2=AeGT8NW3chAPPtUnR5CqFiDAVFIc_OjfIcNUUhz86N8hw47FUImKQ8nYqbkjdTCR47dB19O8SHU9oQ7Mq8a7PrjU&_nc_ohc=mqptDrEKYswQ7kNvgG7MxkA&_nc_ht=scontent.fcjs3-2.fna&oh=00_AYCe4XReXJncUT25cWOVpwrHgJgXMsoaZ7v9ij-6wpr56A&oe=66C72290" 
                             alt="Jay Di Hdez Barrón" class="author-img" onerror="this.outerHTML='<div class=\'author-avatar-text\'>JH</div>'">
                        <div class="author-info">
                            <h4>Jay Di Hdez Barrón</h4>
                            <span>Mudanza Foránea</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="quote-bg">"</div>
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-content">
                        "Excelente servicio, buena calidad, puntualidad y muy buen trato. 100% Recomendable 👌🏼. Muchas gracias 😊"
                    </p>
                    <div class="testimonial-author">
                        <img src="https://scontent.fcjs3-1.fna.fbcdn.net/v/t39.30808-6/429981342_3408233032656209_8135615602069474586_n.jpg?_nc_cat=106&ccb=1-7&_nc_sid=833d8c&_nc_eui2=AeGYcBU4V3duFJryXA5SU8j5U-RfSxBKlqdT5F9LEEqWp0GhN6f2779Iilp3XqYTNxA0NVrLa_Wk9xKeFFqLRajg&_nc_ohc=9nq-8lbIM_oQ7kNvgEqpl13&_nc_ht=scontent.fcjs3-1.fna&oh=00_AYBMicW8-qSxZoMuqQpADVb9E77DfkJFmAA8cN9aBdBWng&oe=66A5B22E" 
                             alt="Mine Sanchez" class="author-img" onerror="this.outerHTML='<div class=\'author-avatar-text\'>MS</div>'">
                        <div class="author-info">
                            <h4>Mine Sanchez</h4>
                            <span>Cliente Verificado</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="quote-bg">"</div>
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-content">
                        "👍 Excelente!!! Muy buena disponibilidad de servicio, puntual y a tiempo con la entrega."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar-text">RV</div>
                        <div class="author-info">
                            <h4>Ricardo Velez</h4>
                            <span>Cliente Verificado</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="quote-bg">"</div>
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-content">
                        "Excelente servicio y puntualidad. Recomendable al 100 %"
                    </p>
                    <div class="testimonial-author">
                        <img src="https://scontent.fcjs3-2.fna.fbcdn.net/v/t1.6435-1/119452341_3428698963834871_7459041854271920693_n.jpg?stp=dst-jpg_s200x200&_nc_cat=110&ccb=1-7&_nc_sid=e4545e&_nc_eui2=AeHv_YqGOikCcHer986Uh4ZWghgQvRbseN6CGBC9Fux43uLNEcXQMUN2O-U6Jca9Z2kfPp4xG9e7WHfAtS0KrEVh&_nc_ohc=4eLht6Ens_4Q7kNvgHiLXeK&_nc_ht=scontent.fcjs3-2.fna&oh=00_AYBxJK9ksqwVLDzjkfPVRiRPOmiUnGXAd9oGX7YyyFQAzA&oe=66C73596" 
                             alt="Cometa Montoya" class="author-img" onerror="this.outerHTML='<div class=\'author-avatar-text\'>CM</div>'">
                        <div class="author-info">
                            <h4>Cometa Montoya</h4>
                            <span>Cliente Verificado</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="quote-bg">"</div>
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-content">
                        "Un buen servicio un gran cuidado en el transporte de muebles y todos los electrodomésticos, lo recomiendo."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar-text"><i class="fa-solid fa-user"></i></div>
                        <div class="author-info">
                            <h4>Cliente Satisfecho</h4>
                            <span>Cliente Verificado</span>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin-top: 20px; display: none;" id="scrollHint" class="d-md-none">
                <p style="font-size: 0.85rem; color: var(--fc-text-muted);">
                    <i class="fa-solid fa-arrow-right"></i> Desliza para ver más reseñas
                </p>
            </div>
        </div>
    </section>

    <section id="ubicacion" class="py-5 bg-white">
        <div class="container py-5">
            <div class="row align-items-start g-4">
                <div class="col-lg-5 order-lg-1 order-2">
                    <div class="location-info-box">
                        <div class="badge-blue mb-3">
                            <i class="fa-solid fa-map-location-dot me-2"></i> Operaciones Locales
                        </div>
                        <h2 class="display-6 fw-bold mb-3">Ubicación Estratégica</h2>
                        <p class="text-muted fs-6 mb-4">
                            Nuestra base de operaciones se encuentra localizada en Cancún. Desde aquí gestionamos, aseguramos y coordinamos toda la logística necesaria para garantizar que tus pertenencias lleguen seguras y a tiempo a cualquier destino del país.
                        </p>
                        
                        <div class="d-flex align-items-start gap-3 mt-3">
                            <div class="icon-box" style="width: 40px; height: 40px; font-size: 1.1rem; flex-shrink: 0; margin-bottom: 0;">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h4 class="mb-1" style="font-size: 1rem;">FletesCun Base Cancún</h4>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                    C. 4ta Priv. Isla Galapagos sm 251<br>
                                    77510 Cancún, Quintana Roo, México.
                                </p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start gap-3 mt-3">
                            <div class="icon-box" style="width: 40px; height: 40px; font-size: 1.1rem; flex-shrink: 0; margin-bottom: 0;">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="mb-1" style="font-size: 1rem;">Horario de Atención</h4>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                    Lunes a Viernes: 9:00 AM - 6:00 PM<br>
                                    Sábados: 9:00 AM - 2:00 PM
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 order-lg-2 order-1">
                    <div class="map-container">
                        <iframe 
                            src="https://maps.google.com/maps?q=FletesCun,%20C.%204ta%20Priv.%20Isla%20Galapagos%20sm%20251,%2077510%20Canc%C3%BAn,%20Q.R.&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                            width="100%" 
                            height="100%" 
                            style="border:0; min-height: 300px;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="container">
            <div class="footer-logo-container">
                <img src="{{ asset('img/logo_fletes.png') }}" alt="Fletescun Logo" onerror="this.style.display='none'" style="max-width: 140px;">
                <p class="text-light opacity-75 mt-2 mx-auto" style="max-width: 500px; font-size: 0.9rem; margin-bottom: 0;">
                    Rutas seguras, contratos transparentes y atención personalizada para tu mudanza.
                </p>
            </div>

            <div class="row g-4 justify-content-between mb-3">
                <div class="col-lg-3 col-md-6 col-12">
                    <button class="footer-section-toggle d-md-none">
                        <span><i class="fa-solid fa-truck me-2"></i>Servicios</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <h5 class="d-none d-md-block"><i class="fa-solid fa-truck me-2" style="color: var(--fc-primary);"></i>Servicios</h5>
                    <div class="footer-section-content">
                        <ul class="list-unstyled d-flex flex-column gap-2">
                            <li><a href="#servicios" class="footer-link"><i class="fa-solid fa-people-carry-box" style="color: var(--fc-primary); width: 16px; text-align: center; margin-right: 6px;"></i>Mudanzas Compartidas</a></li>
                            <li><a href="#servicios" class="footer-link"><i class="fa-solid fa-star" style="color: var(--fc-accent-warning); width: 16px; text-align: center; margin-right: 6px;"></i>Rutas Exclusivas</a></li>
                            <li><a href="#servicios" class="footer-link"><i class="fa-solid fa-building" style="color: var(--fc-primary); width: 16px; text-align: center; margin-right: 6px;"></i>Almacenaje Seguro</a></li>
                            <li><a href="#servicios" class="footer-link"><i class="fa-solid fa-car" style="color: var(--fc-primary); width: 16px; text-align: center; margin-right: 6px;"></i>Traslado de Vehículos</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 col-12">
                    <button class="footer-section-toggle d-md-none">
                        <span><i class="fa-solid fa-file-contract me-2"></i>Legal</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <h5 class="d-none d-md-block"><i class="fa-solid fa-file-contract me-2" style="color: var(--fc-primary);"></i>Legal</h5>
                    <div class="footer-section-content">
                        <ul class="list-unstyled d-flex flex-column gap-2">
                            <li><a href="#" class="footer-link"><i class="fa-solid fa-scroll" style="color: var(--fc-primary); width: 16px; text-align: center; margin-right: 6px;"></i>Términos y Cláusulas</a></li>
                            <li><a href="#" class="footer-link"><i class="fa-solid fa-shield" style="color: var(--fc-primary); width: 16px; text-align: center; margin-right: 6px;"></i>Políticas de Daños</a></li>
                            <li><a href="#" class="footer-link"><i class="fa-solid fa-lock" style="color: var(--fc-primary); width: 16px; text-align: center; margin-right: 6px;"></i>Aviso de Privacidad</a></li>
                            <li><a href="#" class="footer-link"><i class="fa-solid fa-circle-question" style="color: var(--fc-accent-warning); width: 16px; text-align: center; margin-right: 6px;"></i>Preguntas Frecuentes</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-12 col-12">
                    <button class="footer-section-toggle d-md-none">
                        <span><i class="fa-solid fa-headset me-2"></i>Contacto</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <h5 class="d-none d-md-block"><i class="fa-solid fa-headset me-2" style="color: var(--fc-primary);"></i>Contacto</h5>
                    <div class="footer-section-content">
                        <div class="d-flex flex-column">
                            <div class="contact-item">
                                <i class="fa-solid fa-location-pin mt-1" style="color: #FF6B6B; font-size: 1.2rem; width: 20px;"></i>
                                <div>
                                    <span class="d-block text-white mb-1" style="font-size: 0.9rem;">Oficina Principal</span>
                                    <span style="font-size: 0.8rem;">Cancún, Quintana Roo</span>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fa-brands fa-whatsapp mt-1" style="color: #25D366; font-size: 1.2rem; width: 20px;"></i>
                                <div>
                                    <span class="d-block text-white mb-1" style="font-size: 0.9rem;">WhatsApp</span>
                                    <a href="https://wa.me/529982998328" target="_blank" style="font-size: 0.8rem; color: #25D366; text-decoration: none;">+52 998 299 8328</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pt-3 text-center d-flex flex-column flex-md-row justify-content-between align-items-center" style="border-top: 1px solid rgba(255,255,255,0.1);">
                <p class="mb-0 mb-2 mb-md-0" style="font-size: 0.8rem;">© 2026 FLETESCUN. Todos los derechos reservados.</p>
                <div class="d-flex gap-3 mt-2 mt-md-0 flex-wrap justify-content-center justify-content-md-end" style="font-size: 0.8rem;">
                    <a href="#" class="footer-link">Privacidad</a>
                    <a href="#" class="footer-link">Términos</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ========== CARRUSEL DE TESTIMONIOS ==========
        const carousel = document.getElementById('testimonialsCarousel');
        const scrollHint = document.getElementById('scrollHint');
        
        if (carousel) {
            // Mostrar scroll hint en móvil
            if (window.innerWidth < 768) {
                scrollHint.style.display = 'block';
            }
            
            // Touch support mejorado
            let startX, scrollLeft;
            carousel.addEventListener('mousedown', (e) => {
                startX = e.pageX - carousel.offsetLeft;
                scrollLeft = carousel.scrollLeft;
            });
            
            carousel.addEventListener('mouseleave', () => {
                startX = null;
            });
            
            carousel.addEventListener('mouseup', () => {
                startX = null;
            });
            
            carousel.addEventListener('mousemove', (e) => {
                if (!startX) return;
                e.preventDefault();
                const x = e.pageX - carousel.offsetLeft;
                const walk = (x - startX) * 0.5;
                carousel.scrollLeft = scrollLeft - walk;
            });
        }
        else {
            // Si carousel existe
        }

        // ========== ACORDEONES DEL FOOTER ==========
        const footerToggles = document.querySelectorAll('.footer-section-toggle');
        
        footerToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const nextSibling = this.nextElementSibling;
                
                // Cerrar otros acordeones abiertos (solo en móvil)
                if (window.innerWidth < 769) {
                    footerToggles.forEach(otherToggle => {
                        if (otherToggle !== this) {
                            otherToggle.classList.remove('active');
                            const otherContent = otherToggle.nextElementSibling;
                            if (otherContent) {
                                otherContent.classList.add('collapsed');
                            }
                        }
                    });
                }
                
                // Toggle actual
                this.classList.toggle('active');
                nextSibling.classList.toggle('collapsed');
            });
        });

        // Expand all on desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 769) {
                footerToggles.forEach(toggle => {
                    toggle.classList.remove('active');
                    const content = toggle.nextElementSibling;
                    if (content) {
                        content.classList.remove('collapsed');
                    }
                });
            }
        });

        // ========== SMOOTH SCROLL MEJORADO ==========
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href !== '') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });

        // ========== INDICADORES CARRUSEL RESPONSIVE ==========
        window.addEventListener('resize', () => {
            if (window.innerWidth < 768 && scrollHint) {
                scrollHint.style.display = 'block';
            } else if (scrollHint) {
                scrollHint.style.display = 'none';
            }
        });
    </script>
</body>
</html>