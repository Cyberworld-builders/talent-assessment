<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReportData extends Model
{
    protected $table = 'report_data';

    protected $fillable = [
        'user_id',
        'assignment_id',
        'score',
        'slug',
        'html_url',
        'pdf_url'
    ];

    protected $casts = [
        'score' => 'array',
    ];

    /**
     * Get the user that owns the report data.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assignment that owns the report data.
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Generate a unique slug for this report.
     *
     * @return string
     */
    public function generateSlug()
    {
        if ($this->slug) {
            return $this->slug;
        }

        $user = $this->user;
        $assignment = $this->assignment;
        
        if (!$user || !$assignment) {
            return null;
        }

        // Create slug from user name, assignment ID, and timestamp
        $userName = Str::slug($user->name);
        $clientId = $user->client_id ?? 'unknown';
        $assignmentId = $assignment->id;
        $timestamp = $this->created_at ? $this->created_at->format('Ymd-His') : date('Ymd-His');
        
        $this->slug = "{$clientId}-{$userName}-{$assignmentId}-{$timestamp}";
        $this->save();

        return $this->slug;
    }

    /**
     * Get the S3 path for HTML file.
     *
     * @return string
     */
    public function getHtmlS3Path()
    {
        if (!$this->slug) {
            $this->generateSlug();
        }

        $clientId = $this->user->client_id ?? 'unknown';
        return "reports/{$clientId}/{$this->slug}.html";
    }

    /**
     * Get the S3 path for PDF file.
     *
     * @return string
     */
    public function getPdfS3Path()
    {
        if (!$this->slug) {
            $this->generateSlug();
        }

        $clientId = $this->user->client_id ?? 'unknown';
        return "reports/{$clientId}/{$this->slug}.pdf";
    }
}

