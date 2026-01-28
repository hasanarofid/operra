<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545e;
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: none;
        }
        .email-wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #f4f4f7;
        }
        .email-content {
            width: 100%;
            margin: 0;
            padding: 0;
            max-width: 600px;
            margin: 0 auto; 
        }
        .email-masthead {
            padding: 25px 0;
            text-align: center;
        }
        .email-masthead_name {
            font-size: 24px;
            font-weight: bold;
            color: #2F3133; /* Darker text for better readability */
            text-decoration: none;
            text-shadow: 0 1px 0 white;
            letter-spacing: 1px;
        }
        .email-body {
            width: 100%;
            margin: 0;
            padding: 0;
            border-top: 1px solid #e7eaec;
            border-bottom: 1px solid #e7eaec;
            background-color: #FFFFFF;
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .email-body_inner {
            width: 570px;
            margin: 0 auto;
            padding: 35px;
        }
        .email-footer {
            width: 570px;
            margin: 0 auto;
            padding: 25px;
            text-align: center;
        }
        .email-footer p {
            color: #A8AAAF;
            font-size: 13px; /* Slightly larger footer text */
        }
        .body-action {
            width: 100%;
            margin: 30px auto;
            padding: 0;
            text-align: center;
        }
        .button {
            display: inline-block;
            background-color: #22BC66; /* Green accent color */
            color: #ffffff;
            font-size: 16px;
            line-height: 45px;
            text-align: center;
            text-decoration: none;
            width: 200px;
            border-radius: 5px; /* Rounded button */
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
            -webkit-text-size-adjust: none;
        }
        .button--red {
            background-color: #FF5D5D;
        }
        .button--blue {
            background-color: #3869D4;
        }
        .attributes {
            margin: 0 0 21px;
        }
        .attributes_content {
            background-color: #F4F4F7;
            padding: 16px;
            border-radius: 5px;
        }
        .attributes_item {
            padding: 0;
        }
        h1 {
            margin-top: 0;
            color: #2F3133;
            font-size: 24px; /* Larger H1 */
            font-weight: bold;
            text-align: left;
        }
        h2 {
            margin-top: 0;
            color: #2F3133;
            font-size: 18px; /* Larger H2 */
            font-weight: bold;
            text-align: left;
        }
        h3 {
            margin-top: 0;
            color: #2F3133;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
        }
        p {
            margin-top: 0;
            color: #74787E;
            font-size: 16px;
            line-height: 1.6em; /* Better line height */
            text-align: left;
        }
        p.sub {
            font-size: 13px;
        }
        p.center {
            text-align: center;
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table {
            width: 100%;
        }
        td {
            padding: 5px 0;
        }
        /* Gradient Header Line */
        .color-strip {
             height: 5px;
             background: linear-gradient(90deg, #3869D4 0%, #22BC66 100%);
             width: 100%;
             border-radius: 8px 8px 0 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table class="email-content">
            <!-- Logo -->
            <tr>
                <td class="email-masthead">
                    <a href="{{ url('/') }}" class="email-masthead_name">
                        <span style="color: #3869D4;">OP</span>ERRA
                    </a>
                </td>
            </tr>
            <!-- Email Body -->
            <tr>
                <td class="email-body">
                    <div class="color-strip"></div>
                    <div class="email-body_inner">
                        @yield('content')
                    </div>
                    <p style="padding: 0 35px; margin-bottom: 30px; font-size: 14px; color: #999;">
                        Terima kasih,<br>
                        <strong>Tim {{ config('app.name') }}</strong>
                    </p>
                </td>
            </tr>
            <!-- Footer -->
            <tr>
                <td>
                    <div class="email-footer">
                        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        <p>
                            Operra HQ<br>
                            Jakarta, Indonesia
                        </p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
