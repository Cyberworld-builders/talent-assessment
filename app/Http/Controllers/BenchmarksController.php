<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Benchmark;
use App\Dimension;
use App\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BenchmarksController extends Controller
{
    /**
     * Show the assessment selection page for benchmarks.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectAssessment()
    {
        $assessments = Assessment::orderBy('name')->get();
        
        return view('dashboard.benchmarks.select-assessment', compact('assessments'));
    }

    /**
     * Show the industry selection page for benchmarks.
     *
     * @param int $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function selectIndustry($assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $industries = Industry::orderBy('name')->get();
        
        return view('dashboard.benchmarks.select-industry', compact('assessment', 'industries'));
    }

    /**
     * Display the benchmarks for a specific assessment and industry.
     *
     * @param int $assessmentId
     * @param int $industryId
     * @return \Illuminate\Http\Response
     */
    public function index($assessmentId, $industryId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $industry = Industry::findOrFail($industryId);
        $dimensions = $assessment->dimensions()->orderBy('name')->get();
        
        // Get existing benchmarks for this assessment and industry
        $benchmarks = Benchmark::where('industry_id', $industryId)
            ->whereHas('dimension', function ($query) use ($assessmentId) {
                $query->where('assessment_id', $assessmentId);
            })
            ->get()
            ->keyBy('dimension_id');
        
        return view('dashboard.benchmarks.index', compact('assessment', 'industry', 'dimensions', 'benchmarks'));
    }

    /**
     * Store benchmark data.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Debug: Log the incoming data
        \Log::info('Benchmark store request data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required|exists:assessments,id',
            'industry_id' => 'required|exists:industries,id',
            'benchmarks' => 'required|array'
        ]);

        // Debug validation errors
        if ($validator->fails()) {
            \Log::info('Validation errors:', $validator->errors()->toArray());
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $assessmentId = $request->input('assessment_id');
        $industryId = $request->input('industry_id');
        $benchmarks = $request->input('benchmarks');

        foreach ($benchmarks as $benchmarkData) {
            $dimensionId = $benchmarkData['dimension_id'];
            $value = trim($benchmarkData['value'] ?? '');

            // Skip if no value provided
            if (empty($value)) {
                continue;
            }

            // Check if dimension belongs to the assessment
            $dimension = Dimension::where('id', $dimensionId)
                ->where('assessment_id', $assessmentId)
                ->first();

            if (!$dimension) {
                continue; // Skip if dimension doesn't belong to assessment
            }

            // Update or create benchmark
            Benchmark::updateOrCreate(
                [
                    'dimension_id' => $dimensionId,
                    'industry_id' => $industryId
                ],
                [
                    'value' => $value
                ]
            );
        }

        return redirect()->back()
            ->with('success', 'Benchmarks saved successfully!');
    }

    /**
     * Handle Excel file upload for bulk benchmark data.
     *
     * @param Request $request
     * @param int $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, $assessmentId)
    {
        // Suppress deprecation warnings for PHPExcel
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        
        // Debug: Log the uploaded file info
        if ($request->hasFile('excel_file')) {
            $file = $request->file('excel_file');
            \Log::info('Uploaded file info:', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension()
            ]);
        } else {
            \Log::info('No file uploaded');
        }
        
        // Manual validation instead of using Laravel's file validation
        if (!$request->hasFile('excel_file')) {
            return redirect()->back()
                ->with('error', 'Please select a file to upload.')
                ->withInput();
        }

        $file = $request->file('excel_file');
        
        // Check file size (2MB limit)
        if ($file->getSize() > 2048 * 1024) {
            return redirect()->back()
                ->with('error', 'File size must be less than 2MB.')
                ->withInput();
        }

        // Check file extension - CSV only for now
        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension !== 'csv') {
            return redirect()->back()
                ->with('error', 'Please upload a CSV file (.csv). Excel support coming soon.')
                ->withInput();
        }

        $industryId = $request->input('industry_id');
        $assessment = Assessment::findOrFail($assessmentId);
        $industry = Industry::findOrFail($industryId);

        try {
            $file = $request->file('excel_file');
            
            // Debug: Check file details
            \Log::info('Processing file:', [
                'path' => $file->getPathname(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ]);
            
            // ZipArchive check only needed for Excel files, not CSV
            
            // CSV only for now - Excel support coming soon
            $data = [];
            
            // Read CSV file
            $handle = fopen($file->getPathname(), 'r');
            if ($handle) {
                $isFirstRow = true;
                while (($row = fgetcsv($handle)) !== false) {
                    // Skip header row
                    if ($isFirstRow) {
                        $isFirstRow = false;
                        continue;
                    }
                    $data[] = $row;
                }
                fclose($handle);
            }
            \Log::info('CSV file processed successfully. Rows: ' . count($data));

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($data as $row) {
                if (count($row) < 2) {
                    $errorCount++;
                    continue;
                }

                $dimensionName = trim($row[0]);
                $value = trim($row[1]);

                if (empty($dimensionName) || empty($value)) {
                    $errorCount++;
                    continue;
                }

                // Find dimension by name for this assessment
                $dimension = Dimension::where('name', $dimensionName)
                    ->where('assessment_id', $assessmentId)
                    ->first();

                if (!$dimension) {
                    $errors[] = "Dimension '$dimensionName' not found for assessment '{$assessment->name}'";
                    $errorCount++;
                    continue;
                }

                // Create or update benchmark
                Benchmark::updateOrCreate(
                    [
                        'dimension_id' => $dimension->id,
                        'industry_id' => $industryId
                    ],
                    [
                        'value' => $value
                    ]
                );

                $successCount++;
            }

            $message = "Upload completed: $successCount benchmarks processed successfully.";
            if ($errorCount > 0) {
                $message .= " $errorCount rows had errors.";
            }

            return redirect()->back()
                ->with('success', $message)
                ->with('upload_errors', $errors);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error processing Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Download benchmark template Excel file.
     * TEMPORARILY DISABLED - Excel support coming soon
     *
     * @param int $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate($assessmentId)
    {
        return redirect()->back()
            ->with('error', 'Excel template downloads are temporarily unavailable. Please use CSV format instead.');
        
        // TODO: Re-enable when PHP/Laravel versions are upgraded
        /*
        // Suppress deprecation warnings for PHPExcel
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        
        $assessment = Assessment::findOrFail($assessmentId);
        $dimensions = $assessment->dimensions()->orderBy('name')->get();

        $data = [];
        foreach ($dimensions as $dimension) {
            $data[] = [
                'Dimension Name' => $dimension->name,
                'Benchmark Value' => ''
            ];
        }

        Excel::create("benchmarks_template_{$assessment->name}", function($excel) use ($data) {
            $excel->sheet('Benchmarks', function($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->download('xlsx');
        */
    }

    /**
     * Download benchmark template CSV file.
     *
     * @param int $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function downloadCsvTemplate($assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $dimensions = $assessment->dimensions()->orderBy('name')->get();

        $filename = "benchmarks_template_{$assessment->name}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($dimensions) {
            $file = fopen('php://output', 'w');
            
            // Add header row
            fputcsv($file, ['Dimension Name', 'Benchmark Value']);
            
            // Add data rows
            foreach ($dimensions as $dimension) {
                fputcsv($file, [$dimension->name, '']);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
