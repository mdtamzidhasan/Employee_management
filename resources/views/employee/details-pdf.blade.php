<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details — {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DejaVu Sans', sans-serif;
        }

        body {
            background: #ffffff;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.6;
        }

        /* Header */
        .header {
            background: #4f46e5;
            color: white;
            padding: 28px 36px;
            margin-bottom: 28px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .company-sub {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 2px;
        }

        .doc-title {
            text-align: right;
            font-size: 11px;
            opacity: 0.8;
        }

        .doc-title strong {
            display: block;
            font-size: 14px;
            opacity: 1;
            margin-bottom: 2px;
        }

        /* Avatar + Name */
        .profile-section {
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .avatar {
            width: 52px;
            height: 52px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
            color: white;
            border: 2px solid rgba(255,255,255,0.4);
        }

        .profile-name {
            font-size: 18px;
            font-weight: bold;
        }

        .profile-email {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 5px;
            background: rgba(255,255,255,0.2);
            color: white;
        }

        /* Body */
        .body {
            padding: 0 36px 36px;
        }

        /* Section */
        .section {
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #6366f1;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }

        /* Grid */
        .grid {
            width: 100%;
        }

        .grid tr td {
            padding: 6px 0;
            vertical-align: top;
            width: 50%;
        }

        .label {
            font-size: 10px;
            color: #94a3b8;
            margin-bottom: 2px;
            display: block;
        }

        .value {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }

        .value.empty {
            color: #cbd5e1;
            font-weight: normal;
        }

        /* Full width row */
        .full-row td {
            width: 100% !important;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 12px 36px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="header-top">
            <div>
                <div class="company-name">EMS</div>
                <div class="company-sub">Employee Management System</div>
            </div>
            <div class="doc-title">
                <strong>Employee Details</strong>
                Generated: {{ now()->format('d M Y, h:i A') }}
            </div>
        </div>

        <div class="profile-section">

    {{-- Photo or Initial --}}
    <div class="avatar" style="padding:0; overflow:hidden;">
        @if($user->employee->profile_photo)
            <img src="{{ public_path($user->employee->profile_photo) }}"
                 style="width:52px; height:52px; object-fit:cover; border-radius:50%;">
        @else
            {{ strtoupper(substr($user->name, 0, 1)) }}
        @endif
    </div>
            <div>
                <div class="profile-name">{{ $user->name }}</div>
                <div class="profile-email">{{ $user->email }}</div>
                @php $status = $user->employee->status ?? 'active'; @endphp
                <span class="status-badge">{{ ucfirst($status) }}</span>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="body">

        {{-- Account Information --}}
        <div class="section">
            <div class="section-title">Account Information</div>
            <table class="grid">
                <tr>
                    <td>
                        <span class="label">Full Name</span>
                        <span class="value">{{ $user->name }}</span>
                    </td>
                    <td>
                        <span class="label">Email Address</span>
                        <span class="value">{{ $user->email }}</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Job Information --}}
        <div class="section">
            <div class="section-title">Job Information</div>
            <table class="grid">
                <tr>
                    <td>
                        <span class="label">Department</span>
                        <span class="value {{ !$user->employee->department ? 'empty' : '' }}">
                            {{ $user->employee->department ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <span class="label">Position</span>
                        <span class="value {{ !$user->employee->position ? 'empty' : '' }}">
                            {{ $user->employee->position ?? '—' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Salary</span>
                        <span class="value {{ !$user->employee->salary ? 'empty' : '' }}">
                            @if($user->employee->salary)
                                {{ number_format($user->employee->salary, 2) }}
                            @else
                                —
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="label">Joining Date</span>
                        <span class="value {{ !$user->employee->joining_date ? 'empty' : '' }}">
                            {{ $user->employee?->joining_date?->format('d M Y') ?? '—' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Status</span>
                        <span class="value">{{ ucfirst($user->employee->status ?? 'active') }}</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Contact Information --}}
        <div class="section">
            <div class="section-title">Contact Information</div>
            <table class="grid">
                <tr>
                    <td>
                        <span class="label">Phone Number</span>
                        <span class="value {{ !$user->employee->phone ? 'empty' : '' }}">
                            {{ $user->employee->phone ?? '—' }}
                        </span>
                    </td>
                    <td></td>
                </tr>
                @if($user->employee->address)
                <tr class="full-row">
                    <td colspan="2">
                        <span class="label">Address</span>
                        <span class="value">{{ $user->employee->address }}</span>
                    </td>
                </tr>
                @endif
            </table>
        </div>

    </div>

    {{-- Footer --}}
    <div class="footer">
        <span>{{ $user->name }} — Employee Details</span>
        <span>EMS — Employee Management System</span>
    </div>

</body>
</html>