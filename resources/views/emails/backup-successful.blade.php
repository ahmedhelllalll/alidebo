@extends('emails.layout')

@section('content')
    <h1>{{ __('admin.backup_completed') ?? 'Database Backup Successful' }}</h1>
    
    <p>{{ __('admin.backup_queued_desc') ?? 'Your automated database backup was generated, encrypted, and distributed successfully. Your system is fully protected.' }}</p>
    
    <div class="note-section" style="background-color: #f8fafc; border-left: 4px solid #10b981; padding: 16px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0 0 10px 0; font-weight: 600; color: #334155;">Backup Details:</p>
        <ul style="margin: 0; padding-left: 20px; color: #475569; font-size: 14px;">
            <li style="margin-bottom: 5px;"><strong>File Name:</strong> {{ $filename }}</li>
            <li><strong>File Size:</strong> {{ $size }} MB</li>
        </ul>
    </div>

    <div class="btn-container">
        <a href="{{ url('/admin/backups') }}" class="btn">{{ __('admin.manage_secure_backups') ?? 'Manage Backups' }}</a>
    </div>

    <div class="benefit-section">
        <p style="margin-bottom: 0; color: #10b981; font-weight: 600;">System Protected <span style="display:inline-block; width:8px; height:8px; background-color:#34d399; border-radius:50%; margin-left:5px;"></span></p>
        <p style="font-size: 13px; margin-top: 8px; color: #64748b;">
            {{ __('admin.auto_backup_desc') ?? 'Rest easy! Your system automatically generates and securely stores a fresh, encrypted snapshot of all your database records every 12 hours.' }}
        </p>
    </div>
@endsection
