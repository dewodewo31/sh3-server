    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8">
        <title>{{ $event->title }} - Event Brochure</title>

        <style>
            body {
                font-family: Helvetica, Arial, sans-serif;
                background: #f4f6f8;
                padding: 30px;
                color: #333;
            }

            .container {
                max-width: 850px;
                margin: auto;
                background: #fff;
                border-radius: 12px;
                padding: 25px;
            }

            /* HEADER */
            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .logo {
                font-size: 24px;
                font-weight: bold;
                color: #4CAF50;
            }

            .subtitle {
                font-size: 12px;
                color: #888;
            }

            /* BANNER */
            .banner {
                height: 250px;
                border-radius: 12px;
                position: relative;
                overflow: hidden;
                margin-bottom: 20px;
            }

            /* overlay gelap 30% */
            .banner-dark {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
            }

            .banner-overlay {
                position: absolute;
                bottom: 0;
                width: 100%;
                padding: 20px;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            }

            .banner-title {
                color: #fff;
                font-size: 26px;
                font-weight: bold;
            }

            /* STATUS */
            .status {
                text-align: center;
                margin-bottom: 20px;
            }

            .badge {
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: bold;
            }

            .upcoming {
                background: #fff3cd;
                color: #856404;
            }

            .ongoing {
                background: #d4edda;
                color: #155724;
            }

            .finished {
                background: #e2e3e5;
                color: #383d41;
            }

            /* INFO GRID */
            .grid {
                display: flex;
                gap: 10px;
                margin-bottom: 20px;
            }

            .card {
                flex: 1;
                background: #f9fafb;
                padding: 12px;
                border-radius: 10px;
                text-align: center;
            }

            .card small {
                color: #888;
                font-size: 10px;
            }

            .card strong {
                display: block;
                margin-top: 5px;
                font-size: 13px;
            }

            /* SECTION */
            .section {
                margin-top: 20px;
            }

            .section h3 {
                color: #4CAF50;
                font-size: 15px;
                margin-bottom: 10px;
            }

            .section p {
                font-size: 13px;
                line-height: 1.7;
                color: #555;
            }

            /* LIST */
            .list {
                list-style: disc;
                padding: 18px;
            }

            .list li {
                font-size: 13px;
                padding: 5px 0;
            }

            .list li::before {
                color: #4CAF50;
            }

            /* PROGRESS */
            .progress-box {
                margin-top: 15px;
            }

            .progress-bar {
                height: 8px;
                background: #eee;
                border-radius: 5px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                background: #4CAF50;

                width: {
                        {
                        $event->quota >0 ? ($registeredCount / $event->quota) * 100: 0
                    }
                }

                %;
            }

            /* PRICE */
            .price {
                margin-top: 20px;
                padding: 20px;
                text-align: center;
                background: linear-gradient(135deg, #4CAF50, #43a047);
                color: #fff;
                border-radius: 10px;
            }

            .price span {
                font-size: 12px;
            }

            .price h2 {
                margin-top: 5px;
                font-size: 28px;
            }

            /* FOOTER */
            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 11px;
                color: #999;
            }
        </style>
    </head>

    <body>

        <div class="container">

            <!-- HEADER -->
            <div class="header">
                <div class="logo">SH3 EVENT</div>
                <div class="subtitle">Event Management System</div>
            </div>

            <!-- BANNER -->
            <div class="banner"
                style="
            @if($event->image)
                background-image: url('{{ public_path('storage/' . $event->image) }}');
                background-size: cover;
                background-position: center;
            @else
                background: linear-gradient(135deg,#667eea,#764ba2);
            @endif
            ">
            <div class="banner-dark"></div>
                <div class="banner-overlay">
                    <div class="banner-title">{{ $event->title }}</div>
                </div>
            </div>

            <!-- STATUS -->
            <div class="status">
                <span class="badge 
                @if($event->status == 'upcoming') upcoming
                @elseif($event->status == 'ongoing') ongoing
                @else finished @endif">
                    {{ strtoupper($event->status) }}
                </span>
            </div>

            <!-- INFO -->
            <div class="grid">
                <div class="card">
                    <small>DATE</small>
                    <strong>{{ $event->start_date->format('d M Y') }}</strong>
                </div>
                <div class="card">
                    <small>TIME</small>
                    <strong>{{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }}</strong>
                </div>
                <div class="card">
                    <small>LOCATION</small>
                    <strong>{{ $event->location }}</strong>
                </div>
                <div class="card">
                    <small>CATEGORY</small>
                    <strong>{{ $event->category->name ?? 'N/A' }}</strong>
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div class="section">
                <h3>About Event</h3>
                <p>{{ $event->description }}</p>
            </div>

            <!-- KEY POINT -->
            @if($event->key_point && count($event->key_point))
            <div class="section">
                <h3>Key Highlights</h3>
                <ul class="list">
                    @foreach($event->key_point as $point)
                    <li>{{ $point }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- QUOTA -->
            <div class="section">
                <h3>Registration</h3>
                <div class="progress-box">
                    <div style="font-size:12px;margin-bottom:5px;">
                        {{ $registeredCount }} / {{ $event->quota }} peserta
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <div style="font-size:11px;margin-top:5px;color:#777;">
                        Sisa: {{ $remainingQuota }} slot
                    </div>
                </div>
            </div>

            <!-- PRICE -->
            <div class="price">
                <span>Ticket Price</span>
                <h2>
                    @if($event->price > 0)
                    Rp {{ number_format($event->price, 0, ',', '.') }}
                    @else
                    FREE
                    @endif
                </h2>
            </div>

            <!-- FOOTER -->
            <div class="footer">
                <p>Generated by SH3 Event System</p>
            </div>

        </div>

    </body>

    </html>