<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Franchise Affiliation Certificate - {{ $centre->centre_code }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Montserrat:wght@400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Montserrat', sans-serif;
            color: #222;
            margin: 0;
            padding: 20px;
        }

        .cert-container {
            width: 297mm;
            height: 210mm;
            max-width: 1120px;
            margin: 0 auto;
            background: #fff;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            box-sizing: border-box;
            padding: 20px;
            overflow: hidden;
        }

        .cert-outer-border {
            border: 6px solid #b8860b;
            height: 100%;
            padding: 10px;
            position: relative;
            box-sizing: border-box;
        }

        .cert-inner-border {
            border: 2px solid #daa520;
            height: 100%;
            padding: 25px 40px;
            position: relative;
            background: radial-gradient(circle at center, #ffffff 0%, #fdfbf7 100%);
            box-sizing: border-box;
        }

        /* Watermark */
        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            width: 480px;
            pointer-events: none;
            z-index: 1;
        }

        .cert-header {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .cert-title-org {
            font-family: 'Cinzel', serif;
            font-size: 32px;
            font-weight: 900;
            color: #4a154b;
            letter-spacing: 2px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .cert-subtitle {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .cert-heading-badge {
            display: inline-block;
            background: linear-gradient(135deg, #b8860b, #daa520);
            color: #fff;
            font-family: 'Cinzel', serif;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 3px;
            padding: 6px 36px;
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(184, 134, 11, 0.25);
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .cert-body {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-top: 10px;
        }

        .cert-lead {
            font-size: 15px;
            color: #444;
            margin-bottom: 10px;
        }

        .cert-centre-name {
            font-family: 'Cinzel', serif;
            font-size: 26px;
            font-weight: 800;
            color: #1a1a1a;
            border-bottom: 2px solid #daa520;
            display: inline-block;
            padding-bottom: 4px;
            margin-bottom: 12px;
        }

        .cert-desc {
            font-size: 14px;
            color: #444;
            max-width: 820px;
            margin: 0 auto 15px auto;
            line-height: 1.6;
        }

        .cert-meta-grid {
            position: relative;
            z-index: 2;
            margin: 15px auto;
            max-width: 850px;
        }

        .meta-box {
            background: #fdfaf3;
            border: 1px solid #ebd7b2;
            padding: 8px 15px;
            border-radius: 4px;
            text-align: center;
        }

        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #8b6508;
            letter-spacing: 1px;
        }

        .meta-value {
            font-size: 14px;
            font-weight: 700;
            color: #222;
        }

        .cert-footer {
            position: relative;
            z-index: 2;
            margin-top: 25px;
        }

        .sig-block {
            text-align: center;
        }

        .sig-line {
            width: 160px;
            border-top: 1px solid #444;
            margin: 5px auto 4px auto;
        }

        .sig-title {
            font-size: 11px;
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
        }

        .gold-seal {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: radial-gradient(circle, #ffe082 0%, #ffd54f 40%, #b8860b 100%);
            border: 3px dashed #8b6508;
            box-shadow: 0 0 10px rgba(184, 134, 11, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #5d4037;
            font-weight: 800;
            font-size: 10px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 auto;
        }

        /* Non-print toolbar */
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .cert-container {
                box-shadow: none;
                max-width: 100%;
                width: 100%;
                height: 100vh;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Print Control Bar -->
    <div class="no-print text-center mb-3">
        <a href="{{ route('admin.centres.show', $centre) }}" class="btn btn-secondary me-2">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Dossier
        </a>
        <button onclick="window.print()" class="btn btn-primary shadow-sm">
            <i class="fa-solid fa-print me-1"></i> Print Affiliation Certificate
        </button>
    </div>

    <!-- Certificate Container -->
    <div class="cert-container">
        <div class="cert-outer-border">
            <div class="cert-inner-border">
                <!-- Background Seal Watermark -->
                <div class="cert-watermark text-center">
                    <i class="fa-solid fa-graduation-cap" style="font-size: 400px; color: #b8860b;"></i>
                </div>

                <!-- Header -->
                <div class="cert-header">
                    <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="mb-2" style="max-height: 60px; width: auto; object-fit: contain;">
                    <div class="cert-title-org" style="color: #0284c7;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                    <div class="cert-subtitle" style="color: #ea580c;">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                    <div class="small text-muted">{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</div>
                    <div>
                        <span class="cert-heading-badge">Certificate of Affiliation</span>
                    </div>
                </div>

                <!-- Body Content -->
                <div class="cert-body">
                    <div class="cert-lead">This is to officially certify that the learning centre</div>

                    <div class="cert-centre-name">{{ $centre->centre_name }}</div>

                    <div class="cert-desc">
                        Under the directorship of <strong>{{ $centre->director_name }}</strong>, located at <strong>{{ $centre->city }}, {{ $centre->district?->name }}, {{ $centre->state?->name }} (PIN: {{ $centre->pincode }})</strong>, has fulfilled the required standards of computer lab infrastructure, teaching faculty, and institutional ethics, and is hereby accredited as an <strong>Authorized Training Centre (ATC)</strong>.
                    </div>

                    <!-- Meta Grid -->
                    <div class="row g-2 cert-meta-grid">
                        <div class="col-3">
                            <div class="meta-box">
                                <div class="meta-label">Affiliation Code</div>
                                <div class="meta-value font-monospace text-primary">{{ $centre->centre_code }}</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="meta-box">
                                <div class="meta-label">Grant Date</div>
                                <div class="meta-value">{{ $centre->affiliation_date?->format('d M, Y') ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="meta-box">
                                <div class="meta-label">Valid Upto</div>
                                <div class="meta-value">{{ $centre->valid_upto?->format('d M, Y') ?? 'Permanent' }}</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="meta-box">
                                <div class="meta-label">Lab Terminals</div>
                                <div class="meta-value">{{ $centre->lab_capacity }} Workstations</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signatures & Seal -->
                <div class="row align-items-center cert-footer">
                    <div class="col-4">
                        <div class="sig-block">
                            <div class="font-monospace text-muted small" style="height: 35px;">
                                <i class="fa-solid fa-shield-halved text-success fa-2x"></i>
                            </div>
                            <div class="sig-line"></div>
                            <div class="sig-title">Director of Accreditation</div>
                            <div class="small text-muted" style="font-size: 9px;">Central Verification Board</div>
                        </div>
                    </div>

                    <div class="col-4 text-center">
                        <div class="gold-seal">
                            <span>Official<br>Affiliation<br>Seal</span>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="sig-block">
                            <div style="font-family: 'Great Vibes', cursive; font-size: 26px; color: #222; height: 35px; line-height: 35px;">
                                R. K. Sharma
                            </div>
                            <div class="sig-line"></div>
                            <div class="sig-title">Chairman / Director General</div>
                            <div class="small text-muted" style="font-size: 9px;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
